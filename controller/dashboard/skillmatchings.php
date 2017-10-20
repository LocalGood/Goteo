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

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\ACL,
        Goteo\Core\Redirection,
		Goteo\Library\Text,
		Goteo\Library\Feed,
		Goteo\Library\Mail,
		Goteo\Library\Template,
		Goteo\Library\Message,
        Aws\Ses\SesClient,
        Aws\Ses\Exception\SesException;

/*
 * las opciones para /dashboard/skillmatchings:
 * 
 *      'updates' actualizaciones
 *      'supports' editar colaboraciones
 *      'widgets' ofrece el código para poner su proyecto en otras páginas (vertical y horizontal)
 *      'licencia' el acuerdo entre goteo y el usuario, licencia cc-by-nc-nd, enlace al pdf
 *      'gestionar retornos' resumen recompensas/cofinanciadores/conseguido  y lista de cofinanciadores y recompensas esperadas
 *      'messegers' gestionar colaboradores
 *      'contract' contrato
 *      'account'  cuentas
 */            
    class Skillmatchings {
            
        /**
         * Verificación de proyecto de trabajo
         * 
         * @param object $user instancia Model\User del convocador
         * @param string $action por si es 'select'
         * @return array(skillmatching, skillmatchings)
         */
        public static function verifySkillmatching($user, $action) {
            
            $skillmatchings = Model\Skillmatching::ofmine($user->id); // sus proyectos

            // si no tiene, no debería estar aquí
            if (empty($skillmatchings) || !is_array($skillmatchings)) {
                return array(null, null);
            }
            
            // comprobamos que tenga los permisos para editar y borrar
            foreach ($skillmatchings as $proj) {

                // comprueba que puede editar sus proyectos
                if (!ACL::check('/skillmatching/edit/' . $proj->id)) {
                    ACL::allow('/skillmatching/edit/' . $proj->id . '/', '*', 'user', $user);
                }

                // y borrarlos
                if (!ACL::check('/skillmatching/delete/' . $proj->id)) {
                    ACL::allow('/skillmatching/delete/' . $proj->id . '/', '*', 'user', $user);
                }
            }

            // si está seleccionando otro proyecto
            if ($action == 'select' && !empty($_POST['skillmatching'])) {
                $skillmatching = Model\Skillmatching::get($_POST['skillmatching']);
            } elseif (!empty($_SESSION['skillmatching']->id)) {
                // mantener los datos del proyecto de trabajo
                $skillmatching = Model\Skillmatching::get($_SESSION['skillmatching']->id);
            }

            // si aun no tiene proyecto de trabajo, coge el primero
            if (empty($skillmatching)) {
                $skillmatching = $skillmatchings[0];
            }

            // tiene que volver con un proyecto de trabajo
            if ($skillmatching instanceof \Goteo\Model\Skillmatching) {
                $_SESSION['skillmatching'] = $skillmatching; // lo guardamos en sesión para la próxima verificación
            } else {
                Message::Error(Text::get('dashboard-skillmatchings-error-noupdate-sm'));
                $skillmatching = null;
            }

            // devolvemos lista de proyectos y proyecto de trabajo
            return array($skillmatching, $skillmatchings);
        }
        
        /**
         * Verifica que todo está correcto para publicar novedades
         * 
         * @param type $skillmatching Instancia de proyecto de trabajo
         * @return \Goteo\Controller\Dashboard\Blog
         * @throws Redirection a Mis Proyectos si hay algo mal
         */
        public static function verifyBlog($skillmatching) {
            
            $errors = array();
            
            // tenemos proyecto de trabajo, comprobar si el proyecto esta en estado de tener blog
            if ($skillmatching->status < 3 || $skillmatching->status == 6) {
                Message::Error(Text::get('dashboard-skillmatching-blog-wrongstatus'));
                throw new Redirection('/dashboard/skillmatchings/summary');
            }
            
            // si no tiene registro de blog se lo creamos
            $blog = Model\Blog::get(LG_SM_DB_PREFIX . $skillmatching->id);
            if (!$blog instanceof Model\Blog) {
                $blog = new Model\Blog(
                                array(
                                    'id' => '',
                                    'type' => 'project',
                                    'owner' => LG_SM_DB_PREFIX . $skillmatching->id,
                                    'active' => true,
                                    'project' => LG_SM_DB_PREFIX . $skillmatching->id,
                                    'posts' => array()
                                )
                );
                if (!$blog->save($errors)) {
                    Message::Error(Text::get('dashboard-project-blog-fail'));
                    Message::Error(implode('<br />', $errors));
                    throw new Redirection('/dashboard/skillmatchings/summary');
                }
            } elseif (!$blog->active) {
                Message::Error(Text::get('dashboard-skillmatching-blog-inactive'));
                throw new Redirection('/dashboard/skillmatchings/summary');
            }

            return $blog;
        }


        
        /**
         * Gestiona las acciones de gestión de updates
         * 
         * @param type $action (por referencia)
         * @param type $id del post a gestionar
         * @param type $blog id del blog del proyecto
         * @param type $errors (por referencia)
         * @return instancia de Post para las acciones add y edit , array de Posts para las acciones delete y list
         */
        public static function prepare_updates (&$action, $id, $blog) {
            // segun la accion
            switch ($action) {
                case 'none' :
                    $posts = array();
                    break;
                case 'add':
                    $post = new Model\Blog\Post(
                                    array(
                                        'blog' => $blog,
                                        'date' => date('Y-m-d'),
                                        'publish' => false,
                                        'allow' => true
                                    )
                    );
                    return array($post, null);
                    
                    break;
                case 'edit':
                    if (empty($id)) {
                        Message::Error(Text::get('dashboard-project-updates-nopost'));
                        $action = 'list';
                        break;
                    } else {
                        $post = Model\Blog\Post::get($id);

                        if (!$post instanceof Model\Blog\Post) {
                            Message::Error(Text::get('dashboard-project-updates-postcorrupt'));
                            $action = 'list';
                            break;
                        }
                        return array($post, null);
                    }

                    break;
                case 'delete':
                    $post = Model\Blog\Post::get($id);
                    if ($post->delete($id)) {
                        Message::Info(Text::get('dashboard-project-updates-deleted'));
                    } else {
                        Message::Error(Text::get('dashboard-project-updates-delete_fail'));
                    }
                    $posts = Model\Blog\Post::getAll($blog, null, false);
                    $action = 'list';
                    return array(null, $posts);

                    break;
                default:
                    $posts = Model\Blog\Post::getAll($blog, null, false);
                    $action = 'list';
                    return array(null, $posts);

                    break;
            }
        }
        
        
        /**
         * Realiza el envio masivo a participantees o cofinanciadores
         * 
         * @param type $option 'messegers' || 'rewards'
         * @param type $skillmatching Instancia del proyecto de trabajo
         * @return boolean
         */
        public static function process_mailing ($option, $skillmatching) {

            $who = array();

            // verificar que hay mensaje
            if (empty($_POST['message'])) {
                Message::Error(Text::get('dashboard-investors-mail-text-required'));
                return false;
            } else {
                $msg_content = nl2br(\strip_tags($_POST['message']));
            }

            // si a todos los participantes
            if ($option == 'messegers' && !empty($_POST['msg_all'])) {
                // a todos los participantes(if all investors)
                foreach (Model\Message::getMessegers($skillmatching->id) as $messeger => $msgData) {
                    if ($messeger == $skillmatching->owner)
                        continue;
                    $who[$messeger] = $messeger;
                    //unset($msgData); // los datos del mensaje del participante no se usan
                }
            } elseif ($option == 'rewards' && !empty($_POST['msg_all'])) {
                // a todos los cofinanciadores
                foreach (Model\Invest::investors($skillmatching->id, false, true) as $user => $investor) {
                    // no duplicar
                    $who[$investor->user] = $investor->user;

                }
            } elseif (!empty($_POST['msg_user'])) {
                // a usuario individual
                $who[$_POST['msg_user']] = $_POST['msg_user'];
            } elseif ($option == 'rewards') {
                $msg_rewards = array();
                // estos son msg_reward-[rewardId], a un grupo de recompensa
                foreach ($_POST as $key => $value) {
                    $parts = explode('-', $key);
                    if ($parts[0] == 'msg_reward' && $value == 1) {
                        $msg_rewards[] = $parts[1];
                    }
                }

                // para cada recompensa
                foreach ($msg_rewards as $reward) {
                    foreach (Model\Invest::choosed($reward) as $user) {
                        $who[$user] = $user;
                    }
                }
            }

            // no hay destinatarios
            if (count($who) == 0) {
                Message::Error(Text::get('dashboard-investors-mail-nowho'));
                return false;
            }

            // obtener contenido
            // segun destinatarios
            $allsome = explode('/', Text::get('regular-allsome'));
            $enviandoa = !empty($_POST['msg_all']) ? $allsome[0] : $allsome[1];
            if ($option == 'messegers') {
                Message::Info(Text::get('dashboard-messegers-mail-sendto', $enviandoa));
            } else {
                Message::Info(Text::get('dashboard-investors-mail-sendto', $enviandoa));
            }

            // Obtenemos la plantilla para asunto y contenido
            $template = Template::get(2);

            // Sustituimos los datos
            if (!empty($_POST['subject'])) {
                $subject = $_POST['subject'];
            } else {
                $subject = str_replace('%PROJECTNAME%', $skillmatching->name, $template->title);
            }

            $remite = $skillmatching->name . ' ' . Text::get('regular-from') . ' ';
            $remite .= (NODE_ID != GOTEO_NODE) ? NODE_NAME : GOTEO_MAIL_NAME;

            // para usar el proceso Sender:


            // - $who debe ser compatible con el formato $receivers
            // (falta nombre e email), sacarlo con getMini
            $receivers = array();
            foreach ($who as $userId) {
                $user = Model\User::getMini($userId);
                $user->user = $user->id;
                $receivers[] = $user;
            }

            //mailing use aws ses
            try {
                $sesClient = SesClient::factory(array(
                    'credentials' => [
                        'key'     => AWS_SES_ACCESS,
                        'secret'  => AWS_SES_SECERET,
                    ],
                    'region'  => 'us-west-2'
                ));
            } catch (SesException $exc) {
                die($exc->getMessage());
            }
            foreach($receivers as $value){

                $search = array('%MESSAGE%', '%PROJECTNAME%', '%PROJECTURL%', '%OWNERURL%', '%OWNERNAME%', '%USERNAME%');
                $replace = array($msg_content, $skillmatching->name, SITE_URL . "/skillmatching/" . $skillmatching->id,
                    SITE_URL . "/user/profile/" . $skillmatching->owner, $skillmatching->owner, $value->name);
                $content = \str_replace($search, $replace, $template->text);

                try {
                    $result = $sesClient->sendEmail(array(
                        'Source' => AWS_SES_SOURCE,
                        'Destination' => array(
                            'ToAddresses' => array($value->email)
                        ),
                        'Message' => array(
                            'Subject' => array(
                                'Data' => $subject,
                                'Charset' => AWS_SES_CHARSET,
                            ),
                            'Body' => array(
                                'Text' => array(
                                    'Data' => $msg_content,
                                    'Charset' => AWS_SES_CHARSET,
                                ),
                                'Html' => array(
                                    'Data' => $content,
                                    'Charset' => AWS_SES_CHARSET,
                                ),
                            ),
                        ),
                    ));
                    Message::Info(Text::get('dashboard-investors-mail-sended', $value->name));
                } catch (SesException $exc) {
                    Message::Error(Text::get('dashboard-investors-mail-fail', $value->name));
                }   
            }
            return true;
            /* end */

            // - en la plantilla hay que cambiar %NAME% por %USERNAME% para que sender reemplace

            // - 

            // - se crea un registro de tabla mail
            $sql = "INSERT INTO mail (id, email, html, template) VALUES ('', :email, :html, :template)";
            $values = array (
                ':email' => 'any',
                ':html' => $content,
                ':template' => $template->id,
                //':node' => \GOTEO_NODE
            );
            $query = \Goteo\Core\Model::query($sql, $values);
            $mailId = \Goteo\Core\Model::insertId();


            // - se usa el metodo initializeSending para grabar el envío (parametro para autoactivar)
            // , también metemos el reply y repplyName (remitente) en la instancia de envío
            if (\Goteo\Library\Sender::initiateSending($mailId, $subject, $receivers, 1, $skillmatching->user->email, $remite)) {
                Message::Info(Text::get('dashboard-investors-mail-sended', 'la cola de envíos')); // cambiar este texto
            } else {
                Message::Error(Text::get('dashboard-investors-mail-fail', 'la cola de envíos')); // cambiar este texto
            }

            
            return true;
        }
        
        
        /**
         * procesar algo respecto al contrato....
         * 
         * @param object $skillmatching Instancia de proyecto de trabajo
         * @param array $errors (por referncia)
         * @return boolean
         */
        public static function process_contract ($skillmatching, &$errors = array()) {

        }
        
        
        /**
         * Graba las colaboraciones con lo recibido por POST
         * 
         * @param object $skillmatching Instancia de proyecto de trabajo
         * @param array $errors (por referncia)
         * @return object $skillmatching Instancia de proyecto modificada
         */
        public static function process_supports ($skillmatching, &$errors = array()) {
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
                        $msg->save();
                    } else {
                        // grabar nuevo mensaje
                        $msg = new Model\Message(array(
                                    'user' => $skillmatching->owner,
                                    'skillmatching' => $skillmatching->id,
                                    'date' => date('Y-m-d'),
                                    'message' => "{$support->support}: {$support->description}",
                                    'blocked' => true
                                ));
                        if ($msg->save()) {
                            // asignado a la colaboracion como thread inicial
                            $support->thread = $msg->id;

                            // Evento Feed
                            $log = new Feed();
                            $log->setTarget($skillmatching->id);
                            $log->populate('usuario pone una nueva colaboracion en su proyecto (dashboard)', '/admin/skillmatchings', \vsprintf('%s ha publicado una nueva %s en el proyecto %s, con el título "%s"', array(
                                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                        Feed::item('message', 'Colaboración'),
                                        Feed::item('skillmatching', $skillmatching->name, $skillmatching->id),
                                        Feed::item('update', $support->support, $skillmatching->id . '/messages#message' . $msg->id)
                                    )));
                            $log->doAdmin('user');

                            // evento público, si el proyecto es público
                            if ($skillmatching->status > 2) {
                                $log->populate($_SESSION['user']->name, '/user/profile/' . $_SESSION['user']->id, Text::html('feed-new_support', Feed::item('skillmatching', $skillmatching->name, $skillmatching->id), Feed::item('update', $support->support, $skillmatching->id . '/messages#message' . $msg->id)
                                        ), $_SESSION['user']->avatar->id);
                                $log->doPublic('community');
                            }
                            unset($log);
                        }
                    }
                }
            }

            // añadir nueva colaboracion (no hacemos lo del mensaje porque esta sin texto)
            if (!empty($_POST['support-add'])) {

                $new_support = new Model\Skillmatching\Support(array(
                            'skillmatching' => $skillmatching->id,
                            'support' => '',
                            'type' => 'task',
                            'description' => ''
                        ));

                if ($new_support->save($errors)) {

                    $skillmatching->supports[] = $new_support;
                    $_POST['support-' . $new_support->id . '-edit'] = true;
                } else {
                    $skillmatching->supports[] = new Model\Skillmatching\Support(array(
                                'skillmatching' => $skillmatching->id,
                                'support' => '',
                                'type' => 'task',
                                'description' => ''
                            ));
                }
            }

            // guardamos los datos que hemos tratado y los errores de los datos
            $skillmatching->save($errors);
            
            return $skillmatching;
        }
        
        
        /**
         * Graba un registro de novedad con lo recibido por POST
         * 
         * @param array  $action (add o edit) y $id del post
         * @param object $skillmatching Instancia de proyecto de trabajo
         * @param array $errors (por referncia)
         * @return array $action por si se queda editando o sale a la lista y $id por si es un add y se queda editando
         */
        public static function process_updates ($action, $skillmatching, &$errors = array()) {
            

            $editing = false;

            if (!empty($_POST['id'])) {
                $post = Model\Blog\Post::get($_POST['id']);
            } else {
                $post = new Model\Blog\Post();
            }
            // campos que actualizamos
            $fields = array(
                'id',
                'blog',
                'title',
                'text',
                'image',
                'media',
                'legend',
                'date',
                'publish',
                'allow'
            );

            foreach ($fields as $field) {
                $post->$field = $_POST[$field];
            }

            // tratar la imagen y ponerla en la propiedad image
            if (!empty($_FILES['image_upload']['name'])) {
                $post->image = $_FILES['image_upload'];
                $editing = true;
            }

            // tratar las imagenes que quitan
            foreach ($post->gallery as $key => $image) {
                if (!empty($_POST["gallery-{$image->id}-remove"])) {
                    $image->remove('post');
                    unset($post->gallery[$key]);
                    if ($post->image == $image->id) {
                        $post->image = '';
                    }
                    $editing = true;
                }
            }

            if (!empty($post->media)) {
                $post->media = new Model\Skillmatching\Media($post->media);
            }

            // el blog de proyecto no tiene tags?Â¿?
            // $post->tags = $_POST['tags'];
            /// este es el único save que se lanza desde un metodo process_
            if ($post->save($errors)) {
                $id = $post->id;
                if ($action == 'edit') {
                    Message::Info(Text::get('dashboard-project-updates-saved'));
                } else {
                    Message::Info(Text::get('dashboard-project-updates-inserted'));
                }
                $action = $editing ? 'edit' : 'list';

                // si ha marcado publish, grabamos evento de nueva novedad en proyecto
                if ((bool) $post->publish) {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($skillmatching->id);
                    $log->populate('usuario publica una novedad en su proyecto (dashboard)', '/skillmatching/' . $skillmatching->id . '/updates/' . $post->id, 
                            \vsprintf('%s ha publicado un nuevo post en %s sobre el proyecto %s, con el título "%s"', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('blog', Text::get('skillmatching-menu-updates')),
                                Feed::item('skillmatching', $skillmatching->name, $skillmatching->id),
                                Feed::item('update', $post->title, $skillmatching->id . '/updates/' . $post->id)
                            )));
                    $log->unique = true;
                    $log->doAdmin('user');

                    // evento público
                    $log->populate($post->title, '/skillmatching/' . $skillmatching->id . '/updates/' . $post->id, Text::html('feed-new_update', Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id), Feed::item('blog', Text::get('skillmatching-menu-updates')), Feed::item('skillmatching', $skillmatching->name, $skillmatching->id)
                            ), $post->gallery[0]->id);
                    $log->doPublic('skillmatchings');

                    // si no ha encontrado otro, lanzamos la notificación a cofinanciadores
                    if (!$log->unique_issue) {
                        \Goteo\Controller\Cron\Send::toInvestors('update', $skillmatching, $post);
                    }

                    unset($log);
                }
            } else {
                $errors[] = Text::get('dashboard-project-updates-fail');
            }

            return array($action, $id);
            
        }
        
        /**
         * Método de datos para la vista del gráfico goteo-analytics
         * @param object $skillmatching Instancia del proyecto a visualizar
         * @return mixed 
         */
        public static function graph ($id) {

            // aportes
            $invests = array();
            $sql = "SELECT amount, user, invested FROM invest WHERE project = ? AND status IN ('0', '1', '3', '4')"; // solo aportes que aparecen públicamente
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                $invests[] = $row;
            }

            // fechas
            $dates = array();
            $sql = 'SELECT published, closed, success, passed FROM skillmatching WHERE id = ?';
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                $dates = $row;
            }

            // importes objetivo
            $optimum = $minimum = 0;
            $sql = 'SELECT sum(amount) as amount, required FROM cost WHERE project = ? GROUP BY required';
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                if ($row['required'] == 1){
                    $minimum = $row['amount'];
                } else {
                    $optimum = $row['amount'];
                }
            }

            $data = array('invests' => $invests, 
                        'dates' => $dates,
                        'minimum' => $minimum,
                        'optimum' => $optimum
                    );
    
            return json_encode($data);
        }

    }

}
