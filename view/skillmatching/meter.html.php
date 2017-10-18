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

use Goteo\Library\Text;

$level = (int) $this['level'] ?: 3;

$horizontal = !empty($this['horizontal']);
$big = !empty($this['big']);
$activable = false;
$project = $this['skillmatching'];

$minimum    = $project->mincost;
$optimum    = $project->maxcost;
$reached    = $project->invested;
$supporters = count($project->investors);
$days       = $project->total_days;

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

?>    <div class="meter <?php echo $horizontal ? 'hor' : 'ver'; echo $big ? ' big' : ''; echo $activable ? ' activable' : ''; ?>">
        <dl>
            <dt class="days"><span><?php echo Text::get('project-view-metter-days'); ?></span></dt>
            <dd class="days"><strong><?php echo number_format($days) ?></strong><span><?php echo Text::get('regular-days'); ?></span></dd>

            <dt class="supporters"><span><?php echo Text::get('project-view-metter-investors'); ?></span></dt>
            <dd class="supporters"><strong><?php echo number_format($supporters) ?></strong><span>人</span></dd>

        </dl>

        <?php if ($activable) : ?>
        <div class="obtained">
            <strong><?php echo \amount_format($reached) ?> <span>円</span></strong>
            <span class="percent"><?php echo number_format($minimum_done_per) ?>%</span>
        </div>
        <?php endif; ?>

    </div>

