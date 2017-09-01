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
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;

$filters = $this['filters'];
?>
<div class="widget board">
    <form id="filter-form" action="/admin/templates" method="get">
        <table>
            <tr>
                <td>
                    <label for="group-filter"><?php echo Text::_('Filtrar agrupaci&oacute;n'); ?>:</label>
                    <select id="group-filter" name="group">
                        <option value=""><?php echo Text::_('Todas las agrupaciones'); ?></option>
                    <?php foreach ($this['groups'] as $groupId=>$groupName) : ?>
                        <option value="<?php echo $groupId; ?>"<?php if ($filters['group'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="name-filter"><?php echo Text::_('Filtrar por nombre o asunto'); ?>:</label>
                    <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="filter" value="<?php echo Text::_('Buscar'); ?>">
                </td>
            </tr>
        </table>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($this['templates'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th class="template"><?php echo Text::_('Plantilla'); ?></th>
                <th class="description"><?php echo Text::_('Descripción'); ?></th>
                <th><!-- traducir --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this['templates'] as $template) : ?>
            <tr>
                <td class="w70"><a href="/admin/templates/edit/<?php echo $template->id; ?>">[<?php echo Text::_("Editar"); ?>]</a></td>
                <td><?php echo $template->title; ?></td>
                <td><?php echo mb_substr(strip_tags($template->text),0,200); ?>...</td>
                <?php if ($translator) : ?>
                <td class="w70"><a href="/translate/template/edit/<?php echo $template->id; ?>" >[<?php echo Text::_("Traducir"); ?>]</a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p><?php echo Text::_("No se han encontrado registros"); ?></p>
    <?php endif; ?>
</div>