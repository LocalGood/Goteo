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

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Library\Check,
        Goteo\Library\Mail,
        Goteo\Library\Template,
        Goteo\Library\Message,
        Goteo\Library\Feed,
        Goteo\Model;

    class Skillmatching extends \Goteo\Core\Controller {

        public function index($id = null, $show = 'home', $post = null) {
            if ($id !== null) {
                return $this->view($id, $show, $post);
            } else if (isset($_GET['create'])) {
                throw new Redirection("/skillmatching/create");
            } else {
                throw new Redirection("/discover");
            }
        }

        public function raw ($id) {
            $skillmatching = Model\Skillmatching::get($id, LANG);
            $skillmatching->check();
            \trace($skillmatching->call);
            \trace($skillmatching);
            die;
        }

        public function delete ($id) {
            if(!isset($_SESSION['user']->roles['project_owner']) && !isset($_SESSION['user']->roles['localadmin'])) throw new Redirection('/dashboard/profile/');
            $skillmatching = Model\Skillmatching::get($id);
            $errors = array();
            if ($skillmatching->delete($errors)) {
                Message::Info(Text::get('dashboard-project-delete-correctly', $skillmatching->name));
                if ($_SESSION['skillmatching']->id == $id) {
                    unset($_SESSION['skillmatching']);
                }
            } else {
                Message::Info("No se han podido borrar los datos del proyecto '<strong>{$skillmatching->name}</strong>'. Error:" . implode(', ', $errors));
            }
            throw new Redirection("/dashboard/projects");
        }

        //Aunque no esté en estado edición un admin siempre podrá editar un proyecto
        public function edit ($id, $step = 'userProfile') {

            if( !isset($_SESSION['user']->roles['project_owner']) && !isset($_SESSION['user']->roles['localadmin']) ) throw new Redirection('/dashboard/profile/');
            
            $skillmatching = Model\Skillmatching::get($id, null);

            // para que tenga todas las imágenes
            $skillmatching->gallery = Model\Image::getAll($id, 'skillmatching');

            // aunque pueda acceder edit, no lo puede editar si
            if ($skillmatching->owner != $_SESSION['user']->id // no es su proyecto
                && (isset($_SESSION['admin_node']) && $_SESSION['admin_node'] != \GOTEO_NODE) // es admin pero no es admin de central
                && (isset($_SESSION['admin_node']) && $skillmatching->node != $_SESSION['admin_node']) // no es de su nodo
                && !isset($_SESSION['user']->roles['superadmin']) // no es superadmin
                && (isset($_SESSION['user']->roles['checker']) && !Model\User\Review::is_assigned($_SESSION['user']->id, $skillmatching->id)) // no lo tiene asignado
                ) {
                Message::Info(Text::_('No tienes permiso para editar este proyecto'));
                throw new Redirection('/admin/projects');
            }

            // si no tenemos SESSION stepped es porque no venimos del create
            if (!isset($_SESSION['stepped']))
                $_SESSION['stepped'] = array(
                     'userProfile'  => 'userProfile',
                     'userPersonal' => 'userPersonal',
                     'overview'     => 'overview',
//                     'costs'        => 'costs',
                     'rewards'      => 'rewards',
                     'supports'     => 'supports'
                );

            if ($skillmatching->status != 1 && !ACL::check('/skillmatching/edit/todos')) {
                // solo puede estar en preview
                $step = 'preview';
                
                $steps = array(
                    'preview' => array(
                        'name' => Text::get('step-7'),
                        'title' => Text::get('step-preview'),
                        'offtopic' => true
                    )
                );
                 
                 
            } else {
                // todos los pasos
                // entrando, por defecto, en el paso especificado en url
                $steps = array(
                    'userProfile' => array(
                        'name' => Text::get('step-1'),
                        'title' => Text::get('step-userProfile'),
                        'offtopic' => true
                    ),
                    'userPersonal' => array(
                        'name' => Text::get('step-2'),
                        'title' => Text::get('step-userPersonal'),
                        'offtopic' => true
                    ),
                    'overview' => array(
                        'name' => Text::get('step-3'),
                        'title' => Text::get('step-overview')
                    ),
//                    'costs'=> array(
//                        'name' => Text::get('step-4'),
//                        'title' => Text::get('step-costs')
//                    ),
                    'rewards' => array(
                        'name' => Text::get('step-5'),
                        'title' => Text::get('step-rewards')
                    ),
                    'supports' => array(
                        'name' => Text::get('step-6'),
                        'title' => Text::get('step-supports')
                    ),
                    'preview' => array(
                        'name' => Text::get('step-7'),
                        'title' => Text::get('step-preview'),
                        'offtopic' => true
                    )
                );
            }
            
                        
            
            foreach ($_REQUEST as $k => $v) {                
                if (strncmp($k, 'view-step-', 10) === 0 && !empty($v) && !empty($steps[substr($k, 10)])) {
                    $step = substr($k, 10);
                }                
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
                $errors = array(); // errores al procesar, no son errores en los datos del proyecto

                foreach ($steps as $id => &$data) {
                    
                    if (call_user_func_array(array($this, "process_{$id}"), array(&$skillmatching, &$errors))) {
                        // si un process devuelve true es que han enviado datos de este paso, lo añadimos a los pasados
                        if (!in_array($id, $_SESSION['stepped'])) {
                            $_SESSION['stepped'][$id] = $id;
                        }
                    }
                    
                }

                // guardamos los datos que hemos tratado y los errores de los datos
                $skillmatching->save($errors);
                
                // hay que mostrar errores en la imagen
                if (!empty($errors['image'])) {
                    $skillmatching->errors['overview']['image'] = $errors['image'];
                    $skillmatching->okeys['overview']['image'] = null;
                }

                

                // si estan enviando el proyecto a revisión
                if (isset($_POST['process_preview']) && isset($_POST['finish'])) {
                    $errors = array();
                    $old_id = $skillmatching->id;
                    if ($skillmatching->ready($errors)) {

                        if ($_SESSION['skillmatching']->id == $old_id) {
                            $_SESSION['skillmatching'] = $skillmatching;
                        }

                        // email a los de goteo
                        $mailHandler = new Mail();

                        $mailHandler->reply = $skillmatching->user->email;
                        $mailHandler->replyName = "{$skillmatching->user->name}";
                        $mailHandler->to = \GOTEO_MAIL;
                        $mailHandler->toName = 'Revisor de proyectos';
                        $mailHandler->subject = 'Proyecto ' . $skillmatching->name . ' enviado a valoración';
                        $mailHandler->content = '<p>Han enviado un nuevo proyecto a revisión</p><p>El nombre del proyecto es: <span class="message-highlight-blue">'.$skillmatching->name.'</span> <br />y se puede ver en <span class="message-highlight-blue"><a href="'.SITE_URL.'/skillmatching/'.$skillmatching->id.'">'.SITE_URL.'/skillmatching/'.$skillmatching->id.'</a></span></p>';
                        $mailHandler->html = true;
                        $mailHandler->template = 0;
                        if ($mailHandler->send($errors)) {
                            Message::Info(Text::get('project-review-request_mail-success'));
                        } else {
                            Message::Error(Text::get('project-review-request_mail-fail'));
                            Message::Error(implode('<br />', $errors));
                        }

                        unset($mailHandler);

                        // email al autor
                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(8);

                        // Sustituimos los datos
                        $subject = str_replace('%PROJECTNAME%', $skillmatching->name, $template->title);

                        // En el contenido:
                        $search  = array('%USERNAME%', '%PROJECTNAME%');
                        $replace = array($skillmatching->user->name, $skillmatching->name);
                        $content = \str_replace($search, $replace, $template->text);


                        $mailHandler = new Mail();

                        $mailHandler->to = $skillmatching->user->email;
                        $mailHandler->toName = $skillmatching->user->name;
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send($errors)) {
                            Message::Info(Text::get('project-review-confirm_mail-success'));
                        } else {
                            Message::Error(Text::get('project-review-confirm_mail-fail'));
                            Message::Error(implode('<br />', $errors));
                        }

                        unset($mailHandler);

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($skillmatching->id);
                        $log->populate('El proyecto '.$skillmatching->name.' se ha enviado a revision', '/skillmatching/'.$skillmatching->id, \vsprintf('%s ha inscrito el proyecto %s para <span class="red">revisión</span>, el estado global de la información es del %s', array(
                            Feed::item('user', $skillmatching->user->name, $skillmatching->user->id),
                            Feed::item('skillmatching', $skillmatching->name, $skillmatching->id),
                            Feed::item('relevant', $skillmatching->progress.'%')
                        )));
                        $log->doAdmin('skillmatching');
                        unset($log);

                        throw new Redirection("/dashboard?ok");
                    }
                }


            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
                throw new Error(Error::INTERNAL, 'FORM CAPACITY OVERFLOW');
            }

            //re-evaluar el proyecto
            $skillmatching->check();

            // variables para la vista
            $viewData = array(
                'skillmatching' => $skillmatching,
                'steps' => $steps,
                'step' => $step
            );


            // segun el paso añadimos los datos auxiliares para pintar
            switch ($step) {
                case 'userProfile':
                    $owner = Model\User::get($skillmatching->owner, null);
                    // si es el avatar por defecto no lo mostramos aqui
                    if ($owner->avatar->id == 1) {
                        unset($owner->avatar);
                    }
                    $viewData['user'] = $owner;
                    $viewData['interests'] = Model\User\Interest::getAll();
                    $viewData['skills'] = Model\Skill::getAll();

                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/web-(\d+)-edit/', $k, $r)) {
                                $viewData[$k] = true;
                            }
                        }

                        if (!empty($_POST['web-add'])) {
                            $last = end($owner->webs);
                            if ($last !== false) {
                                $viewData["web-{$last->id}-edit"] = true;
                            }
                        }
                    }
                    break;
                case 'userPersonal':
                    $viewData['account'] = Model\Skillmatching\Account::get($skillmatching->id);
                    break;
                
                case 'overview':
                    $viewData['categories'] = Model\Skillmatching\Category::getAll();

