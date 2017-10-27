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
    Goteo\Core\View;

$project = $this['project'];

$index = isset($this['index']) ? $this['index'] : 0;

	if (!empty($project->gallery)) : ?>
        <div class="gallery-image" id="gallery-image-<?php echo ($index + 1) ?>"style="display:block;">
            <img src="<?php echo $project->gallery[$index]->getLink(580, 580); ?>" alt="<?php echo $project->name; ?>" />
        </div>
    <?php endif; ?>
