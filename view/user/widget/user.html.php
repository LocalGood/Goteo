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

$user = $this['user'];
$projectType = !empty($this['projectType']) ? $this['projectType'] : 'project';
$level = (int) $this['level'] ?: 3;

// @todo Esto ya debería venirme en $user
if (!isset($user->webs)) {
    $user->webs = \Goteo\Model\User\Web::get($user->id);
}

$user->about = nl2br(Text::urlink($user->about));
?>

<div class="widget user collapsable">
    <h<?php echo $level ?> class="supertitle"><?php echo ($projectType == 'project') ? Text::get('profile-widget-user-header') : Text::get('profile-widget-user-header-sm'); ?></h<?php echo $level ?>>
    <div class="project-widget-box">

        <div class="user-head">
            <div class="image">
                <a class="" href="/user/<?php echo $user->id; ?>">
                    <?php if (!empty($user->avatar)): ?><img alt="<?php echo htmlspecialchars($user->name) ?>" src="<?php echo $user->avatar->getLink(80, 80, true); ?>" /><?php endif ?>
                </a>
            </div>
            <div class="user-head-right">
                <h<?php echo $level + 1 ?> class="title">
                    <a class="" href="/user/<?php echo $user->id; ?>"><?php echo htmlspecialchars($user->name) ?></a>
                </h<?php echo $level + 1 ?>>

                <?php if (isset($user->location)): ?>
                    <div class="location">
                        <span><?php echo Text::GmapsLink($user->location); ?></span>
                    </div>
                <?php endif ?>
	            <?php echo new View('view/user/widget/social.html.php', array('user' => $user)) ?>
            </div>
        </div>

        <?php if (isset($user->about)): ?>
            <div class="about">
                <p>
                    <?php echo $user->about ?>
                </p>
<!--                <div class="about-link">-->
<!--                    <a class="" href="/user/--><?php //echo $user->id; ?><!--">--><?php //echo Text::get('profile-widget-button'); ?><!--</a>-->
<!--                </div>-->
            </div>
        <?php endif ?>

        <div class="links">
            <?php if (!empty($user->webs)): ?>
                <ul>
                    <?php foreach ($user->webs as $link): ?>
                        <li><a href="<?php echo htmlspecialchars($link->url) ?>" target="_blank"><?php echo htmlspecialchars($link->url) ?></a></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        </div>


        <a class="button aqua profile" href="/user/profile/<?php echo htmlspecialchars($user->id) ?>/message"><?php echo Text::get('regular-send_message')?></a>

    </div>

</div>

