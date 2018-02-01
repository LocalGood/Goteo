<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *  This file is part of Goteo.
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

$mailing = $this['mailing'];
$list = $this['list'];
$detail = $this['detail'];

$link = LG_BASE_URL_GT.'/mail/'.base64_encode(md5(uniqid()).'¬any¬'.$mailing->mail).'/?email=any';

$title = array(
    'receivers' => 'Destinatarios',
    'sended' => 'Enviados',
    'failed' => 'Falllidos',
    'pending' => 'Pendientes'
);
?>
<?php if (!empty($mailing)) : ?>
<div class="widget board">
        <p>
           Asunto: <strong><?php echo $mailing->subject ?></strong><br />
           Iniciado el: <strong><?php echo $mailing->date ?></strong><br />
           Estado del envío automático: <?php echo ($mailing->active) 
               ? '<span style="color:green;font-weight:bold;">' . Text::_('Activo') . '</span>'
               : '<span style="color:red;font-weight:bold;">' . Text::_('Inactivo') . '</span>' ?>
        </p>

    <table>
        <thead>
            <tr>
                <th><!-- Si no ves --></th>
                <th><?php echo Text::_('Fecha'); ?></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=receivers" title="Ver destinatarios"><?php echo Text::_('Destinatarios'); ?></a></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=sended" title="Ver enviados"><?php echo Text::_('Enviados'); ?></a></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=failed" title="Ver fallidos"><?php echo Text::_('Fallidos'); ?></a></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=pending" title="Ver pendientes"><?php echo Text::_('Pendientes'); ?></a></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="<?php echo $link; ?>" target="_blank">[<?php echo Text::_("Si no ves"); ?>]</a></td>
                <td><?php echo $mailing->date; ?></td>
                <td style="width:15%"><?php echo $mailing->receivers; ?></td>
                <td style="width:15%"><?php echo $mailing->sended; ?></td>
                <td style="width:15%"><?php echo $mailing->failed; ?></td>
                <td style="width:15%"><?php echo $mailing->pending; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($detail)) : ?>
<h3><?php echo Text::_('Viendo el listado completo de los '); ?><?php echo $title[$detail] ?></h3>
<div class="widget board">
    <table>
        <tr>
            <th><?php echo Text::_('Email'); ?></th>
            <th><?php echo Text::_('Alias'); ?></th>
            <th><?php echo Text::_('Usuario'); ?></th>
        </tr>
        <?php foreach ($list as $user) : ?>
        <tr>
            <?php echo "<td>$user->email</td><td>$user->name</td><td>$user->user</td>" ?>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>