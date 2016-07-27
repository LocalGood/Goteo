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
<header id="header" class="normal_header header clearfix">

    <h1 class="header__logo">
        <a href=""><img src="/view/css/header_logo.png" alt=""/></a>
    </h1>

    <div class="header__right">
        <div class="header__right__search_box">
            <form role="search" id="search_form" method="get" action="/">
                <input type="text" id="s" name="s" placeholder="キーワード">
                <input type="image" src="/view/css/search.png" alt="検索"
                       id="searchBtn" name="searchBtn">
            </form>
        </div>
        <nav class="header__right__nav">
            <ul id="gnav" class="header__right__nav__gnav">
                <? $a = 'class="active"'; ?>
                <li>
                    <a href="">地域を知る</a>
                    <div class="snav header__right__snav">
                        <div class="header__right__snav__inner">
                            <span class="header__right__snav__second_title">記事</span>
                            <ul>
                                <li><span><a href="">ニュース</a></span></li>
                                <li><span><a href="">データ</a></span></li>
                                <li><span><a href="">人/団体</a></span></li>
                            </ul>
                            <span class="header__right__snav__second_title">みんなの声</span>
                            <ul>
                                <li><span><a href="">投稿一覧</a></span></li>
                                <li><span><a href="">あなたの声を投稿する</a></span></li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li>
                    <a href="/discover/">応援する</a>
                    <div class="header__right__snav">
                        <div class="header__right__snav__inner">
                            <ul>
                                <li><span><a href="">プロジェクト一覧</a></span></li>
                                <li><span><a href="">プロジェクトを立ち上げる</a></span></li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li><a href="">3Dマップ</a>
                </li>
                <li class="gnav_goteo">
                    <a href="/user/login">新規登録/ログイン</a>
                </li>
            </ul>
        </nav>
    </div>

</header>
<!--.header-->

