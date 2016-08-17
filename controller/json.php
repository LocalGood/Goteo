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

    use Goteo\Model,
        Goteo\Model\User,
        Goteo\Model\Promote,
        Goteo\Model\Project
        ;

    class JSON extends \Goteo\Core\Controller {

		private $result = array();

		/**
		 * Solo retorna si la sesion esta activa o no
		 * */
		public function keep_alive() {

			$this->result = array(
				'logged'=>false
			);
			if($_SESSION['user'] instanceof User) {
				$this->result['logged'] = true;
				$this->result['userid'] = $_SESSION['user']->id;
			}

			return $this->output();
		}

        /**
         * API interfaces for LocalGood
         * */

        //  指定したスキルIDの子スキル一覧の取得
        //      /json/get_childrenl?parentid=XX
        public function get_children(){

            if (!empty($_REQUEST['parentid']))
                $parent_id = $_REQUEST['parentid'];

            $this->result[] = \Goteo\Model\Skill::getChildren($parent_id);

            return $this->output();
        }

        //  スキル情報取得
        //      /json/get_skill?skillid=XX
        public function get_skill(){

            if (!empty($_REQUEST['skillid']))
                $skill_id = $_REQUEST['skillid'];

            $this->result[] = \Goteo\Model\Skill::get($skill_id);

            return $this->output();

        }

        public function get_skill_list() {

            $this->result[] = \Goteo\Model\Skill::getList();

            return $this->output();

        }

        /**
         * @return string
         */
        public function get_all_parent() {

            $this->result[] = \Goteo\Model\Skill::getAllParent();

            return $this->output();

        }

        /**
         * @return string
         */
        public function get_users(){
            // parameters
            //  skillid -> Skill ID
            //  userid -> user id
            //  username -> user name
            //  interest -> interest
            //  type -> user type

            $params = array();

            if (!empty($_REQUEST['skillid']))
                $params['skill'] = $_REQUEST['skillid'];

            if (!empty($_REQUEST['userid']))
                $params['id'] = $_REQUEST['id'];

            if (!empty($_REQUEST['username']))
                $params['name'] = $_REQUEST['username'];

            if (!empty($_REQUEST['interest']))
                $params['interest'] = $_REQUEST['interest'];

            if (!empty($_REQUEST['type'])){
                switch ($_REQUEST['type']) {
                    case 'creators':
                        $params['type'] = 'creators';
                        break;
                    case 'investors':
                        $params['type'] = 'investos';
                        break;
                    case 'supporters':
                        $params['type'] = 'supporters';
                        break;
                    case 'lurkers':
                        $params['type'] = 'lurkers';
                        break;
                }
            }

            if (!empty($params)){
                $this->result[] = \Goteo\Model\User::getAll($params);
            }

            return $this->output();
        }

        /**
         * @param $_param
         * @return false|Model\obj
         */
        private function _get_user($_param){
            if (!empty($_param)){
                $_result = \Goteo\Model\User::get($_param, 'ja');
            };
            return $_result;
        }

        /**
         * @param null $_param
         * @return string
         */
        public function get_user($_param = null)
        {
            if (!empty($_REQUEST['id']) || !empty($_param)){

                $param = $_param;
                if (!empty($_REQUEST['id']))
                    $param = $_REQUEST['id'];

                $this->result[] = self::_get_user($param);
            };
            return $this->output();
        }

        /**
         * get User Avatar img
         * @return string
         */
        public function get_user_avatar()
        {
            $size_arr = array(
                '28' => array(28, 28), '32' => array(32, 32), '40' => array(40, 40), '43' => array(43, 43),
                '45' => array(45, 45), '50' => array(50, 50), '56' => array(56, 56), '80' => array(80, 80),
                '90' => array(90, 60), '128' => array(128, 128), '175' => array(175, 100),
                '255' => array(255, 135),'260' => array(260, 135),
                '500' => array(500, 285), '580' => array(580, 580),'700' => array(700, 156)
            );

            if (!empty($_REQUEST['id'])){
                $_ret = self::_get_user($_REQUEST['id']);
                if (!empty($_REQUEST['size']) && (intval($_REQUEST['size']) == $_REQUEST['size'] )){
                    $_size = $_REQUEST['size'];
                } else {
                    $_size = '80';
                }
                if (!empty($_ret)){
                    $this->result[] = $_ret->avatar->getLink($size_arr[$_size][0], $size_arr[$_size][1], true);
                }
            };
            return $this->output();
        }


        /**
         * @param $_param
         * @param bool $_showall
         * @param int $_limit
         * @return array
         * @throws \Goteo\Library\Exception
         */
        private function _get_projects_by_skill($_param, $_add_param = null, $_showall = false, $_limit = 5){

            $params = array(
                'skills'=>array(), 'category'=>array(), 'location'=>array(), 'reward'=>array()
            );

            if (is_array($_add_param) && !empty($_add_param)){
                foreach ($_add_param as $_k => $_v){
                    $params[$_k] = $_v;
                }
            }

            if (strpos($_param,',') > 0){
                $_param_array = array();
                $_param_array = explode(',', $_param);
                foreach($_param_array as $_prm){
                    $params['skills'][] = $_prm;
                }
            } else {
                $params['skills'][] = $_param;
            }

            // query はキーワード検索用？
            $params['query'] = '';

//            var_dump($params);
//            exit;

            return \Goteo\Library\Search::params($params, $_showall, $_limit);

        }

        /**
         * @return string
         */
        public function get_projects_by_skill() {

            if (!empty($_REQUEST['skillid'])){
                $this->result = self::_get_projects_by_skill($_REQUEST['skillid']);
            }
            return $this->output();

        }

        /**
         * for LocalGood App Push Notifier
         */
        public function get_matched_project()
        {
            if (!empty($_REQUEST['userid'])) {
                $_userid = $_REQUEST['userid'];
                $_user = self::_get_user($_userid);

                if (!empty($_user)) {
                    if (!empty($_user->skills)){
                        $_skill = implode(',', $_user->skills);
                        // ステータス:キャンペーン中, プロジェクト"発行"日, 降順, 最新1件 で選択
                        $_result = self::_get_projects_by_skill(
                            $_skill, array('status' => '3', 'orderby'=> 'published', 'order' => 'DESC'),
                            false,
                            1
                        );
                        if (!empty($_result)){
                            $_matched = array();
                            $_matched['id'] = $_result[0]->id;
                            $_matched['url'] = SITE_URL . '/project/' . urlencode($_result[0]->id);
                            $this->result = $_matched;
                        }
                    }
                }
            }
            return $this->output();
        }

        /**
         * get pickup projects
         *
         * @return bool|string
         * @throws \Goteo\Core\Exception
         */
        function get_pickup_projects(){
            $_result = Promote::getAll(true);
            if (count($_result) > 3) {
                for ($i = 0; $i < 3; $i++) {
                    $this->result[] = $_result[$i];
                }
            } else {
                $this->result = $_result;
            }
//            $this->result = Promote::getAll(true);
            return $this->output();
        }

        /**
         * get pickup projects
         *
         * @return bool|string
         * @throws \Goteo\Core\Exception
         */
        function get_goteo_datum(){
            $_result = Promote::getAll(true);
            if (count($_result) > 3) {
                for ($i = 0; $i < 3; $i++) {
                    $this->result[] = $_result[$i];
                }
            } else {
                $this->result = $_result;
            }
//            $this->result = Promote::getAll(true);
//            var_dump($this->result);exit;
            return $this->output();
        }

        /**
         * get goteo investors
         *
         * @return bool|string
         * @throws \Goteo\Core\Exception
         *
         */
        function _get_investors_all(){
            $query = \Goteo\Core\Model::query('select count(distinct user) from invest inner join project on project.id = invest.project where (invest.status = 1 OR invest.status = 3 ) AND project.status >= 3 AND project.created >= \'2014-10-01\'');
            $matched = $query->fetch(\PDO::FETCH_ASSOC);
            return $matched;
        }

        /**
         * get goteo status
         *
         * @return bool|string
         * @throws \Goteo\Core\Exception
         *
         */
        function get_goteo_status(){
            $query = \Goteo\Core\Model::query('select amount, id, status from project where status >= 3 and created >= \'2014-10-01\'');
            $matched = $query->fetchAll();
            $result = [];
            foreach ($matched as $pj) {
                if ($pj['status'] == 4){
                    $result['total'] += $pj['amount'];
                    $result['succeed'] += 1;
                } elseif ($pj['status'] == 3) {
                    $result['progress'] += 1;
                }
            }

            if (!empty($result['total'])){
                $result['total'] = number_format($result['total'], 0, '', ',');
            }

            $investall = "";
            $investall = self::_get_investors_all();
            if (!empty($investall['count(distinct user)'])){
                $result['investors'] = $investall['count(distinct user)'];
            }

            $this->result = $result;
            return $this->output();
        }

        /**
         * return server domain for LocalGood App.
         *
         * @return bool|string
         */
        function get_server_domain(){
//            $_pro = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
            $this->result[] = 'http://d2.yokohama.localgood.jp';
//            $_test = json_encode($this->result);
//            var_dump(json_decode($_test));
//            exit;
            return $this->output();
        }

        /**
		 * Json encoding...
		 * */
		public function output() {
            if ( empty($this->result ) ){
                header("HTTP/1.1 404 Not Found");
                return false;
            } else {
                header("Content-Type: application/json; charset=utf-8");
                return json_encode($this->result);
            }
		}

    }
}
