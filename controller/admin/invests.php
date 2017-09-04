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
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Model,
		Goteo\Library\Text,

        PEAR,
        HTTP_Request2;

    require_once "PEAR.php";
    require_once "HTTP/Request2.php";


    class Invests {

        public static function process ($action = 'list', $id = null, $filters = array('filtered'=>'yes')) {
            $filters['filtered'] = 'yes';
            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;
            
            // métodos de pago
            $methods = Model\Invest::methods();
            // estados del proyecto
            $status = Model\Project::status();
            // estados de aporte
            $investStatus = Model\Invest::status();
            // listado de proyectos
            $projects = Model\Invest::projects(false, $node);
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

            if($action == 'csv'){
                if (!empty($_GET) && $_GET['round']){
                    $invest = Model\Invest::getPreapprovalByRound($id, $_GET['round']);
                } else {
                    $invest = Model\Invest::getPreapproval($id);
                }
                foreach ($invest as $value){
                    $csv[] = array($value->id,$value->amount);
                }
                $fileName = "axes_" . date("YmdHis") . ".csv";

                header("Content-Disposition: attachment; filename=\"$fileName\"");
                header("Content-type: application/octet-stream");
                header("Pragma: no-cache");
                header("Expires: 0");
                $fp= fopen('php://output', 'w');
                foreach ($csv as $fields) fputcsv($fp, $fields);
                fclose($fp);
                exit;
            }

            // todo 要調査
            if ($action == 'dopay') {
                if (!empty($_GET) && $_GET['round']){
                    $sql = "SELECT invest.*, project.published, project.passed, project.success, project.closed
                    FROM  invest
                    INNER JOIN project ON invest.project = project.id
                    WHERE   invest.project = ?
                    AND     (invest.status = 0
                        OR (invest.method = 'tpv'
                            AND invest.status = 1
                        )
                        OR (invest.method = 'cash'
                            AND invest.status = 1
                        )
                    )
                    AND (invest.campaign IS NULL OR invest.campaign = 0)
                    ";
                    $round = $_GET['round'];
                    switch ($round){
                        case "willpass":
                            $sql .= "AND invest.invested >= project.published AND invest.invested < project.passed";
                            break;
                        case "succeed":
                            $sql .= "AND invest.invested >= project.passed AND invest.invested <= project.success";
                            break;
                        case "closed":
                            $sql .= "AND invest.invested >= project.passed AND invest.invested <= project.closed";
                            break;
                        case "all":
                            $sql .= "AND invest.invested >= project.published AND invest.invested <= project.closed";
                            break;
                    }
                } else {
                    $sql = "SELECT  *
                    FROM  invest
                    WHERE   invest.project = ?
                    AND     (invest.status = 0
                        OR (invest.method = 'tpv'
                            AND invest.status = 1
                        )
                        OR (invest.method = 'cash'
                            AND invest.status = 1
                        )
                    )
                    AND (invest.campaign IS NULL OR invest.campaign = 0)
                    ";
                }
//                var_dump($sql);
//                exit;
                $query = \Goteo\Core\Model::query($sql, array($id));
                $invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');
                foreach ($invests as $key=>$invest) {
                    if ($invest->setPayment(date("YmdHis"))) {
                        $invest->setStatus(1);
                        Model\Invest::setDetail($invest->id, 'executed', 'Preapproval has been executed, has initiated the chained payment. Process cron / execute');
                        if ($invest->issue) {
                            Model\Invest::unsetIssue($invest->id);
                            Model\Invest::setDetail($invest->id, 'issue-solved', 'The incidence has been resolved upon success by the automatic process');
                        }
                    }
                }
                Message::Info("処理しました");
                throw new Redirection('/admin/projects/list');
                exit;
            }



            if ($action == 'epsilonpay') {
                if (!empty($_GET) && $_GET['round']){
                    $sql = "SELECT invest.*, project.published, project.passed, project.success, project.closed
                    FROM  invest
                    INNER JOIN project ON invest.project = project.id
                    WHERE   invest.project = ?
                    AND     (invest.status = 0
                        OR (invest.method = 'tpv'
                            AND invest.status = 1
                        )
                        OR (invest.method = 'cash'
                            AND invest.status = 1
                        )
                    )
                    AND (invest.campaign IS NULL OR invest.campaign = 0)
                    ";

                    $round = $_GET['round'];
                    switch ($round){
                        case "willpass":
                            $sql .= "AND invest.invested >= project.published AND invest.invested < project.passed";
                            break;
                        case "succeed":
                            $sql .= "AND invest.invested >= project.passed AND invest.invested <= project.success";
                            break;
                        case "closed":
                            $sql .= "AND invest.invested >= project.passed AND invest.invested <= project.closed";
                            break;
                        case "all":
                            $sql .= "AND invest.invested >= project.published AND invest.invested <= project.closed";
                            break;
                    }
                } else {
                    $sql = "SELECT  *
                    FROM  invest
                    WHERE   invest.project = ?
					AND     invest.method = 'epsilon'
                    AND     (invest.status = 0 
                        OR (invest.method = 'tpv'
                            AND invest.status = 1
                        )
                        OR (invest.method = 'cash'
                            AND invest.status = 1
                        )
                    )
                    AND (invest.campaign IS NULL OR invest.campaign = 0)
                    ";
                }

//                var_dump($sql);
//                exit;

                $query = \Goteo\Core\Model::query($sql, array($id));
                $invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');


				// httpリクエスト用のオプションを指定
				$http_option = array(
					"timeout" => "20", // タイムアウトの秒数指定
					//    "allowRedirects" => true, // リダイレクトの許可設定(true/false)
					//    "maxRedirects" => 3, // リダイレクトの最大回数
				);

				// 契約番号(8桁)
				$contract_code = EPSILON_CONTRACT_CODE;



                foreach ($invests as $key=>$invest) {

					// イプシロンに対して実売上処理を行う
					$request = new HTTP_Request2(EPSILON_SALSED_URL, HTTP_Request2::METHOD_POST, $http_option);
					$request->setConfig(array(
						'ssl_verify_peer' => false,
					));

					// 注文番号
					$order_number = $invest->id;

					$request->addPostParameter('contract_code', $contract_code );
					$request->addPostParameter('order_number', $order_number );

					// HTTPリクエスト実行
					$response = $request->send();

					// 応答内容(XML)の解析
					if (!PEAR::isError($response)) {
						$res_code = $response->getStatus();
						$res_content = $response->getBody();

						$resultno = $err_code = $err_detail = "";

						$xmlobj = simplexml_load_string($res_content);
						foreach ($xmlobj->result as $d) {
							$t = (string) $d->attributes()->result;
							if ($t != "") {
								$resultno = $t;
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
							case 1: // 正常終了
									// 何もしない
									break;

							case 9: // 失敗
									$invest->setStatus('2');

									// エラー出力
									Message::Error( "Epsilon Return Error  $err_code: $err_detail");
									throw new Redirection(SEC_URL."/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
									break;
						}
					} else {
#						Message::Error( "Epsilon Sales Error ");
#						throw new Redirection(SEC_URL."/$projType/$project/invest/?confirm=fail", Redirection::TEMPORARY);
					}



					// 内部データの更新
                    if ($invest->setPayment(date("YmdHis"))) {
                        $invest->setStatus(1);
                        Model\Invest::setDetail($invest->id, 'executed', 'Preapproval has been executed, has initiated the chained payment. Process cron / execute');
                        if ($invest->issue) {
                            Model\Invest::unsetIssue($invest->id);
                            Model\Invest::setDetail($invest->id, 'issue-solved', 'The incidence has been resolved upon success by the automatic process');
                        }
                    }

					sleep( 1 );

                }	// end foreach


                Message::Info("処理しました");
                throw new Redirection('/admin/projects/list');
                exit;
            }


            // detalles del aporte
            if ($action == 'details') {

                $invest = Model\Invest::get($id);
                $project = Model\Project::get($invest->project);
                $userData = Model\User::get($invest->user);

                if (!empty($invest->droped)) {
                    $droped = Model\Invest::get($invest->droped);
                } else {
                    $droped = null;
                }

                if ($project->node != $node) {
                    throw new Redirection('/admin/invests');
                }

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'invests',
                        'file' => 'details',
                        'invest' => $invest,
                        'project' => $project,
                        'user' => $userData,
                        'status' => $status,
                        'investStatus' => $investStatus,
                        'droped' => $droped,
                        'calls' => $calls
                    )
                );
            }

            // listado de aportes
            if ($filters['filtered'] == 'yes') {

                if (!empty($filters['calls']))
                    $filters['types'] = '';

                $list = Model\Invest::getList($filters, $node, 999);
            } else {
                $list = array();
            }

             $viewData = array(
                    'folder' => 'invests',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
                    'projects'      => $projects,
                    'users'         => $users,
                    'calls'         => $calls,
                    'methods'       => $methods,
                    'types'         => $types,
                    'investStatus'  => $investStatus
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }

    }

}
