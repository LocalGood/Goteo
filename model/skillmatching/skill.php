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


namespace Goteo\Model\Skillmatching {
    use Goteo\Library\Text;

    class Skill extends \Goteo\Core\Model {

        public
            $id,
            $project;


        /**
         * Get the skills for a project
         * @param varcahr(50) $id  Project identifier
         * @return array of skills identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT skill FROM project_skill WHERE project = ?", array($id));
                $skills = $query->fetchAll();
                foreach ($skills as $cat) {
                    $array[$cat[0]] = $cat[0];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all skills available
         *
         * @param void
         * @return array
         */
		public static function getAll () {
            $array = array ();
            try {
                $sql = "
                    SELECT
                        skill.id as id,
                        IFNULL(skill_lang.name, skill.name) as name
                    FROM    skill
                    LEFT JOIN skill_lang
                        ON  skill_lang.id = skill.id
                        AND skill_lang.lang = :lang
                    ORDER BY name ASC
                    ";

                $query = static::query($sql, array(':lang'=>\LANG));
                $skills = $query->fetchAll();
                foreach ($skills as $cat) {
                    // la 15 es de testeos
                    if ($cat[0] == 15) continue;
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all skills for this project by name
         *
         * @param void
         * @return array
         */
		public static function getNames ($project = null, $limit = null) {
            $array = array ();
            try {
                $sqlFilter = "";
                if (!empty($project)) {
                    $sqlFilter = " WHERE skill.id IN (SELECT skill FROM project_skill WHERE project = '$project')";
                }

                $sql = "SELECT 
                            skill.id,
                            IFNULL(skill_lang.name, skill.name) as name
                        FROM skill
                        LEFT JOIN skill_lang
                            ON  skill_lang.id = skill.id
                            AND skill_lang.lang = :lang
                        $sqlFilter
                        ORDER BY `order` ASC
                        ";
                if (!empty($limit)) {
                    $sql .= "LIMIT $limit";
                }
                $query = static::query($sql, array(':lang'=>\LANG));
                $skills = $query->fetchAll();
                foreach ($skills as $cat) {
                    // la 15 es de testeos
                    if ($cat[0] == 15) continue;
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = Text::_('No hay ninguna skill para guardar');
                //Text::get('validate-skill-empty');

            if (empty($this->project))
                $errors[] = Text::_('No hay ningun proyecto al que asignar');
                //Text::get('validate-skill-noproject');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO project_skill (project, skill) VALUES(:project, :skill)";
                $values = array(':project'=>$this->project, ':skill'=>$this->id);
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = Text::_("La skill") . $skill .  Text::_("no se ha asignado correctamente. Por favor, revise los datos.") . $e->getMessage();
                return false;
			}

		}

		/**
		 * Quitar una palabra clave de un proyecto
		 *
		 * @param varchar(50) $project id de un proyecto
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':project'=>$this->project,
				':skill'=>$this->id,
			);

			try {
                self::query("DELETE FROM project_skill WHERE skill = :skill AND project = :project", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = Text::_('No se ha podido quitar la skill ') . $this->id . Text::_(' del proyecto ') . $this->project . ' ' . $e->getMessage();
                //Text::get('remove-skill-fail');
                return false;
			}
		}

	}
    
}