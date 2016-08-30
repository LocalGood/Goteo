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
    Goteo\Model\Project,
    Goteo\Library\Text;
//$projects = Goteo\Model\Project::published("available");
//$skillmatchings = Goteo\Model\Skillmatching::published("available");
// random y que solo pinte seis si hubiera más
if ($_GET['type'] == 'p'){
    $allpjsm = Goteo\Model\Project::published("available");
} else if ($_GET['type'] == 's'){
    $allpjsm = Goteo\Model\Skillmatching::published("available");
} else {
    $allpjsm = Goteo\Model\Skillmatching::published("available_pj_sm");
}
?>
<div class="widget projects">

    <?php /*
    <h2 class="title">プロジェクト<?php //echo Text::get('home-projects-header'); ?></h2>
    */ ?>

    <?php foreach ($allpjsm as $pj) :
        if (get_class($pj) == 'Goteo\Model\Skillmatching'){
            echo new View('view/skillmatching/widget/skillmatchings.html.php', array('skillmatching' => $pj));
        } else {
            echo new View('view/project/widget/project.html.php', array('project' => $pj));
        };
    endforeach ?>

</div>
<?php /*
<div class="widget projects">

    <h2 class="title">プロジェクト<?php //echo Text::get('home-projects-header'); ?></h2>

    <?php foreach ($projects as $project) :

        ?>

        <?php echo new View('view/project/widget/project.html.php', array(
            'project' => $project
        )) ?>

    <?php endforeach ?>

</div>

<div class="widget projects">

    <h2 class="title">スキルマッチング<?php //echo Text::get('home-projects-header'); ?></h2>

    <?php foreach ($skillmatchings as $skillmatching) :

        ?>

        <?php echo new View('view/skillmatching/widget/skillmatchings.html.php', array(
        'skillmatching' => $skillmatching
    )) ?>

    <?php endforeach ?>

</div>
 */ ?>