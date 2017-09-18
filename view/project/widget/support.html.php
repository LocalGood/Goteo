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

        <?php if ($project->status == 3): ?>
            <?php if (!empty($project->round)) : ?>
                <span class="round"><?php echo $project->round; if ($project->round == 1 ){ echo 'st '; } else { echo 'nd '; }; echo Text::get('regular-round'); ?></span>
            <?php endif; ?>
        <?php else: ?>
            <?php echo Text::get('regular-fail_mark'); ?>
        <?php endif; ?>
    </h<?php echo $level + 1 ?>>


    <div class="project-widget-box<?php echo $status = $project->round ? '' : ' end'; ?>">
    <?php echo new View('view/project/meter.html.php', array('project' => $project, 'level' => $level) ) ?>

        <?php
        if ($project->status == 3):

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
            <dl class="invest-notice">
                <dt>1stラウンド: </dt><dd><?php echo $published; ?>〜<?php echo $until; ?>23:59</dd>
                <dt>2ndラウンド: </dt><dd><?php echo $willpass; ?>〜<?php echo $willclose; ?>23:59</dd>
            </dl>
            <?php
        endif;
        ?>

    <?php if ($project->status <= 3): ?>
        <div class="buttons">
            <a class="button violet supportit" href="/project/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
        </div>
    <?php endif; ?>
    </div>

</div>
