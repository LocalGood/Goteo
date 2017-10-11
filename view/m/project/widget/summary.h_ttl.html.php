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

use Goteo\Model\Project\Category,
    Goteo\Model\Project\Skill,
    Goteo\Library\Text;

$project = $this['project'];

$categories = Category::getNames($project->id);
$skills = Skill::getNames($project->id);

$level = (int) $this['level'] ?: 3;
?>
    <?php  if (count($project->gallery) > 1) : ?>
		<script type="text/javascript" >
			$(function(){
				$('#prjct-gallery').slides({
					container: 'prjct-gallery-container',
					paginationClass: 'slderpag',
					generatePagination: false,
					play: 0
				});
			});
		</script>
    <?php endif; ?>

<div class="widget project-summary h_ttl">
    
    <h<?php echo $level ?>><?php echo htmlspecialchars($project->name) ?></h<?php echo $level ?>>

    <?php
    $_value = '/project/' . $project->id;
    $_url = urldecode($_SERVER['REQUEST_URI']);
    if(strstr($_url,$_value) && preg_match('/^\/project\/((?!\/).)*$/',$_url)):
        ?>
        <div class="social_bookmark">
            <div id="twitter">
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
            <div id="facebook">
                <div class="fb-like" data-href="<?php echo $ogmeta['url']; ?>" data-layout="button_count" data-action="recommend" data-show-faces="false" data-share="true"></div>
            </div>

            <div class="g-plusone" data-size="medium" data-width="60"></div>
            <script type="text/javascript">
                window.___gcfg = {lang: 'ja'};

                (function() {
                    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                    po.src = 'https://apis.google.com/js/platform.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                })();
            </script>
        </div><!-- #social_bookmark -->
    <?php
    endif;
    ?>

    <div class="project-subtitle"><?php echo htmlspecialchars($project->subtitle) ?></div>

    <?php if(!empty($categories)): ?>
    <div class="categories"><h3><?php echo Text::get('project-view-categories-title'); ?></h3>
        <?php $sep = ''; foreach ($categories as $key=>$value) :
            echo $sep.'<a href="/discover/results/'.$key.'">'.htmlspecialchars($value).'</a>';
            $sep = ', '; endforeach; ?>
    </div>
    <?php endif;?>

</div>