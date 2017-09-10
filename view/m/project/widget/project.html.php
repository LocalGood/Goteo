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
    Goteo\Model\Project\Skill,
    Goteo\Model\Invest,
    Goteo\Model\Image;

$project = $this['project'];
$level = $this['level'] ?: 3;

if ($this['global'] === true) {
    $blank = ' target="_blank"';
} else {
    $blank = '';
}

$categories = Category::getNames($project->id, 2);

//si llega $this['investor'] sacamos el total aportado para poner en "mi aporte"
if (isset($this['investor']) && is_object($this['investor'])) {
    $investor = $this['investor'];
    $invest = Invest::supported($investor->id, $project->id);
}
?>
<div class="widget project activable<?php if (isset($this['balloon'])) echo ' balloon' ?>">
	<a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>" class="expand"></a>
    <?php if (isset($this['balloon'])): ?>
    <div class="balloon"><?php echo $this['balloon'] ?></div>
    <?php endif ?>

    <div class="image">
        <span class="cf-icon"></span>
        <?
        $project->gallery = Goteo\Model\Project\Image::getGallery($project->id);
        ?>

        <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
        <a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>" target="_parent"><img alt="<?php echo $project->name ?>" src="<?php echo current($project->gallery)->getLink(500, 285, true) ?>" /></a>
        <?php endif ?>
    </div>

    <h<?php echo $level ?> class="title"><a href="<?php echo SITE_URL ?>/project/<?php echo $project->id ?>" target="_parent"><?php echo htmlspecialchars(Text::shorten($project->name,50)) ?></a></h<?php echo $level ?>>

    <?php echo new View('view/m/project/meter_hor.html.php', array('project' => $project)) ?>

</div>
