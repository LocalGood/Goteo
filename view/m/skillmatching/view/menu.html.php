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
$menu = array(
    'home'        => Text::get('project-menu-home'),
    'supporters'  => Text::get('skillmatching-menu-supporters').' <span class="digits">'.'('.count($this['skillmatching']->investors).')'.'</span>',
    'messages'    => Text::get('skillmatching-menu-messages').' <span class="digits">'.$this['messages'].'</span>',
    'updates'     => Text::get('project-menu-updates').' <span class="digits">'.$this['updates'].'</span>',
    'evaluation'  => Text::get('project-menu-evaluation')
);
?>
<div class="project-menu viewport_projectnav">
    <ul class="flipsnap_projectnav">
        <?php
        foreach ($menu as $id => $show): ?>
        <li class="item <?php echo $id ?><?php if ($this['show'] == $id) echo ' show' ?>">
        	<a href="/skillmatching/<?php echo htmlspecialchars($this['skillmatching']->id) ?>/<?php echo $id ?>"><?php echo $show ?></a>
        </li>
        <?php endforeach ?>
    </ul>
    <p class="controls">
        <a class="pj_prev">&lt;</a>
        <a class="pj_next">&gt;</a>
    </p>
</div>
