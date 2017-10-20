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

$project = $this['skillmatching'];
$week = array('日','月','火','水','木','金','土');
// $willpass = strtotime($project->willpass);
?>
<div class="widget project-support collapsable" id="project-support">

    <h<?php echo $level + 1 ?> class="supertitle"><?php echo Text::get('project-support-supertitle'); ?></h<?php echo $level + 1 ?>>

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
        <div class="invest-notice">
            このスキルマッチングの募集期間は <?php echo $published; ?>〜<?php echo $willclose; ?>23:59 の <?php echo ( $project->period_1r + $project->period_2r); ?>日間 です。
        </div>
        <?php
    } // if ($project->status == 3) {
    ?>
    <div class="buttons-sm clearfix">
        <a class="button violet supportit" href="/skillmatching/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it-sm'); ?></a>
    </div>
</div>