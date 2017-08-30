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
    Goteo\Library\Text;

$level = (int) $this['level'] ?: 3;

$project = $this['project'];
$week = array('日','月','火','水','木','金','土');
// $willpass = strtotime($project->willpass);
?>
<div class="widget project-support collapsable" id="project-support">

    <h<?php echo $level + 1 ?> class="supertitle">

        <?php if ($project->status < 3): ?>
            <?php if (!empty($project->round)) : ?>
                <span class="round"><?php echo $project->round; if ($project->round == 1 ){ echo 'st '; } else { echo 'nd '; }; echo Text::get('regular-round'); ?></span>
            <?php endif; ?>
            <?php if (!empty($project->days) && $project->days > 0) : ?>
                <span class="days"><?php echo Text::get('regular-remaining'); ?><strong><?php echo number_format($project->days) ?></strong><span><?php echo Text::get('regular-days'); ?></span></span>
            <?php endif; ?>
        <?php else: ?>
            <?php echo Text::get('regular-fail_mark'); ?>
        <?php endif; ?>
    </h<?php echo $level + 1 ?>>


    <div class="project-widget-box<?php echo $status = $project->round ? '' : ' end'; ?>">
    <?php echo new View('view/project/meter.html.php', array('project' => $project, 'level' => $level) ) ?>

        <div class="buttons">
            <a class="button violet supportit" href="/project/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
            <?/*php if ($project->status == 3) : // boton apoyar solo si esta en campaña ?>
            <a class="button violet supportit" href="/project/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
            <?php else : ?>
            <a class="button view" href="/project/<?php echo $project->id ?>/updates"><?php echo Text::get('regular-see_blog'); ?></a>
            <?php endif; */?>
        </div>
    </div>
    <?php
    if ($project->status == 3) {

        $published = date('Y年n月j日', strtotime($project->published));
        $willclose = date('Y年n月j日', strtotime("-1 minute",strtotime($project->willclose)));

        if (($project->round) == 1) {
            $willpass = date('Y年n月j日', strtotime($project->willpass));
            $until = date('Y年n月j日', strtotime("-1 minute",strtotime($project->willpass)));
        } else {
            $willpass = date('Y年n月j日', strtotime($project->passed));
            $until = date('Y年n月j日', strtotime("-1 minute",strtotime($project->passed)));
        }
        $period_1r = $project->period_1r;
        $period_2r = $project->period_2r;

        ?>
        <?/*php todo: 下の1行は募集中じゃないと出ない。ここの見た目をどうするか */?>
        <div class="invest-notice">
            このプロジェクトの挑戦期間は、1stラウンド <?php echo $published; ?>〜<?php echo $until; ?>23:59（<?php echo $project->period_1r; ?>日間）、2ndラウンド<?php echo $willpass; ?>〜<?php echo $willclose; ?>23:59（<?php echo $project->period_2r; ?>日間）です
        </div>
        <?php
    } // if ($project->status == 3) {
    ?>

</div>