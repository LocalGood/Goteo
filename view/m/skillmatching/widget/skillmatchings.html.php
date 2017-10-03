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
    Goteo\Model\Skillmatching\Category,
    Goteo\Model\Skillmatching\Skill,
    Goteo\Model\Invest,
    Goteo\Model\Image;

$project = $this['skillmatching'];
$level = $this['level'] ?: 3;

if ($this['global'] === true) {
    $blank = ' target="_blank"';
} else {
    $blank = '';
}

$categories = Category::getNames($project->prefixed_id, 2);

//si llega $this['investor'] sacamos el total aportado para poner en "mi aporte"
if (isset($this['investor']) && is_object($this['investor'])) {
    $investor = $this['investor'];
    $invest = Invest::supported($investor->id, $project->prefixed_id);
}
?>
<div class="widget project skillmatching activable<?php if (isset($this['balloon'])) echo ' balloon' ?>">
	<!--<a href="<?php echo SITE_URL ?>/skillmatching/<?php echo $project->id ?>" class="expand"></a>-->
    <?php if (isset($this['balloon'])): ?>
    <div class="balloon"><?php echo $this['balloon'] ?></div>
    <?php endif ?>

    <div class="image">
        <!--<span class="sm-icon"></span>-->
        <?
        $project->gallery = Goteo\Model\Skillmatching\Image::getGallery($project->id);
        ?>

        <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
        <a href="<?php echo SITE_URL ?>/skillmatching/<?php echo $project->id ?>" target="_parent"><img alt="<?php echo $project->name ?>" src="<?php echo current($project->gallery)->getLink(500, 285, true) ?>" /></a>
        <?php endif ?>
    </div>

    <div class="project-details">
        <h<?php echo $level ?> class="title">
            <a href="<?php echo SITE_URL ?>/skillmatching/<?php echo $project->id ?>" target="_parent"><?php echo htmlspecialchars(Text::shorten($project->name,50)) ?></a>
        </h<?php echo $level ?>>

        <div class="author">
            <a class="link" href="<?php echo SITE_URL ?>/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"<?php echo $blank; ?>>
                    <span class="author-img">
                        <img src="<?php echo $project->user->avatar->getLink(50, 50, true); ?>" alt="<?php echo $project->user->name; ?>">
                    </span>
                <span><?php echo htmlspecialchars(Text::shorten($project->user->name,40)) ?></span>
            </a>
        </div>

        <?php if (!empty($categories)): ?>
            <div class="categories">
                <?php $sep = ''; foreach ($categories as $key=>$value) :
                    echo $sep.htmlspecialchars($value);
                    $sep = ', '; endforeach; ?>
            </div>
        <?php endif ?>
    </div>

    <?php echo new View(VIEW_PATH . '/skillmatching/meter_hor.html.php', array('skillmatching' => $project)) ?>

    <div class="want-support">
        <div>
            <h<?php echo $level + 1 ?>>必要な支援</h<?php echo $level + 1 ?>>
            <img src="<?php echo SRC_URL ?>/view/images/icon_skill.png" alt="資金">
        </div>
        <div>
            <h<?php echo $level + 1 ?>>残り</h<?php echo $level + 1 ?>>
            <div class="days"><strong><?php echo number_format($days) ?></strong><span><?php echo Text::get('regular-days'); ?></span></div>
        </div>

    </div>

</div>
