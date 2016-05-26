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

$project = $this['skillmatching'];
$images = $this['images'];
$sections = $this['sections'];

function the_section($current, $image, $sections) {
    $select = '<select name="section_image_'.$image.'">';
    foreach ($sections as $secId => $secName) {
        $curSec = ($secId == $current) ? ' selected="selected"' : '';
        $select .= '<option value="'.$secId.'"'.$curSec.'>'.$secName.'</option>';
    }
    $select .= '</select>';
    
    return $select;
}

function the_link($current, $image) {
    return '<input type="text" name="url_image_'.$image.'"  value="'.$current.'" style="width: 100%;"/>';
}

?>
<script type="text/javascript">
function move (img, direction, section) {
    document.getElementById('the_action').value = direction;
    document.getElementById('the_section').value = section;
    document.getElementById('move_pos').value = img;
    document.getElementById('images_form').submit();
}
</script>

<a href="/admin/skillmatchings" class="button"><?php echo Text::_('Volver'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="/skillmatching/<?php echo $project->id; ?>" class="button" target="_blank"><?php echo Text::_('Ver proyecto'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="/skillmatching/edit/<?php echo $project->id; ?>" class="button" target="_blank"><?php echo Text::_('Editar proyecto'); ?></a>
<div class="widget board">
    <?php if (!empty($images)) : ?>
    <form id="images_form" action="/admin/skillmatchings/images/<?php echo $project->id; ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $project->id; ?>" />
        <input type="hidden" id="the_action" name="action" value="apply" />
        <input type="hidden" id="the_section" name="section" value="" />
        <input type="hidden" id="move_pos" name="move" value="" />
    <table>
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th style="width:30px;"></th>
                <th colspan="2"></th> <!-- posicion -->
            </tr>
        </thead>

        <tbody>
        <?php foreach ($sections as $sec=>$secName) : 
            if (empty($images[$sec])) continue;
            ?>
            <tr>
                <td colspan="5" style="text-align: left;"><h3><?php echo Text::_('gallery'); ?></h3></td>
            </tr>
            <?php foreach ($images[$sec] as $image) : ?>
            <tr>
                <td style="width:105px;text-align: left;"><img src="<?php echo $image->imageData->getLink(175, 100); ?>" alt="image" /></td>
                <td>
                    <table>
                        <tr>
                            <td><label><?php echo Text::_('Sección'); ?>:<br /><?php echo the_section($image->section, $image->image, $sections); ?></label></td>
                        </tr>
                        <tr>
                            <td><label><?php echo Text::_('Enlace'); ?>:<br /><?php echo the_link($image->url, $image->image); ?></label></td>
                        </tr>
                    </table>
                </td>
                <td>&nbsp;</td>
                <td><a href="#" onclick="move('<?php echo $image->image; ?>', 'up', '<?php echo $image->section; ?>'); return false;">[&uarr;]</a></td>
                <td><a href="#" onclick="move('<?php echo $image->image; ?>', 'down', '<?php echo $image->section; ?>'); return false;">[&darr;]</a></td>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>

    </table>
        <input type="submit" name="apply_changes" value="<?php echo Text::_('Aplicar'); ?>" />
    </form>
    <?php else : ?>
    <p><?php echo Text::_("No se han encontrado registros"); ?></p>
    <?php endif; ?>
</div>
<form id="sec_form" action="/admin/skillmatchings/image_section/<?php echo $project->id; ?>" method="post">
    <input type="hidden" id="setimg" name="image" value="">
    <input type="hidden" id="setsec" name="section" value="">
</form>