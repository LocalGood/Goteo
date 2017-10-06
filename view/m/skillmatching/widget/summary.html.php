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

//tratamos los saltos de linea y los links en las descripciones del proyecto
//$project->description = nl2br(Text::urlink($skillmatching->description));
//$skillmatching->about       = nl2br(Text::urlink($skillmatching->about));
//$skillmatching->motivation  = nl2br(Text::urlink($skillmatching->motivation));
$skillmatching->goal        = nl2br(Text::urlink($skillmatching->goal));
$skillmatching->related     = nl2br(Text::urlink($skillmatching->related));

$level = (int) $this['level'] ?: 3;
?>

<div class="widget project-summary">

    <?php if (!empty($skillmatching->description)): ?>
    <div class="description">
        <?php echo $skillmatching->description; ?>
    </div>
    <?php endif ?>

    <?php if (!empty($skillmatching->motivation)): ?>
    <div class="motivation">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-motivation-sm'); ?></h<?php echo $level + 1?>>
        <?php echo $skillmatching->motivation; ?>
    </div>
    <?php endif ?>

    <?
        echo new View(VIEW_PATH . '/skillmatching/widget/video.html.php', array('skillmatching' => $skillmatching));
    ?>

    <?php if (!empty($skillmatching->goal)): ?>
    <div class="goal">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-goal-sm'); ?></h<?php echo $level + 1?>>
        <?php echo $skillmatching->goal; ?>
    </div>    
    <?php endif ?>
    
    <?php if (!empty($skillmatching->related)): ?>
    <div class="related">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-related-sm'); ?></h<?php echo $level + 1?>>
        <?php echo $skillmatching->related ?>
    </div>
    <?php endif ?>

</div>

<?php if ($skillmatching->status <= 3): ?>
    <div class="widget project-support_btn">
        <a class="button supportit" href="/skillmatching/<?php echo $skillmatching->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
    </div>
<?php endif; ?>

<?php if (!empty($skillmatching->id)): ?>
    <div class="widget project-share">
        <h<?php echo $level + 1?> class="title"><?php echo Text::get('overview-field-share-head'); ?></h<?php echo $level + 1?>>
        <?
        echo new View(VIEW_PATH . '/skillmatching/widget/share.html.php', array('skillmatching' => $skillmatching));
        ?>
    </div>
<?php endif ?>

