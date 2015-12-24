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

// paginacion
require_once 'library/pagination/pagination.php';

$filters = $this['filters'];
$users = $this['users'];

// la ordenación por cantidad y proyectos hay que hacerla aqui
if ($filters['order'] == 'amount') {
    uasort($users,
        function ($a, $b) {
            if ($a->namount == $b->namount) return 0;
            return ($a->namount < $b->namount) ? 1 : -1;
            }
        );
}
if ($filters['order'] == 'projects') {
    uasort($users,
        function ($a, $b) {
            if ($a->nprojs == $b->nprojs) return 0;
            return ($a->nprojs < $b->nprojs) ? 1 : -1;
            }
        );
}

$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters .= "&{$key}={$value}";
}
$pagedResults = new \Paginated($users, 20, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<a href="/admin/users/add" class="button"><?php echo Text::_('Crear usuario'); ?></a>

<div class="widget board">
    <form id="filter-form" action="/admin/users" method="get">
<!-- Change from the table tag -->
        <dl>
            <dt><label for="role-filter"><?php echo Text::_('Con rol'); ?>:</label></dt>
            <dd>
                <select id="role-filter" name="role" onchange="document.getElementById('filter-form').submit();">
                <option value=""><?php echo Text::_('Cualquier rol'); ?></option>
                <?php foreach ($this['roles'] as $roleId=>$roleName) : ?>
                    <option value="<?php echo $roleId; ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?php echo $roleName; ?></option>
                <?php endforeach; ?>
                </select>
            </dd>

            <dt><label for="interest-filter"><?php echo Text::_('Mostrar usuarios interesados categoría'); ?>:</label></dt>
            <dd>
                <select id="interest-filter" name="interest" onchange="document.getElementById('filter-form').submit();">
                <option value=""><?php echo Text::_('Cualquier interés'); ?></option>
                <?php foreach ($this['interests'] as $interestId=>$interestName) : ?>
                    <option value="<?php echo $interestId; ?>"<?php if ($filters['interest'] == $interestId) echo ' selected="selected"';?>><?php echo $interestName; ?></option>
                <?php endforeach; ?>
                </select>
            </dd>
            <dt><label for="name-filter"><?php echo Text::_('Por nombre o email'); ?>:</label></dt>
            <dd class="name">
                <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" />
                <input type="submit" name="filter" value="<?php echo Text::_('Buscar') ?>">
            </dd>
            <dt><label for="skill-filter"><?php echo Text::_('Mostrar usuarios interesados en'); ?>:</label></dt>
            <dd>
                <select id="skill-filter" name="skill" onchange="document.getElementById('filter-form').submit();">
                    <option value=""><?php echo Text::_('Cualquier skill'); ?></option>

                    <?php foreach ($this['skills'] as $key=>$value) : ?>
                    <?php if(empty($value->parent_skill_id)) : ?>
                    <?php if(!empty($this['skills'][$key-1])) : ?></optgroup><?php endif;?>
                    <optgroup label="<?php echo $value->name?>">
                        <?php else : ?>
                            <option value="<?php echo $key; ?>"<?php if ($filters['skill'] == $key) echo ' selected="selected"';?>><?php echo $key.$value->name; ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </dd>


            <dt><label for="order-filter"><?php echo Text::_('Ver por'); ?>:</label></dt>
            <dd>
                <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this['orders'] as $orderId=>$orderName) : ?>
                        <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>

        <?/*
        <table>
            <tr>
                <td>
                    <label for="role-filter"><?php echo Text::_('Con rol'); ?>:</label><br />
                    <select id="role-filter" name="role" onchange="document.getElementById('filter-form').submit();">
                        <option value=""><?php echo Text::_('Cualquier rol'); ?></option>
                    <?php foreach ($this['roles'] as $roleId=>$roleName) : ?>
                        <option value="<?php echo $roleId; ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?php echo $roleName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="interest-filter"><?php echo Text::_('Mostrar usuarios interesados categoría'); ?>:</label><br />
                    <select id="interest-filter" name="interest" onchange="document.getElementById('filter-form').submit();">
                        <option value=""><?php echo Text::_('Cualquier interés'); ?></option>
                    <?php foreach ($this['interests'] as $interestId=>$interestName) : ?>
                        <option value="<?php echo $interestId; ?>"<?php if ($filters['interest'] == $interestId) echo ' selected="selected"';?>><?php echo $interestName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="role-filter"><?php echo Text::_('Mostrar usuarios con rol'); ?>:</label><br />
                    <select id="role-filter" name="role" onchange="document.getElementById('filter-form').submit();">
                        <option value=""><?php echo Text::_('Cualquier rol'); ?></option>
                    <?php foreach ($this['roles'] as $roleId=>$roleName) : ?>
                        <option value="<?php echo $roleId; ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?php echo $roleName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td colspan="2">
                    <label for="name-filter"><?php echo Text::_('Por nombre o email'); ?>:</label><br />
                    <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" />
                </td>
            </tr>
            <tr>
                <td collspan="5">
                    <label for="skill-filter"><?php echo Text::_('Mostrar usuarios interesados en'); ?>:</label><br />
                    <select id="skill-filter" name="skill" onchange="document.getElementById('filter-form').submit();">
                    <option value=""><?php echo Text::_('Cualquier skill'); ?></option>
                     
                    <?php foreach ($this['skills'] as $key=>$value) : ?>
                        <?php if(empty($value->parent_skill_id)) : ?>
                        <?php if(!empty($this['skills'][$key-1])) : ?></optgroup><?php endif;?>
                        <optgroup label="<?php echo $value->name?>">
                        <?php else : ?>
                            <option value="<?php echo $key; ?>"<?php if ($filters['skill'] == $key) echo ' selected="selected"';?>><?php echo $key.$value->name; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                        </optgroup>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="filter" value="<?php echo Text::_('Buscar') ?>">
                </td>
                <td>
                    <label for="order-filter"><?php echo Text::_('Ver por'); ?>:</label><br />
                    <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this['orders'] as $orderId=>$orderName) : ?>
                        <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
                */?>

    </form>
    <br clear="both" />
    <a href="/admin/users/?reset=filters"><?php echo Text::_("Quitar filtros"); ?></a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p><?php echo Text::_("Es necesario poner algun filtro, hay demasiados registros!"); ?></p>
