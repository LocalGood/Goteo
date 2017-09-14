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
    Goteo\Model\Project;

$level = (int) $this['level'] ?: 3;

$horizontal = !empty($this['horizontal']);
$big = !empty($this['big']);
$activable = !empty($this['activable']);

$project = $this['project'];

$minimum    = $project->mincost;
$optimum    = $project->maxcost;
$reached    = $project->invested;
//$supporters = count($project->investors);
$supporters = $project->getTotalInvestors($project->id);
$days       = $project->days;

// PHP la pifia (y mucho) con los cálculos en coma flotante
if ($reached >= $minimum) {
    $minimum_done = round(($reached / $minimum) * 100);
    $minimum_done_per = round(($reached / $minimum) * 100);
    $minimum_left = 0;
    
} else {
    
    $minimum_done = min(100, round(($reached / $minimum) * 100));
    $minimum_done_per = round(($reached / $minimum) * 100);
    $minimum_left = max(0, round((1 - $reached / $minimum) * 100));
    
    if ($minimum_done >= 100) {
        // No muestres 100 si falta aunque sea un céntimo
        $minimum_done = 99;
    }
}

$more  = $optimum - $minimum;
$over = $reached - $minimum;

if ($over > 0) {
    
    if ($over >= $more) {
        $optimum_done = 100;
    } else {
        $optimum_done = min(100, round($over / ($optimum - $minimum)));
        
        if ($optimum_done >= 100) {
            $optimum_done = 99;
        }
    }    
    
} else {
    $optimum_done = 0;
}

$optimum_left = 100 - $optimum_done;

$minimum_ratio =  min(100, round(($minimum / $optimum) * 100));

?>
    <div class="meter <?php echo $horizontal ? 'hor' : 'ver'; echo $big ? ' big' : ''; echo $activable ? ' activable' : ''; ?> meter_2_record">

        <dl class="reached-bar">
            <dt class="reached"><span><?php echo Text::get('project-view-metter-got'); ?></span></dt>
            <dd class="reached"><strong><?php echo \amount_format($reached) ?></strong><span>円</span></dd>

            <dt class="supporters"><span><?php echo Text::get('project-view-metter-investors'); ?></span></dt>
            <dd class="supporters"><strong><?php echo number_format($supporters) ?></strong><span>人</span></dd>
        </dl>

        <? if ($big): ?>
            <h<?php echo $level ?> class="title investment"><?php echo Text::get('project-view-metter-investment'); ?></h<?php echo $level ?>>
        <? endif; ?>
        <?php if ($activable) : ?><h<?php echo $level ?> class="title obtained"><?php echo Text::get('project-view-metter-got'); ?></h<?php echo $level ?>><?php endif; ?>
        <div class="graph">
            <div class="optimum">
                 <div class="left" style="<?php echo $horizontal ? 'width' : 'width' ?>: <?php echo number_format($optimum_left) ?>%"></div>
                 <div class="done" style="<?php echo $horizontal ? 'width' : 'width' ?>: <?php echo number_format($optimum_done) ?>%"></div>
            </div>
            <div class="minimum" style="<?php echo $horizontal ? 'width' : 'width' ?>: <?php echo number_format($minimum_ratio) ?>%">
                <div class="left" style="<?php echo $horizontal ? 'width' : 'width' ?>: <?php echo number_format($minimum_left) ?>%"><!-- <strong><?php echo number_format($minimum_left) ?>%</strong> --></div>
                <div class="done" style="<?php echo $horizontal ? 'width' : 'width' ?>: <?php echo number_format($minimum_done) ?>%"><strong><?php echo number_format($minimum_done_per) ?>%</strong></div>
            </div>
        </div>

        <dl class="amount-bar">
            <dt class="minimum" style="<?php echo $horizontal ? 'width' : '' ?>: <?php echo number_format($minimum_ratio) ?>%"><span><?php echo Text::get('project-view-metter-minimum'); ?></span></dt>
            <dd class="minimum" style="<?php echo $horizontal ? 'width' : '' ?>: <?php echo number_format($minimum_ratio) ?>%"><strong><?php echo \amount_format($minimum) ?></strong><span>円</span></dd>

            <dt class="optimum"><?php echo Text::get('project-view-metter-optimum'); ?></dt>
            <dd class="optimum"><strong><?php echo \amount_format($optimum) ?></strong><span>円</span></dd>
        </dl>

        <?/*php if ($activable) : */?>
        <dl class="percent">
            <dt>達成率：</dt>
            <dd><strong><?php echo number_format($minimum_done_per) ?></strong>%</dd>
        </dl>
        <?/*php endif; */?>

    </div>
