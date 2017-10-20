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
    Goteo\Model\Project\Category,
    Goteo\Model\Image;

$project = $this['project'];
$level = $this['level'] ?: 3;

if ($this['global'] === true) {
    $blank = ' target="_parent"';
} else {
    $blank = '';
}
$categories = Category::getNames($project->id, 2);
?>

<div class="widget project activable heightLine-project<?php if (isset($this['balloon'])) echo ' balloon' ?>">
    <?php if (isset($this['balloon'])): ?>
    <div class="balloon"><?php echo $this['balloon'] ?></div>
    <?php endif ?>
        <div class="image">
            <?
            $project->gallery = Goteo\Model\Project\Image::getGallery($project->id);
            ?>
            <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
                <a class="link" href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><img alt="<?php echo $project->name ?>" src="<?php echo current($project->gallery)->getLink(260, 135, true) ?>" /></a>
            <?php endif ?>

            <?php switch ( $project->tagmark ) {
                case 'onrun': // "en marcha"
                    echo '<div class="tagmark green">' . Text::get( 'regular-onrun_mark' ) . '</div>';
                    break;
                case 'keepiton': // "aun puedes"
                    echo '<div class="tagmark green">' . Text::get( 'regular-keepiton_mark' ) . '</div>';
                    break;
                case 'onrun-keepiton': // "en marcha" y "aun puedes"
                    //                echo '<div class="tagmark green">' . Text::get('regular-onrun_mark') . '</div>';
                    echo '<div class="tagmark green twolines"><span class="small"><strong>' . Text::get( 'regular-onrun_mark' ) . '</strong><br />' . Text::get( 'regular-keepiton_mark' ) . '</span></div>';
                    break;
                case 'gotit': // "financiado"
                    echo '<div class="tagmark violet">' . Text::get( 'regular-gotit_mark' ) . '</div>';
                    break;
                case 'success': // "exitoso"
                    echo '<div class="tagmark red">' . Text::get( 'regular-success_mark' ) . '</div>';
                    break;
                case 'fail': // "caducado"
                    echo '<div class="tagmark gray">' . Text::get( 'regular-fail_mark' ) . '</div>';
                    break;
            } ?>
        </div>

        <div class="project-details">
            <h<?php echo $level ?> class="title">
                <a class="link" href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><?php echo htmlspecialchars(Text::shorten($project->name,50)) ?></a>
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
				        echo $sep.'<a href="/discover/results/'.$key.'">'.htmlspecialchars($value).'</a>';
				        $sep = ', '; endforeach; ?>
				</div>
	        <?php endif; ?>

        </div>

        <?php echo new View('view/project/meter_hor.html.php', array('project' => $project)) ?>

        <div class="want-support">
            <div>
                <h<?php echo $level + 1 ?>>必要な支援</h<?php echo $level + 1 ?>>
                <img src="<?php echo SRC_URL ?>/view/images/icon_money.png" alt="資金">
                <img src="<?php echo SRC_URL ?>/view/images/icon_skill.png" alt="スキル">
            </div>
            <div>
                <h<?php echo $level + 1 ?>>残り</h<?php echo $level + 1 ?>>
                <div class="days"><strong><?php echo number_format($project->days) ?></strong><span><?php echo Text::get('regular-days'); ?></span></div>
            </div>

        </div>
    
</div>
