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
?>

<nav class="main_nav02 clearfix">
    <a href="<?= LOCALGOOD_WP_BASE_URL; ?>" class="nav_logo">
        <img src="/view/m/images/header/s-header-logo.png" alt=""/>
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
                        <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/lgnews/">
                            ニュース
                        </a>
                    </dd>
                    <dd>
                        <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/event/">
                            イベント
                        </a>
                    </dd>
                    <dd>
                        <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/data/">
                            データ
                        </a>
                    </dd>
                    <dd>
                        <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/lgplayer/">
                            人/団体
                        </a>
                    </dd>
                    <dt>
                        みんなの声
                    </dt>
                    <dd>
                        <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/subject/">
                            投稿一覧
                        </a>
                    </dd>
                    <dd>
                        <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/submit_subject/">
                            あなたの声を投稿する
                        </a>
                    </dd>
                </dl>
            </li>
            <li class="list_open">
                <div class="list01__text">
                    応援する
                </div>
                <dl class="list02">
                    <dt></dt>
                    <dd>
                        <a href="<?= SITE_URL; ?>/discover/">
                            プロジェクト一覧
                        </a>
                    </dd>
                    <dd>
                        <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/challenge/">
                            プロジェクトを立てる
                        </a>
                    </dd>
                </dl>
            </li>
            <li>
                <a href="http://map.yokohama.localgood.jp/" target="_blank" class="list01__text">
                    3Dマップ
                </a>
            </li>
			<li>
				<a href="http://yokohama.localgood.jp.il3c.com/about/" class="list01__text">
					Local Good YOKOHAMAについて
				</a>
			</li>
            <?php if (empty($_SESSION['user'])) {
                //ログインしていない
                ?>
            <li>
                <a href="<?= SITE_URL; ?>/user/login" class="list01__text">
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
                        <a href="<?= SITE_URL; ?>/dashboard/activity">アクティビティ</a>
                    </dd>
                    <dd>
                        <a href="<?= SITE_URL; ?>/dashboard/profile">プロフィール</a>
                    </dd>
                    <dd>
                        <a href="<?= SITE_URL; ?>/community/sharemates">みんなの興味</a>
                    </dd>
                    <dd class="logout mean-last">
                        <a href="<?= SITE_URL; ?>/user/logout">ログアウト</a>
                    </dd>
                </dl>
            </li>
            <?php } ?>
        </ul>
    </nav>


    <script>
        $(function(){
            var linkList = $('.main_nav__link-list');
            $(linkList).slideUp();
            $('.nav_menu-button').click(function(){
                if($(linkList).hasClass('on')){
                    $(linkList).slideUp();
                    $(linkList).removeClass('on');
                    $('.main_nav').css({"display":"none","position":"none"});
                }else{
                    $(linkList).slideDown();
                    $(linkList).addClass('on');
                    $(linkList).css({'position':'fixed','top':'60px','left':'0'});
                    $('.main_nav').css({"display":"block","position":"fixed","top":"0","left":"0","z-index":"99999"});
                }
            });

            $('.list_open dl').slideUp();
            $('.list_open').click(function(){
                if($(this).hasClass('on')){
                    $(this).children('dl').slideUp();
                    $(this).removeClass('on');
                }else{
                    $(this).children('dl').slideDown();
                    $(this).addClass('on');
                }
            });

        });
    </script>
    </nav>