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
    Goteo\Model\License;

$level = (int) $this['level'] ?: 3;

$project = $this['skillmatching'];

$licenses = array();

foreach (License::getAll() as $l) {
    $licenses[$l->id] = $l;
}

if (empty($project->social_rewards) && empty($project->individual_rewards))
    return '';


uasort($project->individual_rewards,
    function ($a, $b) {
        if ($a->amount == $b->amount) return 0;
        return ($a->amount > $b->amount) ? 1 : -1;
        }
    );
?>
<div class="widget project-rewards-summary" id="rewards-summary">

    <h<?php echo $level ?> class="supertitle"><?php echo Text::get('skillmatching-rewards-supertitle'); ?></h<?php echo $level ?>>

    <?php if (!empty($project->individual_rewards)) : ?>
        <div class="individual">
            <h<?php echo $level+1 ?> class="title"><?php echo Text::get('skillmatching-rewards-individual_reward-title'); ?></h<?php echo $level+1 ?>>
            <ul>
                <?php foreach ($project->individual_rewards as $individual) : ?>
                    <li class="<?php echo $individual->icon ?>">
                        <h<?php echo $level + 2 ?> class="name"><?php echo htmlspecialchars($individual->reward) ?></h<?php echo $level + 2 ?>
                        <p><?php echo htmlspecialchars($individual->description)?></p>

                        <div class="buttons">
                            <a class="button violet supportit" href="/skillmatching/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it-sm'); ?></a>
                        </div>

                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

</div>