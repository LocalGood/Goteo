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

	use Goteo\Core\Model,
        Goteo\Library\Text,
        Goteo\Core\Exception;

	/*
	 * Clase para gestionar el contenido de las páginas institucionales
	 */
    class Evaluation {

        public
            $project_id,
//            $lang,
//            $node,
//            $name,
//            $description,
//            $url,
            $content,
            $pendiente; // para si esta pendiente de traduccion

        static public function get ($id) {

            // buscamos la página para este nodo en este idioma
/*
			$sql = "SELECT  evaluation.id as id,
                            IFNULL(evaluation.name, IFNULL(original.name, evaluation.name)) as name,
                            IFNULL(evaluation.description, IFNULL(original.description, evaluation.description)) as description,
                            evaluation.url as url,
                            IFNULL(evaluation.lang, '$lang') as lang,
                            IFNULL(evaluation.node, '$node') as node,
                            IFNULL(evaluation.content, original.content) as content
                     FROM evaluation
                     LEFT JOIN evaluation
                        ON  evaluation.project = evaluation.id
                        AND evaluation.lang = :lang
                        AND evaluation.node = :node
                     LEFT JOIN evaluation as original
                        ON  original.project = evaluation.id
                        AND original.node = :node
                        AND original.lang = 'ja'
                     WHERE evaluation.id = :id
                ";
*/
            $sql = "SELECT * FROM evaluation WHERE project = :id";
/*
			$query = Model::query($sql, array(
                                            ':id' => $id,
                                            ':lang' => $lang,
                                            ':node' => $node
                                        )
                                    );
*/
            $query = Model::query($sql, array(
                    ':id' => $id
                )
            );

			$page = $query->fetchObject(__CLASS__);
            return $page;
		}

		/*
		 *  Metodo para la lista de páginas
		 */
        /*
		public static function getAll($lang = \LANG, $node = \GOTEO_NODE) {
            $pages = array();

            try {

                $values = array(':lang' => $lang, ':node' => $node);

//                if ($node != \GOTEO_NODE) {
//                    $sqlFilter .= " WHERE evaluation.id IN ('about', 'contact', 'press', 'service')";
//                }

                $sql = "SELECT
                            evaluation.id as id,
                            IFNULL(evaluation.name, IFNULL(original.name, evaluation.name)) as name,
                            IFNULL(evaluation.description, IFNULL(original.description, evaluation.description)) as description,
                            IF(evaluation.content IS NULL, 1, 0) as pendiente,
                            evaluation.url as url
                        FROM page
                        LEFT JOIN evaluation
                            ON  evaluation.project = evaluation.id
                            AND evaluation.lang = :lang
                            AND evaluation.node = :node
                         LEFT JOIN evaluation as original
                            ON  original.project = evaluation.id
                            AND original.node = :node
                            AND original.lang = 'es'
                        $sqlFilter
                        ORDER BY pendiente DESC, name ASC
                        ";

                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $page) {
                    $pages[] = $page;
                }
                return $pages;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}
*/
		/*
		 *  Lista simple de páginas
		 */
/*
		public static function getList($node = \GOTEO_NODE) {
            $pages = array();

            try {

                if ($node != \GOTEO_NODE) {
                    $sqlFilter = " WHERE evaluation.id IN ('about', 'contact', 'press', 'service')";
                } else {
                    $sqlFilter = '';
                }

                $values = array(':lang' => 'ja', ':node' => $node);

                $sql = "SELECT
                            evaluation.id as id,
                            IFNULL(evaluation.name, evaluation.name) as name,
                            IFNULL(evaluation.description, evaluation.description) as description,
                            evaluation.url as url
                        FROM page
                        LEFT JOIN evaluation
                           ON  evaluation.page = evaluation.id
                           AND evaluation.lang = :lang
                           AND evaluation.node = :node
                        $sqlFilter
                        ORDER BY name ASC
                        ";

                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $page) {
                    $pages[] = $page;
                }
                return $pages;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}
*/
        public function validate(&$errors = array()) {

            $allok = true;

            if (empty($this->project_id)) {
                $errors[] = Text::_('Registro sin id');
                $allok = false;
            }
/*
            if (empty($this->lang)) {
                $errors[] = Text::_('Registro sin lang');
                $allok = false;
            }

            if (empty($this->node)) {
                $errors[] = Text::_('Registro sin node');
                $allok = false;
            }

            if (empty($this->name)) {
                $errors[] = Text::_('Registro sin nombre');
                $allok = false;
            }
*/
            return $allok;
        }

		/*
		 *  Esto se usara para la gestión de contenido
		 */
		public function save(&$errors = array()) {
            if(!$this->validate($errors)) { return false; }

  			try {
                $values = array(
                    ':project' => $this->project_id,
                    ':contenido' => $this->content
                );

				$sql = "REPLACE INTO evaluation
                            (project,content)
                        VALUES
                            (:project,:contenido)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = Text::_("Ha fallado ") . $sql . Text::_('con') . " <pre>" . print_r($values, 1) . "</pre>";
                    return false;
                }
                
			} catch(\PDOException $e) {
                $errors[] = Text::_('Error sql al grabar el contenido de la pagina. ') . $e->getMessage();
                return false;
			}

		}

		/*
		 *  Esto se usara para la gestión de contenido
		 */
        /*
		public function add(&$errors = array()) {

  			try {
                $values = array(
                    ':id' => $this->project_id,
                    ':name' => $this->name,
                    ':url' => '/about/'.$this->id
                );

				$sql = "INSERT INTO evaluation
                            (id, name, url)
                        VALUES
                            (:id, :name, :url)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = Text::_("Ha fallado ") . $sql . Text::_('con') . " <pre>" . print_r($values, 1) . "</pre>";
                    return false;
                }

			} catch(\PDOException $e) {
                $errors[] = Text::_('Error sql al grabar el contenido de la pagina. ') . $e->getMessage();
                return false;
			}

		}
*/
        /**
         * PAra actualizar solamente el contenido
         * @param <type> $errors
         * @return <type>
         */
		public function update($project_id, $content, &$errors = array()) {
  			try {
                $values = array(
                    ':project' => $project_id,
                    ':content' => $content
                );

				$sql = "REPLACE INTO evaluation
                            (project, content)
                        VALUES
                            (:project, :content)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = Text::_("Ha fallado ") . $sql . Text::_('con') . " <pre>" . print_r($values, 1) . "</pre>";
                    return false;
                }

			} catch(\PDOException $e) {
                $errors[] = Text::_('Error sql al grabar el contenido de la pagina. ') . $e->getMessage();
                return false;
			}

		}


	}
}