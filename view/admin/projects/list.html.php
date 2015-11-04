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

$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters .= "&{$key}={$value}";
}

$pagedResults = new \Paginated($this['projects'], 10, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<a href="/admin/translates" class="button"><?php echo Text::_("Asignar traductores"); ?></a>

<div class="widget board">
    <form id="filter-form" action="/admin/projects" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <table>
            <tr>
                <td>
                    <label for="name-filter"><?php echo Text::_("Alias/Email del autor"); ?>:</label><br />
                    <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
                </td>
                <td>
                    <label for="category-filter"><?php echo Text::_("De la categoría"); ?>:</label><br />
                    <select id="category-filter" name="category" onchange="document.getElementById('filter-form').submit();">
                        <option value=""><?php echo Text::_("Cualquier categoría"); ?></option>
                    <?php foreach ($this['categories'] as $categoryId=>$categoryName) : ?>
                        <option value="<?php echo $categoryId; ?>"<?php if ($filters['category'] == $categoryId) echo ' selected="selected"';?>><?php echo $categoryName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <?/*<label for="skill-filter"><?php echo Text::_("De la skill"); ?>:</label><br />*/?>
                    <label for="skill-filter"><?php echo Text::get("admin-projects-skill"); ?>:</label><br />
                    <select id="skill-filter" name="skill" onchange="document.getElementById('filter-form').submit();">
                    <option value=""><?php echo Text::get('admin-projects-type-skill'); ?></option>
                     
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
                    <label for="proj_name-filter"><?php echo Text::_("Nombre del proyecto"); ?>:</label><br />
                    <input id="proj_name-filter" name="proj_name" value="<?php echo $filters['proj_name']; ?>" style="width:250px"/>
                </td>
                <td>
                    <label for="status-filter"><?php echo Text::_("Mostrar por estado"); ?>:</label><br />
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="-1"<?php if ($filters['status'] == -1) echo ' selected="selected"';?>><?php echo Text::_("Todos los estados"); ?></option>
                        <option value="-2"<?php if ($filters['status'] == -2) echo ' selected="selected"';?>><?php echo Text::_("En negociacion"); ?></option>
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="filter" value="<?php echo Text::_('Buscar') ?>">
                </td>
                <td>
                    <label for="order-filter"><?php echo Text::_("Ordenar por"); ?>:</label><br />
                    <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this['orders'] as $orderId=>$orderName) : ?>
                        <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
    </form>
    <a href="/admin/projects/?reset=filters"><?php echo Text::_("Quitar filtros"); ?></a>
<?php if ($filters['filtered'] != 'yes') : ?>
    <p><?php echo Text::_("Es necesario poner algun filtro, hay demasiados registros!"); ?></p>
<?php elseif (empty($this['projects'])) : ?>
    <p><?php echo Text::_("No se han encontrado registros"); ?></p>
<?php else: ?>
    <p><?php echo Text::_("OJO! Resultado limitado a 999 registros como máximo."); ?></p>
<?php endif; ?>
</div>

    
<?php if (!empty($this['projects'])) : 
    while ($project = $pagedResults->fetchPagedRow()) : 
