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

namespace Goteo\Model {

    use Goteo\Core\ACL,
        Goteo\Library\Check,
        Goteo\Library\Text,
        Goteo\Model\User,
        Goteo\Model\Image,
        Goteo\Model\Message;

    class Skillmatching extends \Goteo\Core\Model {

        public
            $id = null,
            $prefixed_id = null,
            $dontsave = false,
            $owner, // User who created it
            $node, // Node this project belongs to
            $status,
            $progress, // puntuation %
            $amount, // Current donated amount

            $user, // owner's user information

            // Register contract data
            $contract_name, // Nombre y apellidos del responsable del proyecto
            $contract_nif, // Guardar sin espacios ni puntos ni guiones
            $contract_email, // cuenta paypal
            $phone, // guardar sin espacios ni puntos

            // Para marcar física o jurídica
            $contract_entity = false, // false = física (persona)  true = jurídica (entidad)

            // Para persona física
            $contract_birthdate,

            // Para entidad jurídica
            $entity_office, // cargo del responsable dentro de la entidad
            $entity_name,  // denomincion social de la entidad
            $entity_cif,  // CIF de la entidad

            // Campos de Domicilio: Igual para persona o entidad
            $address,
            $zipcode,
            $location, // owner's location
            $country,

            // Domicilio postal
            $secondary_address = false, // si es diferente al domicilio fiscal
            $post_address = null,
            $post_zipcode = null,
            $post_location = null,
            $post_country = null,


            // Edit project description
            $name,
            $subtitle,
            $lang = 'es',
            $image,
            $gallery = array(), // array de instancias image de project_image
            $secGallery = array(), // array de instancias image de project_image (secundarias)
            $description,
             $motivation,
              $video,   // video de motivacion
               $video_usubs,   // universal subtitles para el video de motivacion
             $about,
             $goal,
             $related,
             $reward, // nueva sección, solo editable por admines y traductores
            $categories = array(),
            $skills = array(),
            $media, // video principal
             $media_usubs, // universal subtitles para el video principal
            $keywords, // por ahora se guarda en texto tal cual
            $currently, // Current development status of the project
            $skillmatching_location, // project execution location
            $scope,  // ambito de alcance
            $evaluation,

            $translate,  // si se puede traducir (bool)

            // costs
            $costs = array(),  // project\cost instances with type
            $schedule, // picture of the costs schedule
            $resource, // other current resources

            // Rewards
            $social_rewards = array(), // instances of project\reward for the public (collective type)
            $individual_rewards = array(), // instances of project\reward for investors  (individual type)

            // Collaborations
            $supports = array(), // instances of project\support

            // Comment
            $comment, // Comentario para los admin introducido por el usuario

            //Operative purpose properties
            $mincost = 0,
            $maxcost = 0,

            //Obtenido, Días, Cofinanciadores
            $invested = 0, //cantidad de inversión
            $days = 0, //para 40 desde la publicación o para 80 si no está caducado
            $investors = array(), // aportes individuales a este proyecto
            $num_investors = 0, // numero de usuarios que han aportado

            $total_days = 0, // total acceptale days

            $round = 0, // para ver si ya está en la fase de los 40 a los 80
            $passed = null, // para ver si hemos hecho los eventos de paso a segunda ronda
            $willpass = null, // fecha final de primera ronda

            $errors = array(), // para los fallos en los datos
            $okeys  = array(), // para los campos que estan ok

            // para puntuacion
            $score = 0, //puntos
            $max = 0, // maximo de puntos

            $messages = array(), // mensajes de los usuarios hilos con hijos

            $finishable = false, // llega al progresso mínimo para enviar a revision

            $tagmark = null,  // banderolo a mostrar

            $period_1r = 0,     // 1R期間
            $period_2r = 0,     // 2R期間

            $willclose = null;  // プロジェクト終了予定日


        /**
         * Sobrecarga de métodos 'getter'.
         *
         * @param type string $name
         * @return type mixed
         */
        public function __get ($name) {
            if($name == "allowpp") {
                return Skillmatching\Account::getAllowpp($this->id);
            }
            if($name == "budget") {
	            return self::calcCosts($this->id);
	        }
            return $this->$name;
        }

        /**
         * Inserta un proyecto con los datos mínimos
         *
         * @param array $data
         * @return boolean
         */
        public function create ($node = \GOTEO_NODE, &$errors = array()) {

            $user = $_SESSION['user']->id;

            if (empty($user)) {
                return false;
            }
            // cojemos el número de proyecto de este usuario
            $query = self::query("SELECT COUNT(id) as num FROM skillmatching WHERE owner = ?", array($user));
            if ($now = $query->fetchObject())
                $num = $now->num + 1;
            else
                $num = 1;

            // datos del usuario que van por defecto: name->contract_name,  location->location
            $userProfile = User::get($user);
            // datos del userpersonal por defecto a los cammpos del paso 2
            $userPersonal = User::getPersonal($user);

            $values = array(
                ':id'   => md5($user.'-'.$num),
                ':name' => Text::_("El nuevo proyecto de ").$userProfile->name,
                ':lang' => 'es',
                ':status'   => 1,
                ':progress' => 0,
                ':owner' => $user,
                ':node' => $node,
                ':amount' => 0,
                ':days' => 0,
                ':created'  => date('Y-m-d'),
                ':contract_name' => ($userPersonal->contract_name) ?
                                    $userPersonal->contract_name :
                                    $userProfile->name,
                ':contract_nif' => $userPersonal->contract_nif,
                ':phone' => $userPersonal->phone,
                ':address' => $userPersonal->address,
                ':zipcode' => $userPersonal->zipcode,
                ':location' => ($userPersonal->location) ?
                                $userPersonal->location :
                                $userProfile->location,
                ':country' => ($userPersonal->country) ?
                                $userPersonal->country :
                                Check::country(),
                ':project_location' => ($userPersonal->location) ?
                                $userPersonal->location :
                                $userProfile->location,
                ':period_1r' => 40,
                ':period_2r' => 40
                );

            $campos = array();
            foreach (\array_keys($values) as $campo) {
                $campos[] = \str_replace(':', '', $campo);
            }

            $sql = "REPLACE INTO skillmatching (" . implode(',', $campos) . ")
                 VALUES (" . implode(',', \array_keys($values)) . ")";
            try {
				self::query($sql, $values);

                foreach ($campos as $campo) {
                    $this->$campo = $values[":$campo"];
                }

                return $this->id;
            } catch (\PDOException $e) {
                $errors[] = Text::_("ERROR al crear un nuevo proyecto") . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>";
                \trace($this);
                die($errors[0]);
                return false;
            }
        }

        /*
         *  Cargamos los datos del proyecto
         */
        public static function get($id, $lang = null) {

            try {
				// metemos los datos del proyecto en la instancia
				$query = self::query("SELECT * FROM skillmatching WHERE id = ?", array(urldecode($id)));
				$skillmatching = $query->fetchObject(__CLASS__);

                if (!$skillmatching instanceof \Goteo\Model\Skillmatching) {
                    throw new \Goteo\Core\Error('404', Text::html('fatal-error-project'));
                }

                // si recibimos lang y no es el idioma original del proyecto, ponemos la traducción y mantenemos para el resto de contenido
                if ($lang == $skillmatching->lang) {
                    $lang = null;
                } elseif (!empty($lang)) {
                    $sql = "
                        SELECT
                            IFNULL(project_lang.description, skillmatching.description) as description,
                            IFNULL(project_lang.motivation, skillmatching.motivation) as motivation,
                            IFNULL(project_lang.video, skillmatching.video) as video,
                            IFNULL(project_lang.about, skillmatching.about) as about,
                            IFNULL(project_lang.goal, skillmatching.goal) as goal,
                            IFNULL(project_lang.related, skillmatching.related) as related,
                            IFNULL(project_lang.reward, skillmatching.reward) as reward,
                            IFNULL(project_lang.keywords, skillmatching.keywords) as keywords,
                            IFNULL(project_lang.media, skillmatching.media) as media,
                            IFNULL(project_lang.subtitle, skillmatching.subtitle) as subtitle,
                            IFNULL(project_lang.evaluation, skillmatching.evaluation) as evaluation
                        FROM skillmatching
                        LEFT JOIN project_lang
                            ON  project_lang.id = skillmatching.id
                            AND project_lang.lang = :lang
                        WHERE skillmatching.id = :id
                        ";
                    $query = self::query($sql, array(':id'=>$id, ':lang'=>$lang));
                    foreach ($query->fetch(\PDO::FETCH_ASSOC) as $field=>$value) {
                        $skillmatching->$field = $value;
                    }
                }

                if (isset($skillmatching->media)) {
                    $skillmatching->media = new Skillmatching\Media($skillmatching->media);
                }
                if (isset($skillmatching->video)) {
                    $skillmatching->video = new Skillmatching\Media($skillmatching->video);
                }

                // prefixed id
//                $skillmatching->prefixed_id = LG_SM_DB_PREFIX.$id;
                $skillmatching->prefixed_id = self::getPrefixedId($id);

                // owner
                $skillmatching->user = User::get($skillmatching->owner, $lang);

                // galeria
                $skillmatching->gallery = Skillmatching\Image::getGallery($skillmatching->prefixed_id);

                // imágenes por sección
                foreach (Skillmatching\Image::sections() as $sec => $val) {
                    if ($sec != '') {
                        $skillmatching->secGallery[$sec] = Skillmatching\Image::get($skillmatching->id, $sec);
                    }
                }

                // categorias
                $skillmatching->categories = Skillmatching\Category::get($skillmatching->prefixed_id);

                // skills
                $skillmatching->skills = Skillmatching\Skill::get($skillmatching->prefixed_id);

				// costes y los sumammos
				$skillmatching->costs = Skillmatching\Cost::getAll($id, $lang);
                $skillmatching->minmax();

				// retornos colectivos
				$skillmatching->social_rewards = Skillmatching\Reward::getAll($skillmatching->prefixed_id, 'social', $lang);
				// retornos individuales
				$skillmatching->individual_rewards = Skillmatching\Reward::getAll($skillmatching->prefixed_id, 'individual', $lang);

				// colaboraciones
				$skillmatching->supports = Skillmatching\Support::getAll($skillmatching->prefixed_id, $lang);

                //-----------------------------------------------------------------
                // Diferentes verificaciones segun el estado del proyecto
                //-----------------------------------------------------------------
                $skillmatching->investors = Invest::investors($skillmatching->prefixed_id);
                $skillmatching->num_investors = Invest::numInvestors($skillmatching->prefixed_id);

                $amount = Invest::invested($skillmatching->prefixed_id);
                if ($skillmatching->invested != $amount) {
                    self::query("UPDATE skillmatching SET amount = '{$amount}' WHERE id = ?", array($skillmatching->prefixed_id));
                }
                $skillmatching->invested = $amount;
                $skillmatching->amount   = $amount;

                //mensajes y mensajeros
                $messegers = array();
                $skillmatching->messages = Message::getAll($skillmatching->prefixed_id, $lang);
                $skillmatching->num_messages = 0;
                foreach ($skillmatching->messages as $msg) {
                    $skillmatching->num_messages++;
                    $skillmatching->num_messages+=count($msg->responses);
                    $messegers[$msg->user] = $msg->user;
                }
                $skillmatching->num_messegers = count($messegers);

                $skillmatching->setDays();
                $skillmatching->setTagmark();

                // fecha final primera ronda (fecha campaña + 40)
                if (!empty($skillmatching->published)) {
                    $ptime = strtotime($skillmatching->published);
                    if ($skillmatching->round === 1){
                        $skillmatching->willpass = date('Y-m-d', \mktime(0, 0, 0, date('m', $ptime), date('d', $ptime)+$skillmatching->period_1r, date('Y', $ptime)));
                    } elseif ($skillmatching->round === 2 && !empty($skillmatching->passed)){
                        $pdtime = strtotime($skillmatching->passed);
                        $skillmatching->willpass = date('Y-m-d', \mktime(0, 0, 0, date('m', $pdtime), date('d', $pdtime)+$skillmatching->period_2r, date('Y', $pdtime)));
                    }
                    $skillmatching->willclose = date('Y-m-d', \mktime(0, 0, 0, date('m', $ptime), date('d', $ptime)+($skillmatching->period_1r+$skillmatching->period_2r), date('Y', $ptime)));
                }

                //-----------------------------------------------------------------
                // Fin de verificaciones
                //-----------------------------------------------------------------

				return $skillmatching;

			} catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
			} catch(\Goteo\Core\Error $e) {
                throw new \Goteo\Core\Error('404', Text::html('fatal-error-project'));
			}
		}

