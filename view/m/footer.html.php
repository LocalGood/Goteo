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
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/about/">
                    LOCAL GOOD FUKUOKAについて
                </a>
            </li>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/mailnews/">
                    メルマガ登録
                </a>
            </li>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/syoutorihikihou/">
                    特定商取引法について
                </a>
            </li>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/privacypolicy/">
                    プライバシーポリシー
                </a>
            </li>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/user_guide/">
                    ユーザーガイド
                </a>
            </li>
            <li>
                <a href="http://localgood.jp/riyou_kiyaku_menu/">
                    利用規約
                </a>
            </li>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/contact/">
                    お問い合わせ
                </a>
            </li>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/authors/">
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
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/lgnews/">
                    ニュース
                </a>
            </li>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/lgplayer/">
                    人/団体
                </a>
            </li>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/data/">
                    データ
                </a>
            </li>
        </ul>
        <span>
            みんなの声
        </span>
        <ul>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/subject/">
                    投稿一覧
                </a>
            </li>
            <li>
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/submit_subject/">
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
                <a href="<?= LOCALGOOD_WP_BASE_URL; ?>/challenge/">
                    プロジェクトを立てる
                </a>
            </li>
        </ul>
    </section>

    <section class="footer_bottom">
        <div class="footer_logo">
            <a href="<?= LOCALGOOD_WP_BASE_URL; ?>">
                <img src="/view/css/s-footer-logo.png" alt="LOCAL GOOD">
            </a>
        </div>
        <ul class="sns-area">
            <li>
                <a href="http://yokohama.localgood.jp/feed/" target="_blank">
                    <img src="/view/css/s-footer-sns-icon01.png" alt="rss">
                </a>
            </li>
            <li>
                <a href="https://twitter.com/LogooYOKOHAMA" target="_blank">
                    <img src="/view/css/s-footer-sns-icon03.png" alt="Twitter">
                </a>
            </li>
            <li>
                <a href="https://www.facebook.com/LOCALGOODYOKOHAMA" target="_blank">
                    <img src="/view/css/s-footer-sns-icon04.png" alt="facebook">
                </a>
            </li>
        </ul>
        <div class="link">
            <span>
                >
            </span>
            <a href="http://localgood.jp/">
                LOCAL GOOD 地域課題プラットフォーム
            </a>
        </div>
    </section>
</footer>

<div class="sp_footer_logo-area">
    <ul class="clearfix">
        <li class="left">
            <a href="http://npo-aip.or.jp/" target="_blank">
                <img src="/view/css/logo_aip.png" alt="画像：AIP">
            </a>
        </li>
        <li class="left">
            <a href="http://www.city.fukuoka.lg.jp/" target="_blank">
                <img src="/view/css/logo_fukuoka_city.png" alt="画像：福岡市">
            </a>
        </li>
        <li class="left">
            <a href="http://goteo.org/" target="_blank">
                <img src="/view/css/logo_f_goteo.png" alt="画像：Fundacion Goteo">
            </a>
        </li>
    </ul>
    <div class="cw">
        COPYRIGHT© LOCAL GOOD FUKUOKA. Some rights reserved.
    </div>
</div>