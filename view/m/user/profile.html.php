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
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$bodyClass = 'user-profile';
include VIEW_PATH . '/prologue.html.php';
include VIEW_PATH . '/header.html.php';

$user = $this['user'];
$worthcracy = Worth::getAll();
?>
<script type="text/javascript">

    jQuery(document).ready(function ($) {

        /* todo esto para cada lista de proyectos (flechitas navegacion) */
        <?php foreach ($this['lists'] as $type=>$list) :
            if(array_empty($list)) continue; ?>
            $("#discover-group-<?php echo $type ?>-1").show();
            $("#navi-discover-group-<?php echo $type ?>-1").addClass('active');
        <?php endforeach; ?>

        $(".discover-arrow").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-discover-group-"+this.rev).removeClass('active');
            $(".discover-group-"+this.rev).hide();
            /* Poner acctive a este, mostrar este */
            $("#navi-discover-group-"+this.rel).addClass('active');
            $("#discover-group-"+this.rel).show();
        });

        $(".navi-discover-group").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-discover-group-"+this.rev).removeClass('active');
            $(".discover-group-"+this.rev).hide();
            /* Poner acctive a este, mostrar este */
            $("#navi-discover-group-"+this.rel).addClass('active');
            $("#discover-group-"+this.rel).show();
        });

    });
</script>

<?php echo new View(VIEW_PATH . '/user/widget/header.html.php', array('user'=>$user)) ?>

<?php if(isset($_SESSION['messages'])) { include VIEW_PATH . '/header/message.html.php'; } ?>

<div id="main">

    <div class="center profile">

        <?php echo new View(VIEW_PATH . '/user/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?>

        <?php echo new View(VIEW_PATH . '/user/widget/about.html.php', array('user' => $user, 'projects' => $this['projects'])) ?>

        <?php echo new View(VIEW_PATH . '/user/widget/social.html.php', array('user' => $user)) ?>


        <?php foreach ($this['lists'] as $type=>$list) :
            if (array_empty($list))
                continue;
            ?>
            <div class="widget projects">
                <h2 class="title"><?php echo Text::get('profile-'.$type.'-header'); ?></h2>
                <?php foreach ($list as $group=>$projects) : ?>
                    <div class="discover-group discover-group-<?php echo $type ?>" id="discover-group-<?php echo $type ?>-<?php echo $group ?>">

                        <?php foreach ($projects['items'] as $project) :
                            if ($type == 'my_projects')  {
                                echo new View(VIEW_PATH . '/project/widget/project.html.php', array('project' => $project));
                            } else {
                                echo new View(VIEW_PATH . '/project/widget/project.html.php', array('project' => $project, 'investor' => $user));
                            }
                        endforeach; ?>

                    </div>
                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    </div>
    <div class="side">
        <?php if (!empty($_SESSION['user'])) echo new View(VIEW_PATH . '/user/widget/investors.html.php', $this) ?>
        <?php echo new View(VIEW_PATH . '/user/widget/sharemates.html.php', $this) ?>
    </div>
</div>
<?php include VIEW_PATH . '/footer.html.php' ?>
<?php include VIEW_PATH . '/epilogue.html.php' ?>
