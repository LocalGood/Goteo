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

?>
<script type="text/javascript">

jQuery(document).ready(function ($) {

    $('#criteria-section').change(function () {
        order = $.ajax({async: false, url: '<?php echo LG_BASE_URL_GT; ?>/ws/get_criteria_order/'+$('#criteria-section').val()}).responseText;
        $('#criteria-order').val(order);
        $('#criteria-num').html(order);
    });

});
</script>

<div class="widget board">
    <form method="post" action="/admin/criteria">

        <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
        <input type="hidden" name="id" value="<?php echo $this['criteria']->id; ?>" />

        <?php if ($this['action'] == 'add') : ?>
            <p>
                <label for="criteria-section"><?php echo Text::_('Sección:'); ?></label>
                <select id="criteria-section" name="section">
                    <option value="" disabled><?php echo Text::_('Elige la sección'); ?></option>
                    <?php foreach ($this['sections'] as $id=>$name) : ?>
                        <option value="<?php echo $id; ?>"<?php if ($id == $this['criteria']->section) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        <?php else : ?>
            <p>
                <label for="criteria-section"><?php echo Text::_('Sección:'); ?>: <?php echo $this['sections'][$this['criteria']->section]; ?></label>
                <input type="hidden" name="section" value="<?php echo $this['criteria']->section; ?>" />
            </p>
        <?php endif; ?>

        <dl>
            <dt><label for="criteria-title"><?php echo Text::_('Título:'); ?></label></dt>
            <dd><input type="text" name="title" id="criteria-title" value="<?php echo $this['criteria']->title; ?>" /></dd>
            <dt><label for="criteria-description"><?php echo Text::_('Descripción:'); ?></label></dt>
            <dd><textarea name="description" id="criteria-description" cols="60" rows="10"><?php echo $this['criteria']->description; ?></textarea></dd>
            <dt><label for="criteria-order"><?php echo Text::_('Posición:'); ?></label></dt>
            <dd>
                <select name="move">
                    <option value="same" selected="selected" disabled><?php echo Text::_('Tal cual'); ?></option>
                    <option value="up"><?php echo Text::_('Antes de '); ?></option>
                    <option value="down"><?php echo Text::_('Después de '); ?></option>
                </select>&nbsp;
                <input type="text" name="order" id="criteria-order" value="<?php echo $this['criteria']->order; ?>" size="4" />
                &nbsp;<?php echo Text::_('number'); ?>&nbsp;<span id="criteria-num"><?php echo $this['criteria']->cuantos; ?></span>
            </dd>
        </dl>

        <input type="submit" name="save" value="<?php echo Text::_("Guardar"); ?>" />
    </form>
</div>