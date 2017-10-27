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

$filters = $_SESSION['mailing']['filters'];
$receivers = $_SESSION['mailing']['receivers'];
$users = $this['users'];
$ok = $this['ok'];
?>
<div class="widget">
    <p><?php echo Text::_("La comunicación se ha enviado correctamente con este contenido:"); ?></p>
    <blockquote><?php echo $this['content'] ?></blockquote>
    <blockquote><?php foreach ($users as $usr) {
            echo $ok ? Text::_("Enviado a ") : Text::_("Fallo al enviar a ");
            echo '<strong>' .$receivers[$usr->id]->name . '</strong> ('.$receivers[$usr->id]->id.')' . Text::_("al mail ") . '<strong>' . $receivers[$usr->id]->email . '</strong><br />';
    } ?></blockquote>
</div>

