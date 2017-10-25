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
    Goteo\Library\Worth;

$user = $this['user'];

$worthcracy = Worth::getAll();
//var_dump($_SESSION)

?>
<div class="supporterContainer">
	<?php if ($user->campaign) : ?>
	<a href="/call/<?php echo $user->call ?>" target="_blank">&nbsp;</a>
	<?php elseif ($user->user != 'anonymous') : ?>
	<a class="expand" href="/user/<?php echo htmlspecialchars($user->user) ?>">&nbsp;</a>
	<?php endif; ?>
    <?php if ($user->droped || $user->campaign) : ?>
    <div class="droped">&nbsp;</div>
    <?php endif; ?>
	<div class="supporter">
		<span class="avatar"><img src="<?php echo $user->avatar->getLink(43, 43, true); ?>" /></span>
	    <?php if ($user->user == 'anonymous') : ?>
	    <h4><?php echo $user->name; ?></h4>
	    <?php else : ?>
	    <h4 class="aqua"<?php if ($user->campaign) echo 'style="color: #96238F;"'; ?>><span><?php echo Text::shorten($user->name, 28); ?></span></h4>
	    <?php endif; ?>
	    <dl>
	        <?php  if (isset($user->projects))  : ?>
	        <dt class="projects"><?php echo Text::get('profile-invest_on-title'); ?></dt>
	        <dd class="projects"><strong><?php echo $user->projects ?></strong> <?php echo Text::get('regular-projects'); ?></dd>
	        <?php endif; ?>
	        <dt class="date"><?php echo Text::get('profile-last_worth-title'); ?></dt>
	        <dd class="date"><?php echo date('Y年n月j日', strtotime($user->date)); ?></dd>
	    </dl>
	</div>
</div>
