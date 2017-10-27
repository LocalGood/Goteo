<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *  This file is part of Goteo.
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

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Feed,
        Goteo\Library\Message,
        Goteo\Library\SESMail,
		Goteo\Library\Template,
        Goteo\Library\Evaluation,
        Aws\Ses\SesClient,
        Aws\Ses\Exception\SesException,
        Goteo\Model;

    class Skillmatchings {

        public static function process ($action = 'list', $id = null, $filters = array('filtered' => 'yes')) {
            $log_text = null;
            $errors = array();
            $filters['filtered'] = 'yes';

            // multiples usos
            $nodes = array();

            if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id'])) {

                $projData = Model\Skillmatching::get($_POST['id']);
                if (empty($projData->id)) {
                    Message::Error(Text::get('admin-projects-error-project').$_POST['id'].Text::get('admin-projects-error-there'));
                    throw new Redirection('/admin/skillmatchings/images/'.$id);
                }

                if (isset($_POST['save-dates'])) {
                    $fields = array(
                        'created',
                        'updated',
                        'published',
                        'success',
                        'closed',
                        'passed',
                        'period_1r',
                        'period_2r'
                        );

                    $set = '';
                    $values = array(':id' => $projData->id);

                    foreach ($fields as $field) {
                        if ($set != '') $set .= ", ";
                        $set .= "`$field` = :$field ";
                        if (empty($_POST[$field]) || $_POST[$field] == '0000-00-00')
                            $_POST[$field] = null;

                        $values[":$field"] = $_POST[$field];
                    }

                    try {
                        $sql = "UPDATE skillmatching SET " . $set . " WHERE id = :id";
                        if (Model\Skillmatching::query($sql, $values)) {
                            $log_text = Text::_('El admin %s ha <span class="red">tocado las fechas</span> del proyecto ').$projData->name.' %s';
                        } else {
                            $log_text = Text::_('Al admin %s le ha <span class="red">fallado al tocar las fechas</span> del proyecto ').$projData->name.' %s';
                        }
                    } catch(\PDOException $e) {
                        Message::Error(Text::get('admin-projects-error-save-fail'). $e->getMessage());
                    }
                } elseif (isset($_POST['save-accounts'])) {

                    $accounts = Model\Skillmatching\Account::get($projData->id);
                    $accounts->bank = $_POST['bank'];
                    $accounts->bank_owner = $_POST['bank_owner'];
                    $accounts->paypal = $_POST['paypal'];
                    $accounts->paypal_owner = $_POST['paypal_owner'];
                    if ($accounts->save($errors)) {
                        Message::Info(Text::get('admin-projects-info-update-account').$projData->name);
                    } else {
                        Message::Error(implode('<br />', $errors));
                    }

                } elseif ($action == 'images') {
                    
                    $todook = true;
                    
                    if (!empty($_POST['move'])) {
                        $direction = $_POST['action'];
                        Model\Skillmatching\Image::$direction($id, $_POST['move'], $_POST['section']);
                    }
                    
                    foreach ($_POST as $key=>$value) {
                        $parts = explode('_', $key);
                        
                        if ($parts[1] == 'image' && in_array($parts[0], array('section', 'url'))) {
                            if (Model\Skillmatching\Image::update($id, $parts[2], $parts[0], $value)) {
                                // OK
                            } else {
                                $todook = false;
                                Message::Error(Text::get('admin-projects-info-data-save')." {$parts[0]} -> {$value}");
                            }
                        }
                    }
                    
                    if ($todook) {
                        Message::Info(Text::_('Se han actualizado los datos'));
                    }
                    
                    throw new Redirection('/admin/skillmatchings/images/'.$id);
                    
                } elseif ($action == 'rebase') {
                    
                    $todook = true;
                    
                    if ($_POST['proceed'] == 'rebase' && !empty($_POST['newid'])) {

                        $newid = $_POST['newid'];

                        // pimero miramos que no hay otro proyecto con esa id
                        $test = Model\Skillmatching::getMini($newid);
                        if ($test->id == $newid) {
                            Message::Error(Text::get('admin-projects-error-id-notempty'));
                            throw new Redirection('/admin/skillmatchings/rebase/'.$id);
                        }

                        if ($projData->status >= 3 && $_POST['force'] != 1) {
                            Message::Error(Text::_('El proyecto no está ni en Edición ni en Revisión, no se modifica nada.'));
                            throw new Redirection('/admin/skillmatchings/rebase/'.$id);
                        }

                        if ($projData->rebase($newid)) {
                            Message::Info(Text::get('admin-projects-info-selectproject').' -> <a href="'.SITE_URL.'/skillmatching/'.$newid.'" target="_blank">'.$projData->name.'</a>');
                            throw new Redirection('/admin/skillmatchings');
                        } else {
                            Message::Info(Text::_('Ha fallado algo en el rebase, verificar el proyecto').' -> <a href="'.SITE_URL.'/skillmatching/'.$projData->id.'" target="_blank">'.$projData->name.' ('.$id.')</a>');
                            throw new Redirection('/admin/skillmatchings/rebase/'.$id);
                        }

                        
                    }
                    
                } elseif ($action == 'evaluation') {

                    $todook = true;

                    if (!empty($_POST['id'])){
                        $evltn = new Evaluation();
                        $evltn->project_id = $_POST['id'];
                        $evltn->name = $_POST['name'];
                        if ($evltn->update($_POST['id'], $_POST['content'], $errors)) {
                            throw new Redirection('/admin/skillmatchings/evaluation/'.$id);
                        }

                    } else {
                        $todook = false;
                    }

                    if ($todook) {
                        Message::Info(Text::_('Se han actualizado los datos'));
                    }

                    throw new Redirection('/admin/skillmatchings/evaluation/'.$id);
                }
            }

            /*
             * switch action,
             * proceso que sea,
             * redirect
             *
             */
            if (isset($id)) {
                $skillmatching = Model\Skillmatching::get($id);
            }
            switch ($action) {
                case 'review':
                    // pasar un proyecto a revision
                    if ($skillmatching->ready($errors)) {
                        $redir = '/admin/reviews/add/'.$skillmatching->id;
                        $log_text = Text::_('El admin %s ha pasado el proyecto %s al estado <span class="red">Revision</span>');
                    } else {
                        $log_text = Text::_('Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Revision</span>');
                    }
                    break;
                case 'publish':
                    // poner un proyecto en campa�a
                    if ($skillmatching->publish($errors)) {
                        $log_text = Text::_('El admin %s ha pasado el proyecto %s al estado <span class="red">en Campa�a</span>');
                    } else {
                        $log_text = Text::_('Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">en Campa�a</span>');
                    }
                    break;
                case 'cancel':
                    // descartar un proyecto por malo
                    if ($skillmatching->cancel($errors)) {
                        $log_text = Text::_('El admin %s ha pasado el proyecto %s al estado <span class="red">Descartado</span>');
                    } else {
                        $log_text = Text::_('Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Descartado</span>');
                    }
                    break;
                case 'enable':
                    // si no esta en edicion, recuperarlo
                    if ($skillmatching->enable($errors)) {
                        $log_text = Text::_('El admin %s ha pasado el proyecto %s al estado <span class="red">Edicion</span>');
                    } else {
                        $log_text = Text::_('Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Edicion</span>');
                    }
                    break;
                case 'fulfill':
                    // marcar que el proyecto ha cumplido con los retornos colectivos
                    if ($skillmatching->satisfied($errors)) {
                        $log_text = Text::_('El admin %s ha pasado el proyecto %s al estado <span class="red">Retorno cumplido</span>');
                    } else {
                        $log_text = Text::_('Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Retorno cumplido</span>');
                    }
                    break;
                case 'unfulfill':
                    // dar un proyecto por financiado manualmente
                    if ($skillmatching->rollback($errors)) {
                        $log_text = Text::_('El admin %s ha pasado el proyecto %s al estado <span class="red">Financiado</span>');
                    } else {
                        $log_text = Text::_('Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Financiado</span>');
                    }
                    break;
            }

            if (isset($log_text)) {
                // Evento Feed
                $log = new Feed();
                $log->setTarget($skillmatching->id);
                $log->populate(Text::_('Cambio estado/fechas/cuentas/nodo de un proyecto desde el admin'), '/admin/skillmatchings',
                    \vsprintf($log_text, array(
                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                    Feed::item('skillmatching', $skillmatching->name, $skillmatching->id)
                )));
                $log->doAdmin('admin');

                Message::Info($log->html);
                if (!empty($errors)) {
                    Message::Error(implode('<br />', $errors));
                }

                if ($action == 'publish') {
                    // si es publicado, hay un evento publico
                    $log->populate($skillmatching->name, '/skillmatching/'.$skillmatching->id, Text::html('feed-new_project'), $skillmatching->gallery[0]->id);
                    $log->doPublic('skillmatchings');
                }

                unset($log);

                if (empty($redir)) {
                    throw new Redirection('/admin/skillmatchings/list');
                } else {
                    throw new Redirection($redir);
                }
            }

            if ($action == 'report') {
                // informe financiero
                // Datos para el informe de transacciones correctas
                $Data = Model\Invest::getReportData($skillmatching->id, $skillmatching->status, $skillmatching->round, $skillmatching->passed);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'skillmatchings',
                        'file' => 'report',
                        'skillmatching' => $skillmatching,
                        'Data' => $Data
                    )
                );
            }

            if ($action == 'dates') {
                // cambiar fechas
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'skillmatchings',
                        'file' => 'dates',
                        'skillmatching' => $skillmatching
                    )
                );
            }

            if ($action == 'accounts') {

                $accounts = Model\Skillmatching\Account::get($skillmatching->id);

                // cambiar fechas
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'skillmatchings',
                        'file' => 'accounts',
                        'skillmatching' => $skillmatching,
                        'accounts' => $accounts
                    )
                );
            }

            if ($action == 'images') {
                
                // imagenes
                $images = array();
                
                // secciones
                $sections = Model\Skillmatching\Image::sections();
                foreach ($sections as $sec=>$secName) {
                    $secImages = Model\Skillmatching\Image::get($skillmatching->id, $sec);
                    foreach ($secImages as $img) {
                        $images[$sec][] = $img;
                    }
                }

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'skillmatchings',
                        'file' => 'images',
                        'skillmatching' => $skillmatching,
                        'images' => $images,
                        'sections' => $sections
                    )
                );
            }

            if ($action == 'move') {
                // cambiar el nodo
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'skillmatchings',
                        'file' => 'move',
                        'skillmatching' => $skillmatching,
                        'nodes' => $nodes
                    )
                );
            }


            if ($action == 'rebase') {
                // cambiar la id
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'skillmatchings',
                        'file' => 'rebase',
                        'skillmatching' => $skillmatching
                    )
                );
            }


            // Rechazo express
            if ($action == 'reject') {
                if (empty($skillmatching)) {
                    Message::Error(Text::get('admin-projects-error-nooperate-project'));
                } else {
                    // Obtenemos la plantilla para asunto y contenido
                    $template = Template::get(40);
                    // Sustituimos los datos
                    $subject = str_replace('%PROJECTNAME%', $skillmatching->name, $template->title);
                    $search  = array('%USERNAME%', '%PROJECTNAME%');
                    $replace = array($skillmatching->user->name, $skillmatching->name);
                    $content = \str_replace($search, $replace, $template->text);
                    //mailing use aws ses
                    $sesClient = new SESMail();
                    $sesClient->template = $template->id;
                    try {
                        $sesClient->sendMail(array('to' => array($skillmatching->user->email)), $subject, $content, $content);
                        Message::Info('<strong>'.$skillmatching->user->name.'</strong>' . Text::get('admin-projects-info-sendmail-to') . '<strong>（' . $skillmatching->user->email.'）</strong>' . Text::get('admin-projects-info-sendmail'));
                    } catch (SesException $exc) {
                        Message::Error(Text::_('Ha fallado al enviar el mail a') . '<strong>'.$skillmatching->user->name.'</strong>' . Text::_('a la dirección') . '<strong>'.$skillmatching->user->email.'</strong>');
                        Message::Error($exc->getMessage());
                    }
                }

                throw new Redirection('/admin/skillmatchings/list');
            }

            // Skillmatching Evaluation
            if ($action == 'evaluation') {
//                Message::Error('Skillmatching Evaluation');
                $evaluation = Evaluation::get($skillmatching->id);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'skillmatchings',
                        'file' => 'evaluation',
                        'skillmatching' => $skillmatching,
                        'evaluation' => $evaluation
                    )
                );
            }

            if (!empty($filters['filtered'])) {
                $skillmatchings = Model\Skillmatching::getList($filters, $_SESSION['admin_node']);
            } else {
                $skillmatchings = array();
            }
            $status = Model\Skillmatching::status();
            $categories = Model\Skillmatching\Category::getAll();
            $skills = Model\Skill::getAll();
            //@CONTRACTSYS
            $calls = array();
            // la lista de nodos la hemos cargado arriba
            $orders = array(
                'name' => Text::get('admin-projects-list-order_name'),
                'updated' => Text::get('admin-projects-list-order_updated'),
                'created' => Text::get('admin-projects-list-order_created')
            );

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'skillmatchings',
                    'file' => 'list',
                    'skillmatchings' => $skillmatchings,
                    'filters' => $filters,
                    'status' => $status,
                    'categories' => $categories,
                    'skills' => $skills,
//                    'contracts' => $contracts,
                    'calls' => $calls,
                    'nodes' => $nodes,
                    'orders' => $orders
                )
            );
            
        }

    }

}
