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

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$user = $this['user'];

$categories = Interest::getAll($user->id);

$shares = array();
foreach ($categories as $catId => $catName) {
    $shares[$catId] = Interest::share($user->id, $catId, 6);
}


?>
<script type="text/javascript">
function displayCategories(categoryId1){
  var targetDom = '#mates-' + categoryId1;
  $("div.users").css("display","none");
  $(targetDom).fadeIn("slow");

  //自分が最後じゃなかったら、2つ出す。
  if ($(targetDom).next().attr('class') === 'users') {
    var nextDom = '#' + $(targetDom).next().attr('id');
    $(nextDom).fadeIn("slow");
  }
}
</script>
<div class="widget user-mates">
    <!-- categorias -->
    <h3 class="supertitle"><?php echo Text::get('profile-sharing_interests-header'); ?></h3>
    <div class="project-widget-box">
        <?php if (!empty($shares)) { ?>
            <div class="categories">
                <div class="list">
                    <?php $keys = array_keys($categories);?>
                    <ul>
                        <?php
                        $cnt = 0;
                        foreach ($categories as $catId=>$catName) {
                            if (count($shares[$catId]) == 0) {$cnt++;continue;} ?>
                            <li><a href="#" onclick="displayCategories(<?php echo $catId;?>,
                            <?php
                            if(($cnt+1)==count($categories))echo $keys[0];
                            else echo $keys[$cnt+1];
                            $cnt++;
                            ?>
                            ); return false;">
                            <?php echo $catName?></a></li>
                        <?php
                        } ?>
                    </ul>
                </div>
            </div>

            <!-- usuarios sociales -->
            <?php
            // mostramos 2
            $muestra = 1;

            foreach ($shares as $catId => $sharemates) {
                if (count($sharemates) == 0) continue;
                shuffle($sharemates);
                ?>
            <div class="users" id="mates-<?php echo $catId ?>"
            <?php if ($muestra > 2) {echo 'style="display:none;"';} else {$muestra++;} ?>>

                <h4 class="supertitle"><?php echo $categories[$catId] ?></h4>

                <!--pintar usuarios -->
                <ul>
                <?php $c=1; // limitado a 6 sharemates en el lateral
                foreach ($sharemates as $mate){ ?>
                    <li class="activable<?php if(($c%2)==0)echo " nomarginright"?>">
                        <div class="user">
                            <a href="/user/<?php echo htmlspecialchars($mate->user) ?>" class="expand">&nbsp;</a>
                            <div class="avatar">
                                <a href="/user/<?php echo htmlspecialchars($mate->user) ?>">
                                    <img src="<?php echo $mate->avatar->getLink(43, 43, true) ?>" />
                                </a>
                            </div>
                            <h4>
                                <a href="/user/<?php echo htmlspecialchars($mate->user) ?>">
                                <?//php echo htmlspecialchars(Text::recorta($mate->name,20)) ?>
                                <?php echo htmlspecialchars(Text::shorten($mate->name,18)) ?>
                                </a>
                            </h4>
                            <span class="projects">
                                <?php echo Text::get('regular-projects'); ?> (<?php echo $mate->projects ?>)
                            </span>
                            <span class="invests">
                                <?php echo Text::get('regular-investing'); ?> (<?php echo $mate->invests ?>)
                            </span>
                        </div>
                    </li>
                <?php $c++;
                } ?>

                </ul>

                <a class="more" href="/user/profile/<?php echo $this['user']->id ?>/sharemates/<?php echo $catId ?>"><?php echo Text::get('regular-see_more'); ?></a>

            </div>
            <?php } ?>
        <?php } else { ?>
            <p class="not-has-categories"><?php echo Text::get('project-not-interest-category') ?></p>
        <?php } ?>
    </div>
</div>
