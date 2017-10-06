<?php
use Goteo\Library\Text,
    Goteo\Model\Invest;

$filters = $this['filters'];

$emails = Invest::emails(true);
?>
<div class="widget board">
    <h3 class="title"><?php echo Text::_('Rewards Filtros'); ?></h3>
    <form id="filter-form" action="/admin/rewards" method="get">
        <div class="rewards-search-line">
            <label for="projects-filter"><?php echo Text::_("Proyecto"); ?></label><br />
            <select id="projects-filter" name="project" onchange="document.getElementById('filter-form').submit();">
                <option value=""><?php echo Text::_('Todos los proyectos'); ?></option>
            <?php foreach ($this['projects'] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters['project'] === (string) $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        
        <div class="rewards-search-line">
            <label for="name-filter"><?php echo Text::_('User'); ?></label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <div class="rewards-search-line">
            <label for="status-filter"><?php echo Text::_('Mostrar por estado de recompensa'); ?>:</label><br />
            <select id="status-filter" name="status" >
                <option value=""><?php echo Text::_('Todos'); ?></option>
            <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <input type="submit" value="<?php echo Text::_("Buscar"); ?>" />
    </form>
    <br clear="both" />
    <a href="/admin/rewards/?reset=filters"><?php echo Text::_("Quitar filtros"); ?></a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p><?php echo Text::_("Es necesario poner algun filtro, hay demasiados registros!"); ?></p>
<?php elseif (!empty($this['list'])) : ?>
    <table width="100%">
        <thead>
            <tr>
                <th class="change_btn"></th>
                <th><?php echo Text::_('Cofinanciador'); ?></th>
                <th><?php echo Text::_("Proyecto"); ?></th>
                <th><?php echo Text::_('Recompensa'); ?></th>
                <th class="support_status"><?php echo Text::_("Estado"); ?></th>
                <th class="implementation_status"></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $reward) : ?>
            <tr>
                <td><a href="/admin/rewards/edit/<?php echo $reward->invest ?>" >[<?php echo Text::_("Modificar"); ?>]</a></td>
                <td><a href="/admin/users/manage/<?php echo $reward->user ?>" target="_blank" title="<?php echo $reward->name; ?>"><?php echo $reward->email; ?></a></td>
                <td><a href="/admin/projects/?name=<?php echo $this['projects'][$reward->project] ?>" target="_blank"><?php echo Text::recorta($this['projects'][$reward->project], 20); if (!empty($invest->campaign)) echo '<br />('.$this['calls'][$invest->campaign].')'; ?></a></td>
                <td><?php echo $reward->reward_name ?></td>
                <?php if (!$reward->fulfilled) : ?>
                    <td style="color: red;" ><?php echo Text::_('Pendiente'); ?></td>
                    <td><a href="<?php echo "/admin/rewards/fulfill/{$reward->invest}"; ?>">[<?php echo Text::_("Marcar cumplida"); ?>]</a></td>
                <?php else : ?>
                    <td style="color: green;" ><?php echo Text::_('Cumplido'); ?></td>
                    <td><a href="<?php echo "/admin/rewards/unfill/{$reward->invest}"; ?>">[<?php echo Text::_("Marcar pendiente"); ?>]</a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p><?php echo Text::_('No hay aportes que cumplan con los filtros.'); ?></p>
<?php endif;?>
</div>