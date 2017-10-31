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
    Goteo\Core\View;

$skillmatching = $this['skillmatching'];
$level = (int) $this['level'] ?: 3;

$share_title = $skillmatching->name;

$share_url = SITE_URL . '/skillmatching/' . $skillmatching->id;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/home?status=' . urlencode($share_title . ': ' . $share_url . ' #' . LG_PLACE_LABEL .' @' . LG_TWITTER);

?>
<script type="text/javascript">
            jQuery(document).ready(function ($) { 
				$(".a-proyecto").fancybox({
					'titlePosition'		: 'inside',
					'transitionIn'		: 'none',
					'transitionOut'		: 'none'
				});
			});
</script>

<?php
$_value = '/skillmatching/' . $skillmatching->id;
$_url = urldecode($_SERVER['REQUEST_URI']);
if(strstr($_url,$_value) && preg_match('/^\/skillmatching\/(.*)$/',$_url)): ?>

    <div class="social_bookmark">
        <div id="twitter" class="twitter">
            <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
            <script>
              !function(d,s,id){
                var js,fjs=d.getElementsByTagName(s)[0];
                if(!d.getElementById(id)){
                  js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";
                  fjs.parentNode.insertBefore(js,fjs);
                }
              }(document,"script","twitter-wjs");
            </script>
        </div>
        <div id="facebook" class="facebook">
            <div class="fb-like" data-href="<?php echo $ogmeta['url']; ?>" data-layout="button_count" data-action="recommend" data-show-faces="false" data-share="true"></div>
        </div>
        <div class="gPlus">
            <div id="g-plusone" class="g-plusone" data-size="medium" data-width="60"></div>
            <script type="text/javascript">
              window.___gcfg = {lang: 'ja'};

              (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/platform.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
              })();
            </script>
        </div>
        <div id="embed">
            <a target="_blank" class="a-proyecto" href="#proyecto" title=""><img src="/view/images/embed_btn.png" alt="埋め込み"></a>
            <div style="display: none;">
                <div id="proyecto" class="widget projects" style="width:600px;height:600px;overflow:hidden;">
                    <h2 class="widget-title"><?php echo Text::get('project-spread-widget_title'); ?></h2>
                    <div class="widget-porject-legend"><?php echo Text::get('project-spread-widget_legend'); ?></div>
                    <?php echo new View('view/skillmatching/widget/embed.html.php', array('skillmatching'=>$skillmatching)) ?>
                </div>
            </div>
        </div>
    </div><!-- .social_bookmark -->

<?php endif; ?>
