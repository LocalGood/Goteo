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

?>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.scroll-pane').jScrollPane({showArrows: true});
    });
</script>


<footer class="footer">

    <div class="footer__upper">
        <div class="c-clearfix c-w1200">
            <div class="footer__upper_left">
                <div class="footer__logo">
                    <img src="<?= LOCALGOOD_WP_BASE_URL; ?>/wp-content/themes/localgood/images/footer_logo.png" alt="LOCAL GOOD FUKUOKAロゴ">
                </div>
                <ul class="footer__sns_link">
                    <li class="rss"><a href="<?= LOCALGOOD_WP_BASE_URL . '/feed/'; ?>" target="_blank"><img src="/view/css/rss_btn.png" alt="rss" /></a></li>
                    <li class="tw_btn"><a href="<?= LG_TWITTER; ?>" target="_blank"><img src="/view/css/tw_btn.png" alt="twitter" /></a></li>
                    <li class="fb_btn"><a href="<?= LG_FACEBOOK_PAGE; ?>" target="_blank"><img src="/view/css/fb_btn.png" alt="facebook" /></a></li>
                </ul>
                <a class="footer__integration_site" href="http://localgood.jp/">LOCAL GOOD 地域課題プラットフォーム</a>
            </div>
            <ul class="footer__upper_right">
                <li>
                    <span class="footer__upper_second_title">ご利用にあたって</span>
                    <ul>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/about/">LOCAL GOOD KITAQについて</a></li>
                        <li class="syoutorihikihou"><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/syoutorihikihou/">特定商取引法に基づく表記</a></li>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/privacypolicy/">プライバシーポリシー</a></li>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/user_guide/">ユーザーガイド</a></li>
                        <li><a href="http://localgood.jp/riyou_kiyaku_menu/">利用規約</a></li>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/contact/">お問い合わせ</a></li>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/authors/">記者一覧</a></li>
                    </ul>
                </li>
                <li>
                    <span class="footer__upper_second_title">地域を知る</span>
                    <span class="footer__upper__third_title">記事</span>
                    <ul>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/lgnews/">ニュース</a></li>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/lgplayer/">人/団体</a></li>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/data/">データ</a></li>
                    </ul>
                    <span class="footer__upper__third_title">みんなの声</span>
                    <ul>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/subject/">投稿一覧</a></li>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL; ?>/submit_subject/">あなたの声を投稿する</a></li>
                    </ul>
                </li>
                <li>
                    <span class="footer__upper_second_title">応援する</span>
                    <ul>
                        <li><a href="/discover/">プロジェクト一覧</a></li>
                        <li><a href="<?= SITE_URL; ?>">プロジェクトを立てる</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="footer__under">
        <div class="c-w1096">
            <ul>
                <li><a class="logo_1" href="http://sociofund.org/" target="_blank"><img src="/view/css/logo_socio_fund.png" alt="ソシオファンド北九州"></a></li>
                <li><a class="logo_2" href="https://kyushu.socialvalue.jp/" target="_blank"><img src="/view/css/logo_kses.jpg" alt="社会起業大学・九州校"></a></li>
                <li><a class="logo_3" href="https://www.facebook.com/kokuraprpurojekuto/" target="_blank"><img src="/view/css/logo_kokulike.png" alt="Kokulike"></a></li>
                <li><a href="http://goteo.org/" target="_blank"><img src="/view/css/logo_f_goteo.png" alt="画像：Fundacion Goteo"></a></li>
            </ul>
        </div>
        <p class="footer__copyright">
            <span>COPYRIGHT&copy; <?= LG_NAME; ?>. Some rights reserved.</span>
        </p>
    </div>
</footer>