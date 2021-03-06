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

// paginacion
require_once 'library/pagination/pagination.php';

$filters = $this['filters'];
$templates = $this['templates'];
$the_filters = '';
foreach ($filters as $key => $value) {
    $the_filters .= "&{$key}={$value}";
}

$pagedResults = new \Paginated($this['sended'], 20, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<div class="widget board">
    <form id="filter-form" action="/admin/sended" method="get">
        <div style="float:left;margin:5px;">
            <label for="user-filter"><?php echo Text::_('ID, nombre o email del destinatario'); ?></label><br />
            <input id="user-filter" name="user" value="<?php echo $filters['user']; ?>" style="width:300px;"/>
        </div>

        <div style="float:left;margin:5px;">
            <label for="template-filter"><?php echo Text::_('Plantilla'); ?></label><br />
            <select id="template-filter" name="template" onchange="document.getElementById('filter-form').submit();" >
                <option value=""><?php echo Text::_('Todas las plantillas'); ?></option>
                <?php foreach ($templates as $templateId => $templateName) : ?>
                    <option value="<?php echo $templateId; ?>"<?php if ($filters['template'] == $templateId)
                    echo ' selected="selected"'; ?>><?php echo $templateName; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br clear="both" />


        <div style="float:left;margin:5px;" id="date-filter-from">
            <label for="date-filter-from"><?php echo Text::_('send date(from)'); ?></label><br />
<?php echo new View('library/superform/view/element/datebox.html.php', array('value' => $filters['date_from'], 'id' => 'date-filter-from', 'name' => 'date_from')); ?>
        </div>
        <div style="float:left;margin:5px;" id="date-filter-until">
            <label for="date-filter-until"><?php echo Text::_('send date(to)'); ?></label><br />
<?php echo new View('library/superform/view/element/datebox.html.php', array('value' => $filters['date_until'], 'id' => 'date-filter-until', 'name' => 'date_until')); ?>
        </div>
        <div style="float:left;margin:5px;">
            <input type="submit" name="filter" value="<?php echo Text::_("Buscar"); ?>">
        </div>

    </form>
</div>

<div class="widget board">
    <?php if ($filters['filtered'] != 'yes') : ?>
        <p><?php echo Text::_("Es necesario poner algun filtro, hay demasiados registros!"); ?></p>
<?php elseif (!empty($this['sended'])) : ?>
        <table>
            <thead>
                <tr>
                    <th><!-- Si no ves --></th>
                    <th><?php echo Text::_('Destinatario'); ?></th>
                    <th><?php echo Text::_('Plantilla'); ?></th>
                    <th><?php echo Text::_('Fecha'); ?></th>
                    <th><!-- reenviar --></th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($send = $pagedResults->fetchPagedRow()) :
                    $link = LG_BASE_URL_GT . '/mail/' . base64_encode(md5(uniqid()) . '¬' . $send->email . '¬' . $send->id) . '/?email=' . urlencode($send->email);
                    ?>
                    <tr>
                        <td><a href="<?php echo $link; ?>" target="_blank">[<?php echo Text::_("Send history"); ?>]</a></td>
                        <td><a href="/admin/users/?name=<?php echo urlencode($send->email) ?>"><?php echo $send->email; ?></a></td>
                        <td><?php echo $templates[$send->template]; ?></td>
                        <td><?php echo $send->date; ?></td>
                        <td></td>
                    </tr>
    <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <ul id="pagination">
    <?php $pagedResults->setLayout(new DoubleBarLayout());
    echo $pagedResults->fetchPagedNavigation(str_replace('?', '&', $the_filters)); ?>
    </ul>
<?php else : ?>
    <p><?php echo Text::_("No se han encontrado registros"); ?></p>
<?php endif; ?>