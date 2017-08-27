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
    Goteo\Library\Text,
    Goteo\Model\Project\Category,
    //Goteo\Model\Project\Skill,
    Goteo\Model\Invest,
    Goteo\Model\Image;

$project = $this['skillmatching'];
$level = $this['level'] ?: 3;
//var_dump($project->individual_rewards);
if ($this['global'] === true) {
    $blank = ' target="_parent"';
} else {
    $blank = '';
}
//var_dump($project);exit;
$categories = Category::getNames($project->prefixed_id, 2);

//si llega $this['investor'] sacamos el total aportado para poner en "mi aporte"
if (isset($this['investor']) && is_object($this['investor'])) {
    $investor = $this['investor'];
    $invest = Invest::supported($investor->id, $project->prefixed_id);
}
?>

<div class="widget project skillmatching activable heightLine-project<?php if (isset($this['balloon'])) echo ' balloon' ?>">
    <?php if (isset($this['balloon'])): ?>
    <div class="balloon"><?php echo $this['balloon'] ?></div>
    <?php endif ?>
        <div class="image">
            <span class="sm-icon"></span>
            <?
            $project->gallery = Goteo\Model\Skillmatching\Image::getGallery($project->id);
            ?>
            <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
                <a href="<?php echo SITE_URL ?>/skillmatching/<?php echo $project->id ?>"<?php echo $blank; ?>><img alt="<?php echo $project->name ?>" src="<?php echo current($project->gallery)->getLink(260, 135, true) ?>" /></a>
            <?php endif ?>
        </div>

        <div class="project-details">
            <h<?php echo $level ?> class="title">
                <a class="link" href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><?php echo htmlspecialchars(Text::shorten($project->name,50)) ?></a>
            </h<?php echo $level ?>>

            <div class="author">
                <a class="link" href="<?php echo SITE_URL ?>/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"<?php echo $blank; ?>>
                    <span class="author-img">
                        <img src="<?php echo $project->user->avatar->getLink(50, 50, true); ?>" alt="<?php echo $project->user->name; ?>">
                    </span>
                    <span><?php echo htmlspecialchars(Text::shorten($project->user->name,40)) ?></span>
                </a>
            </div>

            <?php if (!empty($categories)): ?>
                <div class="categories">
                    <?php $sep = ''; foreach ($categories as $key=>$value) :
                        echo $sep.htmlspecialchars($value);
                        $sep = ', '; endforeach; ?>
                </div>
            <?php endif ?>
        </div>

        <?/*php
        // スキル表示
        $skills = Skill::getNames($project->prefixed_id);
        if (!empty($skills)): ?>
        <div class="skills">
            <?
            foreach( $skills as $_skill_id => $_skill_name):
                // ログイン中のユーザーのスキルとマッチすればハイライト
                $_match_skill = '';
                if (!empty($_SESSION['user']->skills)){
                    foreach ($_SESSION['user']->skills as $_id){
                        if ($_id == $_skill_id){
                            $_match_skill = ' class="matched_skill"';
                            break;
                        }
                    }
                }
            ?>
                <a<?= $_match_skill; ?> id="skill_id_<?= $_skill_id; ?>" href=""><?php echo $_skill_name ?></a>
            <? endforeach; ?>
        </div>
        <? endif; */?>

    <?php echo new View('view/skillmatching/meter_hor.html.php', array('skillmatching' => $project)) ?>

    <div class="want-support">
        <div>
            <h<?php echo $level + 1 ?>>必要な支援</h<?php echo $level + 1 ?>>
            <img src="<?php echo SRC_URL ?>/view/images/icon_skill.png" alt="資金">
        </div>
        <div>
            <h<?php echo $level + 1 ?>>残り</h<?php echo $level + 1 ?>>
            <div class="days"><strong><?php echo number_format($days) ?></strong><span><?php echo Text::get('regular-days'); ?></span></div>
        </div>

    </div>

    <?php
    /*
     * quitamos los botones
     *
    if ($this['dashboard'] === true) : // si estamos en el dashboard no hay (apoyar y el ver se abre en una ventana nueva) ?>
    <div class="buttons">
        <?php if ($this['own'] === true) : // si es propio puede ir a editarlo ?>
        <a class="button red suportit" href="<?php echo SITE_URL ?>/skillmatching/edit/<?php echo $project->id ?>"><?php echo Text::get('regular-edit'); ?></a>
        <?php endif; ?>
        <a class="button view" href="<?php echo SITE_URL ?>/skillmatching/<?php echo $project->id ?>" target="_blank"><?php echo Text::get('regular-view_project'); ?></a>
    </div>
    <?php else : // normal ?>
    <div class="buttons">
        <?php if ($project->status == 3) : // si esta en campa�a se puede aportar ?>
        <a class="button violet supportit" href="<?php echo SITE_URL ?>/skillmatching/<?php echo $project->id ?>/invest"<?php echo $blank; ?>><?php echo Text::get('regular-invest_it-sm'); ?></a>
        <?php else : ?>
        <a class="button view" href="<?php echo SITE_URL ?>/skillmatching/<?php echo $project->id ?>/updates"<?php echo $blank; ?>><?php echo Text::get('regular-see_blog'); ?></a>
        <?php endif; ?>
        <a class="button view" href="<?php echo SITE_URL ?>/skillmatching/<?php echo $project->id ?>"<?php echo $blank; ?>><?php echo Text::get('regular-view_project'); ?></a>
    </div>
    <?php endif;
     *
     */
    ?>
</div>
