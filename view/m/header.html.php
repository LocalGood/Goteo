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
    <a href="http://yokohama.localgood.jp.il3c.com" class="nav_logo">
        <img src="/view/css/header/s-header-logo.png" alt=""/>
    </a>
    <div class="nav_menu-button">
        <span></span>
        <span></span>
        <span></span>
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
                        <a href="http://yokohama.localgood.jp.il3c.com/lgnews/">
                            ニュース
                        </a>
                    </dd>
                    <dd>
                        <a href="http://yokohama.localgood.jp.il3c.com/data/">
                            データ
                        </a>
                    </dd>
                    <dd>
                        <a href="http://yokohama.localgood.jp.il3c.com/lgplayer/">
                            人/団体
                        </a>
                    </dd>
                    <dt>
                        みんなの声
                    </dt>
                    <dd>
                        <a href="http://yokohama.localgood.jp.il3c.com/subject/">
                            投稿一覧
                        </a>
                    </dd>
                    <dd>
                        <a href="http://yokohama.localgood.jp.il3c.com/submit_subject/">
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
                        <a href="https://cf.yokohama.localgood.jp/discover/">
                            プロジェクト一覧
                        </a>
                    </dd>
                    <dd>
                        <a href="https://cf.yokohama.localgood.jp/challenge/">
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
                <a href="https://cf.yokohama.localgood.jp/user/login" class="list01__text">
                    新規登録/ログイン
                </a>
            </li>
        </ul>
    </nav>


    <script>
        var linkList = $('.main_nav__link-list');
        $(linkList).slideUp();
        $('.nav_menu-button').click(function () {
            if ($(linkList).hasClass('on')) {
                $(linkList).slideUp();
                $(linkList).removeClass('on');
                $('.main_nav').css({"display": "none", "position": "none"});
            } else {
                $(linkList).slideDown();
                $(linkList).addClass('on');
                $(linkList).css({'position': 'fixed', 'top': '60px', 'left': '0'});
                $('.main_nav').css({
                    "display": "block",
                    "position": "fixed",
                    "top": "0",
                    "left": "0",
                    "z-index": "99999"
                });
            }
        });
        $('.list_open dl').slideUp();
        $('.list_open').click(function () {
            if ($(this).hasClass('on')) {
                $(this).children('dl').slideUp();
                $(this).removeClass('on');
            } else {
                $(this).children('dl').slideDown();
                $(this).addClass('on');
            }
        });
    </script>
    </nav>