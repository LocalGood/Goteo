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
    Goteo\Model\User,
    Goteo\Model\Project\Cost,
    Goteo\Model\Project\Support,
    Goteo\Model\Project\Category,
    Goteo\Model\Project\Skill,
    Goteo\Model\Blog,
    Goteo\Library\Text;

$project = $this['project'];
$show    = $this['show'];
$step    = $this['step'];
$post    = $this['post'];
$blog    = $this['blog'];
$thread    = $this['thread'];
$evaluation = \Goteo\Library\Evaluation::get($project->id);
$level = (int) $this['level'] ?: 3;

$user    = $_SESSION['user'];
$personalData = ($user instanceof User) ? User::getPersonal($user->id) : new stdClass();

$categories = Category::getNames($project->id);

$skills = Skill::getNames($project->id);

if (!empty($project->investors)) {
    $supporters = ' (' . $project->num_investors . ')';
} else {
    $supporters = '';
}
if (!empty($project->messages)) {
    $messages = ' (' . $project->num_messages . ')';
} else {
    $messages = '';
}
if (!empty($blog->posts)) {
    $updates = ' (' . count($blog->posts) . ')';
} else {
    $updates = '';
}
if (!empty($evaluation->content)){
    $ev_label = '評価';
} else {
    $ev_label = '';
}


$bodyClass = 'project-show'; include 'view/m/prologue.html.php' ?>

<?php include 'view/m/header.html.php' ?>

        <div id="sub-header">
            <div class="sub-menu">
                <?php echo new View('view/m/project/view/menu.html.php',
                    array(
                        'project' => $project,
                        'show' => $show,
                        'supporters' => $supporters,
                        'messages' => $messages,
                        'updates' => $updates,
                        'evaluation' => $evaluation
                    )
                );
                ?>
            </div>
        </div>

<?php if(isset($_SESSION['messages'])) { include 'view/m/header/message.html.php'; } ?>


        <div id="main" class="threecols">


            <div class="center <?php echo $show; ?>">
            <?php

            $non_flug = 0;
                // los modulos centrales son diferentes segun el show
                switch ($show) {
                    case 'needs':
                        echo new View('view/m/project/widget/summary.h_ttl.html.php', array('project' => $project));
                        if ($this['non-economic']) {
                            echo
                                new View('view/m/project/widget/non-needs.html.php',
                                    array('project' => $project, 'types' => Support::types()));
                            $non_flug = 1;
                        } else {
                        echo
                            new View('view/m/project/widget/needs.html.php', array('project' => $project, 'types' => Cost::types())),
                            new View('view/m/project/widget/schedule.html.php', array('project' => $project)),
                            new View('view/m/project/widget/sendMsg.html.php', array('project' => $project));
                        }
                        break;
                        
                    case 'supporters':
                        echo new View('view/m/project/widget/summary.h_ttl.html.php', array('project' => $project));

                        // segun el paso de aporte
                        if (!empty($step) && in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail'))) {

                            switch ($step) {
                                case 'continue':
                                    echo
                                        new View('view/m/project/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        new View('view/m/project/widget/invest_redirect.html.php', array('project' => $project, 'personal' => $personalData, 'step' => $step, 'allowpp'=> $this['allowpp']));
                                    break;
                                    
                                case 'ok':
                                    echo
                                        new View('view/m/project/widget/investMsg.html.php', array('message' => $step, 'user' => $user)), new View('view/project/widget/spread.html.php',array('project' => $project));
                                        //sacarlo de div#center
                                        $printSendMsg=true;
                                    break;
                                    
                                case 'fail':
                                    echo
                                        new View('view/m/project/widget/investMsg.html.php', array('message' => $step, 'user' => User::get($_SESSION['user']->id))),
                                        new View('view/m/project/widget/invest.html.php', array('project' => $project, 'personal' => User::getPersonal($_SESSION['user']->id), 'allowpp'=> $this['allowpp']));
                                    break;
                                default:
                                    echo
                                        new View('view/m/project/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        new View('view/m/project/widget/invest.html.php', array('project' => $project, 'personal' => $personalData, 'step' => $step, 'allowpp'=> $this['allowpp']));
                                    break;
                            }
                        } else {
                            echo
                                new View('view/m/project/widget/supporters.html.php', $this),
                                new View('view/m/worth/legend.html.php');
                        }
                        break;
                        
                    case 'messages':
                        echo
                            new View('view/m/project/widget/summary.h_ttl.html.php', array('project' => $project)),
                            new View('view/m/project/widget/collaborations_message.html.php', array('project' => $project,'thread' => $thread)),
                            new View('view/m/project/widget/messages.html.php', array('project' => $project,'thread' => $thread));
                        break;
                   
                    case 'rewards':
                        echo
                            new View('view/m/project/widget/summary.h_ttl.html.php', array('project' => $project)),
                            new View('view/m/project/widget/rewards-summary.html.php', array('project' => $project));
                        break;
                    
                    case 'updates':
                        echo
                            new View('view/m/project/widget/summary.h_ttl.html.php', array('project' => $project)),
                            new View('view/m/project/widget/updates.html.php', array('project' => $project, 'blog' => $blog, 'post' => $post));
                        break;

                    case 'evaluation':
                        echo
                            new View('view/m/project/widget/summary.h_ttl.html.php', array('project' => $project)),
                            new View('view/m/project/widget/evaluation.html.php', array('project' => $project, 'evaluation' => $evaluation));
                        break;

                    case 'home':
                    
                    default:
                        echo
                            new View('view/m/project/widget/summary.h_ttl.html.php', array('project' => $project)),
                            new View('view/m/project/widget/support.html.php', array('project' => $project)),
                        new View('view/m/project/widget/gallery.html.php', array('project' => $project)),
                            new View('view/m/project/widget/summary.html.php', array('project' => $project));
                        break;
                }
                ?>

             </div>

            <div class="side">
            <?php echo new View('view/m/user/widget/user.html.php', array('user' => $project->user)); ?>
            </div>

            <?php $printSendMsg = false; ?>

			<?php
				if($printSendMsg){
					 echo new View('view/m/project/widget/sendMsg.html.php',array('project' => $project));
				}
            ?>

        </div>

        <?php include 'view/m/footer.html.php' ?>
		<?php include 'view/m/epilogue.html.php' ?>
