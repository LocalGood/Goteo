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
                <a href="<?= LG_INTEGRATION_URL; ?>/riyou_kiyaku_menu/">
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
                <a href="<?= SITE_URL; ?>/discover">
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
                <a href="<?= LOCALGOOD_WP_BASE_URL . '/feed/'; ?>" target="_blank">
                    <img src="/view/css/s-footer-sns-icon01.png" alt="rss">
                </a>
            </li>
            <li>
                <a href="<?= LG_TWITTER; ?>" target="_blank">
                    <img src="/view/css/s-footer-sns-icon03.png" alt="Twitter">
                </a>
            </li>
            <li>
                <a href="<?= LG_FACEBOOK_PAGE; ?>" target="_blank">
                    <img src="/view/css/s-footer-sns-icon04.png" alt="facebook">
                </a>
            </li>
        </ul>
        <div class="link">
            <span>
                >
            </span>
            <a href="<?= LG_INTEGRATION_URL; ?>">
                LOCAL GOOD 地域課題プラットフォーム
            </a>
        </div>
    </section>
</footer>

<div class="sp_footer_logo-area">
    <ul class="clearfix">
        <li>
            <a class="logo_1" href="http://sociofund.org/" target="_blank">
                <img src="/view/css/logo_socio_fund.png" alt="ソシオファンド北九州">
            </a>
        </li>
        <li>
            <a class="logo_2" href="https://kyushu.socialvalue.jp/" target="_blank">
                <img src="/view/css/logo_kses.jpg" alt="社会起業大学・九州校">
            </a>
        </li>
        <li>
            <a class="logo_3" href="https://www.facebook.com/kokuraprpurojekuto/" target="_blank">
                <img src="/view/css/logo_kokulike.png" alt="Kokulike">
            </a>
        </li>
        <li class="left">
            <a href="http://goteo.org/" target="_blank">
                <img src="/view/css/logo_f_goteo.png" alt="画像：Fundacion Goteo">
            </a>
        </li>
    </ul>
    <div class="cw">
        COPYRIGHT© <?= LG_NAME; ?>. Some rights reserved.
    </div>
</div>