<?php elseif (!empty($users)) : ?>
    <table>
        <thead>
            <tr>
                <th><?php echo Text::_('Nombre de usuario'); ?></th> <!-- view profile -->
                <th><?php echo Text::_('User ID'); ?></th>
                <th><?php echo Text::_('Email'); ?></th>
                <th><?php echo Text::_('Amount of project'); ?></th>
                <th><?php echo Text::_('Cantidad aportada'); ?></th>
                <th><?php echo Text::_('Registration Date'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php while ($user = $pagedResults->fetchPagedRow()) :
                $adminNode = ($user->admin) ? $user->admin_node : null;
                ?>
            <tr class="record1">
                <td><a href="/user/profile/<?php echo $user->id; ?>" target="_blank" <?php echo ($adminNode != 'goteo') ? 'style="color: green;" title="Admin nodo '.$adminNode.'"' : 'title="Ver perfil público"'; ?>><?php echo mb_substr($user->name, 0, 20); ?></a></td>
                <td><strong><?php echo substr($user->id, 0, 20); ?></strong></td>
                <td><a href="mailto:<?php echo $user->email; ?>"><?php echo $user->email; ?></a></td>
                <td class="pj_count"><?php echo $user->nprojs; ?></td>
                <td class="admin_amount"><?php echo \amount_format($user->namount) . Text::_('yen'); ?> </td>
                <td><?php echo date('Y/m/d H:i:s', strtotime(preg_replace('/\//', '-', $user->register_date))); ?></td>
            </tr>
            <tr class="record2">
                <td><a href="/admin/users/manage/<?php echo $user->id; ?>" title="Gestionar">[<?php echo Text::_("Gestionar"); ?>]</a></td>
                <td><?php if ($user->nprojs > 0) {
                    if (!isset($_SESSION['admin_node']) || (isset($_SESSION['admin_node']) && $user->node == $_SESSION['admin_node'])) : ?>

                <a href="/admin/accounts/?name=<?php echo $user->email; ?>" title="Ver sus aportes">[<?php echo Text::_("To management of support"); ?>]</a>
                <?php else:  ?>
                <a href="/admin/invests/?name=<?php echo $user->email; ?>" title="Ver sus aportes">[<?php echo Text::_("Aportes"); ?>]</a>
                <?php endif; } ?></td>
                <td colspan="5" style="color:blue;">
                    <?php echo (!$user->active && $user->hide) ? Text::_('Baja') : ''; ?>
                    <?php echo $user->active ? '' : Text::_('Inactivo'); ?>
                    <?php echo $user->hide ? Text::_('Oculto') : ''; ?>
                    <?php echo $user->checker ? Text::_('Revisor') : ''; ?>
                    <?php echo $user->translator ? Text::_('Traductor') : ''; ?>
                    <?php echo $user->caller ? Text::_('Convocador') : ''; ?>
                    <?php echo $user->admin ? Text::_('Admin') : ''; ?>
                    <?php echo $user->manager ? Text::_('Gestor') : ''; ?>
                    <?php echo $user->vip ? Text::_('VIP') : ''; ?>
                    <?php echo $user->localadmin ? Text::_('LocalAdmin') : ''; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>

    </table>
</div>
<ul id="pagination">
<?php   $pagedResults->setLayout(new DoubleBarLayout());
        echo $pagedResults->fetchPagedNavigation($the_filters); ?>
</ul>
<?php else : ?>
<p><?php echo Text::_("No se han encontrado registros"); ?></p>
<?php endif; ?>
