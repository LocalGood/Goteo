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
    Goteo\Library\Text;

echo new View ('view/review/reviews/selector.html.php', $this);

$review = $this['review'];

?>

<div class="widget">
    <p>El proyecto <strong><?php echo $review->name; ?></strong> de <strong><?php echo $review->owner_name; ?></strong></p>
    <p><?php echo Text::_('La edición del proyecto alcanzó el '); ?><strong><?php echo $review->progress; ?>%</strong>, <?php echo Text::_('la puntuación actual de la revisión es de '); ?><strong><?php echo $review->score; ?>/<?php echo $review->max; ?></strong></p>
    <p><a href="<?php echo LG_BASE_URL_GT . '/project/' . $review->project; ?>" target="_blank"><?php echo Text::_('Abrir el proyecto') ?></a><br /><a href="<?php echo LG_BASE_URL_GT . '/user/' . $review->owner; ?>" target="_blank"><?php echo Text::_('Abrir el perfil del creador') ?></a></p>
</div>

<div class="widget">
    <?php echo Text::_('Comentario del administrador:') ?><br />
    <blockquote><?php echo $review->comment; ?></blockquote>
</div>

<div class="widget">
    <p><?php echo Text::_('Tu revisión está '); ?><?php echo $review->ready == 1 ? Text::_('Lista') : Text::_('Pendiente'); ?><?php if ($review->ready != 1) : ?><?php echo Text::_(' Puedes completarla en ') ?><a href="/review/reviews/evaluate/open/<?php echo $review->id; ?>"><?php echo Text::_('tus revisiones') ?></a><?php endif; ?></p>
    <?php
    if ($review->ready == 0) {
        echo '<a href="/review/reviews/summary/ready/' . $review->id . '">[' . Text::_('Dar por terminada mi revisión') . ']</a>';
    } else {
        echo 'Continúa con otra o espera instrucciones.';
    }
    ?>
</div>