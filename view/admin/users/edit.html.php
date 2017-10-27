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

$data = $this['data'];
$user = $this['user'];

$roles = $user->roles;
array_walk($roles, function (&$role) { $role = $role->name; });
?>
<div class="widget">
    <dl>
        <dt><?php echo Text::_('Nombre de usuario'); ?>:</dt>
        <dd><?php echo $user->name ?></dd>
    </dl>
    <dl>
        <dt><?php echo Text::_('User ID'); ?>:</dt>
        <dd><strong><?php echo $user->id ?></strong></dd>
    </dl>
    <dl>
        <dt><?php echo Text::_('Email'); ?>:</dt>
        <dd><?php echo $user->email ?></dd>
    </dl>
    <dl>
        <dt><?php echo Text::_('Authority of user'); ?>:</dt>
        <dd><?php echo implode(', ', $roles); ?></dd>
    </dl>
    <dl>
        <dt><?php echo Text::_('Estado de la cuenta'); ?>:</dt>
        <dd><strong><?php echo $user->active ? Text::_('Activa') : Text::_('Inactiva'); ?></strong></dd>
    </dl>

    <form action="/admin/users/edit/<?php echo $user->id ?>" method="post">
        <p>
            <label for="user-email"><?php echo Text::_('After the change') . Text::_('Email address'); ?></label><br />
            <input type="text" id="user-email" name="email" value="<?php echo $data['email'] ?>" style="width:500px" maxlength="255"/>
        </p>
        <p>
            <label for="user-password"><?php echo Text::_('After the change') . Text::_('Contraseña'); ?></label><span style="font-style:italic;"><?php echo Text::_('Mínimo 6 caracteres. Se va a encriptar y no se puede consultar'); ?></span><br />
            <input type="text" id="user-password" name="password" value="<?php echo $data['password'] ?>" style="width:500px" maxlength="255"/>
        </p>

        <input type="submit" name="edit" value="<?php echo Text::_("Actualizar"); ?>"  onclick="return confirm('<?php echo Text::_("Entiendes que vas a cambiar datos críticos de la cuenta de este usuario?"); ?>');"/><br />
        <span style="font-style:italic;font-weight:bold;color:red;"><?php echo Text::_('Atención! Se están substituyendo directamente los datos introducidos, no habrá email de autorización.'); ?></span>

    </form>
</div>