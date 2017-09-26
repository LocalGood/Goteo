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
        Goteo\Core\View,

		PEAR,
		HTTP_Request2,
		DOMDocument
        ;


    //パスを通してインクルード
//    $path = dirname(__FILE__)."/../library";
//    set_include_path(get_include_path().PATH_SEPARATOR.$path);
    require_once "HTTP/Request2.php" ;

//	require_once "PEAR.php";
//	require_once "HTTP/Request2.php";


    class Invest extends \Goteo\Core\Controller {

        // metodos habilitados
        public static function _methods() {
             return array(
                    'cash' => 'cash',
                    //'tpv' => 'tpv',
                    //'paypal' => 'paypal'
                    'axes' => 'axes',
                    'epsilon' => 'epsilon',
                    'epsilonrepeat' => 'epsilonrepeat',
                    'conveni' => 'conveni'
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
            if ($projectData->status != 3) {		// miyamoto change
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
                        'disp_name' => $_POST['disp_name'],
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


                        case 'epsilon':
                            $viewData = array('invest'=>$invest);

                            // todo: Mobile対応
                            $view = new View (
                                VIEW_PATH . "/invest/epsilon.html.php",
                                $viewData
                            );
                            return $view;
                            break;

                        case 'epsilonrepeat':
                            $viewData = array('invest'=>$invest);

                            // todo: Mobile対応
                            $view = new View (
                                VIEW_PATH . "/invest/epsilonrepeat.html.php",
                                $viewData
                            );
                            return $view;
                            break;


                        case 'conveni':
                            $viewData = array('invest'=>$invest);
                            // todo: Mobile対応
                            $view = new View (
                                VIEW_PATH . "/invest/conveni.html.php",
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







		//
		//		イプシロン　決済ページが完了したときの処理
		//
		//
        public function epsilonpaid ($id = null) {

            #if($_GET['result'] != 'ok') die();

            $trans_code = $_GET['trans_code'];
            $user_id = $_GET['user_id'];
            $result = $_GET['result'];
            $order_number = $_GET['order_number'];		// order_number は、goteo の invest->id 


            $invest = Model\Invest::get($order_number);
            if ($invest->status != "-1") die();

            $project = $invest->project;
            $projType = $invest->project_type;


			if ($result != 1) {   // 1:成功 0:失敗
                Message::Error("return error from Epsilon" );
                throw new Redirection(SEC_URL."/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
			}


            $projectData = Model\Project::getMedium($invest->project);


            // para evitar las duplicaciones de feed y email
            if (isset($_SESSION['invest_'.$invest->id.'_completed'])) {
                die();
            }

            $user = Model\User::get($invest->user);


            if ($invest->method == 'epsilon' || $invest->method == 'epsilonrepeat') {

                // hay que cambiarle el status a 0
                $invest->setStatus('0');

				// trans_code の保管
				$invest->setTransaction( $trans_code );

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


            if ($invest->method == 'conveni') {

                // hay que cambiarle el status a 0
                $invest->setStatus('0');

				// trans_code の保管
				$invest->setTransaction( $trans_code );

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


			if ( !defined('DEBUGTEST')) {
              if ($mailHandler->send($errors)) {
                Message::Info(Text::get('project-invest-thanks_mail-success'));
              } else {
                Message::Error(Text::get('project-invest-thanks_mail-fail'));
                Message::Error(implode('<br />', $errors));
			  }
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

			if ( !defined('DEBUGTEST')) {
              $mailHandler->send();
			}

            unset($mailHandler);



            // marcar que ya se ha completado el proceso de aportar
            $_SESSION['invest_'.$invest->id.'_completed'] = true;
            // log
            Model\Invest::setDetail($invest->id, 'confirmed', 'El usuario regresó a /invest/confirmed');


            throw new Redirection("/$projType/$project/invest/?confirm=ok", Redirection::TEMPORARY);
        }





		//
		//		コンビニ入金時に、イプシロンからPOSTで通知が来る。
		//
		//
        public function convpaid ($id = null) {

            $trans_code = $_POST['trans_code'];
            $order_number = $_POST['order_number'];			// = $invest->id
            $user_id = $_POST['user_id'];
            $user_mail_add = $_POST['user_mail_add'];
            $paid = $_POST['paid'];							// 数字1：入金済み
            $item_name = mb_convert_encoding($_POST['item_name'], "UTF-8", "auto");
            $item_price = $_POST['item_price'];
            $conveni_name = mb_convert_encoding($_POST['conveni_name'], "UTF-8", "auto");
            $conveni_code = $_POST['conveni_code'];			// 11:セブンイレブン 21:ファミリーマート
            $conveni_date = $_POST['conveni_date'];			// 入金日時
            $memo1 = $_POST['memo1'];
            $memo2 = $_POST['memo2'];


			$returnmsg = "1";
			$errmsg = "0 999 PAID_ERROR";

			// 入金済みでないならエラー
            if($paid != 1) {
				$returnmsg = "0 999 PAID_ERROR";
			};


			// エラー時は返送して終了
			if ($returnmsg != "1") {
				print "Content-type:text/plain\n\n";
				print $errmsg;
				die();
			}


			// 
			// コンビニ決済の場合は、ＤＢの決済の有効化を行う。
			// 
            $invest = Model\Invest::get($order_number);

            if ($invest->status != "0") {
				print "Content-type:text/plain\n\n";
				print $errmsg;
				die();
			}


            $project = $invest->project;
            $projType = $invest->project_type;

			// trans_code のチェック
			if ( strcmp($invest->transaction, $trans_code) ) {
				print "Content-type:text/plain\n\n";
				print $errmsg;
				die();
			}
			

			$invest->setStatus('1');	// 合計を有効にする。


			// 正常終了のレスポンスコードを返信して終了

			print "Content-type:text/plain\n\n";
			print $returnmsg;
			die();
		}



		//
		//		イプシロンの決済ページへ遷移する処理
		//
		//
        public function epsilongo($id=null, $processcode) {

            if (empty($id)) {
                Message::Error(Text::get('invest-data-error'));
                throw new Redirection('/', Redirection::TEMPORARY);
            }
            if (empty($processcode)) {
                Message::Error(Text::get('invest-data-error'));
                throw new Redirection('/', Redirection::TEMPORARY);
            }
            $invest = Model\Invest::get($id);
            $project = $invest->project;
            $projType = $invest->project_type;


            $projectData = Model\Project::getMedium($invest->project);


			// イプシロンに決済処理を飛ばす　事前処理
			//   1. 決済cgi にデータを送る
			//	 2. xml でステータスと、決済URL が返るので取得
			//	 3. 決済URLに、header() でロケーション。


			// httpリクエスト用のオプションを指定
			$http_option = array(
			  "timeout" => "20", // タイムアウトの秒数指定
			  //    "allowRedirects" => true, // リダイレクトの許可設定(true/false)
			  //    "maxRedirects" => 3, // リダイレクトの最大回数
			);


			$request = new HTTP_Request2(EPSILON_ORDER_URL, HTTP_Request2::METHOD_POST, $http_option);
			$request->setConfig(array(
				'ssl_verify_peer' => false,
			));


			// 契約番号(8桁)
			$contract_code = EPSILON_CONTRACT_CODE;

			// 注文番号
			$order_number = $invest->id;

			// 決済区分
			$st_code = array( 
				'normal'  => '10100-0000-00000-00010-00000-00000-00000',
  				'card'    => '10000-0000-00000-00000-00000-00000-00000',
   				'conveni' => '00100-0000-00000-00000-00000-00000-00000',
   				'atobarai'=> '00000-0000-00000-00010-00000-00000-00000',
			);

			$memo1 = "";
			$memo2 = "";

			// 商品コード
			$item_code = $invest->project;

			// 商品名と価格
			$item_name = $projectData->name;
			$item_price = $invest->amount;

			// 課金区分
			$mission_code  = 1;

			// 処理区分
			$process_code = $processcode;


			// ユーザー固有情報

			$user = Model\User::get($invest->user);
			#var_dump($user);

			$user_tel = 'user_tel';          // ユーザ電話番号
			$user_name_kana = 'user_name_kana'; // ユーザー名(カナ)

			$user_id = $user->id;            // ユーザーID

			$user_name = $user->name;        // ユーザー氏名

			$user_mail_add = $user->email;	// メールアドレス

			$st = "card";	// クレジットカード設定
			$stc = $st_code[$st];



			$request->addPostParameter('version', '2' );
			$request->addPostParameter('contract_code', $contract_code);
			$request->addPostParameter('user_id', $user_id);
			$request->addPostParameter('user_name', $user_name);
			$request->addPostParameter('user_mail_add', $user_mail_add);
			$request->addPostParameter('item_code', $item_code);
			$request->addPostParameter('item_name', mb_convert_encoding(mb_strcut(mb_convert_encoding($item_name,"SJIS","UTF-8"),0,64,"SJIS"),"UTF-8","SJIS"), "UTF-8", "auto");
			$request->addPostParameter('order_number', $order_number);
			$request->addPostParameter('st_code', $st_code[$st]);
			$request->addPostParameter('mission_code', $mission_code);
			$request->addPostParameter('item_price', $item_price);
			$request->addPostParameter('process_code', $process_code);
			$request->addPostParameter('memo1', $memo1);
			$request->addPostParameter('memo2', $memo2);
			$request->addPostParameter('xml', '1');
			$request->addPostParameter('character_code', 'UTF8' );



			// HTTPリクエスト実行
			$response = $request->send();

			// 応答内容(XML)の解析
//			if (!PEAR::isError($response)) {
                if (true) {
				$res_code = $response->getStatus();
				$res_content = $response->getBody();

				$resultno = $redirect = $err_code = $err_detail = "";

				$xmlobj = simplexml_load_string($res_content);
				foreach ($xmlobj->result as $d) {
					$t = (string) $d->attributes()->result;
					if ($t != "") {
						$resultno = $t;
					}
					$t = (string) $d->attributes()->redirect;
					if ($t != "") {
						$redirect = urldecode($t);
					}
					$t = (string) $d->attributes()->err_code;
					if ($t != "") {
						$err_code = $t;
					}
					$t = (string) $d->attributes()->err_detail;
					if ($t != "") {
						$err_detail = urldecode($t);
					}
				}


				// アクセスエラーのチェック
				switch ( $resultno ) {
					case 1:	// 正常終了
						// 何もしない

						break;
					case 0:	// 失敗
						// エラー出力
						Message::Error(Text::get('Epsilon Return Error') . "\n" . $err_code ."\n" .$err_detail);
            			throw new Redirection(SEC_URL."/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
						break;
				}


			}

			header("Location: " . $redirect);
			exit;

        }



		//
		//		コンビニ決済ページへ遷移する処理
		//
		//
        public function convenigo($id=null) {

            if (empty($id)) {
                Message::Error(Text::get('invest-data-error'));
                throw new Redirection('/', Redirection::TEMPORARY);
            }
            $invest = Model\Invest::get($id);
            $project = $invest->project;
            $projType = $invest->project_type;

            $projectData = Model\Project::getMedium($invest->project);


			// イプシロンに決済処理を飛ばす　事前処理
			//   1. 決済cgi にデータを送る
			//	 2. xml でステータスと、決済URL が返るので取得
			//	 3. 決済URLに、header() でロケーション。


			// httpリクエスト用のオプションを指定
			$http_option = array(
			  "timeout" => "20", // タイムアウトの秒数指定
			  //    "allowRedirects" => true, // リダイレクトの許可設定(true/false)
			  //    "maxRedirects" => 3, // リダイレクトの最大回数
			);


			$request = new HTTP_Request2(EPSILON_ORDER_URL, HTTP_Request2::METHOD_POST, $http_option);
			$request->setConfig(array(
				'ssl_verify_peer' => false,
			));


			// 契約番号(8桁)
			$contract_code = EPSILON_CONTRACT_CODE;

			// 注文番号
			$order_number = $invest->id;

			// 決済区分
			$st_code = array( 
				'normal'  => '10100-0000-00000-00010-00000-00000-00000',
  				'card'    => '10000-0000-00000-00000-00000-00000-00000',
   				'conveni' => '00100-0000-00000-00000-00000-00000-00000',
   				'atobarai'=> '00000-0000-00000-00010-00000-00000-00000',
			);

			$memo1 = "";
			$memo2 = "";

			// 商品コード
			$item_code = $invest->project;

			// 商品名と価格
			$item_name = $projectData->name;
			$item_price = $invest->amount;

			// 課金区分
			$mission_code  = 1;

			// 処理区分
			$process_code = 1;


			// ユーザー固有情報

			$user = Model\User::get($invest->user);
			#var_dump($user);

			$user_tel = 'user_tel';          // ユーザ電話番号
			$user_name_kana = 'user_name_kana'; // ユーザー名(カナ)

			$user_id = $user->id;            // ユーザーID

			$user_name = $user->name;        // ユーザー氏名

			$user_mail_add = $user->email;	// メールアドレス

			$st = "conveni";	// コンビニ決済
			$stc = $st_code[$st];


			$request->addPostParameter('version', '2' );
			$request->addPostParameter('contract_code', $contract_code);
			$request->addPostParameter('user_id', $user_id);
			$request->addPostParameter('user_name', $user_name);
			$request->addPostParameter('user_mail_add', $user_mail_add);
			$request->addPostParameter('item_code', $item_code);
			$request->addPostParameter('item_name', mb_convert_encoding(mb_strcut(mb_convert_encoding($item_name,"SJIS","UTF-8"),0,64,"SJIS"),"UTF-8","SJIS"), "UTF-8", "auto");
			$request->addPostParameter('order_number', $order_number);
			$request->addPostParameter('st_code', $st_code[$st]);
			$request->addPostParameter('mission_code', $mission_code);
			$request->addPostParameter('item_price', $item_price);
			$request->addPostParameter('process_code', $process_code);
			$request->addPostParameter('memo1', $memo1);
			$request->addPostParameter('memo2', $memo2);
			$request->addPostParameter('xml', '1');
			$request->addPostParameter('character_code', 'UTF8' );



			// HTTPリクエスト実行
			$response = $request->send();

			// 応答内容(XML)の解析
			if (true) {
//                if (!PEAR::isError($response)) {
				$res_code = $response->getStatus();
				$res_content = $response->getBody();

				$resultno = $redirect = $err_code = $err_detail = "";

				$xmlobj = simplexml_load_string($res_content);
				foreach ($xmlobj->result as $d) {
					$t = (string) $d->attributes()->result;
					if ($t != "") {
						$resultno = $t;
					}
					$t = (string) $d->attributes()->redirect;
					if ($t != "") {
						$redirect = urldecode($t);
					}
					$t = (string) $d->attributes()->err_code;
					if ($t != "") {
						$err_code = $t;
					}
					$t = (string) $d->attributes()->err_detail;
					if ($t != "") {
						$err_detail = urldecode($t);
					}
				}


				// アクセスエラーのチェック
				switch ( $resultno ) {
					case 1:	// 正常終了
						// 何もしない

						break;
					case 0:	// 失敗
						// エラー出力
						Message::Error(Text::get('Epsilon Return Error'));
            			throw new Redirection(SEC_URL."/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
						break;
				}

			}

			header("Location: " . $redirect);
			exit;

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
			// イプシロンの場合、$id はカラで来る。
			// GET で(ユーザＩＤ，実行結果、注文番号等)が送られてくるので、それを処理する。

            $trans_code = $_GET['trans_code'];
            $user_id = $_GET['user_id'];
            $result = $_GET['result'];
            $order_number = $_GET['order_number'];		



            #if (empty($id))
            #    throw new Redirection('/discover', Redirection::TEMPORARY);

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
