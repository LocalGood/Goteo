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
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest,
    Goteo\Core\Redirection;

$bodyClass = 'user-profile';
include VIEW_PATH . '/prologue.html.php';
include VIEW_PATH . '/header.html.php';

$user = $this['user'];
$worthcracy = Worth::getAll();

$categories = Interest::getAll($user->id);

if (empty($categories)) {
    throw new Redirection('/user/profile/' . $this['user']->id);
}

$limit = empty($this['category']) ? 6 : 20;

$shares = array();
foreach ($categories as $catId => $catName) {
    $gente = Interest::share($user->id, $catId, $limit);
    if (count($gente) == 0) continue;
    $shares[$catId] = $gente;
}

if (empty($shares)) {
    throw new Redirection('/user/profile/' . $this['user']->id);
}

?>

<?php echo new View(VIEW_PATH . '/user/widget/header.html.php', array('user'=>$user)) ?>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

<div id="main">

    <div class="center sharemates">

        <div class="widget user-mates">
           <!-- lista de categorías -->
            <div class="categories">
                <h3 class="supertitle"><?php echo Text::get('profile-sharing_interests-header');?></h3>
                <script type="text/javascript">
                function displayCategory(categoryId){
                    $(".user-mates").css("display","none");
                    $("#cat" + categoryId).fadeIn("slow");
                    $(".active").removeClass('active');
                    $("#catlist" + categoryId).addClass('active');
                }
                </script>
                <ul>
                    <?php foreach ($categories as $catId=>$catName) : if (count($shares[$catId]) == 0) continue; ?>
                    <li><a id="catlist<?php echo $catId ?>" href="/user/profile/<?php echo $this['user']->id ?>/sharemates/<?php echo $catId ?>" <?php if (!empty($this['category'])) : ?>onclick="displayCategory(<?php echo $catId ?>); return false;"<?php endif; ?> <?php if ($catId == $this['category']) echo 'class="active"'?>><?php echo $catName ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- fin lista de categorías -->
        </div>

        <!-- detalle de categoría (cabecera de categoría) -->
        <?php foreach ($shares as $catId => $sharemates) :
            if (count($sharemates) == 0) continue;
            shuffle($sharemates);
            ?>
            <div class="widget user-mates" id="cat<?php echo $catId;?>" <?php if (!empty($this['category']) && $catId != $this['category']) echo 'style="display:none;"'?>>
                <h4 class="supertitle"><?php echo $categories[$catId] ?></h4>
                <div class="users">
                    <ul>
                    <?php 
                    $cnt = 1;
                    foreach ($sharemates as $mate) :
                        if (empty($this['category']) && $cnt > 6) break;
                    ?>
                        <li class="">
                            <div class="user">
                                <a href="/user/<?php echo htmlspecialchars($mate->user) ?>" class="expand">&nbsp;</a>
                                <a class="avatar" href="/user/<?php echo htmlspecialchars($mate->user) ?>">
                                    <img src="<?php echo $mate->avatar->getLink(43, 43, true) ?>" />
                                </a>
                                <div class="user-right">
                                    <h4><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><?php echo htmlspecialchars(Text::shorten($mate->name,20)) ?></a></h4>
                                    <span class="projects"><?php echo Text::get('regular-projects'); ?> (<?php echo $mate->projects ?>)</span>
                                    <span class="invests"><?php echo Text::get('regular-investing'); ?> (<?php echo $mate->invests ?>)</span>
                                    <span class="profile"><a href="/user/profile/<?php echo htmlspecialchars($mate->user) ?>"><?php echo Text::get('profile-widget-button'); ?></a> </span>
                                </div>
                            </div>
                        </li>
                    <?php 
                    $cnt ++;
                    endforeach; ?>
                    </ul>
                </div>
        <?php if (empty($this['category'])) : ?>
            <a class="more" href="/user/profile/<?php echo $this['user']->id ?>/sharemates/<?php echo $catId ?>"><?php echo Text::get('regular-see_more'); ?></a>
        <?php else : ?>
            <a class="more" href="/user/profile/<?php echo $this['user']->id ?>/sharemates"><?php echo Text::get('regular-see_all'); ?></a>
        <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <!-- fin detalle de categoría (cabecera de categoría) -->
        
    </div>
    <div class="side">
        <?php if (!empty($_SESSION['user'])) echo new View(VIEW_PATH . '/user/widget/investors.html.php', $this) ?>
        <?php echo new View(VIEW_PATH . '/user/widget/user.html.php', $this) ?>
    </div>

</div>

<?php include VIEW_PATH . '/footer.html.php' ?>

<?php include VIEW_PATH . '/epilogue.html.php' ?>
