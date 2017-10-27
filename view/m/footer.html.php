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
$apikeys    = json_decode(file_get_contents( '/var/www/html/omniconfig/apikeys.json'));

if (file_exists('omniconfig/footer-sp.html')):
    include 'omniconfig/footer-sp.html';
else:
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
                <a href="<?php echo LG_BASE_URL_WP; ?>/about/">
                    <?php echo GOTEO_META_TITLE; ?>について
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/syoutorihikihou/">
                    特定商取引法について
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/privacypolicy/">
                    プライバシーポリシー
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/user_guide/">
                    ユーザーガイド
                </a>
            </li>
            <li>
                <a href="<?php echo $apikeys->other->integrationurl; ?>/riyou_kiyaku_menu/">
                    利用規約
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/contact/">
                    お問い合わせ
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/authors/">
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
                <a href="<?php echo LG_BASE_URL_WP; ?>/lgnews/">
                    ニュース
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/event/">
                    イベント
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/lgplayer/">
                    人/団体
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/data/">
                    データ
                </a>
            </li>
        </ul>
        <span>
            みんなの声
        </span>
        <ul>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/subject/">
                    投稿一覧
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/submit_subject/">
                    あなたの声を投稿する
                </a>
            </li>
        </ul>

        <div class="footer__link-title">
            応援する
        </div>
        <ul>
            <li>
                <a href="<?php echo LG_BASE_URL_GT; ?>/discover">
                    プロジェクト一覧
                </a>
            </li>
            <li>
                <a href="<?php echo LG_BASE_URL_WP; ?>/challenge/">
					プロジェクトを立ち上げる
                </a>
            </li>
        </ul>
    </section>

    <section class="footer_bottom">
        <div class="footer_logo">
            <a href="<?php echo LG_BASE_URL_WP; ?>">
                <img src="/view/images/s-footer-logo.png" alt="LOCAL GOOD">
            </a>
        </div>
        <ul class="sns-area">
            <li>
                <a href="http://yokohama.localgood.jp/feed/" target="_blank">
                    <img src="/view/images/s-footer-sns-icon01.png" alt="rss">
                </a>
            </li>
            <li>
                <a href="https://plus.google.com/112981975493826894716/posts" target="_blank">
                    <img src="/view/images/s-footer-sns-icon02.png" alt="google+">
                </a>
            </li>
            <li>
                <a href="https://twitter.com/LogooYOKOHAMA" target="_blank">
                    <img src="/view/images/s-footer-sns-icon03.png" alt="Twitter">
                </a>
            </li>
            <li>
                <a href="https://www.facebook.com/LOCALGOODYOKOHAMA" target="_blank">
                    <img src="/view/images/s-footer-sns-icon04.png" alt="facebook">
                </a>
            </li>
        </ul>
        <div class="link">
            <span>
                >
            </span>
            <a href="<?php echo $apikeys->other->integrationurl; ?>">
                LOCAL GOOD 地域課題プラットフォーム
            </a>
        </div>
    </section>
</footer>

    <div class="sp_footer_logo-area">
        <ul class="clearfix">
            <li class="left">
                <a href="http://yokohamalab.jp/" target="_blank">
                    <img src="/view/images/logo_f_labo.jpg" alt="NPO法人 横浜コミュニティデザイン・ラボ">
                </a>
            </li>
            <li class="left">
                <a href="http://www.city.yokohama.lg.jp/seisaku/" target="_blank">
                    <img src="/view/images/s-footer-logo03.png" alt="画像：OPEN YOKOHAMA">
                </a>
            </li>
            <li class="left">
                <a href="http://goteo.org/" target="_blank">
                    <img src="/view/images/s-footer-logo04.png" alt="画像：Fundacion Goteo">
                </a>
            </li>
            <li class="left">
                <a href="http://www.ycu-coc.jp/" target="_blank">
                    <img src="/view/images/s-footer-logo05.png" alt="画像：横浜市立大学影山摩子弥研究室">
                </a>
            </li>
            <li class="left">
                <a href="http://labo.wtnv.jp/" target="_blank">
                    <img src="/view/images/s-footer-logo06.png" alt="画像：首都大学東京渡邉英徳研究室">
                </a>
            </li>
            <li class="left">
                <a href="http://designcat.co/" target="_blank">
                    <img src="/view/images/s-footer-logo07.png" alt="画像：Design Cat">
                </a>
            </li>
            <li class="left">
                <a href="http://info-lounge.jp/" target="_blank">
                    <img src="/view/images/s-footer-logo08.jpg" alt="画像：インフォ・ラウンジ合同会社">
                </a>
            </li>
            <li class="left">
                <a href="http://www.accenture.com/jp-ja/Pages/index.aspx" target="_blank">
                    <img src="/view/images/s-footer-logo02.png" alt="画像：アクセンチュア株式会社">
                </a>
            </li>
        </ul>
        <div class="cw">
            COPYRIGHT© LOCAL GOOD YOKOHAMA. Some rights reserved.
        </div>
    </div>
    <?php
endif;
?>
