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
// 応募済チェック
$user    = $_SESSION['user'];
$isInvested = Goteo\Model\User::isInvestedSM($user->id,$project->prefixed_id);

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
	<h<?php echo $level + 1 ?> class="supertitle"><?php echo Text::get('skillmatching-rewards-individual_reward-title'); ?></h<?php echo $level + 1 ?>>

    <div class="project-widget-box">

        <?php if (!empty($project->individual_rewards)) :
            $count = 1;
        ?>
            <div class="individual">

                <ul>
                    <?php foreach ($project->individual_rewards as $individual) :?>

                        <li class="<?php echo $individual->icon ?>">
                            <h<?php echo $level + 3 ?> class="name"><a href="/skillmatching/<?php echo $project->id; ?>/rewards#<? echo 'individual_num' . $count; ?>"><?php echo htmlspecialchars($individual->reward) ?></a></h<?php echo $level + 3 ?>>
                            <?php if (!empty($individual->units)):
                                $units = ($individual->units - $individual->taken);
                                ?>
                            <p class="remain"><strong><?php echo Text::get('skillmatching-rewards-individual_reward-limited'); ?> <?php echo $units; ?></strong></p>
                            <?php endif; ?>
                            <p><?php echo nl2br(htmlspecialchars($individual->description))?></p>

                            <div class="buttons">
                                <a class="button violet supportit" href="/skillmatching/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it-sm'); ?></a>
                            </div>
                        </li>
                        <? $count++; ?>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

    </div>
    
</div>

<div class="widget project-rewards collapsable" id="project-rewards">

    <h<?php echo $level + 1 ?> class="supertitle"><span><?php echo Text::get('skillmatching-rewards-social_reward-title'); ?></span></h<?php echo $level + 1?>>

    <div class="project-widget-box">

        <?php if (!empty($project->social_rewards)) :
            $count = 1;
        ?>
        <div class="social">

            <ul>
            <?php foreach ($project->social_rewards as $social) : ?>
                <li class="<?php echo $social->icon ?>">
                    <h<?php echo $level + 3 ?> class="name"><a href="/skillmatching/<?php echo $project->id; ?>/rewards#<? echo 'social_num' . $count; ?>"><span><?php echo htmlspecialchars($social->reward) ?></span></a></h<?php echo $level + 3 ?>>
                    <p><?php echo htmlspecialchars($social->description)?></p>
                    <?php if (!empty($social->license) && array_key_exists($social->license, $licenses)): ?>
                    <div class="license <?php echo htmlspecialchars($social->license) ?>">
                        <h<?php echo $level + 2 ?>><span><?php echo Text::get('regular-license'); ?></span></h<?php echo $level + 2 ?>>
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

</div>

