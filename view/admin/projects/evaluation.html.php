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

$project = $this['project'];
$evaluation = $this['evaluation'];
?>
<div class="widget board">
    <form method="post" action="/admin/projects/evaluation/<?php echo $this['project']->id; ?>">
        <input type="hidden" name="id" value="<?php echo $project->id; ?>" />
        <p>プロジェクト: <span style="font-weight:bold"><?php echo $this['project']->name; ?></span> の評価</p>
        <textarea id="richtext_content" name="content" cols="100" rows="20"><?php echo $evaluation->content; ?></textarea>
        <p><input type="submit" name="save" value="<?= Text::_('Guardar'); ?>" /></p>
    </form>
</div>