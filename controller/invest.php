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


namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Text,
        Goteo\Library\Mail,
        Goteo\Library\Template,
        Goteo\Library\Message,
        //Goteo\Library\Paypal,
        //Goteo\Library\Tpv;
        Goteo\Core\View
        ;

    class Invest extends \Goteo\Core\Controller {

        // metodos habilitados
        public static function _methods() {
             return array(
                    'cash' => 'cash',
                    //'tpv' => 'tpv',
                    //'paypal' => 'paypal'
                    'axes' => 'axes'
                );
        }

        /*
         *  Este controlador no sirve ninguna página
         */
        public function index ($project = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::TEMPORARY);

            $message = '';

            try{
                $projectData = Model\Project::get($project);
                $projType = 'project';
            } catch(\Goteo\Core\Error $e){
                if ( $e->getCode() === 404){
                    $projectData = Model\Skillmatching::get($project);
                    if ($projectData){
                        $projType = 'skillmatching';
                    }
                } else {
                    throw $e;
                }
            }
            $methods = static::_methods();

            // si no está en campaña no pueden esta qui ni de coña
            if ($projectData->status != 3) {
                throw new Redirection("/$projType/".$project, Redirection::TEMPORARY);
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $errors = array();
                $los_datos = $_POST;
                $method = \strtolower($_POST['method']);

                if (!isset($methods[$method])) {
                    Message::Error(Text::get('invest-method-error'));
                    throw new Redirection(SEC_URL."/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                if (empty($_POST['amount']) && ($projType === 'project')) {
                    Message::Error(Text::get('invest-amount-error'));
                    throw new Redirection(SEC_URL."/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                // dirección de envio para las recompensas
                // o datoas fiscales del donativo
                $address = array(
                    'name'     => $_POST['fullname'],
                    'nif'      => $_POST['nif'],
                    'address'  => $_POST['address'],
                    'zipcode'  => $_POST['zipcode'],
                    'location' => $_POST['location'],
                    'country'  => $_POST['country']
                );

                if ($projectData->owner == $_SESSION['user']->id) {
                    Message::Error(Text::get('invest-owner-error'));
                    throw new Redirection(SEC_URL."/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                // añadir recompensas que ha elegido
                $chosen = $_POST['selected_reward'];
                if ($chosen == 0) {
                    // renuncia a las recompensas, bien por el/ella
                    $resign = true;
                    $reward = false;
                } else {
                    // ya no se aplica esto de recompensa es de tipo Reconocimiento para donativo
                    $resign = false;
                    $reward = true;
                }

                if ( $reward && (empty($_POST['fullname'])||empty($_POST['address']))){
                    Message::Error(Text::get('invest-required-error'));
                    throw new Redirection(SEC_URL."/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                // insertamos los datos personales del usuario si no tiene registro aun
                Model\User::setPersonal($_SESSION['user']->id, $address, false);

                if (!empty($projType) && ($projType === 'skillmatching')){
                    $_project = LG_SM_DB_PREFIX . $project;
                    $_amount = 0;
                } else {
                    $_project = $project;
                    $_amount = $_POST['amount'];
                }

                $invest = new Model\Invest(
                    array(
                        'amount' => $_amount,
                        'user' => $_SESSION['user']->id,
                        'project' => $_project,
                        'method' => $method,
                        'status' => '-1',               // aporte en proceso
                        'invested' => date('Y-m-d'),
                        'anonymous' => $_POST['anonymous'],
                        'resign' => $resign
                    )
                );
                if ($reward) {
                    $invest->rewards = array($chosen);
                }
                $invest->address = (object) $address;
                $invest->project_name = $projectData->name;

                if ($invest->save($errors)) {
                    $invest->urlOK  = SEC_URL."/invest/done/{$invest->id}";
                    $invest->urlNOK = SEC_URL."/invest/fail/{$invest->id}";
                    Model\Invest::setDetail($invest->id, 'init', 'Se ha creado el registro de aporte, el usuario ha clickado el boton de tpv o paypal. Proceso controller/invest');

                    switch($method) {
                        case 'axes':
                            $viewData = array('invest'=>$invest);
                            // todo: Mobile対応
                            $view = new View (
                                VIEW_PATH . "/invest/axes.html.php",
                                $viewData
                            );
                            return $view;
                            break;
                        case 'cash':
                            $invest->setStatus('1');
                            if ($projType == 'skillmatching'){
                                // スキルマッチング時は寄付->メール通知
                                // setDetail = confirm
                                $user = Model\User::get($invest->user);

                                // Notificación al autor
                                $template = Template::get(39);
                                // Sustituimos los datos
                                $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

                                $invested_reward = Model\Skillmatching\Reward::get($invest->rewards[0]);
//                                var_dump($invested_reward->reward);exit;
//                                var_dump($invest);exit;
                                //var_dump($user);exit;

                                // En el contenido:
                                $search  = array('%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%SITEURL%', '%REWARD%', '%USEREMAIL%','%MESSAGEURL%');
                                $replace = array($projectData->user->name, $user->name, $projectData->name, SITE_URL, $invested_reward->reward, $user->email, SITE_URL.'/user/profile/'.$user->id.'/message');
                                $content = \str_replace($search, $replace, $template->text);

                                $mailHandler = new Mail();

                                $mailHandler->to = $projectData->user->email;
                                $mailHandler->toName = $projectData->user->name;
                                $mailHandler->subject = $subject;
                                $mailHandler->content = $content;
                                $mailHandler->html = true;
                                $mailHandler->template = $template->id;
                                $mailHandler->send();

                                unset($mailHandler);

                                // log
                                Model\Invest::setDetail($invest->id, 'confirmed', 'Skillmatching entry');
                            }
                            throw new Redirection($invest->urlOK);
                            break;
                    }
                } else {
                    Message::Error(Text::get('invest-create-error'));
                }
            } else {
                Message::Error(Text::get('invest-data-error'));
            }

            throw new Redirection("/$projType/$project/invest/?confirm=fail");
        }

        public function paid ($id = null) {

            if($_GET['result'] != 'ok') die();

            $id = $_GET['sendid'];

            if (empty($id)) die();

            // el aporte
            $invest = Model\Invest::get($id);
            if ($invest->status != "-1") die();

            $projectData = Model\Project::getMedium($invest->project);


            // para evitar las duplicaciones de feed y email
            if (isset($_SESSION['invest_'.$invest->id.'_completed'])) {
                die();
            }

            $user = Model\User::get($invest->user);

            // Paypal solo disponible si activado
            if ($invest->method == 'axes') {

                // hay que cambiarle el status a 0
                $invest->setStatus('0');

                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Aporte Axes', '/admin/invests',
                    \vsprintf("%s ha aportado %s al proyecto %s mediante PayPal",
                        array(
                        Feed::item('user', $user->name, $user->id),
                        Feed::item('money', $invest->amount.' &yen;'),
                        Feed::item('project', $projectData->name, $projectData->id))
                    ));
                $log->doAdmin('money');
                // evento público
                $log_html = Text::html('feed-invest',
                                    Feed::item('money', $invest->amount.' &yen;'),
                                    Feed::item('project', $projectData->name, $projectData->id));
                if ($invest->anonymous) {
                    $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
                } else {
                    $log->populate($user->name, '/user/profile/'.$user->id, $log_html, $user->avatar->id);
                }
                $log->doPublic('community');
                unset($log);
            }
            // fin segun metodo

            // texto recompensa
            // @TODO quitar esta lacra de N recompensas porque ya es solo una recompensa siempre
            $rewards = $invest->rewards;
            array_walk($rewards, function (&$reward) { $reward = $reward->reward; });
            $txt_rewards = implode(', ', $rewards);

            // recaudado y porcentaje
            $amount = $projectData->invested;
            $percent = floor(($projectData->invested / $projectData->mincost) * 100);


            // email de agradecimiento al cofinanciador
            // primero monto el texto de recompensas
            //@TODO el concepto principal sería 'renuncia' (porque todos los aportes son donativos)
            if ($invest->resign) {
                // Plantilla de donativo segun la ronda
                if ($projectData->round == 2) {
                    $template = Template::get(36); // en segunda ronda
                } else {
                    $template = Template::get(28); // en primera ronda
                }
            } else {
                // plantilla de agradecimiento segun la ronda
                if ($projectData->round == 2) {
                    $template = Template::get(34); // en segunda ronda
                } else {
                    $template = Template::get(10); // en primera ronda
                }
            }

            
            // Dirección en el mail (y version para regalo)
            $txt_address = Text::get('invest-address-address-field') . ' ' . $invest->address->address;
            $txt_address .= '<br> ' . Text::get('invest-address-zipcode-field') . ' ' . $invest->address->zipcode;
//            $txt_address .= '<br> ' . Text::get('invest-address-location-field') . ' ' . $invest->address->location;
//            $txt_address .= '<br> ' . Text::get('invest-address-country-field') . ' ' . $invest->address->country;

            $txt_destaddr = $txt_address;
            $txt_address = Text::get('invest-mail_info-address') .'<br>'. $txt_address;

            // Agradecimiento al cofinanciador
            // Sustituimos los datos
            $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

            // En el contenido:
            $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%REWARDS%', '%ADDRESS%');
            $replace = array($user->name, $projectData->name, SITE_URL.'/project/'.$projectData->id, $confirm->amount, $txt_rewards, $txt_address);
            $content = \str_replace($search, $replace, $template->text);

            $mailHandler = new Mail();
            $mailHandler->reply = GOTEO_CONTACT_MAIL;
            $mailHandler->replyName = GOTEO_MAIL_NAME;
            $mailHandler->to = $user->email;
            $mailHandler->toName = $user->name;
            $mailHandler->subject = $subject;
            $mailHandler->content = $content;
            $mailHandler->html = true;
            $mailHandler->template = $template->id;
            if ($mailHandler->send($errors)) {
                Message::Info(Text::get('project-invest-thanks_mail-success'));
            } else {
                Message::Error(Text::get('project-invest-thanks_mail-fail'));
                Message::Error(implode('<br />', $errors));
            }

            unset($mailHandler);
            

            // Notificación al autor
            $template = Template::get(29);
            // Sustituimos los datos
            $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

            // En el contenido:
            $search  = array('%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%SITEURL%', '%AMOUNT%', '%MESSAGEURL%');
            $replace = array($projectData->user->name, $user->name, $projectData->name, SITE_URL, $invest->amount, SITE_URL.'/user/profile/'.$user->id.'/message');
            $content = \str_replace($search, $replace, $template->text);

            $mailHandler = new Mail();

            $mailHandler->to = $projectData->user->email;
            $mailHandler->toName = $projectData->user->name;
            $mailHandler->subject = $subject;
            $mailHandler->content = $content;
            $mailHandler->html = true;
            $mailHandler->template = $template->id;
            $mailHandler->send();

            unset($mailHandler);



            // marcar que ya se ha completado el proceso de aportar
            $_SESSION['invest_'.$invest->id.'_completed'] = true;
            // log
            Model\Invest::setDetail($invest->id, 'confirmed', 'El usuario regresó a /invest/confirmed');
        }
        public function done ($id=null) {
            if (empty($id)) {
                Message::Error(Text::get('invest-data-error'));
                throw new Redirection('/', Redirection::TEMPORARY);
            }
            $invest = Model\Invest::get($id);
            $project = $invest->project;
            $projType = $invest->project_type;

            throw new Redirection("/$projType/$project/invest/?confirm=ok", Redirection::TEMPORARY);
        }

        /*
         * @params project id del proyecto
         * @params is id del aporte
         */
        public function fail ($id = null) {

            if (empty($id))
                throw new Redirection('/discover', Redirection::TEMPORARY);

            // quitar el preapproval y cancelar el aporte
            $invest = Model\Invest::get($id);
            $invest->cancel();
            $project = $invest->project;
            $projType = $invest->project_type;

            // mandarlo a la pagina de aportar para que lo intente de nuevo
            throw new Redirection("/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
        }

        // resultado del cargo
        public function charge ($result = null, $id = null) {
            if (empty($id) || !\in_array($result, array('fail', 'success'))) {
                die;
            }
            // de cualquier manera no hacemos nada porque esto no lo ve ningun usuario
            die;
        }


    }

}
