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
    Goteo\Model\Category,
    Goteo\Model\Post,
    Goteo\Model\Sponsor;
//@NODESYS

$lang = (LANG != 'es') ? '?lang='.LANG : '';

$categories = Category::getList();  // categorias que se usan en proyectos
$posts      = Post::getList('footer');
$sponsors   = Sponsor::getList();
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('.scroll-pane').jScrollPane({showArrows: true});
});
</script>

<footer>
    <section class="footer_menu_links">
        <div class="footer__link-title">
            ご利用にあたって
        </div>
        <ul>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/about/">
                    LOCAL GOOD YOKOHAMAについて
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/mailnews/">
                    メルマガ登録
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/syoutorihikihou/">
                    特定商取引法について
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/privacypolicy/">
                    プライバシーポリシー
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/user_guide/">
                    ユーザーガイド
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/riyou_kiyaku_menu/">
                    利用規約
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/contact/">
                    お問い合わせ
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/authors/">
                    記者一覧
                </a>
            </li>
        </ul>

        <div class="footer__link-title">
            地域を知る
        </div>
        <span>
            記事
        </span>
        <ul>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/category/news/">
                    ニュース
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/lgplayer/">
                    人/団体
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/data/">
                    データ
                </a>
            </li>
        </ul>
        <span>
            みんなの声
        </span>
        <ul>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/subject/">
                    投稿一覧
                </a>
            </li>
            <li>
                <a href="http://yokohama.localgood.jp.il3c.com/submit_subject/">
                    あなたの声を投稿する
                </a>
            </li>
        </ul>

        <div class="footer__link-title">
            応援する
        </div>
        <ul>
            <li>
                <a href="https://cf.yokohama.localgood.jp/discover">
                    プロジェクト一覧
                </a>
            </li>
            <li>
                <a href="https://cf.yokohama.localgood.jp/challenge/">
                    プロジェクトを立てる
                </a>
            </li>
        </ul>
    </section>

    <section class="footer_bottom">
        <div class="footer_logo">
            <a href="http://yokohama.localgood.jp.il3c.com">
                <img src="/view/css/s-footer-logo.png" alt="LOCAL GOOD">
            </a>
        </div>
        <ul class="sns-area">
            <li>
                <a href="http://yokohama.localgood.jp/feed/" target="_blank">
                    <img src="/view/css/s-footer-sns-icon01.jpg" alt="">
                </a>
            </li>
            <li>
                <a href="https://plus.google.com/112981975493826894716/posts" target="_blank">
                    <img src="/view/css/s-footer-sns-icon02.jpg" alt="">
                </a>
            </li>
            <li>
                <a href="https://twitter.com/LogooYOKOHAMA" target="_blank">
                    <img src="/view/css/s-footer-sns-icon03.jpg" alt="">
                </a>
            </li>
            <li>
                <a href="https://www.facebook.com/LOCALGOODYOKOHAMA" target="_blank">
                    <img src="/view/css/s-footer-sns-icon04.png" alt="">
                </a>
            </li>
        </ul>
        <div class="link">
            <span>
                >
            </span>
            <a href="">
                LOCAL GOOD 地域課題プラットフォーム
            </a>
        </div>
    </section>
</footer>

    <div class="sp_footer_logo-area">
        <ul class="clearfix">
            <li class="left">
                <img src="/view/css/s-footer-logo01.png" alt="">
            </li>
            <li class="left">
                <img src="/view/css/s-footer-logo02.png" alt="">
            </li>
            <li class="left">
                <img src="/view/css/s-footer-logo03.png" alt="">
            </li>
            <li class="left">
                <img src="/view/css/s-footer-logo04.png" alt="">
            </li>
            <li class="left">
                <img src="/view/css/s-footer-logo05.png" alt="">
            </li>
            <li class="left">
                <img src="/view/css/s-footer-logo06.png" alt="">
            </li>
            <li class="left">
                <img src="/view/css/s-footer-logo07.png" alt="">
            </li>
        </ul>
        <div class="cw">
            COPYRIGHT© LOCAL GOOD YOKOHAMA. Some rights reserved.
        </div>
    </div>