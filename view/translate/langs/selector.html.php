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

use Goteo\Library\i18n\Lang,
    Goteo\Library\Text;

$langs = Lang::getAll();
unset($langs['es']); // no se puede traducir a español

$actual = Lang::get($_SESSION['translate_lang']);

$section = isset($this['table']) ? $this['table'] : $this['section'];

?>
<div id="lang-selector">
    <form id="selector-form" name="selector_form" action="<?php echo '/translate/select/'.$section.'/'.$this['action'].'/'.$this['id'].'/'.$this['filter'].'&page='.$_GET['page']; ?>" method="post">
    <?php if (!empty($actual->id)) : ?>
        <?php echo Text::get('dashboard-translate_lang'); ?><strong><?php echo $actual->name ?></strong>　<label for="selector"><?php echo Text::get('dashboard-translate_lang_change'); ?></label>
    <?php else : ?>
        <?php echo Text::get('dashboard-translate_no_select_lang'); ?><label for="selector">Traducir a:</label>
    <?php endif; ?>
    <select id="selector" name="lang" onchange="document.getElementById('selector-form').submit();">
    <?php foreach ($langs as $lang) : ?>
        <option value="<?php echo $lang->id; ?>"<?php if ($lang->id == $actual->id) echo ' selected="selected"'; ?>><?php echo $lang->name; ?></option>
    <?php endforeach; ?>
    </select>
    </form>
</div>
