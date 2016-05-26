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
    Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\Message;

$project = $this['skillmatching'];

if (!$project instanceof Model\Project) {
    Message::Error(Text::get('admin-rebase-error-corruptproject'));
    throw new Redirection('/admin/skillmatchings');
}


?>
<script type="text/javascript">
    function idverify() {
        if ($('#newid').val() == '') {
            alert('No has puesto la nueva id');
            return false;
        } else {
            return true;
        }
    }
</script>
<div class="widget">
    <p><?php echo Text::_('Caution id change'); ?></p>
    <?/*<p><?php echo Text::_('OJO! Cambiar la Id del proyecto afecta a'); ?> <strong><?php echo Text::_('TODO'); ?></strong> <?php echo Text::_('lo referente al proyecto!.'); ?></p>*/?>

    <form method="post" action="/admin/skillmatchings/rebase/<?php echo $project->id; ?>" onsubmit="return idverify();">
        <input type="hidden" name="id" value="<?php echo $project->id ?>" />
        <input type="hidden" name="oldid" value="<?php echo $project->id ?>" />

        <p>
            <label><?php echo Text::_('Nueva ID para el proyecto'); ?>:<br />
                <input type="text" name="newid"  id="newid" />
                       
            </label>
        </p>

        <?php if ($project->status >= 3) : ?>
        <h3><?php echo Text::_('OJO!! El proyecto est&aacute; publicado'); ?></h3>
        <p>
            <?/*php echo Text::_('Debes marcar expresamente la siguiente casilla, sino dar&aacute; error por estado de proyecto.'); ?><br />*/?>
            <?php echo Text::_('Caution pj error'); ?><br />
            <label><?php echo Text::_('Lavel rebase'); ?>:<br />
                <input type="checkbox" name="force" value="1" />
            </label>

        </p>
        <?php endif; ?>
        <input type="submit" name="proceed" value="<?php echo Text::_('Rebase'); ?>" />

    </form>
</div>
