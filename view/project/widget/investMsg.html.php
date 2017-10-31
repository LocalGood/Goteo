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
    Goteo\Model;

// aviso para el usuario, puede ser start->hola , ok->gracias o fail->lo sentimos

$user = $this['user'];
if (!$user instanceof Model\User) {
    $name = '';
    $avatarhtml = '';
} else {
    $name = $user->name . "さん";
    $avatar = ($user->avatar instanceof Model\Image) ? $user->avatar : Model\Image::get(1);
    $avatarhtml = '<img src="'.$avatar->getLink(50, 50, true).'" />';
}

switch ($this['message']) {
    case 'start':
        $title   = Text::get('regular-hello') . " $name";
        break;
    case 'login':
        $title   = Text::get('regular-hello') . " $name";
        break;
    case 'confirm':
        $title   = Text::get('regular-hello') . " $name";
        break;
    case 'continue':
        $title   = Text::get('regular-hello') . " $name";
        break;
    case 'ok':
        $title   = Text::get('regular-thanks') . " {$name}!";
        break;
    case 'fail':
        $title   = Text::get('regular-sorry') . " {$name}";
        break;
}

$level = (int) $this['level'] ?: 3;

?>
<div class="widget invest-message">
    <h2>
        <span class="avatar"><?php echo $avatarhtml; ?></span>
        <span><?php echo $title; ?></span>
    </h2>
</div>