        /*
         *  Cargamos los datos mínimos de un proyecto
         */
        public static function getMini($id) {

            try {
				// metemos los datos del proyecto en la instancia
				$query = self::query("SELECT id, name, owner, comment, lang, status FROM skillmatching WHERE id = ?", array($id));
				$skillmatching = $query->fetchObject(); // stdClass para qno grabar accidentalmente y machacar todo

                // prefixed id
//                $skillmatching->prefixed_id = LG_SM_DB_PREFIX.$id;
                $skillmatching->prefixed_id = self::getPrefixedId($id);

                // owner
                $skillmatching->user = User::getMini($skillmatching->owner);

				return $skillmatching;

			} catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
			}
		}

        /*
         *  Cargamos los datos suficientes para pintar un widget de proyecto
         */
        public static function getMedium($id, $lang = \LANG) {

            try {
				// metemos los datos del proyecto en la instancia
				$query = self::query("SELECT * FROM skillmatching WHERE id = ?", array($id));
				$skillmatching = $query->fetchObject(__CLASS__);

                // prefixed id
//                $skillmatching->prefixed_id = LG_SM_DB_PREFIX.$id;
                $skillmatching->prefixed_id = self::getPrefixedId($id);

                // primero, que no lo grabe
                $skillmatching->dontsave = true;

                // si recibimos lang y no es el idioma original del proyecto, ponemos la traducción y mantenemos para el resto de contenido
                if ($lang == $skillmatching->lang) {
                    $lang = null;
                } elseif (!empty($lang)) {
                    $sql = "
                        SELECT
                            IFNULL(project_lang.description, skillmatching.description) as description,
                            IFNULL(project_lang.subtitle, skillmatching.subtitle) as subtitle
                        FROM skillmatching
                        LEFT JOIN project_lang
                            ON  project_lang.id = skillmatching.id
                            AND project_lang.lang = :lang
                        WHERE skillmatching.id = :id
                        ";
                    $query = self::query($sql, array(':id'=>$id, ':lang'=>$lang));
                    foreach ($query->fetch(\PDO::FETCH_ASSOC) as $field=>$value) {
                        $skillmatching->$field = $value;
                    }
                }

                // owner
                $skillmatching->user = User::getMini($skillmatching->owner);

                // imagen
                $skillmatching->image = Skillmatching\Image::getFirst($skillmatching->id);

                // categorias
                $skillmatching->categories = Skillmatching\Category::getNames($skillmatching->prefixed_id, 2);

                // skills
                $skillmatching->skills = Skillmatching\Skill::getNames($skillmatching->prefixed_id, 2);

				// retornos colectivos
				$skillmatching->social_rewards = Skillmatching\Reward::getAll($skillmatching->prefixed_id, 'social', $lang);
				// retornos individuales
				$skillmatching->individual_rewards = Skillmatching\Reward::getAll($skillmatching->prefixed_id, 'individual', $lang);
//                $prefixed_id = LG_SM_DB_PREFIX . $id;
//                $amount = Invest::invested($prefixed_id);
                $amount = Invest::invested($skillmatching->prefixed_id);
                $skillmatching->invested = $amount;
                $skillmatching->amount   = $amount;

//                $skillmatching->investors = Invest::Investors($id);
                $skillmatching->num_investors = Invest::numInvestors($skillmatching->prefixed_id);
                $skillmatching->num_messegers = Message::numMessegers($id);

                // sacamos rapidamente el presupuesto mínimo y óptimo
//                $costs = self::calcCosts($id);
//                $skillmatching->mincost = $costs->mincost;
//                $skillmatching->maxcost = $costs->maxcost;


                $skillmatching->setDays();
                $skillmatching->setTagmark();

				return $skillmatching;

			} catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
			}
		}

        /*
         * Listado simple de todos los proyectos
         */
        public static function getAll($node = \GOTEO_NODE) {

            $list = array();

            $query = static::query("
                SELECT
                    skillmatching.id as id,
                    skillmatching.name as name
                FROM skillmatching
                ORDER BY skillmatching.name ASC
                ", array(':node' => $node));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         *  Para calcular los dias y la ronda
         */
        // todo 日付関係
        private function setDays() {
            //para proyectos en campaña o posterior
            $this->getDays();
            if ($this->status > 2) {
                // tiempo de campaña
                if ($this->status == 3) {
                    $days = $this->daysActive();
                    if ($days > ($this->period_1r + $this->period_2r + 1)) {
                        $this->round = 0;
                        $days = 0;
                    } elseif ($days >= $this->period_1r) {
                        $days = ($this->period_1r + $this->period_2r) - $days;
                        $this->round = 2;
                    } else {
                        $days = $this->period_1r - $days;
                        $this->round = 1;
                    }

                    if ($days < 0) {
                        // no deberia estar en campaña sino financuiado o caducado
                        $days = 0;
                    }

                } else {
                    $this->round = 0;
                    $days = 0;
                }


            } else {
                $days = 0;
            }

            if ($this->days != $days) {
                self::query("UPDATE skillmatching SET days = '{$days}' WHERE id = ?", array($this->id));
            }

            $totaldays = ($this->period_1r + $this->period_2r) - $this->daysActive();
            $this->total_days = ($totaldays >= 0) ? $totaldays : 0;
            $this->days = $days;
        }

        private function getDays() {
            try {
                $sql = "SELECT period_1r, period_2r FROM skillmatching WHERE id = :id";
                $query = self::query($sql, array(':id'=>$this->id));
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $items) {
                    $this->period_1r = $items->period_1r;
                    $this->period_2r = $items->period_2r;
                }
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        /*
         *  Para ver que tagmark le toca
         */
        private function setTagmark() {
            // a ver que banderolo le toca
            // "financiado" al final de de los 80 dias
            if ($this->status == 4) :
                $this->tagmark = 'gotit';
            // "en marcha" cuando llega al optimo en primera o segunda ronda
            elseif ($this->status == 3 && $this->amount >= $this->maxcost) :
                $this->tagmark = 'onrun';
            // "en marcha" y "aun puedes" cuando está en la segunda ronda
            elseif ($this->status == 3 && $this->round == 2) :
                $this->tagmark = 'onrun-keepiton';
            // Obtiene el mínimo durante la primera ronda, "aun puedes seguir aportando"
            elseif ($this->status == 3 && $this->round == 1 && $this->amount >= $this->mincost ) :
                $this->tagmark = 'keepiton';
            // tag de exitoso cuando es retorno cumplido
            elseif ($this->status == 5) :
                $this->tagmark = 'success';
            // tag de caducado
            elseif ($this->status == 6) :
                $this->tagmark = 'fail';
            endif;
        }

        private function getPrefixedId($id){
            return preg_match('/^[A-Fa-f0-9]{32}$/',$id) ? $id : LG_SM_DB_PREFIX . $id;
        }

        /*
         *  Para validar los campos del proyecto que son NOT NULL en la tabla
         */
        public function validate(&$errors = array()) {

            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = Text::_('El proyecto no tiene id');

            if (empty($this->lang))
                $this->lang = 'es';

            if (empty($this->status))
                $this->status = 1;

            if (empty($this->progress))
                $this->progress = 0;

            if (empty($this->owner))
                $errors[] = Text::_('El proyecto no tiene usuario creador');

            if (empty($this->node))
                $this->node = 'goteo';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

        /**
         * actualiza en la tabla los datos del proyecto
         * @param array $skillmatching->errors para guardar los errores de datos del formulario, los errores de proceso se guardan en $skillmatching->errors['process']
         */
        public function save (&$errors = array()) {
            if ($this->dontsave) { return false; }

            if(!$this->validate($errors)) { return false; }

  			try {
                // fail para pasar por todo antes de devolver false
                $fail = false;

                // los nif sin guiones, espacios ni puntos
                $this->contract_nif = str_replace(array('_', '.', ' ', '-', ',', ')', '('), '', $this->contract_nif);
                $this->entity_cif = str_replace(array('_', '.', ' ', '-', ',', ')', '('), '', $this->entity_cif);

                // Image
                if (is_array($this->image) && !empty($this->image['name'])) {
                    $image = new Image($this->image);
                    if ($image->save($errors)) {
                        $this->gallery[] = $image;
                        $this->image = $image->id;

                        /**
                         * Guarda la relación NM en la tabla 'project_image'.
                         */
                        if(!empty($image->id)) {
                            self::query("REPLACE project_image (project, image) VALUES (:project, :image)", array(':project' => $this->id, ':image' => $image->id));
                        }
                    }
                }

                $fields = array(
                    'contract_name',
                    'contract_nif',
                    'contract_email',
                    'contract_entity',
                    'contract_birthdate',
                    'entity_office',
                    'entity_name',
                    'entity_cif',
                    'phone',
                    'address',
                    'zipcode',
                    'location',
                    'country',
                    'secondary_address',
                    'post_address',
                    'post_zipcode',
                    'post_location',
                    'post_country',
                    'name',
                    'subtitle',
                    'image',
                    'description',
                    'motivation',
                    'video',
                    'video_usubs',
                    'about',
                    'goal',
                    'related',
                    'reward',
                    'keywords',
                    'media',
                    'media_usubs',
                    'currently',
                    'project_location',
                    'scope',
                    'resource',
                    'comment',
                    'evaluation'
                    );

                $set = '';
                $values = array();

                foreach ($fields as $field) {
                    if ($set != '') $set .= ', ';
                    $set .= "$field = :$field";
                    $values[":$field"] = $this->$field;
                }

                // Solamente marcamos updated cuando se envia a revision desde el superform o el admin
//				$set .= ", updated = :updated";
//				$values[':updated'] = date('Y-m-d');
				$values[':id'] = $this->id;

				$sql = "UPDATE skillmatching SET " . $set . " WHERE id = :id";
				if (!self::query($sql, $values)) {
                    $errors[] = $sql . '<pre>' . print_r($values, 1) . '</pre>';
                    $fail = true;
                }

//                echo "$sql<br />";
                // y aquí todas las tablas relacionadas
                // cada una con sus save, sus new y sus remove
                // quitar las que tiene y no vienen
                // añadir las que vienen y no tiene

                //categorias
                $tiene = Skillmatching\Category::get($this->id);
                $viene = $this->categories;
                $quita = array_diff_assoc($tiene, $viene);
                $guarda = array_diff_assoc($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    $category = new Skillmatching\Category(
                        array(
                            'id'=>$item,
                            'project'=>$this->id)
                    );
                    if (!$category->remove($errors))
                        $fail = true;
                }
                foreach ($guarda as $key=>$item) {
                    if (!method_exists($item, 'save') || !$item->save($errors))
                        $fail = true;
                }
                // recuperamos las que le quedan si ha cambiado alguna
                if (!empty($quita) || !empty($guarda))
                    $this->categories = Skillmatching\Category::get($this->id);

                //skills
                $tiene = Skillmatching\Skill::get($this->id);
                $viene = $this->skills;
                $quita = array_diff_assoc($tiene, $viene);
                $guarda = array_diff_assoc($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    $skill = new Skillmatching\Skill(
                        array(
                            'id'=>$item,
                            'project'=>$this->id)
                    );
                    if (!$skill->remove($errors))
                        $fail = true;
                }
                foreach ($guarda as $key=>$item) {
                    if (!method_exists($item, 'save') || !$item->save($errors))
                        $fail = true;
                }
                // recuperamos las que le quedan si ha cambiado alguna
                if (!empty($quita) || !empty($guarda))
                    $this->skills = Skillmatching\Skill::get($this->id);

                //costes
                $tiene = Skillmatching\Cost::getAll($this->id);
                $viene = $this->costs;
                $quita = array_diff_key($tiene, $viene);
                $guarda = array_diff_key($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    if (!$item->remove($errors)) {
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    if (!method_exists($item, 'save') || !$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
                    $this->costs = Skillmatching\Cost::getAll($this->id);

                // recalculo de minmax
                $this->minmax();

                //retornos colectivos
				$tiene = Skillmatching\Reward::getAll($this->id, 'social');
                $viene = $this->social_rewards;
                $quita = array_diff_key($tiene, $viene);
                $guarda = array_diff_key($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    if (!$item->remove($errors)) {
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    if (!method_exists($item, 'save') || !$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
    				$this->social_rewards = Skillmatching\Reward::getAll($this->id, 'social');

                //recompenssas individuales
				$tiene = Skillmatching\Reward::getAll($this->id, 'individual');
                $viene = $this->individual_rewards;
                $quita = array_diff_key($tiene, $viene);
                $guarda = array_diff_key($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    if (!method_exists($item, 'remove') || !$item->remove($errors)){
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    if (!method_exists($item, 'save') || !$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
    				$this->individual_rewards = Skillmatching\Reward::getAll($this->id, 'individual');

				// colaboraciones
				$tiene = Skillmatching\Support::getAll($this->id);
                $viene = $this->supports;
                $quita = array_diff_key($tiene, $viene); // quitar los que tiene y no viene
                $guarda = array_diff_key($viene, $tiene); // añadir los que viene y no tiene
                foreach ($quita as $key=>$item) {
                    if (!$item->remove($errors)) {
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    if (!method_exists($item, 'save') || !$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
    				$this->supports = Skillmatching\Support::getAll($this->id);

                //listo
                return !$fail;
			} catch(\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                //Text::get('save-project-fail');
                return false;
			}

        }

        public function saveLang (&$errors = array()) {

  			try {
                $fields = array(
                    'id'=>'id',
                    'lang'=>'lang_lang',
                    'subtitle'=>'subtitle_lang',
                    'description'=>'description_lang',
                    'motivation'=>'motivation_lang',
                    'video'=>'video_lang',
                    'about'=>'about_lang',
                    'goal'=>'goal_lang',
                    'related'=>'related_lang',
                    'reward'=>'reward_lang',
                    'keywords'=>'keywords_lang',
                    'media'=>'media_lang',
                    'evaluation'=>'evaluation_lang',
                    );

                $set = '';
                $values = array();

                foreach ($fields as $field=>$ffield) {
                    if ($set != '') $set .= ', ';
                    $set .= "$field = :$field";
                    if (empty($this->$ffield)) {
                        $this->$ffield = null;
                    }
                    $values[":$field"] = $this->$ffield;
                }

				$sql = "REPLACE INTO project_lang SET " . $set;
				if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = $sql . '<pre>' . print_r($values, 1) . '</pre>';
                    return false;
                }
			} catch(\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
			}

        }

        /*
         * comprueba errores de datos y actualiza la puntuación
         */
        public function check() {
            
            $errors = &$this->errors;
            $okeys  = &$this->okeys ;

            // reseteamos la puntuación
            $this->setScore(0, 0, true);

            /***************** Revisión de campos del paso 1, PERFIL *****************/
            $score = 0;
            // obligatorios: nombre, email, ciudad
            // 2 - 名前
            if (empty($this->user->name)) {
                $errors['userProfile']['name'] = Text::get('validate-user-field-name');
            } else {
                $okeys['userProfile']['name'] = 'ok';
                $score+=2;
            }

            // se supone que tiene email porque sino no puede tener usuario, no?
            // 3 - メアド
            if (!empty($this->user->email)) {
                ++$score;
            }

            // 4 - 居住地
            if (empty($this->user->location)) {
                $errors['userProfile']['location'] = Text::get('validate-user-field-location');
            } else {
                $okeys['userProfile']['location'] = 'ok';
                ++$score;
            }

            // 5 - プロフィール画像
            if(!empty($this->user->avatar) && $this->user->avatar->id != 1) {
                $okeys['userProfile']['avatar'] = empty($errors['userProfile']['avatar']) ? 'ok' : null;
                $score+=2;
            }

            // 6 - 自己紹介
            if (!empty($this->user->about)) {
                $okeys['userProfile']['about'] = 'ok';
                ++$score;
                // otro +1 si tiene más de 1000 caracteres (pero menos de 2000)
                // +1 - 1000文字以上2000文字以下でプラス1????
                if (\strlen($this->user->about) > 1000 && \strlen($this->user->about) < 2000) {
                    ++$score;
                }
            } else {
                $errors['userProfile']['about'] = Text::get('validate-user-field-about');
            }

            // 7 - 関心のあるプロジェクトカテゴリ
            if (empty($this->user->interests)) {
                $errors['userProfile']['interests'] = Text::get('カテゴリの選択は必須です。');
            } else {
                $okeys['userProfile']['interests'] = 'ok';
                ++$score;
            }

            // 8 - スキル
            if (empty($this->user->skills)) {
                $errors['userProfile']['skills'] = Text::get('スキルの選択は必須です。');
            } else {
                $okeys['userProfile']['skills'] = 'ok';
                ++$score;
            }

            // 9 - 自分のホームページサイトやブログのアドレス
            if (empty($this->user->webs)) {
                $errors['userProfile']['webs'] = Text::get('validate-project-userProfile-web');
            } else {
                $okeys['userProfile']['webs'] = 'ok';
                ++$score;
                //if (count($this->user->webs) > 2) ++$score;

                $anyerror = false;
                foreach ($this->user->webs as $web) {
                    if (trim(str_replace('http://','',$web->url)) == '') {
                        $anyerror = !$anyerror ?: true;
                        $errors['userProfile']['web-'.$web->id.'-url'] = Text::get('validate-user-field-web');
                    } else {
                        $okeys['userProfile']['web-'.$web->id.'-url'] = 'ok';
                    }
                }

                if ($anyerror) {
                    unset($okeys['userProfile']['webs']);
                    $errors['userProfile']['webs'] = Text::get('validate-project-userProfile-any_error');
                }
            }

            if (!empty($this->user->facebook)) {
                $okeys['userProfile']['facebook'] = 'ok';
            }

            if (!empty($this->user->twitter)) {
                $okeys['userProfile']['twitter'] = 'ok';
            }

            if (!empty($this->user->linkedin)) {
                $okeys['userProfile']['linkedin'] = 'ok';
            }

            //puntos
            $this->setScore($score, 9);
//            $this->setScore($score, 10);
            /***************** FIN Revisión del paso 1, PERFIL *****************/

            /***************** Revisión de campos del paso 2,DATOS PERSONALES *****************/
            $score = 0;
            // obligatorios: todos
            // +2
            if(empty($this->contract_entity)) {
                $score+=2;
            } else {
                if (empty($this->entity_name)) {
                    $errors['userPersonal']['entity_name'] = Text::get('mandatory-project-field-entity_name');
                } else {
                    $okeys['userPersonal']['entity_name'] = 'ok';
                    ++$score;
                }
                if (empty($this->entity_office)) {
                    $errors['userPersonal']['entity_office'] = Text::get('mandatory-project-field-entity_office');
                } else {
                    $okeys['userPersonal']['entity_office'] = 'ok';
                    ++$score;
                }
            }

            // +3
            if (empty($this->contract_name)) {
                $errors['userPersonal']['contract_name'] = Text::get('mandatory-project-field-contract_name');
            } else {
                 $okeys['userPersonal']['contract_name'] = 'ok';
                 ++$score;
            }

            // +4
            if (empty($this->contract_email)) {
                $errors['userPersonal']['contract_email'] = Text::get('mandatory-project-field-contract_email');
            } elseif (!Check::mail($this->contract_email)) {
                $errors['userPersonal']['contract_email'] = Text::get('validate-project-value-contract_email');
            } else {
                 $okeys['userPersonal']['contract_email'] = 'ok';
                ++$score;
            }
/*
            if (empty($this->contract_birthdate)) {
                $errors['userPersonal']['contract_birthdate'] = Text::get('mandatory-project-field-contract_birthdate');
            } else {
                 $okeys['userPersonal']['contract_birthdate'] = 'ok';
                ++$score;
            }
*/
            // +5
            if (empty($this->phone)) {
                $errors['userPersonal']['phone'] = Text::get('mandatory-project-field-phone');
            } elseif (!Check::phone($this->phone)) {
                $errors['userPersonal']['phone'] = Text::get('validate-project-value-phone');
            } else {
                 $okeys['userPersonal']['phone'] = 'ok';
                 ++$score;
            }

            // +6
            if (empty($this->address)) {
                $errors['userPersonal']['address'] = Text::get('mandatory-project-field-address');
            } else {
                 $okeys['userPersonal']['address'] = 'ok';
                 ++$score;
            }

            // +7
            if (empty($this->zipcode)) {
                $errors['userPersonal']['zipcode'] = Text::get('mandatory-project-field-zipcode');
            } else {
                 $okeys['userPersonal']['zipcode'] = 'ok';
                 ++$score;
            }

            /*if (empty($this->location)) {
                $errors['userPersonal']['location'] = Text::get('mandatory-project-field-residence');
            } else {
                 $okeys['userPersonal']['location'] = 'ok';
                ++$score;
            }*/
/*
            if (empty($this->country)) {
                $errors['userPersonal']['country'] = Text::get('mandatory-project-field-country');
            } else {
                 $okeys['userPersonal']['country'] = 'ok';
                 ++$score;
            }
*/
            $this->setScore($score, 7);
            /***************** FIN Revisión del paso 2, DATOS PERSONALES *****************/

            /***************** Revisión de campos del paso 3, DESCRIPCION *****************/
            $score = 0;
            // obligatorios: nombre, subtitulo, imagen, descripcion, about, motivation, categorias, video, localización
            // +3
            if (empty($this->name)) {
                $errors['overview']['name'] = Text::get('mandatory-project-field-name');
            } else {
                 $okeys['overview']['name'] = 'ok';
                 $score+=3;
            }

            if (!empty($this->subtitle)) {
                 $okeys['overview']['subtitle'] = 'ok';
            }

            // +6 || +7
            if (empty($this->gallery) && empty($errors['overview']['image'])) {
                $errors['overview']['image'] .= Text::get('mandatory-project-field-image');
            } else {
                 $okeys['overview']['image'] = (empty($errors['overview']['image'])) ? 'ok' : null;
                 $score+=3;
//                 if (count($this->gallery) >= 2) ++$score;
            }

            // +8 || +9
            if (empty($this->description)) {
                $errors['overview']['description'] = Text::get('mandatory-project-field-description');
/*            } elseif (!Check::words($this->description, 80)) {
                 $errors['overview']['description'] = Text::get('validate-project-field-description');*/
            } else {
                 $okeys['overview']['description'] = 'ok';
                 $score+=2;
            }

//            if (empty($this->about)) {
//                $errors['overview']['about'] = Text::get('mandatory-project-field-about');
//             } else {
/*                 $okeys['overview']['about'] = 'ok';
                 ++$score;*/
//            }

            // +9 || +10
            if (empty($this->motivation)) {
                $errors['overview']['motivation'] = Text::get('mandatory-project-field-motivation');
            } else {
                 $okeys['overview']['motivation'] = 'ok';
                 ++$score;
            }

            // +10 || +11
            if (empty($this->goal)) {
                $errors['overview']['goal'] = Text::get('mandatory-project-field-goal');
            } else {
                 $okeys['overview']['goal'] = 'ok';
                 ++$score;
            }

            // +11 || +12
            if (empty($this->related)) {
                $errors['overview']['related'] = Text::get('mandatory-project-field-related');
            } else {
                 $okeys['overview']['related'] = 'ok';
                 ++$score;
            }

            // +12 || +13
            if (empty($this->categories)) {
                $errors['overview']['categories'] = Text::get('mandatory-project-field-category');
            } else {
                 $okeys['overview']['categories'] = 'ok';
                 ++$score;
            }

            // +13 || +14
            if (empty($this->project_location)) {
                $errors['overview']['project_location'] = Text::get('mandatory-project-field-location');
            } else {
                 $okeys['overview']['project_location'] = 'ok';
                 ++$score;
            }

            $this->setScore($score, 13);
//            $this->setScore($score, 15);
            /***************** FIN Revisión del paso 3, DESCRIPCION *****************/

            /***************** Revisión de campos del paso 4, COSTES *****************/
            $score = 0; $scoreName = $scoreDesc = $scoreAmount = $scoreDate = 0;
//            if (count($this->costs) < 2) {
//                $errors['costs']['costs'] = Text::get('mandatory-project-costs');
//            } else {
//                $okeys['costs']['costs'] = 'ok';
//                $score+=2;
//            }

            $anyerror = false;
/*
            foreach($this->costs as $cost) {
                if (empty($cost->cost)) {
                    $errors['costs']['cost-'.$cost->id.'-cost'] = Text::get('mandatory-cost-field-name');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['costs']['cost-'.$cost->id.'-cost'] = 'ok';
                     $scoreName = 1;
                }

                if (empty($cost->type)) {
                    $errors['costs']['cost-'.$cost->id.'-type'] = Text::get('mandatory-cost-field-type');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['costs']['cost-'.$cost->id.'-type'] = 'ok';
                }

                if (empty($cost->description)) {
                    $errors['costs']['cost-'.$cost->id.'-description'] = Text::get('mandatory-cost-field-description');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['costs']['cost-'.$cost->id.'-description'] = 'ok';
                     $scoreDesc = 1;
                }

                if (empty($cost->amount)) {
                    $errors['costs']['cost-'.$cost->id.'-amount'] = Text::get('mandatory-cost-field-amount');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['costs']['cost-'.$cost->id.'-amount'] = 'ok';
                     $scoreAmount = 1;
                }

                if ($cost->type == 'task' && (empty($cost->from) || empty($cost->until))) {
                    $errors['costs']['cost-'.$cost->id.'-dates'] = Text::get('mandatory-cost-field-task_dates');
                    $anyerror = !$anyerror ?: true;
                } elseif ($cost->type == 'task') {
                    $okeys['costs']['cost-'.$cost->id.'-dates'] = 'ok';
                    $scoreDate = 1;
                }
            }
*/
            if ($anyerror) {
                unset($okeys['costs']['costs']);
                $errors['costs']['costs'] = Text::get('validate-project-costs-any_error');
            } else {
                $score = $score + $scoreName + $scoreDesc + $scoreAmount + $scoreDate;
            }

            $costdif = $this->maxcost - $this->mincost;
            $maxdif = $this->mincost * 0.50;
            $scoredif = $this->mincost * 0.35;
//            if ($this->mincost == 0) {
//                $errors['costs']['total-costs'] = Text::get('mandatory-project-total-costs');
//            } elseif ($costdif > $maxdif ) {
//                $errors['costs']['total-costs'] = Text::get('validate-project-total-costs');
//            } else {
//                $okeys['costs']['total-costs'] = 'ok';
//            }
            if ($costdif <= $scoredif ) {
                ++$score;
            }

            $this->setScore($score, 0);
            /***************** FIN Revisión del paso 4, COSTES *****************/

            /***************** Revisión de campos del paso 5, RETORNOS *****************/
            $score = 0; $scoreName = $scoreDesc = $scoreAmount = $scoreLicense = 0;
            if (empty($this->social_rewards)) {
                $errors['rewards']['social_rewards'] = Text::get('validate-project-social_rewards');
            } else {
                 $okeys['rewards']['social_rewards'] = 'ok';
                // 公共リターン２件以上で +2
//                 if (count($this->social_rewards) >= 2) {
//                     $score+=2;
//                 }
            }

            if (empty($this->individual_rewards)) {
                $errors['rewards']['individual_rewards'] = Text::get('validate-project-individual_rewards');
            } else {
                $okeys['rewards']['individual_rewards'] = 'ok';
                // 個人リターン３件以上で +1
//                if (count($this->individual_rewards) >= 3) {
//                    ++$score;
//                }
            }

            $anyerror = false;
            foreach ($this->social_rewards as $social) {
                if (empty($social->reward)) {
                    $errors['rewards']['social_reward-'.$social->id.'reward'] = Text::get('mandatory-social_reward-field-name');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['rewards']['social_reward-'.$social->id.'reward'] = 'ok';
                     $scoreName = 1;
                }

                if (empty($social->description)) {
                    $errors['rewards']['social_reward-'.$social->id.'-description'] = Text::get('mandatory-social_reward-field-description');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['rewards']['social_reward-'.$social->id.'-description'] = 'ok';
                     $scoreDesc = 1;
                }

                if (empty($social->icon)) {
                    $errors['rewards']['social_reward-'.$social->id.'-icon'] = Text::get('mandatory-social_reward-field-icon');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['rewards']['social_reward-'.$social->id.'-icon'] = 'ok';
                }

                if (!empty($social->license)) {
                    $scoreLicense = 1;
                }
            }

            if ($anyerror) {
                unset($okeys['rewards']['social_rewards']);
                $errors['rewards']['social_rewards'] = Text::get('validate-project-social_rewards-any_error');
            } else {
                // +3
                $score = $score + $scoreName + $scoreDesc + $scoreLicense;
            }
            $scoreName = $scoreDesc = $scoreAmount = 0;

            $anyerror = false;
            foreach ($this->individual_rewards as $individual) {
                if (empty($individual->reward)) {
                    $errors['rewards']['individual_reward-'.$individual->id.'-reward'] = Text::get('mandatory-individual_reward-field-name');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['rewards']['individual_reward-'.$individual->id.'-reward'] = 'ok';
                     $scoreName = 1;
                }

                if (empty($individual->description)) {
                    $errors['rewards']['individual_reward-'.$individual->id.'-description'] = Text::get('mandatory-individual_reward-field-description');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['rewards']['individual_reward-'.$individual->id.'-description'] = 'ok';
                     $scoreDesc = 1;
                }

                if (empty($individual->amount)) {
                    $errors['rewards']['individual_reward-'.$individual->id.'-amount'] = Text::get('mandatory-individual_reward-field-amount');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['rewards']['individual_reward-'.$individual->id.'-amount'] = 'ok';
                     $scoreAmount = 1;
                }

                if (empty($individual->icon)) {
                    $errors['rewards']['individual_reward-'.$individual->id.'-icon'] = Text::get('mandatory-individual_reward-field-icon');
                    $anyerror = !$anyerror ?: true;
                } else {
                     $okeys['rewards']['individual_reward-'.$individual->id.'-icon'] = 'ok';
                }

            }

            if ($anyerror) {
                unset($okeys['rewards']['individual_rewards']);
                $errors['rewards']['individual_rewards'] = Text::get('validate-project-individual_rewards-any_error');
            } else {
                // +3
                $score = $score + $scoreName + $scoreDesc + $scoreAmount;
            }
            // +6 || +9
            $this->setScore($score, 6);
            /***************** FIN Revisión del paso 5, RETORNOS *****************/

            /***************** Revisión de campos del paso 6, COLABORACIONES *****************/
            $scoreName = $scoreDesc = 0;
            $support_type_task = false;
            $anyerror = false;
            /*
            foreach ($this->supports as $support) {
                if (empty($support->support)) {
                    $errors['supports']['support-'.$support->id.'-support'] = Text::get('スキル/物品名の入力は必須です');;
                    $anyerror = !$anyerror ?: true;
                }else {
                     $okeys['supports']['support-'.$support->id.'-support'] = 'ok';
                     $scoreName = 1;
                }

                if (empty($support->description)) {
                    $errors['supports']['support-'.$support->id.'-description'] = Text::get('内容説明は必須です');;
                    $anyerror = !$anyerror ?: true;
                }else{
                     $okeys['supports']['support-'.$support->id.'-description'] = 'ok';
                     $scoreDesc = 1;
                }

                if($support->type == 'task'){
                    $support_type_task = true;
                }
            }
            */
            if($support_type_task){

                if ($anyerror){
                    unset($okeys['supports']['supports']);
                    $errors['supports']['supports'] = Text::get('validate-project-social_rewards-any_error');
                }else{
                    $score = $scoreName + $scoreDesc;
                }

                if(!count($this->skills)){
                    $errors['supports']['skills'] = Text::get('スキルを選択してください');
                } else {
                    ++$score;
                }
            } else {
                $score = 3;
            }

            $this->setScore($score, 3);
            /***************** FIN Revisión del paso 6, COLABORACIONES *****************/

            //-------------- Calculo progreso ---------------------//
            $this->setProgress();
            //-------------- Fin calculo progreso ---------------------//

            return true;
        }

        /*
         * reset de puntuación
         */
        public function setScore($score, $max, $reset = false) {
            //echo "score:".$score."/max".$max."/rest:".$reset.PHP_EOL;
            if ($reset == true) {
                $this->score = $score;
                $this->max = $max;
            } else {
                $this->score += $score;
                $this->max += $max;
            }
        }

        /*
         * actualizar progreso segun score y max
         */
        // todo 日付関係
        public function setProgress () {
            // Cálculo del % de progreso
            $progress = 100 * $this->score / $this->max;
            $progress = round($progress, 0);
            
            if ($progress > 100) $progress = 100;
            if ($progress < 0)   $progress = 0;

            if ($this->status == 1 && 
                $progress >= ($this->period_1r + $this->period_2r) &&
                \array_empty($this->errors)
                ) {
                $this->finishable = true;
            }
            $this->progress = $progress;
            // actualizar el registro
            self::query("UPDATE skillmatching SET progress = :progress WHERE id = :id",
                array(':progress'=>$this->progress, ':id'=>$this->id));
        }



        /*
         * Listo para revisión
         */
        public function ready(&$errors = array()) {
			try {
				$this->rebase();

                $sql = "UPDATE skillmatching SET status = :status, updated = :updated WHERE id = :id";
                self::query($sql, array(':status'=>2, ':updated'=>date('Y-m-d'), ':id'=>$this->id));
                
                return true;
                
            } catch (\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
            }
        }

        /*
         * Devuelto al estado de edición
         */
        public function enable(&$errors = array()) {
			try {
				$sql = "UPDATE skillmatching SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>1, ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado de publicación
         */
        public function publish(&$errors = array()) {
			try {
				$sql = "UPDATE skillmatching SET passed = NULL, status = :status, published = :published WHERE id = :id";
//                $sql = "UPDATE skillmatching SET passed = NULL, status = :status, published = :published, period_1r = :period_1r, period_2r = :period_2r WHERE id = :id";
				self::query($sql, array(':status'=>3, ':published'=>date('Y-m-d'), ':id'=>$this->id));
//                self::query($sql, array(':status'=>3, ':published'=>date('Y-m-d'), ':id'=>$this->id, ':period_1r'=>$this->period_1r, ':period_2r'=>$this->period_2r));

                // borramos mensajes anteriores que sean de colaboraciones
                self::query("DELETE FROM message WHERE id IN (SELECT thread FROM support WHERE project = ?)", array($this->id));

                // creamos los hilos de colaboración en los mensajes
                foreach ($this->supports as $id => $support) {
                    $msg = new Message(array(
                        'user'    => $this->owner,
                        'project' => $this->id,
                        'date'    => date('Y-m-d'),
                        'message' => "{$support->support}: {$support->description}",
                        'blocked' => true
                        ));
                    if ($msg->save()) {
                        // asignado a la colaboracion como thread inicial
                        $sql = "UPDATE support SET thread = :message WHERE id = :support";
                        self::query($sql, array(':message'=>$msg->id, ':support'=>$support->id));
                    }
                    unset($msg);
                }

                return true;
            } catch (\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado canecelado
         */
        public function cancel(&$errors = array()) {
			try {
				$sql = "UPDATE skillmatching SET status = :status, closed = :closed WHERE id = :id";
				self::query($sql, array(':status'=>0, ':closed'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado caducado
         */
        public function fail(&$errors = array()) {
			try {
				$sql = "UPDATE skillmatching SET status = :status, closed = :closed WHERE id = :id";
				self::query($sql, array(':status'=>6, ':closed'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado Financiado
         */
        public function succeed(&$errors = array()) {
			try {
				$sql = "UPDATE skillmatching SET status = :status, success = :success WHERE id = :id";
				self::query($sql, array(':status'=>4, ':success'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
            }
        }

        /*
         * Marcamos la fecha del paso a segunda ronda
         */
        public function passed(&$errors = array()) {
			try {
				$sql = "UPDATE skillmatching SET passed = :passed WHERE id = :id";
				self::query($sql, array(':passed'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado Retorno cumplido
         */
        public function satisfied(&$errors = array()) {
			try {
				$sql = "UPDATE skillmatching SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>5, ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
            }
        }

        /*
         * Devuelve a estado financiado (por retorno pendiente) pero no modifica fecha
         */
        public function rollback(&$errors = array()) {
			try {
				$sql = "UPDATE skillmatching SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>4, ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = Text::_('No se ha grabado correctamente. ') . $e->getMessage();
                return false;
            }
        }

        /*
         * Si no se pueden borrar todos los registros, estado cero para que lo borre el cron
         */
        public function delete(&$errors = array()) {

            if ($this->status > 1) {
                $errors[] = Text::_("El proyecto no esta descartado ni en edicion");
                return false;
            }

            $db_prefix = "`" . COMMON_AUTH_DB_SCHEMA . "`.";

            self::query("START TRANSACTION");
            try {
                //borrar todos los registros
                self::query("DELETE FROM project_category WHERE project = ?", array($this->id));
                self::query("DELETE FROM project_skill WHERE project = ?", array($this->id));
                self::query("DELETE FROM cost WHERE project = ?", array($this->id));
                self::query("DELETE FROM reward WHERE project = ?", array($this->id));
                self::query("DELETE FROM support WHERE project = ?", array($this->id));
                self::query("DELETE FROM {$db_prefix}image WHERE id IN (SELECT image FROM project_image WHERE project = ?)", array($this->id));
                self::query("DELETE FROM project_image WHERE project = ?", array($this->id));
                self::query("DELETE FROM message WHERE project = ?", array($this->id));
                self::query("DELETE FROM project_account WHERE project = ?", array($this->id));
                self::query("DELETE FROM review WHERE project = ?", array($this->id));
                self::query("DELETE FROM project_lang WHERE id = ?", array($this->id));
                self::query("DELETE FROM skillmatching WHERE id = ?", array($this->id));
                // y los permisos
                self::query("DELETE FROM acl WHERE url like ?", array('%'.$this->id.'%'));
                // si todo va bien, commit y cambio el id de la instancia
                self::query("COMMIT");
                return true;
            } catch (\PDOException $e) {
                self::query("ROLLBACK");
				$sql = "UPDATE skillmatching SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>0, ':id'=>$this->id));
                $errors[] = Text::_("Fallo en la transaccion, el proyecto ha quedado como descartado");
                return false;
            }
        }

        /*
         * Para cambiar el id temporal a idealiza
         * solo si es md5
         */
        public function rebase($newid = null) {
            try {
                if (preg_match('/^[A-Fa-f0-9]{32}$/',$this->id)) {
                    // idealizar el nombre
                    $newid = self::checkId(self::idealiza($this->name));
                    if ($newid == false) return false;
                    $prefixed_new_id = LG_SM_DB_PREFIX . $newid;
                    // actualizar las tablas relacionadas en una transacción
                    $fail = false;
                    if (self::query("START TRANSACTION")) {
                        try {
                            self::query("UPDATE project_category SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE project_skill SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE cost SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE reward SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE support SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE message SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE project_image SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE project_account SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE invest SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE review SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE project_lang SET id = :newid WHERE id = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE blog SET owner = :newid WHERE owner = :id AND type='project'", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE skillmatching SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            // borro los permisos, el dashboard los creará de nuevo
                            self::query("DELETE FROM acl WHERE url like ?", array('%'.$this->id.'%'));

                            // si todo va bien, commit y cambio el id de la instancia
                            self::query("COMMIT");
                            $this->id = $newid;
                            $this->prefixed_id = $prefixed_new_id;
                            return true;

                        } catch (\PDOException $e) {
                            self::query("ROLLBACK");
                            return false;
                        }
                    } else {
                        throw new \Goteo\Core\Exception('Fallo al iniciar transaccion rebase. ' . \trace($e));
                    }
                } elseif (!empty ($newid)) {
//                   echo "Cambiando id proyecto: de {$this->id} a {$newid}<br /><br />";
                    $fail = false;

                    $prefixed_new_id = LG_SM_DB_PREFIX . $newid;

                    if (self::query("START TRANSACTION")) {
                        try {

//                            echo 'en transaccion <br />';

                            // acls
                            $acls = self::query("SELECT * FROM acl WHERE url like :id", array(':id'=>"%{$this->id}%"));
                            foreach ($acls->fetchAll(\PDO::FETCH_OBJ) as $rule) {
                                $url = str_replace($this->id, $newid, $rule->url);
                                self::query("UPDATE `acl` SET `url` = :url WHERE id = :id", array(':url'=>$url, ':id'=>$rule->id));
                                
                            }
//                            echo 'acls listos <br />';

                            // mails
                            $mails = self::query("SELECT * FROM mail WHERE html like :id", array(':id'=>"%{$this->id}%"));
                            foreach ($mails->fetchAll(\PDO::FETCH_OBJ) as $mail) {
                                $html = str_replace($this->id, $newid, $mail->html);
                                self::query("UPDATE `mail` SET `html` = :html WHERE id = :id;", array(':html'=>$html, ':id'=>$mail->id));

                            }
//                            echo 'mails listos <br />';

                            // feed
                            $feeds = self::query("SELECT * FROM feed WHERE url like :id", array(':id'=>"%{$this->id}%"));
                            foreach ($feeds->fetchAll(\PDO::FETCH_OBJ) as $feed) {
                                $title = str_replace($this->id, $newid, $feed->title);
                                $html = str_replace($this->id, $newid, $feed->html);
                               self::query("UPDATE `feed` SET `title` = :title, `html` = :html  WHERE id = :id", array(':title'=>$title, ':html'=>$html, ':id'=>$feed->id));

                            }

                            // feed
                            $feeds2 = self::query("SELECT * FROM feed WHERE target_type = 'project' AND target_id = :id", array(':id'=>$this->id));
                            foreach ($feeds2->fetchAll(\PDO::FETCH_OBJ) as $feed2) {
                                self::query("UPDATE `feed` SET `target_id` = '{$newid}'  WHERE id = '{$feed2->id}';");

                            }
                            
                            // traductores
                            $sql = "UPDATE `user_translate` SET `item` = '{$newid}' WHERE `user_translate`.`type` = 'project' AND `user_translate`.`item` = :id;";
                            self::query($sql, array(':id'=>$this->id));

                            self::query("UPDATE cost SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE message SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE project_category SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE project_skill SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE project_image SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE project_lang SET id = :newid WHERE id = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE reward SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE support SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE project_account SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE invest SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE promote SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
//                            self::query("UPDATE patron SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE invest SET project = :newid WHERE project = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));
                            self::query("UPDATE skillmatching SET id = :newid WHERE id = :id", array(':newid'=>$prefixed_new_id, ':id'=>$this->id));


                            // si todo va bien, commit y cambio el id de la instancia
                            if (!$fail) {
                                self::query("COMMIT");
                                $this->id = $newid;
                                return true;
                            } else {
                                self::query("ROLLBACK");
                                return false;
                            }

                        } catch (\PDOException $e) {
                            self::query("ROLLBACK");
                            return false;
                        }
                    } else {
                        throw new Goteo\Core\Exception('Fallo al iniciar transaccion rebase. ' . \trace($e));
                    }
                    
                    
                    
                }

                return true;
            } catch (\PDOException $e) {
                throw new \Goteo\Core\Exception('Fallo rebase id temporal. ' . \trace($e));
            }

        }

        /*
         *  Para verificar id única
         */
        public static function checkId($id, $num = 1) {
            try
            {
                $query = self::query("SELECT id FROM skillmatching WHERE id = :id", array(':id'=>$id));
                $exist = $query->fetchObject();
                // si  ya existe, cambiar las últimas letras por un número
                if (!empty($exist->id)) {
                    $sufix = (string) $num;
                    if ((strlen($id)+strlen($sufix)) > 49)
                        $id = substr($id, 0, (strlen($id) - strlen($sufix))) . $sufix;
                    else
                        $id = $id . $sufix;
                    $num++;
                    $id = self::checkId($id, $num);
                }
                return $id;
            }
            catch (\PDOException $e) {
                throw new Goteo\Core\Exception(Text::_('Fallo al verificar id única para el proyecto. ') . $e->getMessage());
            }
        }

        /*
         *  Para actualizar el minimo/optimo de costes
         */
        public function minmax() {
            $this->mincost = 0;
            $this->maxcost = 0;
            
            foreach ($this->costs as $item) {
                if ($item->required == 1) {
                    $this->mincost += $item->amount;
                    $this->maxcost += $item->amount;
                }
                else {
                    $this->maxcost += $item->amount;
                }
            }
        }



        /**
         * Metodo que devuelve los días que lleva de publicación
         *
         * @return numeric days active from published
         */
        public function daysActive() {
            // días desde el published
            $now_local = $this->localNow();
            $sql = "
                SELECT DATE_FORMAT(from_unixtime(unix_timestamp('${now_local}') - unix_timestamp(CONCAT(published, DATE_FORMAT('${now_local}', ' %H:%i:%s')))), '%j') as days
                FROM skillmatching
                WHERE id = ?";
            $query = self::query($sql, array($this->id));
            $past = $query->fetchObject();

            return $past->days - 1;
        }

        /**
         * Metodo que devuelve los días que quedan para finalizar la ronda actual
         *
         * @return numeric days remaining to go
         */
        // todo 日付関係
        public function daysRemain($id) {
            // primero, días desde el published
            $now_local = $this->localNow();
            $sql = "
                SELECT DATE_FORMAT(from_unixtime(unix_timestamp('${now_local}') - unix_timestamp(published)), '%j') as days
                FROM skillmatching
                WHERE id = ?";
            $query = self::query($sql, array($id));
            $days = $query->fetchColumn(0);
            $days--;

            if ($days > $this->period_1r) {
                $rest = ($this->period_1r + $this->period_2r) - $days; //en segunda ronda
            } else {
                $rest = $this->period_1r - $days; // en primera ronda
            }

            return $rest;
        }

        /*
         * Lista de proyectos de un usuario
         */
        public static function ofmine($owner, $published = false)
        {
            $skillmatchings = array();

            $sql = "SELECT * FROM skillmatching WHERE owner = ?";
            if ($published) {
                $sql .= " AND status > 2";
            } /* else {
                $sql .= " AND status > 0";
            } */
            $sql .= " ORDER BY created DESC";
            $query = self::query($sql, array($owner));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $skillmatchings[] = self::getMedium($proj->id);
            }
            
            return $skillmatchings;
        }

        /**
         * @param $owner
         * @param bool $published
         * @return array
         * @throws \Goteo\Core\Exception
         */
        public static function ofmatched($_skills)
        {

            $skillmatchings = array();
            if (!empty($_skills)) {
                $sql = "SELECT * FROM skillmatching WHERE status > 2";

                $skills_str = implode(',', $_skills);

                $sql .= ' AND id IN (
                    SELECT distinct(project)
                    FROM project_skill
                    WHERE skill IN (' . $skills_str . ')
                    )';

                $sql .= " ORDER BY created DESC";
                $query = self::query($sql);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                    $skillmatchings[] = self::getMedium($proj->id);
                }
            }
            return $skillmatchings;
        }

        /*
         * Lista de proyectos publicados
         */
        public static function published($type = 'all', $limit = null, $mini = false)
        {
            $values = array();
            // si es un nodo, filtrado
            if (\NODE_ID != \GOTEO_NODE) {
                $sqlFilter = "AND skillmatching.node = :node";
                $values[':node'] = NODE_ID;
            } else {
                $sqlFilter = "";
            }

            // segun el tipo (ver controller/discover.php)
            $lg_sm_db_prefix = "'" . LG_SM_DB_PREFIX . "'";
            switch ($type) {
                case 'popular':
                    // de los que estan en campaña,
                    // los que tienen más usuarios entre cofinanciadores y mensajeros
                    $sql = "SELECT skillmatching.id as id,
                                   skillmatching.name as name,
                                    (SELECT COUNT(DISTINCT(invest.user))
                                        FROM    invest
                                        WHERE   invest.project = ($lg_sm_db_prefix + skillmatching.id)
                                        AND     invest.status IN ('0', '1')
                                    )
                                    +
                                    (SELECT  COUNT(DISTINCT(message.user))
                                        FROM    message
                                        WHERE   message.project = ($lg_sm_db_prefix + skillmatching.id)
                                    ) as followers
                            FROM skillmatching
                            WHERE skillmatching.status= 3
                            $sqlFilter
                            HAVING followers > 20
                            ORDER BY followers DESC";
                    break;
                case 'outdate':
                    // los que les quedan 15 dias o menos
                    $sql = "SELECT  id,
                                   name
                            FROM    skillmatching
                            WHERE   days <= 15
                            AND     days > 0
                            AND     status = 3
                            $sqlFilter
                            ORDER BY days ASC";
// Quitamos lo de "si ya han conseguido el minimo"
/*
,
                                (SELECT  SUM(amount)
                                FROM    cost
                                WHERE   project = skillmatching.id
                                AND     required = 1
                                ) as `mincost`,
                                (SELECT  SUM(amount)
                                FROM    invest
                                WHERE   project = skillmatching.id
                                AND     (invest.status = 0
                                        OR invest.status = 1
                                        OR invest.status = 3
                                        OR invest.status = 4)
                                ) as `getamount` */
//                            HAVING (getamount < mincost OR getamount IS NULL)

                    break;
                case 'recent':
                    // los que llevan menos tiempo desde el published, hasta 15 dias
                    // Cambio de criterio: Los últimos 9
                    //,  DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(published)), '%e') as day
                    //        HAVING day <= 15 AND day IS NOT NULL
                    $limit = 9;
                    $sql = "SELECT 
                                skillmatching.id as id,
                                skillmatching.name as name
                            FROM skillmatching
                            WHERE skillmatching.status = 3
                            AND skillmatching.passed IS NULL
                            $sqlFilter
                            ORDER BY published DESC";
                    break;
                case 'success':
                    // los que han conseguido el mínimo
                    $sql = "SELECT
                                id,
                                name,
                                (SELECT  SUM(amount)
                                FROM    cost
                                WHERE   project = skillmatching.id
                                AND     required = 1
                                ) as `mincost`,
                                (SELECT  SUM(amount)
                                FROM    invest
                                WHERE   project = ($lg_sm_db_prefix + skillmatching.id)
                                AND     invest.status IN ('0', '1', '3', '4')
                                ) as `getamount`
                        FROM skillmatching
                        WHERE status IN ('3', '4', '5')
                        $sqlFilter
                        HAVING getamount >= mincost
                        ORDER BY published DESC";
                    break;
                case 'almost-fulfilled':
                    // para gestión de retornos
                    $sql = "SELECT id, name FROM skillmatching WHERE status IN ('4','5') $sqlFilter ORDER BY name ASC";
                    break;
                case 'fulfilled':
                    // retorno cumplido
                    $sql = "SELECT id, name FROM skillmatching WHERE status IN ('5') $sqlFilter ORDER BY name ASC";
                    break;
                case 'available':
                    // ni edicion ni revision ni cancelados, estan disponibles para verse publicamente
                    $sql = "SELECT id, name FROM skillmatching WHERE status > 2 AND status < 6 $sqlFilter ORDER BY status ASC";
                    break;
                case 'available_pj_sm':
                    // ni edicion ni revision ni cancelados, estan disponibles para verse publicamente
                    $sql = "(SELECT id, name, status, published, 'project' as type FROM project WHERE status > 2 AND status < 6 $sqlFilter) UNION (SELECT id, name, status, published, 'skillmatching' as type FROM skillmatching WHERE status > 2 AND status < 6 $sqlFilter) ORDER BY published DESC, status ASC";
//                    $sql = "SELECT id, name FROM skillmatching WHERE status > 2 AND status < 6 $sqlFilter ORDER BY status ASC";
                    break;
                case 'archive':
                    // caducados, financiados o casos de exito
                    $sql = "SELECT id, name FROM skillmatching WHERE status = 6 $sqlFilter ORDER BY closed DESC";
                    break;
                case 'others':
                    // todos los que estan 'en campaña', en otro nodo
                    if (!empty($sqlFilter)) $sqlFilter = \str_replace('=', '!=', $sqlFilter);
                    // cambio de criterio, en otros nodos no filtramos por followers,
                    //   mostramos todos los que estan en campaña (los nuevos primero)
                    //  limitamos a 40
                    /*
                    $sql = "SELECT skillmatching.id as id,
                                    (SELECT COUNT(DISTINCT(invest.user))
                                        FROM    invest
                                        WHERE   invest.project = skillmatching.id
                                        AND     invest.status IN ('0', '1')
                                    )
                                    +
                                    (SELECT  COUNT(DISTINCT(message.user))
                                        FROM    message
                                        WHERE   message.project = skillmatching.id
                                    ) as followers
                            FROM project
                            WHERE skillmatching.status= 3
                            $sqlFilter
                            HAVING followers > 20
                            ORDER BY followers DESC";
                    */
                    $limit = 40;
                    $sql = "SELECT
                                skillmatching.id as id,
                                skillmatching.name as name
                            FROM skillmatching
                            WHERE skillmatching.status = 3
                            $sqlFilter
                            ORDER BY published DESC";
                    break;
                default: 
                    // todos los que estan 'en campaña', en cualquier nodo
                    $sql = "SELECT id, name FROM skillmatching WHERE status = 3 ORDER BY name ASC";
            }

            // Limite
            if (!empty($limit) && \is_numeric($limit)) {
                $sql .= " LIMIT $limit";
            }

            $skillmatchings = array();
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                if ($mini) {
                    $skillmatchings[$proj['id']] = $proj['name'];
                } else {
                    if (($type == 'available_pj_sm') && ($proj['type'] == 'project')){
                        $skillmatchings[] = Project::getMedium($proj['id']);
                    } else {
                        $skillmatchings[] = self::getMedium($proj['id']);
                    }
                }
            }
            return $skillmatchings;
        }

        // 
        /**
         * Lista de proyectos en campaña y/o financiados 
         *   para crear aporte manual
         *   para gestión de contratos
         * 
         * @param bool $campaignonly  solo saca proyectos en proceso de campaña  (parece que esto no se usa...)
         * @param bool $mini  devuelve array asociativo id => nombre, para cuando no se necesita toda la instancia
         * @return array de instancias de proyecto // array asociativo (si recibe mini = true)
         */
        public static function active($campaignonly = false, $mini = false)
        {
            $skillmatchings = array();

            if ($campaignonly) {
                $sqlFilter = " WHERE skillmatching.status = 3";
            } else {
                $sqlFilter = " WHERE skillmatching.status = 3 OR skillmatching.status = 4";
            }

            $sql = "SELECT id, name FROM skillmatching {$sqlFilter} ORDER BY name ASC";

            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                if ($mini) {
                    $skillmatchings[$proj->id] = $proj->name;
                } else {
                    $skillmatchings[] = self::get($proj->id);
                }
            }
            return $skillmatchings;
        }

        /**
         * Lista de proyectos para ser revisados por el cron/daily
         * en campaña
         *  o financiados hace más de dos meses y con retornos/recompensas pendientes
         * 
         * solo carga datos necesarios para cron/daily
         * 
         * @return array de instancias parciales de proyecto
         */
        public static function review()
        {
            $skillmatchings = array();
            $now_local = static::localNow();

            $sql = "SELECT 
                    id, status, 
                    DATE_FORMAT(from_unixtime(unix_timestamp('${now_local}') - unix_timestamp(published)), '%j') as dias
                FROM skillmatching 
                WHERE status IN ('3', '4')
                HAVING status = 3 OR (status = 4 AND  dias > 138)
                ORDER BY days ASC";
            
            
            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $the_proj = self::getMedium($proj->id);
                $the_proj->percent = floor(($the_proj->invested / $the_proj->mincost) * 100);
                $the_proj->days = (int) $proj->dias - 1;
                $the_proj->patrons = Patron::numRecos($proj->id);
                
                $skillmatchings[] = $the_proj;
            }
            return $skillmatchings;
        }

        /*
         * Lista de proyectos en campaña (para ser revisados por el cron/execute)
         */
        public static function getActive()
        {
            $skillmatchings = array();

            $now_local = static::localNow();
            // 終了まで 35 or 75 日以上のプロジェクトのみアクティブと判断される？
            /*
            $sql = "
                SELECT skillmatching.id as id
                FROM  skillmatching
                WHERE skillmatching.status = 3
                AND (
                    (DATE_FORMAT(from_unixtime(unix_timestamp('${now_local}') - unix_timestamp(published)), '%j') >= 35
                        AND (passed IS NULL OR passed = '0000-00-00')
                        )
                    OR
                    (DATE_FORMAT(from_unixtime(unix_timestamp('${now_local}') - unix_timestamp(published)), '%j') >= 75
                        AND (success IS NULL OR success = '0000-00-00')
                        )
                    )
                ORDER BY name ASC
            ";
            */
            // debug用
            $sql = "
                SELECT skillmatching.id as id
                FROM  skillmatching
                WHERE skillmatching.status = 3
                AND (
                    (DATE_FORMAT(from_unixtime(unix_timestamp('${now_local}') - unix_timestamp(published)), '%j') >= (period_1r - 5)
                        AND (passed IS NULL OR passed = '0000-00-00')
                        )
                    OR
                    (DATE_FORMAT(from_unixtime(unix_timestamp('${now_local}') - unix_timestamp(published)), '%j') >= (period_2r - 5)
                        AND (success IS NULL OR success = '0000-00-00')
                        )
                    )
                ORDER BY name ASC
            ";
            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $proj) {
                $skillmatchings[] = self::get($proj->id);
            }
            return $skillmatchings;
        }

        /**
         * Saca una lista completa de proyectos
         *
         * @param string node id
         * @return array of project instances
         */
        public static function getList($filters = array(), $node = null) {
            $skillmatchings = array();

            $values = array();

            // los filtros
            $sqlFilter = "";
            if (!empty($filters['multistatus'])) {
                $sqlFilter .= " AND status IN ({$filters['multistatus']})";
            }
            if ($filters['status'] > -1) {
                $sqlFilter .= " AND status = :status";
                $values[':status'] = $filters['status'];
            } elseif ($filters['status'] == -2) {
                $sqlFilter .= " AND (status = 1  AND id NOT REGEXP '[0-9a-f]{5,40}')";
            } else {
                $sqlFilter .= " AND (status > 1  OR (status = 1 AND id NOT REGEXP '[0-9a-f]{5,40}') )";
            }
            if (!empty($filters['owner'])) {
                $sqlFilter .= " AND owner = :owner";
                $values[':owner'] = $filters['owner'];
            }
            if (!empty($filters['name'])) {
                $sqlFilter .= " AND owner IN (SELECT id FROM user WHERE (name LIKE :user OR email LIKE :user))";
                $values[':user'] = "%{$filters['name']}%";
            }
            if (!empty($filters['proj_name'])) {
                $sqlFilter .= " AND name LIKE :name";
                $values[':name'] = "%{$filters['proj_name']}%";
            }
            if (!empty($filters['category'])) {
                $prefix = LG_SM_DB_PREFIX;
                $sqlFilter .= " AND CONCAT('$prefix',id) IN (
                    SELECT project
                    FROM project_category
                    WHERE category = :category
                    )";
                $values[':category'] = $filters['category'];
            }
            if (!empty($filters['skill'])) {
                $prefix = LG_SM_DB_PREFIX;
                $sqlFilter .= " AND CONCAT('$prefix',id) IN (
                    SELECT project
                    FROM project_skill
                    WHERE skill = :skill
                    )";
                $values[':skill'] = $filters['skill'];
            }

            //el Order
            if (!empty($filters['order'])) {
                switch ($filters['order']) {
                    case 'created':
                        $sqlOrder .= " ORDER BY created DESC";
                        break;
                    case 'updated':
                        $sqlOrder .= " ORDER BY updated DESC";
                        break;
                    case 'name':
                        $sqlOrder .= " ORDER BY name ASC";
                        break;
                    default:
//                        $sqlOrder .= " ORDER BY {$filters['order']}";
                        $sqlOrder .= " ORDER BY created DESC";
                        break;
                }
            }

            // la select
            $sql = "SELECT 
                        id,
                        id REGEXP '[0-9a-f]{5,40}' as draft
                    FROM skillmatching
                    WHERE id != ''
                        $sqlFilter
                        $sqlOrder
                    LIMIT 999
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $the_proj = self::getMedium($proj['id']);
                $the_proj->draft = $proj['draft'];
                $skillmatchings[] = $the_proj;
            }
            return $skillmatchings;
        }

        /**
         * Saca una lista de proyectos disponibles para traducir
         *
         * @param array filters
         * @param string node id
         * @return array of project instances
         */
        public static function getTranslates($filters = array(), $node = \GOTEO_NODE) {
            $skillmatchings = array();

            $values = array(':node' => $node);

            $sqlFilter = "";
            if (!empty($filters['owner'])) {
                $sqlFilter .= " AND owner = :owner";
                $values[':owner'] = $filters['owner'];
            }
            if (!empty($filters['translator'])) {
                $sqlFilter .= " AND id IN (
                    SELECT item
                    FROM user_translate
                    WHERE user = :translator
                    AND type = 'project'
                    )";
                $values[':translator'] = $filters['translator'];
            }

            $sql = "SELECT
                        id
                    FROM skillmatching
                    WHERE translate = 1
                    AND node = :node
                        $sqlFilter
                    ORDER BY name ASC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $skillmatchings[] = self::getMini($proj['id']);
            }
            return $skillmatchings;
        }

        /**
         *  Metodo para obtener cofinanciadores agregados por usuario
         *  y sin convocadores
         */
        public function agregateInvestors () {
            $investors = array();

            foreach($this->investors as $investor) {

                if (!empty($investor->campaign)) continue;
                
                $investors[$investor->user] = (object) array(
                    'user' => $investor->user,
                    'name' => $investor->name,
                    'avatar' => $investor->avatar,
                    'projects' => $investor->projects,
                    'worth' => $investor->worth,
                    'amount' => $investors[$investor->user]->amount + $investor->amount,
                    'date' => !empty($investors[$investor->user]->date) ?$investors[$investor->user]->date : $investor->date
                );
            }

            return $investors;
        }

        /*
        Método para calcular el mínimo y óptimo de un proyecto
        */
        public static function calcCosts($id) {
            $cost_query = self::query("SELECT
                        (SELECT  SUM(amount)
                        FROM    cost
                        WHERE   project = skillmatching.id
                        AND     required = 1
                        ) as `mincost`,
                        (SELECT  SUM(amount)
                        FROM    cost
                        WHERE   project = skillmatching.id
                        ) as `maxcost`
                FROM skillmatching
                WHERE id =?", array($id));
            $costs = $cost_query->fetchObject();
            
            return $costs;
        }


        /*
         * Para saber si ha conseguido el mínimo
         */
        public static function isSuccessful($id) {
            $sql = "SELECT
                            id,
                            (SELECT  SUM(amount)
                            FROM    cost
                            WHERE   project = skillmatching.id
                            AND     required = 1
                            ) as `mincost`,
                            (SELECT  SUM(amount)
                            FROM    invest
                            WHERE   project = skillmatching.id
                            AND     invest.status IN ('0', '1', '3', '4')
                            ) as `getamount`
                    FROM skillmatching
                    WHERE skillmatching.id = ?
                    HAVING getamount >= mincost
                    LIMIT 1
                    ";

            $query = self::query($sql, array($id));
            return ($query->fetchColumn() == $id);
        }

        /*
         * Para saber si un usuario es el impulsor
         */
        public static function isMine($id, $user) {
            $sql = "SELECT id, owner FROM skillmatching WHERE id = :id AND owner = :owner";
            $values = array(
                ':id' => $id,
                ':owner' => $user
            );
            $query = static::query($sql, $values);
            $mine = $query->fetchObject();
            if ($mine->owner == $user && $mine->id == $id) {
                return true;
            } else {
                return false;
            }
        }

        /*
         * Estados de desarrollo del propyecto
         */
        public static function currentStatus () {
            return array(
                1=>Text::get('overview-field-options-currently_inicial'),
                2=>Text::get('overview-field-options-currently_medio'),
                3=>Text::get('overview-field-options-currently_avanzado'),
                4=>Text::get('overview-field-options-currently_finalizado'));
        }

        /*
         * Ámbito de alcance de un proyecto
         */
        public static function scope () {
            return array(
                1=>Text::get('overview-field-options-scope_local'),
                2=>Text::get('overview-field-options-scope_regional'),
                3=>Text::get('overview-field-options-scope_nacional'),
                4=>Text::get('overview-field-options-scope_global'));
        }

        /*
         * Estados de publicación de un proyecto
         */
        public static function status () {
            return array(
                0=>Text::get('form-project_status-cancelled'),
                1=>Text::get('form-project_status-edit'),
                2=>Text::get('form-project_status-review'),
                3=>Text::get('form-project_status-campaing'),
                4=>Text::get('form-project_status-success'),
                5=>Text::get('form-project_status-fulfilled'),
                6=>Text::get('form-project_status-expired'));
        }

        /*
         * Estados de proceso de campaña
         */
        public static function procStatus () {
            return array(
                'first' => Text::_('En primera ronda'),
                'second' => Text::_('En segunda ronda'),
                'completed' => Text::_('Campaña completada')
                );
        }

        /*
         * Siguiente etapa en la vida del proyeto
         */
        public static function waitfor () {
            return array(
                0=>Text::get('form-project_waitfor-cancel'),
                1=>Text::get('form-project_waitfor-edit'),
                2=>Text::get('form-project_waitfor-review'),
                3=>Text::get('form-project_waitfor-campaing'),
                4=>Text::get('form-project_waitfor-success'),
                5=>Text::get('form-project_waitfor-fulfilled'),
                6=>Text::get('form-project_waitfor-expired'));
        }


        public static function blankErrors() {
            // para guardar los fallos en los datos
            $errors = array(
                'userProfile'  => array(),  // Errores en el paso 1
                'userPersonal' => array(),  // Errores en el paso 2
                'overview'     => array(),  // Errores en el paso 3
                'costs'        => array(),  // Errores en el paso 4
                'rewards'      => array(),  // Errores en el paso 5
                'supports'     => array()   // Errores en el paso 6
            );

            return $errors;
        }
        public static function getLocationNames () {
			$env_locations = explode(',', getenv('GOTEO_LOCATION_NAMES'));
			$result = array();
			foreach ($env_locations as $index => $location) {
				$idx = ($index === 0) ? 1 : $index + 10;
				$result[$idx] = Text::get($location);
			}
			return $result;
        }
        public static function getTotalInvestors($id){
            $prefixed_id = self::getPrefixedId($id);
            $sql = "SELECT COUNT(id) FROM invest WHERE project = ? AND status IN ('0', '1', '3', '4')";
            $query = self::query($sql, array($prefixed_id));
            return ($query->fetchColumn());
        }
    }

}