?>
<div class="widget board">
    <table class="t_border">
        <thead>
            <tr>
                <th><?php echo Text::_("Proyecto"); ?></th> <!-- edit -->
                <th style="min-width: 150px;"><?php echo Text::_("Creador"); ?></th> <!-- mailto -->
                <th style="width: 100px;"><?php echo Text::_("Recibido"); ?></th> <!-- enviado a revision -->
                <th style="min-width: 70px;"><?php echo Text::_("Estado"); ?></th>
                <th style="min-width: 50px;"><?php echo Text::_("Nodo"); ?></th>
                <th style="min-width: 88px;"><?php echo Text::_("M&iacute;nimo"); ?></th>
                <th style="min-width: 88px;"><?php echo Text::_("&Oacute;ptimo"); ?></th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td><a href="/project/<?php echo $project->id; ?>" target="_blank" title="Preview"><?php echo $project->name; ?></a></td>
                <td style="word-break: break-all;"><a href="mailto:<?php echo $project->user->email; ?>"><?php echo substr($project->user->email, 0, 100); ?></a></td>
                <?php $s_date = (empty($project->updated)) ? '未完了' : date('y/m/d', strtotime($project->updated)); ?>
                <?/*<td><?php echo date('Y年m月n日', strtotime($project->updated)); ?></td>*/?>
                <td><?php echo $s_date; ?></td>

                <td><?php echo ($project->status == 1 && !$project->draft) ? '<span style="color: green;">' . Text::_('En negociación') . '</span>' : $this['status'][$project->status]; ?></td>
                <td style="text-align: center;"><?php echo $project->node; ?></td>
                <td style="text-align: right;"><?php echo \amount_format($project->mincost) . Text::_('yen'); ?></td>
                <td style="text-align: right;"><?php echo \amount_format($project->maxcost) . Text::_('yen'); ?></td>
            </tr>
            <tr>
                <td colspan="7"><?php 
                    if ($project->status < 3)  echo Text::_('information rate') . "<strong>{$project->progress}%</strong>";
                    $round ='';
                    if ($project->status == 3 && $project->round > 0):
                        if($project->round == 1):
                            $round = 'st';
                        elseif($project->round == 2):
                            $round = 'nd';
                        endif;
                        echo "{$project->round}".$round.Text::_('ronda')."/".Text::_('Le quedan')."{$project->days}".Text::_('días') . "&nbsp;&nbsp;&nbsp;<strong>" . Text::_('Achieved') . ":</strong> ".\amount_format($project->invested). Text::_('yen') . "&nbsp;&nbsp;&nbsp;<strong>" . Text::_('Cofin') . ":</strong> {$project->num_investors}&nbsp;&nbsp;&nbsp;<strong>" . Text::_('Colab') . ":</strong> {$project->num_messegers}";
                    endif;
                ?></td>
            </tr>
            <tr>
                <td colspan="7">
                    <?php echo Text::_("IR A"); ?>:&nbsp;
                    <a href="/project/edit/<?php echo $project->id; ?>" target="_blank">[<?php echo Text::_("Edit project"); ?>]</a>
                    <a href="/admin/users/?id=<?php echo $project->owner; ?>" target="_blank">[<?php echo Text::_("Impulsor"); ?>]</a>
                    <?php if (!isset($_SESSION['admin_node']) 
                            || (isset($_SESSION['admin_node']) && $_SESSION['admin_node'] == \GOTEO_NODE)
                            || (isset($_SESSION['admin_node']) && $user->node == $_SESSION['admin_node'])) : ?>
                    <a href="/admin/accounts/?projects=<?php echo $project->id; ?>" title="Ver sus aportes">[<?php echo Text::_("Support situation"); ?>]</a>
                        <?php if($project->status >= 4): ?>
                    <a href="/admin/projects/evaluation/<?php echo $project->id; ?>" title="Project Evaluation">[<?php echo Text::get("project-menu-evaluation"); ?>]</a>
                        <?php endif; ?>
                    <?php else:  ?>
                    <a href="/admin/invests/?projects=<?php echo $project->id; ?>" title="Ver sus aportes">[<?php echo Text::_("Supporters List"); ?>]</a>
                    <?php endif; ?>
                    <a href="/admin/users/?project=<?php echo $project->id; ?>" title="Ver sus cofinanciadores">[<?php echo Text::_("Cofinanciadores"); ?>]</a>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    :&nbsp;
                    <a href="<?php echo "/admin/projects/dates/{$project->id}"; ?>">[<?php echo Text::_("Fechas"); ?>]</a>
                    <?/*<a href="<?php echo "/admin/projects/accounts/{$project->id}"; ?>">[<?php echo Text::_("Cuentas"); ?>]</a>*/?>
                    <?php if ($project->status < 4) : ?><a href="<?php echo "/admin/projects/rebase/{$project->id}"; ?>" onclick="return confirm('<?php echo Text::_("Esto es MUY DELICADO, seguimos?"); ?>');">[<?php echo Text::_("Id"); ?>]</a><?php endif; ?>
                    &nbsp;|&nbsp;
                    <?php if ($project->status < 2) : ?><a href="<?php echo "/admin/projects/review/{$project->id}"; ?>" onclick="return confirm('<?php echo Text::_("El creador no podrá editarlo más, ok?"); ?>');">[<?php echo Text::_("A revisión"); ?>]</a><?php endif; ?>
                    <?php if ($project->status < 3 && $project->status > 0) : ?><a href="<?php echo "/admin/projects/publish/{$project->id}"; ?>" onclick="return confirm('<?php echo Text::_("El proyecto va a comenzar los 40 dias de la primera ronda de campaña, ¿comenzamos?"); ?>');">[<?php echo Text::_("Publicar"); ?>]</a><?php endif; ?>
                    <?php if ($project->status != 1) : ?><a href="<?php echo "/admin/projects/enable/{$project->id}"; ?>" onclick="return confirm('<?php echo Text::_("Mucho Ojo! si el proyecto esta en campaña, ¿Reabrimos la edicion?"); ?>');">[<?php echo Text::_("Reabrir edición"); ?>]</a><?php endif; ?>
                    <?php if ($project->status == 4) : ?><a href="<?php echo "/admin/projects/fulfill/{$project->id}"; ?>" onclick="return confirm('<?php echo Text::_("El proyecto pasara a ser un caso de éxito, ok?"); ?>');">[<?php echo Text::_("Retorno Cumplido"); ?>]</a><?php endif; ?>
                    <?php if ($project->status == 5) : ?><a href="<?php echo "/admin/projects/unfulfill/{$project->id}"; ?>" onclick="return confirm('<?php echo Text::_("Lo echamos un paso atras, ok?"); ?>');">[<?php echo Text::_("Retorno Pendiente"); ?>]</a><?php endif; ?>
                    <?php if ($project->status < 3 && $project->status > 0) : ?><a href="<?php echo "/admin/projects/cancel/{$project->id}"; ?>" onclick="return confirm('<?php echo Text::_("El proyecto va a desaparecer del admin, solo se podra recuperar desde la base de datos, Ok?"); ?>');">[<?php echo Text::_("Descartar"); ?>]</a><?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <?php echo Text::_("GESTIONAR"); ?>:&nbsp;
                    <?php if ($project->status == 1) : ?><a href="<?php echo "/admin/reviews/add/{$project->id}"; ?>" onclick="return confirm('<?php echo Text::_("Se va a iniciar revisión de un proyecto en estado Edición, ok?"); ?>');">[<?php echo Text::_("Iniciar revisión"); ?>]</a><?php endif; ?>
                    <?php if ($project->status == 2) : ?><a href="<?php echo "/admin/reviews/?project=".urlencode($project->id); ?>">[<?php echo Text::_("Ir a la revisión"); ?>]</a><?php endif; ?>
                    <?php if ($project->translate) : ?><a href="<?php echo "/admin/translates/edit/{$project->id}"; ?>">[<?php echo Text::_("Ir a la traducción"); ?>]</a>
                    <?php else : ?><a href="<?php echo "/admin/translates/add/?project={$project->id}"; ?>">[<?php echo Text::_("Habilitar traducción"); ?>]</a><?php endif; ?>
                    <a href="/admin/projects/images/<?php echo $project->id; ?>">[<?php echo Text::_("Organizar imágenes"); ?>]</a>
                    <?php if ($project->status < 3) : ?><a href="<?php echo "/admin/projects/reject/{$project->id}"; ?>" onclick="return confirm('<?php echo Text::_("Se va a enviar un mail automáticamente pero no cambiará el estado, ok?"); ?>');">[<?php echo Text::_("Rechazo express"); ?>]</a><?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <?php if (in_array($project->status,array(3,4))) : ?><a href="<?php echo "/admin/invests/csv/{$project->id}"; ?>">[AXES用の決済CSVをダウンロード]</a><?php endif; ?>
                    <?php if (in_array($project->status,array(3,4))) : ?><a href="<?php echo "/admin/invests/dopay/{$project->id}"; ?>" onclick="return confirm('AXES用の決済CSVの処理後に実行してください。実行してよろしいですか？');">[AXES用の決済CSVの処理後に実行]</a><?php endif; ?>
                </td>
            </tr>
        </tbody>

    </table>
</div>
<?php endwhile; ?>
<ul id="pagination">
<?php   $pagedResults->setLayout(new DoubleBarLayout());
        echo $pagedResults->fetchPagedNavigation($the_filters); ?>
</ul>
<?php endif; ?>
