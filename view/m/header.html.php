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

use Goteo\Library\Text,
    Goteo\Library\i18n\Lang;

//@NODESYS
$configJson = json_decode( file_get_contents( __DIR__ . '/../../omniconfig/apikeys.json' ) );
?>

<nav class="main_nav02">
    <a href="<?php echo LG_BASE_URL_WP; ?>" class="nav_logo">
        <img src="<?php echo $configJson->images->header_logo_2; ?>" alt=""/>
    </a>
    <div class="nav_menu-button">
        <span></span>
        <span></span>
        <span></span>
        <div class="close_button">×</div><!-- /.close_button -->
    </div>
    <nav class="main_nav__link-list">
        <ul class="list01">
            <li class="list_open">
                <div class="list01__text">
                    地域を知る
                </div>
                <dl class="list02">
                    <dt>
                        記事
                    </dt>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_WP; ?>/lgnews/">
                            ニュース
                        </a>
                    </dd>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_WP; ?>/event/">
                            みんなの拠点/イベント
                        </a>
                    </dd>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_WP; ?>/data/">
                            データ
                        </a>
                    </dd>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_WP; ?>/lgplayer/">
                            人/団体
                        </a>
                    </dd>
                    <dt>
                        みんなの声
                    </dt>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_WP; ?>/subject/">
                            投稿一覧
                        </a>
                    </dd>
                    <?php /* if( strpos($_SERVER['HTTP_HOST'], 'sendai') !== FALSE ): ?>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_WP; ?>/submit_subject/">
                            あなたの声を投稿する
                        </a>
                    </dd>
                    <?php endif;*/ ?>
                </dl>
            </li>
            <li class="list_open">
                <div class="list01__text">
                    応援する
                </div>
                <dl class="list02">
                    <dt></dt>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_GT; ?>/discover/">
                            プロジェクト一覧
                        </a>
                    </dd>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_WP; ?>/challenge/">
							プロジェクトを立ち上げる
                        </a>
                    </dd>
                </dl>
            </li>
            <li>
                <a href="<?php echo $configJson->other->earthviewurl; ?>" target="_blank" class="list01__text">
                    3Dマップ
                </a>
            </li>
			<li>
				<a href="<?php echo LG_BASE_URL_WP; ?>/about/" class="list01__text">
                    <?php echo GOTEO_META_TITLE; ?>について
				</a>
			</li>
            <?php if (empty($_SESSION['user'])) {
                //ログインしていない
                ?>
            <li>
                <a href="<?php echo LG_BASE_URL_GT; ?>/user/login" class="list01__text">
                    新規登録/ログイン
                </a>
            </li>
            <?php } elseif (!empty($_SESSION['user'])) {
            //ログインしてる
            ?>
            <li class="list_open">
                <div class="list01__text">
                    マイページ
                </div>
                <dl class="list02">
                    <dt></dt>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_GT; ?>/dashboard/activity">アクティビティ</a>
                    </dd>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_GT; ?>/dashboard/profile">プロフィール</a>
                    </dd>
                    <dd>
                        <a href="<?php echo LG_BASE_URL_GT; ?>/community/sharemates">みんなの興味</a>
                    </dd>
                    <dd class="logout mean-last">
                        <a href="<?php echo LG_BASE_URL_GT; ?>/user/logout">ログアウト</a>
                    </dd>
                </dl>
            </li>
            <?php } ?>
        </ul>
    </nav>

</nav>