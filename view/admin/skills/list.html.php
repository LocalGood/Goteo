<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundaci��n Fuentes Abiertas (see README for details)
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

$botones = array(
    'edit' => '[' . Text::_('Editar') . ']',
    'remove' => '[' . Text::_('Quitar') . ']',
    'translate' => '[' . Text::_('Traducir') . ']',
    'up' => '[&uarr;]',
    'down' => '[&darr;]'
);

// ancho de los tds depende del numero de columnas
$cols = count($this['columns']);
$per = 100 / $cols;

?>
<?php if (!empty($this['addbutton'])) : ?>
<a href="<?php echo $this['url'] ?>/add" class="button"><?php echo $this['addbutton'] ?></a>
<?php endif; ?>
<?php if (!empty($this['otherbutton'])) : ?>
<?php echo $this['otherbutton'] ?>
<?php endif; ?>
<!-- Filtro -->
<?php if (!empty($filters)) : ?>
<div class="widget board">
    <form id="filter-form" action="<?php echo $this['url']; ?>" method="get">
        <?php foreach ($filters as $id=>$fil) : ?>
        <?php if ($fil['type'] == 'select') : ?>
            <label for="filter-<?php echo $id; ?>"><?php echo $fil['label']; ?></label>
            <select id="filter-<?php echo $id; ?>" name="<?php echo $id; ?>" onchange="document.getElementById('filter-form').submit();">
            <?php foreach ($fil['options'] as $val=>$opt) : ?>
                <option value="<?php echo $val; ?>"<?php if ($fil['value'] == $val) echo ' selected="selected"';?>><?php echo $opt; ?></option>
            <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <?php if ($fil['type'] == 'input') : ?>
            <br />
            <label for="filter-<?php echo $id; ?>"><?php echo $fil['label']; ?></label>
            <input name="<?php echo $id; ?>" value="<?php echo (string) $fil['value']; ?>" />
            <input type="submit" name="filter" value="<?php echo Text::_('Buscar') ?>">
        <?php endif; ?>
        <?php endforeach; ?>
    </form>
</div>
<?php endif; ?>

<!-- lista -->
<div class="widget board">
    <?php if (!empty($this['data'])) : ?>
    <table>
        <thead>
            <tr>
                <?php foreach ($this['columns'] as $key=>$label) : ?>
                    <th class="<?php echo $key; ?>"><?php echo !empty($label) ? Text::_($label) : $label; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($this['data'] as $item) : ?>
            <tr>
            <?php foreach ($this['columns'] as $key=>$label) : ?>
                <?php if ($key == 'translate') : ?>
                    <td class="w70"><?php if ($translator) : ?><a href="/translate/<?php echo $this['model'].'/edit/'.$item->id; ?>" >[<?php echo Text::_("Traducir"); ?>]</a><?php endif; ?>
                    </td>
                <?php elseif ($key == 'remove') : ?>
                    <td class="w70"><a href="<?php echo $this['url']?>/remove/<?php echo (is_object($item)) ? $item->id : $item['id']; ?>" onclick="return confirm('<?php echo Text::_("Seguro que deseas eliminar este registro?"); ?>');">[<?php echo Text::_('Quitar'); ?>]</a></td>
                <?php elseif (in_array($key, array('edit', 'up', 'down'))) :
                    $id = (is_object($item)) ? $item->id : $item['id'];
                    $class = ($key == 'edit') ? 'class="w70"' : 'width="5%"';
                    ?>
                    <td <?php echo $class; ?>>
                        <a title="<?php echo Text::get('admin_registro') . $id; ?>" href="<?php echo "{$this['url']}/{$key}/{$id}/{$filter}"; ?>"><?php echo $botones[$key]; ?></a>
                    </td>
                <?php elseif ($key == 'image') :
                    $_id = (is_object($item)) ? $item->$key : $item[$key];
                    $_image = \Goteo\Model\Image::get($_id);
                    ?>
                    <td><?php if (!empty($item->$key)) : ?><img src="<?php echo $_image->getLink(110,110)  ?>" alt="image" /><?php endif; ?></td>
                <?php elseif ($key == 'name') : ?>
                   <td class="w200"><?php echo!empty($item->parent_skill_id) ? "&nbsp;&lfloor;" : ""?><?php echo (is_object($item)) ? $item->$key : $item[$key]; ?></td>
                <?php elseif ($key == 'order') : ?>
                   <td><?php echo!empty($item->parent_skill_id) ? "&nbsp;&lfloor;" : ""?><?php echo (is_object($item)) ? $item->$key : $item[$key]; ?></td>
                <?php else : ?>
                    <td><?php echo (is_object($item)) ? $item->$key : $item[$key]; ?></td>
                <?php endif; ?>
            <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p><?php echo Text::_("No se han encontrado registros"); ?></p>
    <?php endif; ?>
</div>
