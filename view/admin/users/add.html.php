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
?>
<div class="widget">
    <form action="/admin/users/add" method="post">
        <p>
            <label for="user-user"><?php echo Text::_('User ID'); ?></label><span style="font-style:italic;"><?php echo Text::_("Solo letras, números y guion '-'. Sin espacios ni tildes ni 'ñ' ni 'ç' ni otros caracteres que no sean letra, número o guión."); ?></span><br />
            <input type="text" id="user-user" name="userid" value="<?php echo $data['user'] ?>" style="width:250px" maxlength="50"/>
        </p>
        <p>
            <label for="user-name"><?php echo Text::_('Nombre público'); ?></label><br />
            <input type="text" id="user-name" name="name" value=<?php echo $data['name'] ?>"" style="width:500px" maxlength="255"/>
        </p>
        <p>
            <label for="user-email"><?php echo Text::_('Email address'); ?></label><br />
            <input type="text" id="user-email" name="email" value="<?php echo $data['email'] ?>" style="width:500px" maxlength="255"/>
        </p>
        <p>
            <label for="user-password"><?php echo Text::_('Contraseña'); ?></label><span style="font-style:italic;"><?php echo Text::_('Mínimo 6 caracteres. Se va a encriptar y no se puede consultar'); ?></span><br />
            <input type="password" id="user-password" name="password" value="<?php echo $data['password'] ?>" style="width:500px" maxlength="255"/>
        </p>

        <input type="submit" name="add" value="<?php echo Text::_("Crear este usuario"); ?>" /><br />
        <span style="font-style:italic;font-weight:bold;"><?php echo Text::_('Atención! Le llegará email de verificación al usuario como si se hubiera registrado.'); ?></span>

    </form>
</div>