//                    $viewData['currently'] = Model\Skillmatching::currentStatus();
//                    $viewData['scope'] = Model\Skillmatching::scope();
                    $viewData['project_location'] = Model\Skillmatching::yokohamaLocation();
                    break;

                case 'costs':
                    $viewData['types'] = Model\Skillmatching\Cost::types();
                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/cost-(\d+)-edit/', $k, $r)) {
                                $viewData[$k] = true;
                            }
                        }
                        
                        if (!empty($_POST['cost-add'])) {
                            $last = end($skillmatching->costs);
                            if ($last !== false) {
                                $viewData["cost-{$last->id}-edit"] = true;
                            }
                        }
                    }
                    break;

                case 'rewards':
                    $viewData['itypes'] = Model\Skillmatching\Reward::icons('individual');
                    $viewData['stypes'] = Model\Skillmatching\Reward::icons('social');
                    $viewData['licenses'] = Model\Skillmatching\Reward::licenses();
//                    $viewData['types'] = Model\Skillmatching\Support::types();
                    
                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/((social)|(individual))_reward-(\d+)-edit/', $k)) {                                
                                $viewData[$k] = true;
                            }                            
                        }
                        
                        if (!empty($_POST['social_reward-add'])) {
                            $last = end($skillmatching->social_rewards);
                            if ($last !== false) {
                                $viewData["social_reward-{$last->id}-edit"] = true;
                            }
                        }
                        if (!empty($_POST['individual_reward-add'])) {

                            $last = end($skillmatching->individual_rewards);

                            if ($last !== false) {
                                $viewData["individual_reward-{$last->id}-edit"] = true;
                            }
                        }
                    }

                    
                    break;

                case 'supports':
                    $viewData['types'] = Model\Skillmatching\Support::types();
                    $viewData['skills'] = Model\Skill::getAll();
                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/support-(\d+)-edit/', $k, $r)) {
                                $viewData[$k] = true;
                            }
                        }
                        
                        if (!empty($_POST['support-add'])) {
                            $last = end($skillmatching->supports);
                            if ($last !== false) {
                                $viewData["support-{$last->id}-edit"] = true;
                            }
                        }
                    }                   
                    
                    break;
                
                case 'preview':
                    $success = array();
                    if (empty($skillmatching->errors)) {
                        $success[] = Text::get('guide-project-success-noerrors');
                    }
                    if ($skillmatching->finishable) {
                        $success[] = Text::get('guide-project-success-minprogress');
                        $success[] = Text::get('guide-project-success-okfinish');
                    }
                    $viewData['success'] = $success;
                    $viewData['types'] = Model\Skillmatching\Cost::types();
                    break;
            }


            $view = new View (
                "view/skillmatching/edit.html.php",
                $viewData
            );

            return $view;

        }

        public function create () {
            if(!isset($_SESSION['user']->roles['project_owner']) && !isset($_SESSION['user']->roles['localadmin'])) header("Location:" . LOCALGOOD_WP_BASE_URL . "/challenge");

            if (empty($_SESSION['user'])) {
                $_SESSION['jumpto'] = '/skillmatching/create';
                Message::Info(Text::get('user-login-required-to_create'));
                throw new Redirection(SEC_URL."/user/login");
            }

            if ($_POST['action'] != 'continue' || $_POST['confirm'] != 'true') {
                throw new Redirection("/about/howto");
            }

            $skillmatching = new Model\Skillmatching;
            if ($skillmatching->create(\GOTEO_NODE)) {
                $_SESSION['stepped'] = array();
                
                // permisos para editarlo y borrarlo
                ACL::allow('/skillmatching/edit/'.$skillmatching->id.'/', '*', 'user', $_SESSION['user']->id);
                ACL::allow('/skillmatching/delete/'.$skillmatching->id.'/', '*', 'user', $_SESSION['user']->id);

                // Evento Feed
                $log = new Feed();
                $log->setTarget($_SESSION['user']->id, 'user');
                $log->populate('usuario crea nuevo proyecto', 'admin/projects',
                    \vsprintf('%s ha creado un nuevo proyecto, %s', array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('skillmatching', $skillmatching->name, $skillmatching->id))
                    ));
                $log->doAdmin('skillmatching');
                unset($log);


                throw new Redirection("/skillmatching/edit/{$skillmatching->id}");
            }

            throw new \Goteo\Core\Exception('Fallo al crear un nuevo proyecto');
        }

        private function view ($id, $show, $post = null) {
            $skillmatching = Model\Skillmatching::get($id, LANG);

            // recompensas
            foreach ($skillmatching->individual_rewards as &$reward) {
                $reward->none = false;
                $reward->taken = $reward->getTaken(); // cofinanciadores quehan optado por esta recompensas
                // si controla unidades de esta recompensa, mirar si quedan
                if ($reward->units > 0 && $reward->taken >= $reward->units) {
                    $reward->none = true;
                }
            }
//            var_dump($skillmatching);
//            exit;

            // mensaje cuando, sin estar en campaña, tiene fecha de publicación, es que la campaña ha sido cancelada
            if ($skillmatching->status < 3 && !empty($skillmatching->published)) 
                Message::Info(Text::get('project-unpublished'));
            elseif ($skillmatching->status < 3) 
                // mensaje de no publicado siempre que no esté en campaña
                Message::Info(Text::get('project-not_published'));

            
            // solamente se puede ver publicamente si...
            $grant = false;
            if ($skillmatching->status > 2) // está publicado
                $grant = true;
            elseif ($skillmatching->owner == $_SESSION['user']->id)  // es el dueño
                $grant = true;
            elseif (ACL::check('/skillmatching/edit/todos'))  // es un admin
                $grant = true;
            elseif (ACL::check('/skillmatching/view/todos'))  // es un usuario con permiso
                $grant = true;
            elseif (isset($_SESSION['user']->roles['checker']) && Model\User\Review::is_assigned($_SESSION['user']->id, $skillmatching->id)) // es un revisor y lo tiene asignado
                $grant = true;
            // (Callsys)

            // si lo puede ver
            if ($grant) {
                $viewData = array(
                        'skillmatching' => $skillmatching,
                        'show' => $show
                    );

                // sus entradas de novedades
                $blog = Model\Blog::get($skillmatching->id);
                // si está en modo preview, ponemos  todas las entradas, incluso las no publicadas
                if (isset($_GET['preview']) && $_GET['preview'] == $_SESSION['user']->id) {
                    $blog->posts = Model\Blog\Post::getAll($blog->id, null, false);
                }

                $viewData['blog'] = $blog;
                        
                // tenemos que tocar esto un poquito para motrar las necesitades no economicas
                if ($show == 'needs-non') {
                    $viewData['show'] = 'needs';
                    $viewData['non-economic'] = true;
                }

                if($show == 'messages' && is_numeric($_GET['msgto'])){
                    $support = Model\Skillmatching\Support::get($_GET['msgto']);
                    if(is_numeric($support->thread)){
                        $viewData['thread'] = $support->thread;
                    }
                }

                // -- Mensaje azul molesto para usuarios no registrados
                if (($show == 'messages' || $show == 'updates' || $show == 'evaluation') && empty($_SESSION['user'])) {
                    Message::Info(Text::html('user-login-required'));
                }

                //tenemos que tocar esto un poquito para gestionar los pasos al aportar
                if ($show == 'invest') {

                    // si no está en campaña no pueden estar aqui ni de coña
                    if ($skillmatching->status != 3) {
                        Message::Info(Text::get('project-invest-closed'));
                        throw new Redirection('/skillmatching/'.$id, Redirection::TEMPORARY);
                    }

                    $viewData['show'] = 'supporters';
                    /* pasos de proceso aporte
                     *
                     * 1, 'start': ver y seleccionar recompensa (y cantidad)
                     * 2, 'login': loguear con usuario/contraseña o con email (que crea el usuario automáticamente)
                     * 3, 'confirm': confirmar los datos y saltar a la pasarela de pago
                     * 4, 'ok'/'fail': al volver de la pasarela de pago, la confirmación nos dice si todo bien o algo mal
                     * 5, 'continue': recuperar aporte incompleto (variante de confirm)
                     */

                    // usamos la variable de url $post para movernos entre los pasos
                    $step = (isset($post) && in_array($post, array('start', 'login', 'confirm', 'continue'))) ? $post : 'start';

                    // si llega confirm ya ha terminado el proceso de aporte
                    if (isset($_GET['confirm']) && \in_array($_GET['confirm'], array('ok', 'fail'))) {
                        unset($_SESSION['invest-amount']);
                        // confirmación
                        $step = $_GET['confirm'];
                    } else {
                        // si no, a ver en que paso estamos
                        if (isset($_GET['amount']))
                            $_SESSION['invest-amount'] = $_GET['amount'];

                        // si el usuario está validado, recuperamos posible amount y mostramos
                        if ($_SESSION['user'] instanceof Model\User) {
                            $step = 'confirm';
                        } elseif ($step != 'start' && empty($_SESSION['user'])) {
                            // si no está validado solo puede estar en start
                            Message::Info(Text::get('user-login-required-to_invest'));
                            $step = 'start';
                        } elseif ($step == 'start') {
                            // para cuando salte
                            $_SESSION['jumpto'] = SEC_URL.'/skillmatching/' .  $id . '/invest/#continue';
                        } else {
                            $step = 'start';
                        }
                    }

                    $viewData['step'] = $step;
                }

                if ($show == 'updates') {
                    $viewData['post'] = $post;
                    $viewData['owner'] = $skillmatching->owner;
                }


                if ($show == 'messages' && $skillmatching->status < 3) {
                    Message::Info(Text::get('project-messages-closed'));
                }

                return new View(VIEW_PATH . '/skillmatching/view.html.php', $viewData);

            } else {
                // no lo puede ver
                throw new Redirection("/");
            }
        }

        //-----------------------------------------------
        // Métodos privados para el tratamiento de datos
        // del save y remove de las tablas relacionadas se enmcarga el model/project
        // primero añadir y luego quitar para que no se pisen los indices
        // En vez del hidden step, va a comprobar que esté definido en el post el primer campo del proceso
        //-----------------------------------------------
        /*
         * Paso 1 - PERFIL
         */
        private function process_userProfile(&$skillmatching, &$errors) {
            if (!isset($_POST['process_userProfile'])) {
                return false;
            }

            $user = Model\User::get($skillmatching->owner);

            // tratar la imagen y ponerla en la propiedad avatar
            // __FILES__

            $fields = array(
                'user_name'=>'name',
                'user_location'=>'location',
                'user_avatar'=>'avatar',
                'user_about'=>'about',
                'user_keywords' => 'keywords',
                'user_contribution' => 'contribution',
                'user_facebook'=>'facebook',
                'user_google'=>'google',
                'user_twitter'=>'twitter',
                'user_identica'=>'identica',
                'user_linkedin'=>'linkedin'
            );
                        
            foreach ($fields as $fieldPost=>$fieldTable) {
                if (isset($_POST[$fieldPost])) {
                    $user->$fieldTable = $_POST[$fieldPost];
                }
            }
            
            // Avatar
            if (isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] != UPLOAD_ERR_NO_FILE) {
                $user->avatar = $_FILES['avatar_upload'];
            }

            // tratar si quitan la imagen
            if (!empty($_POST['avatar-' . $user->avatar->id .  '-remove'])) {
                $user->avatar->remove();
                $user->avatar = null;
            }

            $user->interests = $_POST['user_interests'];
            $user->skills = $_POST['user_skills'];

            //tratar webs existentes
            foreach ($user->webs as $i => &$web) {
                // luego aplicar los cambios
                
                if (isset($_POST['web-'. $web->id . '-url'])) {
                    $web->url = $_POST['web-'. $web->id . '-url'];
                }
                
                //quitar las que quiten
                if (!empty($_POST['web-' . $web->id .  '-remove'])) {
                    unset($user->webs[$i]);
                }                                                    
                
            }

            //tratar nueva web
            if (!empty($_POST['web-add'])) {
                $user->webs[] = new Model\User\Web(array(
                    'url'   => 'http://'
                ));
            }

            /// este es el único save que se lanza desde un metodo process_
            $user->save($skillmatching->errors['userProfile']);
            
            // si hay errores en la imagen hay que mostrarlos
            if (!empty($skillmatching->errors['userProfile']['image'])) {
                $skillmatching->errors['userProfile']['avatar'] = $skillmatching->errors['userProfile']['image'];
            }
            
            $user = Model\User::flush();
            $skillmatching->user = $user;
            return true;
        }

        /*
         * Paso 2 - DATOS PERSONALES
         */
        private function process_userPersonal(&$skillmatching, &$errors) {
            if (!isset($_POST['process_userPersonal'])) {
                return false;
            }

            // campos que guarda este paso
            $fields = array(
                'contract_name',
                'contract_nif',
                'contract_email',
                'contract_entity',
                'entity_name',
                'entity_office',
                'phone',
                'contract_birthdate',
                'address',
                'zipcode',
                'location',
                'country',
//                'secondary_address',
//                'post_address',
//                'post_zipcode',
//                'post_location',
//                'post_country',
            );

            $personalData = array();

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $skillmatching->$field = $_POST[$field];
                    $personalData[$field] = $_POST[$field];
                }
            }
