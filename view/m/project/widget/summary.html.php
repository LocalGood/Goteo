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

$project = $this['project'];

$project->goal        = nl2br(Text::urlink($project->goal));
$project->related     = nl2br(Text::urlink($project->related));

$level = (int) $this['level'] ?: 3;
?>

<div class="widget project-summary">

    <?php if (!empty($project->description)): ?>
    <div class="description">
        <?php echo $project->description; ?>
    </div>
    <?php endif ?>

    <?php if (!empty($project->description_1)): ?>
        <div class="description">
            <?php echo $project->description_1; ?>
        </div>
    <?php endif ?>

    <?php if (!empty($project->description_2)): ?>
        <div class="description">
            <?php echo $project->description_2; ?>
        </div>
    <?php endif ?>

    <?php if (!empty($project->motivation)): ?>
    <div class="motivation">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-motivation'); ?></h<?php echo $level + 1?>>
        <?php echo $project->motivation; ?>
    </div>
    <?php endif ?>

    <?
        echo new View(VIEW_PATH . '/project/widget/video.html.php', array('project' => $project));
    ?>

    <?php if (!empty($project->goal)): ?>
    <div class="goal">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-goal'); ?></h<?php echo $level + 1?>>
        <?php echo $project->goal; ?>
    </div>    
    <?php endif ?>
    
    <?php if (!empty($project->related)): ?>
    <div class="related">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-related'); ?></h<?php echo $level + 1?>>
        <?php echo $project->related ?>
    </div>
    <?php endif ?>

</div>

<?php if ($project->status <= 3): ?>
    <div class="widget project-support_btn">
        <a class="button supportit" href="/project/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
    </div>
<?php endif; ?>

<?php if (!empty($project->id)): ?>
    <div class="widget project-share">
        <h<?php echo $level + 1?> class="title"><?php echo Text::get('overview-field-share-head'); ?></h<?php echo $level + 1?>>
        <?
        echo new View(VIEW_PATH . '/project/widget/share.html.php', array('project' => $project));
        ?>
    </div>
<?php endif ?>
