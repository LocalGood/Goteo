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
		Goteo\Library\Tpv,
		Goteo\Library\Paypal,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Library\Text,
        Goteo\Model;

    class Accounts {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors = array();

           // reubicando aporte,
           if ($action == 'move') {

                // el aporte original
                $original = Model\Invest::get($id);
                $userData = Model\User::getMini($original->user);
                $projectData = Model\Project::getMini($original->project);

                //el original tiene que ser de tpv o cash y estar como 'cargo ejecutado'
                if ($original->method == 'paypal' || $original->status != 1) {
                    Message::Error(Text::get('admin-error-invest-no_reposition'));
                    throw new Redirection('/admin/accounts');
                }


                // generar aporte manual y caducar el original
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['move']) ) {

                    // si falta proyecto, error

                    $projectNew = $_POST['project'];

                    // @TODO a saber si le toca dinero de alguna convocatoria
                    $campaign = null;

                    $invest = new Model\Invest(
                        array(
                            'amount'    => $original->amount,
                            'user'      => $original->user,
                            'project'   => $projectNew,
                            'account'   => $userData->email,
                            'method'    => 'cash',
                            'status'    => '1',
                            'invested'  => date('Y-m-d'),
                            'charged'   => $original->charged,
                            'anonymous' => $original->anonymous,
                            'resign'    => $original->resign,
                            'admin'     => $_SESSION['user']->id,
                            'campaign'  => $campaign
                        )
                    );
                    //@TODO si el proyecto seleccionado

                    if ($invest->save($errors)) {

                        //recompensas que le tocan (si no era resign)
                        if (!$original->resign) {
                            // sacar recompensas
                            $rewards = Model\Project\Reward::getAll($projectNew, 'individual');

                            foreach ($rewards as $rewId => $rewData) {
                                $invest->setReward($rewId); //asignar
                            }
                        }

                        // cambio estado del aporte original a 'Reubicado' (no aparece en cofinanciadores)
                        // si tuviera que aparecer lo marcaríamos como caducado
                        /*if ($original->setStatus('5')) {
                            // Evento Feed
                            $log = new Feed();
                            $log->setTarget($projectData->id);
                            $log->populate('Aporte reubicado', '/admin/accounts',
                                \vsprintf("%s ha aportado %s al proyecto %s en nombre de %s", array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('money', $_POST['amount'].' &yen;'),
                                    Feed::item('project', $projectData->name, $projectData->id),
                                    Feed::item('user', $userData->name, $userData->id)
                            )));
                            $log->doAdmin('money');
                            unset($log);

                            Message::Info(Text::get('admin-info-invest-reposition'));
                            throw new Redirection('/admin/accounts');
                        } else {
                            $errors[] = Text::_('A fallado al cambiar el estado del aporte original') . ' ('.$original->id.')';
                        }*/
                    } else{
                        $errors[] = Text::_('Ha fallado algo al reubicar el aporte');
                    }

                }

                $viewData = array(
                    'folder' => 'accounts',
                    'file' => 'move',
                    'original' => $original,
                    'user'     => $userData,
                    'project'  => $projectData
                );

                return new View(
                    'view/admin/index.html.php',
                    $viewData
                );

                // fin de la historia dereubicar
           }

           // cambiando estado del aporte aporte,
           if ($action == 'update') {

                // el aporte original
                $invest = Model\Invest::get($id);
                if (!$invest instanceof Model\Invest) {
                    Message::Error(Text::get('admin-error-invest-no_record').$id);
                    throw new Redirection('/admin/accounts');
                }

                $status = Model\Invest::status();

                $new = isset($_POST['status']) ? $_POST['status'] : null;

                if ($invest->issue && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update']) && $_POST['resolve'] == 1) {
                    Model\Invest::unsetIssue($id);
                    Model\Invest::setDetail($id, 'issue-solved', 'La incidencia se ha dado por resuelta por el usuario ' . $_SESSION['user']->name);
                    Message::Info(Text::get('admin-info-invest-incidence'));
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update']) && isset($new) && isset($status[$new])) {
                    
                    if ($new != $invest->status) {
                        if (Model\Invest::query("UPDATE invest SET status=:status WHERE id=:id", array(':id'=>$id, ':status'=>$new))) {
                            Model\Invest::setDetail($id, 'status-change'.rand(0, 9999), 'El admin ' . $_SESSION['user']->name . ' ha cambiado el estado del apote a '.$status[$new]);
                            Message::Info(Text::get('admin-info-invest-status-updating'));
                        } else {
                            Message::Error(Text::get('admin-error-invest-status-no_updating'));
                        }
                    } else {
                        Message::Error(Text::get('admin-error-invest-no_change'));
                    }
                    throw new Redirection('/admin/accounts/details/'.$id);
                }

                return new View('view/admin/index.html.php', array(
                    'folder' => 'accounts',
                    'file' => 'update',
                    'invest' => $invest,
                    'status' => $status
                ));

                // fin de la historia actualizar estado
           }

           // resolviendo incidencias
           if ($action == 'solve') {

                // el aporte original
                $invest = Model\Invest::get($id);
                if (!$invest instanceof Model\Invest) {
                    Message::Error(Text::get('admin-error-invest-no_record').$id);
                    throw new Redirection('/admin/accounts');
                }
                $projectData = Model\Project::getMini($invest->project);

                $errors = array();

                // primero cancelar
                switch ($invest->method) {
                    case 'paypal':
                        $err = array();
                        if (Paypal::cancelPreapproval($invest, $err)) {
                            $errors[] = Text::_('Preaproval paypal cancelado.');
                            $log_text = Text::_("El admin %s ha cancelado aporte y preapproval de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s");
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = Text::_('Fallo al cancelar el preapproval en paypal: ') . $txt_errors;
                            $log_text = Text::_("El admin %s ha fallado al cancelar el aporte de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: ") . $txt_errors;
                            if ($invest->cancel()) {
                                $errors[] = Text::_('Aporte cancelado');
                            } else{
                                $errors[] = Text::_('Fallo al cancelar el aporte');
                            }
                        }
                        break;
                    case 'tpv':
                        $err = array();
                        if (Tpv::cancelPreapproval($invest, $err)) {
                            $txt_errors = implode('; ', $err);
                            $errors[] = Text::_('Aporte cancelado correctamente. ') . $txt_errors;
                            $log_text = Text::_("El admin %s ha anulado el cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s");
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = Text::_('Fallo en la operación. ') . $txt_errors;
                            $log_text = Text::_("El admin %s ha fallado al solicitar la cancelación del cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores:") . $txt_errors;
                        }
                        break;
                    case 'cash':
                        if ($invest->cancel()) {
                            $log_text = Text::_("El admin %s ha cancelado aporte manual de %s de %s (id: %s) al proyecto %s del dia %s");
                            $errors[] = Text::_('Aporte cancelado');
                        } else{
                            $log_text = Text::_("El admin %s ha fallado al cancelar el aporte manual de %s de %s (id: %s) al proyecto %s del dia %s. ");
                            $errors[] = Text::_('Fallo al cancelar el aporte');
                        }
                        break;
                }

                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Cargo cancelado manualmente (admin)', '/admin/accounts',
                    \vsprintf($log_text, array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('user', $userData->name, $userData->id),
                        Feed::item('money', $invest->amount.' &yen;'),
                        Feed::item('system', $invest->id),
                        Feed::item('project', $projectData->name, $projectData->id),
                        Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                )));
                $log->doAdmin();
                unset($log);

                // luego resolver
                if ($invest->solve($errors)) {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($projectData->id);
                    $log->populate('Incidencia resuelta (admin)', '/admin/accounts',
                        \vsprintf("El admin %s ha dado por resuelta la incidencia con el botón \"Nos han hecho la transferencia\" para el aporte %s", array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('system', $id, 'accounts/details/'.$id)
                    )));
                    $log->doAdmin('admin');
                    unset($log);

                    Message::Info(Text::get('admin-info-invest-contribution'));
                    throw new Redirection('/admin/accounts');
                } else {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($projectData->id);
                    $log->populate('Fallo al resolver incidencia (admin)', '/admin/accounts',
                        \vsprintf("Al admin %s le ha fallado el botón \"Nos han hecho la transferencia\" para el aporte %s", array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('system', $id, 'accounts/details/'.$id)
                    )));
                    $log->doAdmin('admin');
                    unset($log);

                    Message::Error(Text::get('admin-error-invest-failure') . ': ' . implode (',', $errors));
                    throw new Redirection('/admin/accounts/details/'.$id);
                }
           }

            // aportes manuales, cargamos la lista completa de usuarios, proyectos y campañas
           if ($action == 'add') {

                // listado de proyectos en campaña
                $projects = Model\Project::active(false, true);
                // usuarios
                $users = Model\User::getAllMini();
                // campañas
//@CALLSYS
                $calls = array();
                   
                
                // generar aporte manual
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add']) ) {

                    $userData = Model\User::getMini($_POST['user']);
                    $projectData = Model\Project::getMini($_POST['project']);

                    $invest = new Model\Invest(
                        array(
                            'amount'    => $_POST['amount'],
                            'user'      => $userData->id,
                            'project'   => $projectData->id,
                            'account'   => $userData->email,
                            'method'    => 'cash',
                            'status'    => '1',
                            'invested'  => date('Y-m-d'),
                            'charged'   => date('Y-m-d'),
                            'anonymous' => $_POST['anonymous'],
                            'resign'    => 1,
                            'admin'     => $_SESSION['user']->id
                        )
                    );
//@CALLSYS

                    if ($invest->save($errors)) {
                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($projectData->id);
                        $log->populate('Aporte manual (admin)', '/admin/accounts',
                            \vsprintf("%s ha aportado %s al proyecto %s en nombre de %s", array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('money', $_POST['amount'].' &yen;'),
                                Feed::item('project', $projectData->name, $projectData->id),
                                Feed::item('user', $userData->name, $userData->id)
                        )));
                        $log->doAdmin('money');
                        unset($log);

                        Model\Invest::setDetail($invest->id, 'admin-created', 'Este aporte ha sido creado manualmente por el admin ' . $_SESSION['user']->name);
                        Message::Info(Text::get('admin-info-invest-save-reward'));
                        throw new Redirection('/admin/rewards/edit/'.$invest->id);
                    } else{
                        $errors[] = Text::_('Ha fallado algo al crear el aporte manual');
                    }

                }

                 $viewData = array(
                        'folder' => 'accounts',
                        'file' => 'add',
                        'autocomplete'  => true,
                        'users'         => $users,
                        'projects'      => $projects,
                        'calls'         => $calls
                    );

                return new View(
                    'view/admin/index.html.php',
                    $viewData
                );

                // fin de la historia

           }

            // Informe de la financiación de un proyecto
            if ($action == 'report') {
                // estados de aporte
                $project = Model\Project::get($id);
                if (!$project instanceof Model\Project) {
                    Message::Error(Text::get('admin-error-invest-project_no_valid'));
                    throw new Redirection('/admin/accounts');
                }
                $invests = Model\Invest::getAll($id);
                $project->investors = Model\Invest::investors($id, false, true);
                $users = $project->agregateInvestors();
                $investStatus = Model\Invest::status();

                // Datos para el informe de transacciones correctas
                $Data = Model\Invest::getReportData($project->id, $project->status, $project->round, $project->passed);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'accounts',
                        'file' => 'report',
                        'invests' => $invests,
                        'project' => $project,
                        'status' => $status,
                        'users' => $users,
                        'investStatus' => $investStatus,
                        'Data' => $Data
                    )
                );
            }

            // cancelar aporte antes de ejecución, solo aportes no cargados
            if ($action == 'cancel') {
                $invest = Model\Invest::get($id);
                if (!$invest instanceof Model\Invest) {
                    Message::Error(Text::get('admin-error-invest-no_object').$id);
                    throw new Redirection('/admin/accounts');
                }
                $project = Model\Project::get($invest->project);
                $userData = Model\User::get($invest->user);

                if ($project->status > 3 && $project->status < 6) {
                    $errors[] = Text::_('No debería poderse cancelar un aporte cuando el proyecto ya está financiado. Si es imprescindible, hacerlo desde el panel de paypal o tpv');
                    break;
                }

                switch ($invest->method) {
                    case 'paypal':
                        $err = array();
                        if (Paypal::cancelPreapproval($invest, $err)) {
                            $errors[] = Text::_('Preaproval paypal cancelado.');
                            $log_text = Text::_("El admin %s ha cancelado aporte y preapproval de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s");
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = Text::_('Fallo al cancelar el preapproval en paypal: ') . $txt_errors;
                            $log_text = Text::_("El admin %s ha fallado al cancelar el aporte de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores:") . $txt_errors;
                            if ($invest->cancel()) {
                                $errors[] = Text::_('Aporte cancelado');
                            } else{
                                $errors[] = Text::_('Fallo al cancelar el aporte');
                            }
                        }
                        break;
                    case 'tpv':
                        $err = array();
                        if (Tpv::cancelPreapproval($invest, $err)) {
                            $txt_errors = implode('; ', $err);
                            $errors[] = Text::_('Aporte cancelado correctamente. ') . $txt_errors;
                            $log_text = Text::_("El admin %s ha anulado el cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s");
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = Text::_('Fallo en la operación. ') . $txt_errors;
                            $log_text = Text::_("El admin %s ha fallado al solicitar la cancelación del cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores:") . $txt_errors;
                        }
                        break;
                    case 'cash':
                        if ($invest->cancel()) {
                            $log_text = Text::_("El admin %s ha cancelado aporte manual de %s de %s (id: %s) al proyecto %s del dia %s");
                            $errors[] = Text::_('Aporte cancelado');
                        } else{
                            $log_text = Text::_("El admin %s ha fallado al cancelar el aporte manual de %s de %s (id: %s) al proyecto %s del dia %s. ");
                            $errors[] = Text::_('Fallo al cancelar el aporte');
                        }
                        break;
                    case 'axes':
                        if ($invest->cancel()) {
                            $errors[] = Text::_('Aporte cancelado');
                        } else{
                            $errors[] = Text::_('Fallo al cancelar el aporte');
                        }
                        break;
                }

                // Evento Feed
                $log = new Feed();
                $log->setTarget($project->id);
                $log->populate('Cargo cancelado manualmente (admin)', '/admin/accounts',
                    \vsprintf($log_text, array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('user', $userData->name, $userData->id),
                        Feed::item('money', $invest->amount.' &yen;'),
                        Feed::item('system', $invest->id),
                        Feed::item('project', $project->name, $project->id),
                        Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                )));
                $log->doAdmin();
                Model\Invest::setDetail($invest->id, 'manually-canceled', $log->html);
                unset($log);
            }

            // ejecutar cargo ahora!!, solo aportes no ejecutados
            // si esta pendiente, ejecutar el cargo ahora (como si fuera final de ronda), deja pendiente el pago secundario
            if (($action == 'execute' && $invest->status == 0) || ($action == 'execute' && $invest->status == 5)) {
                $invest = Model\Invest::get($id);
                if (!$invest instanceof Model\Invest) {
                    Message::Error(Text::get('admin-error-invest-no_object').$id);
                    throw new Redirection('/admin/accounts');
                }
                $project = Model\Project::get($invest->project);
                $userData = Model\User::get($invest->user);

                switch ($invest->method) {
                    case 'axes':
                        if ($invest->setPayment(date("YmdHis"))){
                            $invest->setStatus(1);
                            Model\Invest::setDetail($invest->id, 'executed', 'Preapproval has been executed, manually');
                            if ($invest->issue) {
                                Model\Invest::unsetIssue($invest->id);
                                Model\Invest::setDetail($invest->id, 'issue-solved', 'The incidence has been resolved upon success by the manual process');
                            }
                        };
                        break;
                    case 'cash':
                        $invest->setStatus('1');
                        $errors[] = Text::_('Aporte al contado, nada que ejecutar.');
                        $log_text = Text::_("El admin %s ha dado por ejecutado el aporte manual a nombre de %s por la cantidad de %s (id: %s) al proyecto %s del dia %s");
                        $invest->status = 1;
                        break;
                }

                if (!empty($log_text)) {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($project->id);
                    $log->populate('Cargo ejecutado manualmente (admin)', '/admin/accounts',
                        \vsprintf($log_text, array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('user', $userData->name, $userData->id),
                            Feed::item('money', $invest->amount.' &yen;'),
                            Feed::item('system', $invest->id),
                            Feed::item('project', $project->name, $project->id),
                            Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                    )));
                    $log->doAdmin();
                    Model\Invest::setDetail($invest->id, 'manually-executed', $log->html);
                    unset($log);
                }
            }

            // visor de logs
            if ($action == 'viewer') {
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'accounts',
                        'file' => 'viewer'
                    )
                );
            }

            if ($action == 'resign' && !empty($id) && $_GET['token'] == md5('resign')) {
                if ($invest->setResign(true)) {
                    Model\Invest::setDetail($invest->id, 'manually-resigned', 'Se ha marcado como donativo independientemente de las recompensas');
                    throw new Redirection('/admin/accounts/detail/'.$invest->id);
                } else {
                    $errors[] = Text::_('Ha fallado al marcar donativo');
                }
            }

            if (!empty($errors)) {
                Message::Error(implode('<br />', $errors));
            }

            // tipos de aporte
            $methods = Model\Invest::methods();
            if ($methods){
                $methods['epsilonrepeat'] = $methods['epsilon'];
            }

            // estados del proyecto
            $status = Model\Project::status();
            $procStatus = Model\Project::procStatus();
            // estados de aporte
            $investStatus = Model\Invest::status();
            // listado de proyectos
            $projects = Model\Invest::projects();
            // usuarios cofinanciadores
            $users = Model\Invest::users(true);
            // campañas que tienen aportes
            $calls = Model\Invest::calls();

            // extras
            $types = array(
                'donative' => 'Solo los donativos',
                'anonymous' => 'Solo los anónimos',
                'manual' => 'Solo los manuales',
                'campaign' => 'Solo con riego',
            );
            
            // filtros de revisión de proyecto
            $review = array(
                'collect' => 'Recaudado',
                'paypal'  => 'Rev. PayPal',
                'tpv'     => 'Rev. TPV',
                'online'  => 'Pagos Online'
            );

            $issue = array(
                'show' => 'Solamente las incidencias',
                'hide' => 'Ocultar las incidencias'
            );


            /// detalles de una transaccion
            if ($action == 'details') {
                $invest = Model\Invest::get($id);
                $project = Model\Project::get($invest->project);
                $userData = Model\User::get($invest->user);
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'accounts',
                        'file' => 'details',
                        'invest'=>$invest,
                        'project'=>$project,
                        'user'=>$userData,
                        'details'=>$details,
                        'status'=>$status,
                        'investStatus'=>$investStatus
                    )
                );
            }

            if($action == 'pay_failed'){
                $invest = Model\Invest::get($id);
                $invest->setStatus(5);
            };

            // listado de aportes
            if ($filters['filtered'] == 'yes') {
                $list = Model\Invest::getList($filters, null, 999);
            } else {
                $list = array();
            }

             $viewData = array(
                    'folder' => 'accounts',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
                    'users'         => $users,
                    'projects'      => $projects,
                    'calls'         => $calls,
                    'review'        => $review,
                    'methods'       => $methods,
                    'types'         => $types,
                    'status'        => $status,
                    'procStatus'    => $procStatus,
                    'issue'         => $issue,
                    'investStatus'  => $investStatus
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }

    }

}
