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
<div class="widget project-rewards-summary" id="rewards-summary">

    <?php if (!empty($project->individual_rewards)) :
        $count = 1;
    ?>
    <div class="individual">
        <h<?php echo $level+1 ?> id="individual_ttl" class="title"><?php echo Text::get('project-rewards-individual_reward-title'); ?></h<?php echo $level+1 ?>>
        <ul>
        <?php foreach ($project->individual_rewards as $individual) : ?>
        <li class="<?php echo $individual->icon ?>">
            <dl class="amount">
                <dt><?php echo Text::get('regular-support-amount'); ?></dt>
                <dd><strong><?php echo \amount_format($individual->amount); ?></strong>円</dd>
            </dl>
            <?php if (!empty($individual->units)):
                $units = ($individual->units - $individual->taken);
                ?>
                <dl class="remain">
                    <dt><?php echo Text::get('project-rewards-individual_reward-limited'); ?></dt>
                    <dd><?php echo $units; ?></dd>
                </dl>
            <?php endif; ?>
            <div class="left">
                <h<?php echo $level + 2 ?> id="<? echo 'individual_num' . $count; ?>" class="name"><?php echo htmlspecialchars($individual->reward) ?></h<?php echo $level + 2 ?>>
                <p><?php echo htmlspecialchars($individual->description)?></p>
                <div class="buttons">
                    <a class="button violet supportit" href="/project/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest'); ?></a>
                </div>
            </div>
            <div class="right">
                <img src="<?php echo SRC_URL ?>/view/images/" alt="<?/*php todo: $project->新しく追加されるお礼画像のキャプション　を出す */?>">
            </div>

        </li>
            <? $count++; ?>
        <?php endforeach ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($project->social_rewards)) :
        $count = 1;
    ?>
        <div class="social">
            <h<?php echo $level + 1 ?> id="social_ttl" class="title"><?php echo Text::get('project-rewards-social_reward-title'); ?></h<?php echo $level + 1 ?>>
            <ul>
                <?php foreach ($project->social_rewards as $social) : ?>
                    <li class="<?php echo $social->icon ?>">
                        <h<?php echo $level + 2 ?> id="<? echo 'social_num' . $count; ?>" class="name"><?php echo htmlspecialchars($social->reward) ?></h<?php echo $level + 2 ?>
                        <p><?php echo htmlspecialchars($social->description)?></p>
                        <?php if (!empty($social->license) && array_key_exists($social->license, $licenses)): ?>
                            <div class="license <?php echo htmlspecialchars($social->license) ?>">
                                <h<?php echo $level + 3 ?> id="<? echo 'individual_num' . $count; ?>" ><?php echo Text::get('regular-license'); ?></h<?php echo $level + 3 ?>>
                                <a href="<?php echo htmlspecialchars($licenses[$social->license]->url) ?>" target="_blank">
                                    <strong><?php echo htmlspecialchars($licenses[$social->license]->name) ?></strong>

                                    <?php if (!empty($licenses[$social->license]->description)): ?>
                                        <p><?php echo htmlspecialchars($licenses[$social->license]->description) ?></p>
                                    <?php endif ?>
                                </a>
                            </div>
                        <?php endif ?>
                    </li>
                    <? $count++; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

</div>