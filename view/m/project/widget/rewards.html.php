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

$project = $this['project'];

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
<div class="widget project-rewards collapsable" id="project-rewards">
    
    <h<?php echo $level + 1 ?> class="supertitle"><span><?php echo Text::get('project-rewards-supertitle'); ?></span></h<?php echo $level + 1?>>

    <div class="project-widget-box">

        <?php if (!empty($project->individual_rewards)) : ?>
        <div class="individual">
            <h<?php echo $level+2 ?> class="title"><?php echo Text::get('project-rewards-individual_reward-title'); ?></h<?php echo $level+2 ?>>
            <ul>
            <?php foreach ($project->individual_rewards as $individual) : ?>
            <li class="<?php echo $individual->icon ?>">
                <div class="amount"><?php echo Text::get('regular-investing'); ?> <span><?php echo \amount_format($individual->amount); ?>円</span></div>
                <h<?php echo $level + 3 ?> class="name"><?php echo htmlspecialchars($individual->reward) ?></h<?php echo $level + 3 ?>>
                <?php
                if (!empty($individual->image)):
                    $img_src = $individual->image->getLink(200,200,false);
                    ?>
                    <img src="<?php echo $img_src ?>" alt="<?php echo htmlspecialchars($individual->reward) ?>" />
                    <?php
                endif;
                ?>
                <?php if (!empty($individual->units)):
                    $units = ($individual->units - $individual->taken);
                    ?>
                    <p class="remain"><strong><?php echo Text::get('project-rewards-individual_reward-limited'); ?> <?php echo $units; ?></strong></p>
                <?php endif; ?>
                <p><?php echo nl2br(htmlspecialchars($individual->description))?></p>
                <div class="buttons">
                    <a class="button violet supportit" href="/project/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
                </div>

            </li>
            <?php endforeach ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($project->social_rewards)) : ?>
            <div class="social">
                <h<?php echo $level + 2 ?> class="title"><?php echo Text::get('project-rewards-social_reward-title'); ?></h<?php echo $level + 2 ?>>
                <ul>
                    <?php foreach ($project->social_rewards as $social) : ?>
                        <li class="<?php echo $social->icon ?>">
                            <h<?php echo $level + 3 ?> class="name"><?php echo htmlspecialchars($social->reward) ?></h<?php echo $level + 3 ?>
                            <p><?php echo htmlspecialchars($social->description)?></p>
                            <?php if (!empty($social->license) && array_key_exists($social->license, $licenses)): ?>
                                <div class="license <?php echo htmlspecialchars($social->license) ?>">
                                    <h<?php echo $level + 2 ?>><?php echo Text::get('regular-license'); ?></h<?php echo $level + 2 ?>>
                                    <a href="<?php echo htmlspecialchars($licenses[$social->license]->url) ?>" target="_blank">
                                        <strong><?php echo htmlspecialchars($licenses[$social->license]->name) ?></strong>

                                        <?php if (!empty($licenses[$social->license]->description)): ?>
                                            <p><?php echo htmlspecialchars($licenses[$social->license]->description) ?></p>
                                        <?php endif ?>
                                    </a>
                                </div>
                            <?php endif ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <a class="more" href="/project/<?php echo $project->id; ?>/rewards"><?php echo Text::get('regular-see_more'); ?></a>
    </div>
    
</div>