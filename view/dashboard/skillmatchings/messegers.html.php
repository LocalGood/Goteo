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
    Goteo\Model\Skillmatching\Message;

$project = $this['skillmatching'];

$messegers = $this['messegers'];
?>
<ul>
<?php foreach($messegers as $value) : ?>
	<li>
		<a href="/user/<?php echo htmlspecialchars($value->id) ?>">
		<?php if (!empty($value->avatar)): ?><img alt="<?php echo htmlspecialchars($value->name) ?>" src="<?php echo $value->avatar->getLink(80, 80, true); ?>" /><?php endif ?>
		<?php echo $value->id; ?>
		</a>
	</li>
<?php endforeach;?>
 </ul>
