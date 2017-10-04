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
	Goteo\Library\Text;

$bodyClass = 'discover';
include VIEW_PATH . '/prologue.html.php';
include VIEW_PATH . '/header.html.php' ?>

<script type="text/javascript">	
    jQuery(document).ready(function ($) {
        /* todo esto para cada tipo de grupo */
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
    <div id="main">
        <?php echo new View(VIEW_PATH . '/discover/searcher.html.php',
                            array(
                                'categories' => $categories,
                                'locations'  => $locations,
                                'rewards'    => $rewards,
                                'skills' => $skills,
                                'types' => $types
                            )
            ); ?>

    <?php foreach ($this['lists'] as $type=>$list) :
        if (array_empty($list))
            continue;
        ?>
        <div class="widget projects">
            <h2 class="title"><?php echo $this['title'][$type]; ?></h2>
            <?php foreach ($list as $group=>$projects) : ?>
            <? if($group == 1): ?>

                <div class="discover-group discover-group-<?php echo $type ?>" id="discover-group-<?php echo $type ?>-<?php echo $group ?>">

                <?php foreach ($projects['items'] as $project) :
                    if (get_class($project) == 'Goteo\Model\Skillmatching'){
                        echo new View(VIEW_PATH . '/skillmatching/widget/skillmatchings.html.php', array('skillmatching' => $project));
                    } else {
                        echo new View(VIEW_PATH . '/project/widget/project.html.php', array('project' => $project));
                    }
                endforeach; ?>

                </div>
            <? endif; ?>

            <?php endforeach; ?>

            <!-- carrusel de imagenes -->
            <div class="navi-bar">
                <a class="all" href="/discover/view/<?php echo $type; ?>"><?php echo Text::get('regular-see_all'); ?></a>
            </div>

        </div>

    <?php endforeach; ?>

    </div>

    <?php include VIEW_PATH . '/footer.html.php' ?>

<?php include VIEW_PATH . '/epilogue.html.php' ?>