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
    Goteo\Core\ACL,
    Goteo\Library\i18n\Lang;
//@NODESYS

?>

<script>
    $(function(){
        $(window).resize(function(){
            var width = $(window).width();
            if (width <= 1140) {
                $('#header').children('.logo_wrapper').children('a').css('display','none');
            } else {
                $('#header').children('.logo_wrapper').children('a').css('display','block');
            }
        });
        $(window).resize();

    })
</script>
<div class="header_wrapper">
<header id="header" class="normal_header header clearfix">

    <h1 class="header__logo">
        <a href="<?= LOCALGOOD_WP_BASE_URL; ?>"><img src="http://yokohama.localgood.jp/wp-content/themes/localgood/images/header_logo.png" alt=""/></a>
    </h1>

    <div class="header__right">
        <nav class="header__right__nav">
            <ul id="gnav" class="header__right__nav__gnav">
                <? $a = 'class="active"'; ?>
                <li>
                    <a href="<?= LOCALGOOD_WP_BASE_URL . '/lgnews/' ?>">地域を知る</a>
                    <div class="snav header__right__snav">
                        <div class="header__right__snav__inner">
                            <span class="header__right__snav__second_title">記事</span>
                            <ul>
                                <li><span><a href="<?= LOCALGOOD_WP_BASE_URL . '/lgnews/' ?>">ニュース</a></span></li>
                                <li><span><a href="<?= LOCALGOOD_WP_BASE_URL . '/event/' ?>">イベント</a></span></li>
                                <li><span><a href="<?= LOCALGOOD_WP_BASE_URL . '/data/' ?>">データ</a></span></li>
                                <li><span><a href="<?= LOCALGOOD_WP_BASE_URL . '/lgplayer/' ?>">人/団体</a></span></li>
                            </ul>
                            <span class="header__right__snav__second_title">みんなの声</span>
                            <ul>
                                <li><span><a href="<?= LOCALGOOD_WP_BASE_URL . '/subject/' ?>">投稿一覧</a></span></li>
                                <li><span><a href="<?= LOCALGOOD_WP_BASE_URL . '/submit_subject/' ?>">あなたの声を投稿する</a></span></li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li>
                    <a href="<?= SITE_URL; ?>">応援する</a>
                    <div class="header__right__snav">
                        <div class="header__right__snav__inner">
                            <ul>
                                <li><span><a href="<?= SITE_URL; ?>/discover/">プロジェクト一覧</a></span></li>
                                <li><span><a href="<?= LOCALGOOD_WP_BASE_URL . '/challenge/'; ?>">プロジェクトを立ち上げる</a></span></li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li><a href="http://map.yokohama.localgood.jp/" target="_blank">3Dマップ</a>
                <li><a href="<?= LOCALGOOD_WP_BASE_URL . '/about/'; ?>" >Local Good YOKOHAMAについて</a>
                </li>
                <li class="gnav_goteo">
                    <?php if (empty($_SESSION['user'])) {
                    //ログインしていない
                    ?>
                        <a href="/user/login">新規登録/ログイン</a>
                    <?php } elseif (!empty($_SESSION['user'])) {
                    //ログインしてる
                    ?>
                        <div id="goteo_menu" class="goteo_menu">
                            <ul>
                                <li class="dashboard active"><a href="/dashboard"><span>マイページ</span><img src="<?php echo $_SESSION['user']->avatar->getLink(28,28, true)?>" alt="<?php echo $_SESSION['user']->name?>"></a>
                                    <div>
                                        <ul>
                                            <li><a href="/dashboard/activity"><span>アクティビティ</span></a></li>
                                            <li><a href="/dashboard/profile"><span>プロフィール</span></a></li>
                                            <li><a href="/dashboard/projects"><span>マイプロジェクト</span></a></li>
                                            <li><a href="/community/sharemates"><span>みんなの興味</span></a></li>
                                            <?php if (ACL::check('/admin')) : ?>
                                                <li><a href="/admin"><span>管理者パネル</span></a></li>
                                            <?php endif; ?>
                                            <li class="logout"><a href="/user/logout"><span>ログアウト</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    <? } ?>
                </li>
            </ul>
        </nav>
    </div>

</header>
<!--.header-->
</div>
