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
<?php
if (file_exists('omniconfig/footer.html')):
    include 'omniconfig/footer.html';
else:
	$apikeys = json_decode(file_get_contents( '/var/www/html/omniconfig/apikeys.json'));
?>
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $('.scroll-pane').jScrollPane({showArrows: true});
      });
    </script>
    <footer class="footer">

        <div class="footer__upper">
            <div class="c-w1096">
                <div class="footer__upper_left">
                    <div class="footer__logo">
                        <img src="/view/images/footer_logo.png" alt="<?php echo GOTEO_META_TITLE; ?>ロゴ">
                    </div>
                    <ul class="footer__sns_link">
                        <li class="rss"><a href="<?php echo LG_BASE_URL_WP . '/feed/'; ?>" target="_blank"><img src="/view/images/rss_btn.png" alt="rss" /></a></li>
                        <li class="g_plus"><a href="<?php echo $apikeys->snslinks->googleplus; ?>" target="_blank"><img src="/view/images/gplus_btn.png" alt="google plus" /></a></li>
                        <li class="tw_btn"><a href="<?php echo $apikeys->snslinks->twitter; ?>" target="_blank"><img src="/view/images/tw_btn.png" alt="twitter" /></a></li>
                        <li class="fb_btn"><a href="<?php echo $apikeys->snslinks->facebook; ?>" target="_blank"><img src="/view/images/fb_btn.png" alt="facebook" /></a></li>
                    </ul>
                    <a class="footer__integration_site" href="<?php echo $apikeys->other->integrationurl; ?>">LOCAL GOOD 地域課題プラットフォーム</a>
                </div>
                <ul class="footer__upper_right">
                    <li>
                        <span class="footer__upper_second_title">ご利用にあたって</span>
                        <ul>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/about/"><?php echo GOTEO_META_TITLE; ?>について</a></li>
                            <li class="syoutorihikihou"><a href="<?php echo LG_BASE_URL_WP; ?>/syoutorihikihou/">特定商取引法に基づく表記</a></li>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/privacypolicy/">プライバシーポリシー</a></li>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/user_guide/">ユーザーガイド</a></li>
                            <li><a href="<?php echo $apikeys->other->integrationurl; ?>/riyou_kiyaku_menu/">利用規約</a></li>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/contact/">お問い合わせ</a></li>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/authors/">記者一覧</a></li>
                        </ul>
                    </li>
                    <li>
                        <span class="footer__upper_second_title">地域を知る</span>
                        <span class="footer__upper__third_title">記事</span>
                        <ul>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/lgnews/">ニュース</a></li>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/event/">みんなの拠点/イベント</a></li>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/lgplayer/">人/団体</a></li>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/data/">データ</a></li>
                        </ul>
                        <span class="footer__upper__third_title">みんなの声</span>
                        <ul>
                            <li><a href="<?php echo LOCALGOOD_WP_BASE_URL; ?>/subject/">投稿一覧</a></li>
                            <?php /* if( strpos($_SERVER['HTTP_HOST'], 'sendai') !== FALSE ): ?>
                            <li><a href="<?php echo LOCALGOOD_WP_BASE_URL; ?>/submit_subject/">あなたの声を投稿する</a></li>
                            <?php endif;*/ ?>
                        </ul>
                    </li>
                    <li>
                        <span class="footer__upper_second_title">応援する</span>
                        <ul>
                            <li><a href="/discover/">プロジェクト一覧</a></li>
                            <li><a href="<?php echo LG_BASE_URL_WP; ?>/challenge/">プロジェクトを立ち上げる</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer__under">
            <div class="c-w1096">
                <ul>
                    <li><a href="http://yokohamalab.jp/" target="_blank"><img src="/view/images/logo_f_labo.jpg" alt="NPO法人 横浜コミュニティデザイン・ラボ"></a></li>
                    <li><a href="http://www.city.yokohama.lg.jp/seisaku/" target="_blank"><img src="/view/images/logo_f_open_yokohama.png" alt="画像：OPEN YOKOHAMA"></a></li>
                    <li><a href="http://goteo.org/" target="_blank"><img src="/view/images/logo_f_goteo.png" alt="画像：Fundacion Goteo"></a></li>
                    <li><a href="http://www.ycu-coc.jp/" target="_blank"><img src="/view/images/logo_f_YCU.png" alt="画像：横浜市立大学<br>影山摩子弥研究室"></a></li>
                    <li><a href="http://labo.wtnv.jp/" target="_blank"><img src="/view/images/logo_f_wtnv.png" alt="画像：首都大学東京<br>渡邉英徳研究室"></a></li>
                    <li><a href="http://designcat.co/" target="_blank"><img src="/view/images/logo_f_design_cat.png" alt="画像：Design Cat"></a></li>
                    <li><a href="http://info-lounge.jp/" target="_blank"><img src="/view/images/logo_f_info_lounge.png" alt="画像：インフォ・ラウンジ合同会社"></a></li>
                    <li><a href="http://www.accenture.com/jp-ja/Pages/index.aspx" target="_blank"><img src="/view/images/logo_f_accenture.png" alt="画像：アクセンチュア株式会社"></a></li>
                </ul>
            </div>
            <p class="footer__copyright">
                <span>COPYRIGHT© <?php echo GOTEO_META_TITLE; ?>. Some rights reserved.</span>
            </p>
        </div>
    </footer>
<?php
endif;
?>

