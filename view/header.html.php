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

$configJson = json_decode(file_get_contents( '/var/www/html/omniconfig/apikeys.json'));
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
        <a href="<?php echo LG_BASE_URL_WP; ?>"><img src="<?php echo $configJson->images->header_logo_2; ?>" alt=""/></a>
    </h1>

    <div class="header__right">
        <nav class="header__right__nav">
            <ul id="gnav" class="header__right__nav__gnav">
                <? $a = 'class="active"'; ?>
                <li>
                    <a href="<?php echo LG_BASE_URL_WP . '/lgnews/' ?>">地域を知る</a>
                    <div class="snav header__right__snav">
                        <div class="header__right__snav__inner">
                            <span class="header__right__snav__second_title">記事</span>
                            <ul>
                                <li><span><a href="<?php echo LG_BASE_URL_WP . '/lgnews/' ?>">ニュース</a></span></li>
                                <li><span><a href="<?php echo LG_BASE_URL_WP . '/event/' ?>">みんなの拠点/イベント</a></span></li>
                                <li><span><a href="<?php echo LG_BASE_URL_WP . '/data/' ?>">データ</a></span></li>
                                <li><span><a href="<?php echo LG_BASE_URL_WP . '/lgplayer/' ?>">人/団体</a></span></li>
                            </ul>
                            <span class="header__right__snav__second_title">みんなの声</span>
                            <ul>
                                <li><span><a href="<?php echo LOCALGOOD_WP_BASE_URL . '/subject/' ?>">投稿一覧</a></span></li>
                                <?php /* if( strpos($_SERVER['HTTP_HOST'], 'sendai') !== FALSE ): ?>
                                <li><span><a href="<?php echo LOCALGOOD_WP_BASE_URL . '/submit_subject/' ?>">あなたの声を投稿する</a></span></li>
                                <?php endif; */ ?>
                            </ul>
                        </div>
                    </div>
                </li>
                <li>
                    <a href="<?php echo LG_BASE_URL_GT; ?>">応援する</a>
                    <div class="header__right__snav">
                        <div class="header__right__snav__inner">
                            <ul>
                                <li><span><a href="<?php echo LG_BASE_URL_GT; ?>/discover/">プロジェクト一覧</a></span></li>
                                <li><span><a href="<?php echo LG_BASE_URL_WP . '/challenge/'; ?>">プロジェクトを立ち上げる</a></span></li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li><a href="<?php echo $configJson->other->earthviewurl; ?>" target="_blank">3Dマップ</a>
                <li><a href="<?php echo LG_BASE_URL_WP . '/about/'; ?>" ><?php echo GOTEO_META_TITLE; ?>について</a>
                </li>
                <?php if( strpos($_SERVER['HTTP_HOST'], 'kitaq') === FALSE ): ?>
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
                                <li class="dashboard active"><a href="/dashboard"><span>マイページ</span><img src="<?php
                                        if(property_exists($_SESSION['user'], 'avatar') && (($_SESSION['user']->avatar) instanceof \Goteo\Model\Image)) { echo $_SESSION['user']->avatar->getLink(28,28, true); } ?>" alt="<?php echo $_SESSION['user']->name?>"></a>
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
                <?php endif; ?>
            </ul>
        </nav>
    </div>

</header>
<!--.header-->
</div>