/*
            if (!$_POST['secondary_address']) {
                $skillmatching->post_address = null;
                $skillmatching->post_zipcode = null;
                $skillmatching->post_location = null;
                $skillmatching->post_country = null;
            }
*/
            // actualizamos estos datos en los personales del usuario
            if (!empty ($personalData)) {
                Model\User::setPersonal($skillmatching->owner, $personalData, true);
            }

            // cuentas bancarias
            $ppacc = (!empty($_POST['paypal'])) ? $_POST['paypal'] : '';
            $bankacc = (!empty($_POST['bank'])) ? $_POST['bank'] : '';

            // primero checkeamos si la cuenta Paypal es tipo email
            /*if (!Check::mail($ppacc)) {
                $skillmatching->errors['userPersonal']['paypal'] = Text::get('validate-project-paypal_account');
            } else {
                $skillmatching->okeys['userPersonal']['paypal'] = true;
            }*/

            $accounts = Model\Skillmatching\Account::get($skillmatching->id);
            $accounts->paypal = $ppacc;
            $accounts->bank = $bankacc;
            $accounts->save($skillmatching->errors['userPersonal']);
            
            
            return true;
        }

        /*
         * Paso 3 - DESCRIPCIÓN
         */

        private function process_overview(&$skillmatching, &$errors) {
            if (!isset($_POST['process_overview'])) {
                return false;
            }

            // campos que guarda este paso
            // image, media y category  van aparte
            $fields = array(
                'name',
                'subtitle',
                'description',
                'motivation',
                'video',
                'video_usubs',
                'about',
                'goal',
                'related',
                'reward',
                'keywords',
                //'media',
                //'media_usubs',
                'project_location',
                'evaluation'
            );

            foreach ($fields as $field) {
                    $skillmatching->$field = $_POST[$field];
            }
            
            // tratar la imagen que suben
            if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] != UPLOAD_ERR_NO_FILE) {
                $skillmatching->image = $_FILES['image_upload'];
            }
