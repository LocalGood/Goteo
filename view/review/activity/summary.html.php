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
    Goteo\Model,
    Goteo\Library\Text;

?>
<?php if (!empty($this['reviews'])) : ?>
    <h2 class="title">Mis revisiones actuales</h2>
    <?php foreach ($this['reviews'] as $review) : ?>
        <div class="widget">
            <p><?php echo Text::_('El proyecto ') ?><strong><?php echo $review->name; ?></strong> de <strong><?php echo $review->owner_name; ?></strong></p>
            <p><?php echo Text::_('La edición del proyecto alcanzó el '); ?><strong><?php echo $review->progress; ?>%</strong>, <?php echo Text::_('la puntuación actual de la revisión es de '); ?><strong><?php echo $review->score; ?>/<?php echo $review->max; ?></strong></p>
            <p><?php echo Text::_('Tu revisión está ') ?><?php echo $review->ready == 1 ? Text::_('Lista') : Text::_('Pendiente'); ?><?php if ($review->ready != 1) : ?><?php echo Text::_(' Puedes completarla en ') ?><a href="/review/reviews/evaluate/open/<?php echo $review->id; ?>"><?php echo Text::_('tus revisiones') ?></a><?php endif; ?></p>
            <p><a href="<?php echo LG_BASE_URL_GT . '/project/' . $review->project; ?>" target="_blank"><?php echo Text::_('Abrir el proyecto') ?></a><br /><a href="<?php echo LG_BASE_URL_GT . '/user/' . $review->owner; ?>" target="_blank"><?php echo Text::_('Abrir el perfil del creador') ?></a></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>