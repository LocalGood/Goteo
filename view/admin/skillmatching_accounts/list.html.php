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

$filters = $this['filters'];
?>
<!-- filtros -->
<?php $the_filters = array(
    'projects' => array (
        'label' => Text::_("Proyecto"),
        'first' => Text::_("Todos los proyectos")),
    'users' => array (
        'label' => Text::_("Accounts Usuario"),
        'first' => Text::_("Todos los usuarios")),
    'methods' => array (
        'label' => Text::_("Método de pago"),
        'first' => Text::_("Todos los tipos")),
    'campaigns' => array (
        'label' => Text::_("Campaña"),
        'first' => Text::_("Todas las campañas")),
    'review' => array (
        'label' => Text::_("Para revisión"),
        'first' => Text::_("Todos")),
); ?>
<a href="/admin/skillmatching_accounts/viewer" class="button"><?php echo Text::_("Visor de logs"); ?></a>
<div class="widget board">
    <h3 class="title"><?php echo Text::_("Account information"); ?></h3>
    <form id="filter-form" action="/admin/skillmatching_accounts" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <input type="hidden" name="status" value="all" />
        <?php foreach ($the_filters as $filter=>$data) :
            // レビュースキップ
            if ($filter == 'review') {
                continue;
            }
            // 支払い方法=現金固定
            if ($filter == 'methods'){ ?>
                <input type="hidden" name="methods" value="cash" />
            <?php
                continue;
            }
            ?>
            <?php if ($data['first'] !== 'New campaign') { ?>
            <div style="float:left;margin:5px;">
                <label for="<?php echo $filter ?>-filter"><?php echo $data['label'] ?></label>
                <select id="<?php echo $filter ?>-filter" name="<?php echo $filter ?>" onchange="document.getElementById('filter-form').submit();">
                    <option value="<?php if ($filter == 'investStatus' || $filter == 'status') echo 'all' ?>"<?php if (($filter == 'investStatus' || $filter == 'status') && $filters[$filter] == 'all') echo ' selected="selected"'?>><?php echo $data['first'] ?></option>
                <?php foreach ($this[$filter] as $itemId=>$itemName) : ?>
                    <option value="<?php echo $itemId; ?>"<?php if ($filters[$filter] === (string) $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
                <?php endforeach; ?>
                </select>
            </div>
            <? } ?>
        <?php endforeach; ?>
        <div style="float:left;margin:5px;">
            <label for="date-filter-from"><?php echo Text::_("Fecha desde"); ?></label>
            <input type="text" id ="date-filter-from" name="date_from" value ="" />
        </div>
        <div style="float:left;margin:5px;">
            <label for="date-filter-until"><?php echo Text::_('Fecha hasta'); ?></label>
            <input type="text" id ="date-filter-until" name="date_until" value ="<?php echo date('Y-m-d') ?>" />
        </div>
        <div style="clear:both;">
            <input type="submit" value="<?php echo Text::_("Buscar"); ?>" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/skillmatching_accounts"><?php echo Text::_("Quitar filtros"); ?></a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p><?php echo Text::_("Es necesario poner algun filtro, hay demasiados registros!"); ?></p>
<?php elseif (!empty($this['list'])) : /* ?>
<?php $Total = 0; foreach ($this['list'] as $invest) { $Total += $invest->amount; } ?>
    <p><strong><?php echo Text::_("TOTAL"); ?>:</strong>  <?php echo number_format($Total, 0, '', '.') ?> 円</p>
<?php */ ?>
    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th><?php echo Text::_("Aporte ID"); ?></th>
                <th><?php echo Text::_("Fecha"); ?></th>
                <th><?php echo Text::_("Cofinanciador"); ?></th>
                <th><?php echo Text::_("Proyecto"); ?></th>
                <th><?php echo Text::_("Estado"); ?></th>
                <th><?php echo Text::_("Estado aporte"); ?></th>
                <th><?php echo Text::_("Extra"); ?></th>
                <th></th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $invest) : ?>
            <tr>
                <td><a href="/admin/skillmatching_accounts/details/<?php echo $invest->id ?>">[<?php echo Text::_("Detalles"); ?>]</a></td>
                <td><?php echo $invest->id ?></td>

                <td><?php /* 支援をした日時 */ echo $invest->invested ?></td>
                <td><?php echo $this['users'][$invest->user] ?></td>
                <td><?php
                    $inv_pj = preg_replace('/^'.LG_SM_DB_PREFIX.'/','',$invest->project);
                    echo $this['projects'][$inv_pj];
                    if (!empty($invest->campaign)) echo '<br />('.$this['campaigns'][$invest->campaign].')';
                    ?>
                </td>
                <td><?php echo $this['status'][$invest->status] ?></td>
                <td><?php echo $this['investStatus'][$invest->investStatus] ?></td>
                <td><?php echo $invest->charged ?></td>
                <td><?php echo $invest->returned ?></td>
                <td>
                    <?php if ($invest->anonymous == 1)  echo Text::_("Anónimo ") ?>
                    <?php if ($invest->resign == 1)  echo Text::_("Donativo ") ?>
                    <?php if (!empty($invest->admin)) echo Text::_("Manual") ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p><? echo Text::_("No hay transacciones que cumplan con los filtros."); ?></p>
<?php endif;?>
</div>