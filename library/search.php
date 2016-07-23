<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *	This file is part of Goteo.
 *
 *  Goteo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Goteo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Goteo.  If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */


namespace Goteo\Library {

//    use Goteo\Controller\Skillmatching;
    use Goteo\Core\Model,
        Goteo\Model\Project,
        Goteo\Model\Skillmatching;

	/*
	 * Clase para realizar búsquedas de proyectos
	 *
	 */
    class Search {

        /**
         * Metodo para buscar un textxto entre todos los contenidos de texto de un proyecto
         * @param string $value
         * @return array results
         */
		public static function text ($value) {

            $results = array();

            $values = array(':text'=>"%$value%");

            $sql = "SELECT id
                    FROM project
                    WHERE status > 2
                    AND (name LIKE :text
                        OR description LIKE :text
                        OR motivation LIKE :text
                        OR about LIKE :text
                        OR goal LIKE :text
                        OR related LIKE :text
                        OR keywords LIKE :text
                        OR location LIKE :text
                        )
                    ORDER BY name ASC";

            try {
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $match) {
                    $results[] = Project::getMedium($match->id);
                }
                return $results;
            } catch (\PDOException $e) {
                throw new Exception('Fallo la sentencia de busqueda');
            }
		}

        public static function text_all ($value) {

            $results = array();

            $values = array(':text'=>"%$value%");

            $sql = "(
                        SELECT id, 'pj' as type
                        FROM project
                        WHERE status > 2
                        AND (name LIKE :text
                            OR description LIKE :text
                            OR motivation LIKE :text
                            OR about LIKE :text
                            OR goal LIKE :text
                            OR related LIKE :text
                            OR keywords LIKE :text
                            OR location LIKE :text
                            )
                    ) UNION (
                        SELECT id, 'sm' as type
                        FROM skillmatching
                        WHERE status > 2
                        AND (name LIKE :text
                            OR description LIKE :text
                            OR motivation LIKE :text
                            OR about LIKE :text
                            OR goal LIKE :text
                            OR related LIKE :text
                            OR keywords LIKE :text
                            OR location LIKE :text
                            )
                    )
                    ORDER BY name ASC";

            try {
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $match) {
                    if ($match->type == 'pj'){
                        $results[] = Project::getMedium($match->id);
                    } else {
                        $results[] = Skillmatching::getMedium($match->id);
                    }
                }
                return $results;
            } catch (\PDOException $e) {
                throw new Exception('Fallo la sentencia de busqueda');
            }
        }


        /**
         * Metodo para realizar una busqueda por parametros
         * @param array multiple $params 'category', 'location', 'reward'
         * @param bool showall si true, muestra tambien proyectos en estado de edicion y revision
         * @return array results
         */
		public static function params ($params, $showall = false, $limit = null) {

            $results = array();
            $where   = array();
            $values  = array();

            if (!empty($params['skills'])) {
                $where[] = 'AND id IN (
                                    SELECT distinct(project)
                                    FROM project_skill
                                    WHERE skill IN ('. implode(', ', $params['skills']) . ')
                                )';
            }


            if (!empty($params['category'])) {
                $where[] = 'AND id IN (
                                    SELECT distinct(project)
                                    FROM project_category
                                    WHERE category IN ('. implode(', ', $params['category']) . ')
                                )';
            }

            if (!empty($params['location'])) {
                $where[] = 'AND MD5(project_location) IN ('. implode(', ', $params['location']) .')';
            }

            if (!empty($params['reward'])) {
                $where[] = 'AND id IN (
                                    SELECT DISTINCT(project)
                                    FROM reward
                                    WHERE icon IN ('. implode(', ', $params['reward']) . ')
                                    )';
            }

            if (!empty($params['query'])) {
                $where[] = ' AND (name LIKE :text
                                OR description LIKE :text
                                OR motivation LIKE :text
                                OR about LIKE :text
                                OR goal LIKE :text
                                OR related LIKE :text
                                OR keywords LIKE :text
                                OR location LIKE :text
                            )';
                $values[':text'] = "%{$params['query']}%";
            }

            
            if (!empty($params['node'])) {
                $where[] = ' AND node = :node';
                $values[':node'] = NODE_ID;
            }

            if (!empty($params['status'])) {
                $where[] = ' AND status = :status';
                $values[':status'] = $params['status'];
            }

            $minstatus = ($showall) ? '1' : '2';
            $maxstatus = ($showall) ? '4' : '7';

            $where_sql = '';
            if (!empty($where)) {
                $where_sql = implode (' ', $where);
            };

            $sql = "(
                        SELECT id, 'sm' as type, status, name
                        FROM skillmatching
                        WHERE status > $minstatus
                        AND status < $maxstatus
                        $where_sql
                    ) UNION (
                        SELECT id, 'pj' as type, status, name
                        FROM project
                        WHERE status > $minstatus
                        AND status < $maxstatus
                        $where_sql
                    )";

            if (!empty($params['types'])) {
                if ($params['types'][0] == 'project'){
                    $sql = "SELECT id, 'pj' as type
                    FROM project
                    WHERE status > $minstatus
                    AND status < $maxstatus
                    $where_sql
                    ";
                } else if ($params['types'][0] == 'skillmatching'){

                    $sql = "SELECT id, 'sm' as type
                        FROM skillmatching
                        WHERE status > $minstatus
                        AND status < $maxstatus
                        $where_sql
                    ";
                }
            };

//            if (!empty($where)) {
//                $sql .= implode (' ', $where);
//            }

            $sql .= " ORDER BY ";

            if (!empty($params['orderby'])){
                $sql .= $params['orderby'];
                if (!empty($params['order'])){
                    $sql .= " " . $params['order'];
                } else {
                    $sql .= " ASC";
                }
                $sql .= ',';
            }
            $sql .= "status ASC, name ASC";

            // Limite
            if (!empty($limit) && \is_numeric($limit)) {
                $sql .= " LIMIT $limit";
            }

//            var_dump($sql);
//            var_dump($values);
//            exit;
//var_dump($sql);exit;
            try {
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $match) {
                    if ($match->type == 'pj'){
                        $results[] = Project::getMedium($match->id);
                    } else {
                        $results[] = Skillmatching::getMedium($match->id);
                    }
                }
                return $results;
            } catch (\PDOException $e) {
                throw new Exception('Fallo la sentencia de busqueda');
            }
		}

	}

}