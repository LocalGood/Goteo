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

<div class="footer">
    <div class="footer_link_wrapper">
        <div id="to_page_top" style="bottom:0; margin-left: 880px;">
            <a href="#page_top"><img src="/view/css/page_topBtn.png" alt="ページの上部へ" /></a>
        </div><!--#to_page_top-->
        <div class="inner cf">
            <ul class="footer_link">
                <li class="about"><a href="<?= LOCALGOOD_WP_BASE_URL ?>/about/"><?= LG_NAME; ?>について</a></li>
                <li><a href="<?= LG_INTEGRATION_URL ?>/riyou_kiyaku_menu/">利用規約</a></li>
                <li class="syoutorihikihou"><a href="<?= LOCALGOOD_WP_BASE_URL ?>/syoutorihikihou/">特定商取引法に基づく表記</a></li>
                <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/user_guide/">ユーザーガイド</a></li>
                <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/privacypolicy/">プライバシーポリシー</a></li>
                <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/mailnews/">メルマガ登録</a></li>
                <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/contact/">お問い合わせ</a></li>
                <li class="integration"><a href="<?= LG_INTEGRATION_URL ?>/">LOCAL GOOD全国版トップページ</a></li>
            </ul>
            <ul class="sns_link">
                <li class="fb_btn"><a href="https://www.facebook.com/LOCALGOODYOKOHAMA" target="_blank"><img src="/view/css/fb_btn.png" alt="facebook" /></a></li>
                <li class="tw_btn"><a href="https://twitter.com/LogooYOKOHAMA" target="_blank"><img src="/view/css/tw_btn.png" alt="twitter" /></a></li>
                <li class="g_plus"><a href="https://plus.google.com/112981975493826894716/" target="_blank"><img src="/view/css/gplus_btn.png" alt="google plus" /></a></li>
            </ul>
        </div>
    </div>
    <div class="foot_bar_wrapper cf">
        <div class="foot_bar_inner cf">
            <a class="logo_1" href="http://yokohamalab.jp/" target="_blank"><img src="/view/css/ycdl_logo.png" alt="Yokohama Community Design Lab." /></a>
            <a class="logo_2" href="http://www.accenture.com/jp-ja/Pages/index.aspx" target="_blank"><img src="/view/css/accenture_logo.png" alt="accenture" /></a>
            <img class="logo_3" src="/view/css/open_yokohama_logo.png" alt="Open Yokohama" />
            <a class="logo_4" href="https://goteo.org/" target="_blank"><img src="/view/css/logo-goteo-H-100.png" alt="Goteo" /></a>
            <p class="copyright">
                <span>COPYRIGHT&copy; LOCAL GOOD YOKOHAMA. Some rights reserved.</span>
            </p>
            <div class="platoniq">
                <?php // You are not allowed to remove this links. If so, you'll make a legal fault regarding the release license. You can read it at https://github.com/Goteo/Goteo/blob/master/GNU-AGPL-3.0 ?>
                <span class="text"><a href="http://goteo.org" target="_blank" class="poweredby">Powered by Goteo.org</a></span>
                <?php // You are not allowed to remove this links. If so, you'll make a legal fault regarding the release license. You can read it at https://github.com/Goteo/Goteo/blob/master/GNU-AGPL-3.0 ?>
                <span class="logo"><a href="http://fuentesabiertas.org" target="_blank" class="foundation">FFA</a></span>
                <?php // You are not allowed to remove this links. If so, you'll make a legal fault regarding the release license. You can read it at https://github.com/Goteo/Goteo/blob/master/GNU-AGPL-3.0 ?>
                <span class="logo"><a href="https://github.com/Goteo/Goteo" target="_blank" class="growby">GNU-AGPL-3</a></span>
                <?php // You are not allowed to remove this links. If so, you'll make a legal fault regarding the release license. You can read it at https://github.com/Goteo/Goteo/blob/master/GNU-AGPL-3.0 ?>
            </div>
        </div>
    </div>
</div>