//            var_dump($_FILES['image_upload']);

            // tratar las imagenes que quitan
            foreach ($skillmatching->gallery as $key=>$image) {
                if (!empty($_POST["gallery-{$image->id}-remove"])) {
                    $image->remove('skillmatching');
                    unset($skillmatching->gallery[$key]);
                }
            }

            // Media
            /*if (!empty($skillmatching->media)) {
                $skillmatching->media = new Model\Skillmatching\Media($skillmatching->media);
            }*/
            // Video de motivación
            if (!empty($skillmatching->video)) {
                $skillmatching->video = new Model\Skillmatching\Media($skillmatching->video);
            }

            //categorias
            // añadir las que vienen y no tiene
            $tiene = $skillmatching->categories;
            if (isset($_POST['categories'])) {
                $viene = $_POST['categories'];
                $quita = array_diff($tiene, $viene);
            } else {
                $quita = $tiene;
            }
            $guarda = array_diff($viene, $tiene);
            foreach ($guarda as $key=>$cat) {
                $category = new Model\Skillmatching\Category(array('id'=>$cat,'project'=>$skillmatching->id));
                $skillmatching->categories[] = $category;
            }

            // quitar las que tiene y no vienen
            foreach ($quita as $key=>$cat) {
                unset($skillmatching->categories[$key]);
            }

            $quedan = $skillmatching->categories; // truki para xdebug

            return true;
        }

        /*
         * Paso 4 - COSTES
         */
        private function process_costs(&$skillmatching, &$errors) {
            if (!isset($_POST['process_costs'])) {
                return false;
            }

            
            //tratar costes existentes
            foreach ($skillmatching->costs as $key => $cost) {
                
                if (!empty($_POST["cost-{$cost->id}-remove"])) {
                    unset($skillmatching->costs[$key]);
                    continue;
                }

                if (isset($_POST['cost-' . $cost->id . '-cost'])) {
                    $cost->cost = $_POST['cost-' . $cost->id . '-cost'];
                    $cost->description = $_POST['cost-' . $cost->id .'-description'];
                    $cost->amount = $_POST['cost-' . $cost->id . '-amount'];
                    $cost->type = $_POST['cost-' . $cost->id . '-type'];
                    $cost->required = $_POST['cost-' . $cost->id . '-required'];
                    $cost->from = $_POST['cost-' . $cost->id . '-from'];
                    $cost->until = $_POST['cost-' . $cost->id . '-until'];
                }
            }

            //añadir nuevo coste
            if (!empty($_POST['cost-add'])) {
                
                $skillmatching->costs[] = new Model\Skillmatching\Cost(array(
                    'skillmatching' => $skillmatching->id,
                    'cost'  => '',
                    'type'  => 'task',
                    'required' => 1,
                    'from' => date('Y-m-d'),
                    'until' => date('Y-m-d')
                    
                ));
                
            }
           
            return true;
        }

        /*
         * Paso 5 - RETORNO
         */
        private function process_rewards(&$skillmatching, &$errors) {
            if (!isset($_POST['process_rewards'])) {
                return false;
            }

            $types = Model\Skillmatching\Reward::icons('');

            // retornos individuales
            foreach ($skillmatching->individual_rewards as $k => $reward) {
                
                if (!empty($_POST["individual_reward-{$reward->id}-remove"])) {
                    unset($skillmatching->individual_rewards[$k]);
                    continue;
                }

                if (isset($_POST['individual_reward-' . $reward->id .'-reward'])) {
                    $reward->reward = $_POST['individual_reward-' . $reward->id .'-reward'];
                    $reward->description = $_POST['individual_reward-' . $reward->id . '-description'];
                    $reward->icon = $_POST['individual_reward-' . $reward->id . '-icon'];
                    if ($reward->icon == 'other') {
                        $reward->other = $_POST['individual_reward-' . $reward->id . '-other'];
                    }
                    $reward->amount = $_POST['individual_reward-' . $reward->id . '-amount'];
                    $reward->units = $_POST['individual_reward-' . $reward->id . '-units'];
                    $reward->icon_name = $types[$reward->icon]->name;
                }
                
            }

            //tratar retornos sociales
            foreach ($skillmatching->social_rewards as $k => $reward) {

                if (!empty($_POST["social_reward-{$reward->id}-remove"])) {
                    unset($skillmatching->social_rewards[$k]);
                    continue;
                }

                if (isset($_POST['social_reward-' . $reward->id . '-reward'])) {
                    $reward->reward = $_POST['social_reward-' . $reward->id . '-reward'];
                    $reward->description = $_POST['social_reward-' . $reward->id . '-description'];
                    $reward->icon = $_POST['social_reward-' . $reward->id . '-icon'];
                    if ($reward->icon == 'other') {
                        $reward->other = $_POST['social_reward-' . $reward->id . '-other'];
                    }
                    $reward->license = $_POST['social_reward-' . $reward->id . '-' . $reward->icon . '-license'];
                    $reward->icon_name = $types[$reward->icon]->name;
                }

            }

            // tratar nuevos retornos
            if (!empty($_POST['individual_reward-add'])) {
                $skillmatching->individual_rewards[] = new Model\Skillmatching\Reward(array(
                    'type'      => 'individual',
                    'project'   => $skillmatching->id,
                    'reward'    => '',
                    'icon'      => '',
                    'amount'    => '',
                    'units'     => ''
                ));
            }

            if (!empty($_POST['social_reward-add'])) {
                $skillmatching->social_rewards[] = new Model\Skillmatching\Reward(array(
                    'type'      => 'social',
                    'project'   => $skillmatching->id,
                    'reward'    => '',
                    'icon'      => '',
                    'license'   => ''

                ));
            }

            return true;
            
        }

        /*
         * Paso 6 - COLABORACIONES
         */
         private function process_supports(&$skillmatching, &$errors) {            
            if (!isset($_POST['process_supports'])) {
                return false;
            }
            // tratar colaboraciones existentes
            foreach ($skillmatching->supports as $key => $support) {
                
                // quitar las colaboraciones marcadas para quitar
                if (!empty($_POST["support-{$support->id}-remove"])) {
                    unset($skillmatching->supports[$key]);
                    continue;
                }

                if (isset($_POST['support-' . $support->id . '-support'])) {
                    $support->support = $_POST['support-' . $support->id . '-support'];
                    $support->description = $_POST['support-' . $support->id . '-description'];
                    $support->type = $_POST['support-' . $support->id . '-type'];

                    if (!empty($support->thread)) {
                        // actualizar ese mensaje
                        $msg = Model\Message::get($support->thread);
                        $msg->date = date('Y-m-d');
                        $msg->message = "{$support->support}: {$support->description}";
                        $msg->blocked = true;
                        $msg->save($errors);
                    } else {
                        // grabar nuevo mensaje
                        $msg = new Model\Message(array(
                            'user'    => $skillmatching->owner,
                            'skillmatching' => $skillmatching->id,
                            'date'    => date('Y-m-d'),
                            'message' => "{$support->support}: {$support->description}",
                            'blocked' => true
                            ));
                        if ($msg->save($errors)) {
                            // asignado a la colaboracion como thread inicial
                            $support->thread = $msg->id;
                        }
                    }
                }
            }

            //skills
            // añadir las que vienen y no tiene
            $tiene = $skillmatching->skills;
            if (isset($_POST['skills'])) {
                $viene = $_POST['skills'];
                $quita = array_diff($tiene, $viene);
            } else {
                $quita = $tiene;
            }
            $guarda = array_diff($viene, $tiene);
            foreach ($guarda as $key=>$cat) {
                $skill = new Model\Skillmatching\Skill(array('id'=>$cat,'project'=>$skillmatching->id));
                $skillmatching->skills[] = $skill;
            }

            // quitar las que tiene y no vienen
            foreach ($quita as $key=>$cat) {
                unset($skillmatching->skills[$key]);
            }

            $quedan = $skillmatching->skills; // truki para xdebug
            
            // añadir nueva colaboracion
            if (!empty($_POST['support-add'])) {
                $skillmatching->supports[] = new Model\Skillmatching\Support(array(
                    'project'       => $skillmatching->id,
                    'support'       => '',
                    'type'          => 'task',
                    'description'   => ''
                ));
            }

            return true;
        }

        /*
         * Paso 7 - PREVIEW
         * No hay nada que tratar porque aq este paso no se le envia nada por post
         */
        private function process_preview(&$skillmatching) {
            if (!isset($_POST['process_preview'])) {
                return false;
            }

            if (!empty($_POST['comment'])) {
                $skillmatching->comment = $_POST['comment'];
            }

            return true;
        }
        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}