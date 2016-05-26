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

?>
    <?php  if (count($project->gallery) > 1) : ?>
		<script type="text/javascript" >
			$(function(){
				$('#prjct-gallery').slides({
					container: 'prjct-gallery-container',
					paginationClass: 'slderpag',
					generatePagination: false,
					play: 0
				});
			});
		</script>
    <?php endif; ?>

    <?php if (count($project->gallery) > 1): ?>
	<div id="prjct-gallery">
		<div class="prjct-gallery-container">
			<?php $i = 1; foreach ($project->gallery as $image) : ?>
			<div class="gallery-image" id="gallery-image-<?php echo $i ?>">
				<img src="<?php echo $image->getLink(540, 540); ?>" alt="<?php echo $project->name; ?>" />
			</div>
			<?php $i++; endforeach; ?>
		</div>
		<!-- carrusel de imagenes si hay mas de una -->
        <a class="prev">prev</a>
            <ul class="slderpag">
                <?php $i = 1; foreach ($project->gallery as $image) : ?>
                <li><a href="#" id="navi-gallery-image-<?php echo $i ?>" rel="gallery-image-<?php echo $i ?>" class="navi-gallery-image">
                <?php echo htmlspecialchars($image->name) ?></a>
                </li>
                <?php $i++; endforeach ?>
            </ul>
        <a class="next">next</a>
		<!-- carrusel de imagenes -->
	</div>
    <?php elseif (!empty($project->gallery)) : ?>
        <div class="gallery-image" id="gallery-image-<?php echo $i ?>"style="display:block;">
            <img src="<?php echo $project->gallery[0]->getLink(580, 580); ?>" alt="<?php echo $project->name; ?>" />
        </div>
    <?php endif; ?>
