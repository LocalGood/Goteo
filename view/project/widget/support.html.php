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

    <h<?php echo $level + 1 ?> class="supertitle"><?php echo Text::get('project-support-supertitle'); ?></h<?php echo $level + 1 ?>>

    <?php //var_dump($project);
    switch ($project->tagmark) {
        case 'onrun': // "en marcha"
            echo '<div class="tagmark green">' . Text::get('regular-onrun_mark') . '</div>';
            break;
        case 'keepiton': // "aun puedes"
            echo '<div class="tagmark green">' . Text::get('regular-keepiton_mark') . '</div>';
            break;
        case 'onrun-keepiton': // "en marcha" y "aun puedes"
            echo '<div class="tagmark green twolines"><span class="small"><strong>' . Text::get('regular-onrun_mark') . '</strong><br />' . Text::get('regular-keepiton_mark') . '</span></div>';
            break;
        case 'gotit': // "financiado"
            echo '<div class="tagmark violet">' . Text::get('regular-gotit_mark') . '</div>';
            break;
        case 'success': // "exitoso"
            echo '<div class="tagmark red">' . Text::get('regular-success_mark') . '</div>';
            break;
        case 'fail': // "caducado"
            echo '<div class="tagmark grey">' . Text::get('regular-fail_mark') . '</div>';
            break;
    } ?>

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
        $willclose = date('Y年n月j日', strtotime($project->willclose));

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
        <div class="invest-notice">
            このプロジェクトの挑戦期間は、1stラウンド <?php echo $published; ?>〜<?php echo $until; ?>23:59（<?php echo $project->period_1r; ?>日間）、2ndラウンド<?php echo $willpass; ?>〜<?php echo $willclose; ?>23:59（<?php echo $project->period_2r; ?>日間）です
        </div>
        <?php
    } // if ($project->status == 3) {
    ?>

    <a class="more" href="/project/<?php echo $project->id; ?>/needs"><?php echo Text::get('regular-see_more'); ?></a>

</div>
