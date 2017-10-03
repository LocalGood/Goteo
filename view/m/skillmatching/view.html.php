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
    Goteo\Model\Skillmatching\Cost,
    Goteo\Model\Skillmatching\Support,
    Goteo\Model\Skillmatching\Category,
    Goteo\Model\Skillmatching\Skill,
    Goteo\Model\Blog,
    Goteo\Library\Text;

$project = $this['skillmatching'];
$show    = $this['show'];
$step    = $this['step'];
$post    = $this['post'];
$blog    = $this['blog'];
$thread    = $this['thread'];
$evaluation = \Goteo\Library\Evaluation::get($project->id);
$level = (int) $this['level'] ?: 3;

$user    = $_SESSION['user'];
$personalData = ($user instanceof User) ? User::getPersonal($user->id) : new stdClass();

$categories = Category::getNames($project->prefixed_id);

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


$bodyClass = 'project-show'; include VIEW_PATH . '/prologue.html.php' ?>

<?php include VIEW_PATH . '/header.html.php' ?>

        <div id="sub-header">
            <div class="sub-menu">
                <?php echo new View(VIEW_PATH . '/skillmatching/view/menu.html.php',
                    array(
                        'skillmatching' => $project,
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

<?php if(isset($_SESSION['messages'])) { include VIEW_PATH . '/header/message.html.php'; } ?>


        <div id="main" class="threecols">


            <div class="center <?php echo $show; ?>">
            <?php

            $non_flug = 0;
                // los modulos centrales son diferentes segun el show
                switch ($show) {
                    case 'needs':
                        echo new View(VIEW_PATH . '/skillmatching/widget/summary.h_ttl.html.php', array('skillmatching' => $project));
                        if ($this['non-economic']) {
                            echo
                                new View(VIEW_PATH . '/skillmatching/widget/non-needs.html.php',
                                    array('skillmatching' => $project, 'types' => Support::types()));
                            $non_flug = 1;
                        } else {
                        echo
                            new View(VIEW_PATH . '/skillmatching/widget/needs.html.php', array('skillmatching' => $project, 'types' => Cost::types())),
                            new View(VIEW_PATH . '/skillmatching/widget/schedule.html.php', array('skillmatching' => $project)),
                            new View(VIEW_PATH . '/skillmatching/widget/sendMsg.html.php', array('skillmatching' => $project));
                        }
                        break;
                        
                    case 'supporters':
                        echo new View(VIEW_PATH . '/skillmatching/widget/summary.h_ttl.html.php', array('skillmatching' => $project));

                        // segun el paso de aporte
                        if (!empty($step) && in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail'))) {

                            switch ($step) {
                                case 'continue':
                                    echo
                                        new View(VIEW_PATH . '/skillmatching/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        new View(VIEW_PATH . '/skillmatching/widget/invest_redirect.html.php', array('skillmatching' => $project, 'personal' => $personalData, 'step' => $step, 'allowpp'=> $this['allowpp']));
                                    break;
                                    
                                case 'ok':
                                    echo
                                        new View(VIEW_PATH . '/skillmatching/widget/investMsg.html.php', array('message' => $step, 'user' => $user)), new View('view/skillmatching/widget/spread.html.php',array('skillmatching' => $project));
                                        //sacarlo de div#center
                                        $printSendMsg=true;                                     
                                    break;
                                    
                                case 'fail':
                                    echo
                                        new View(VIEW_PATH . '/skillmatching/widget/investMsg.html.php', array('message' => $step, 'user' => User::get($_SESSION['user']->id))),
                                        new View(VIEW_PATH . '/skillmatching/widget/invest.html.php', array('skillmatching' => $project, 'personal' => User::getPersonal($_SESSION['user']->id), 'allowpp'=> $this['allowpp']));
                                    break;
                                default:
                                    echo
                                        new View(VIEW_PATH . '/skillmatching/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        new View(VIEW_PATH . '/skillmatching/widget/invest.html.php', array('skillmatching' => $project, 'personal' => $personalData, 'step' => $step, 'allowpp'=> $this['allowpp']));
                                    break;
                            }
                        } else {
                            echo
                                new View(VIEW_PATH . '/skillmatching/widget/supporters.html.php', $this),
                                new View(VIEW_PATH . '/worth/legend.html.php');
                        }
                        break;
                        
                    case 'messages':
                        echo
                            new View(VIEW_PATH . '/skillmatching/widget/summary.h_ttl.html.php', array('skillmatching' => $project)),
//                            new View(VIEW_PATH . '/skillmatching/widget/collaborations_message.html.php', array('skillmatching' => $project,'thread' => $thread)),
                            new View(VIEW_PATH . '/skillmatching/widget/messages.html.php', array('skillmatching' => $project,'thread' => $thread));
                        break;
                   
                    case 'rewards':
                        echo
                            new View(VIEW_PATH . '/skillmatching/widget/summary.h_ttl.html.php', array('skillmatching' => $project)),
                            new View(VIEW_PATH . '/skillmatching/widget/rewards-summary.html.php', array('skillmatching' => $project));
                        break;
                    
                    case 'updates':
                        echo
                            new View(VIEW_PATH . '/skillmatching/widget/summary.h_ttl.html.php', array('skillmatching' => $project)),
                            new View(VIEW_PATH . '/skillmatching/widget/updates.html.php', array('skillmatching' => $project, 'blog' => $blog, 'post' => $post));
                        break;

                    case 'evaluation':
                        echo
                        new View(VIEW_PATH . '/skillmatching/widget/summary.h_ttl.html.php', array('skillmatching' => $project)),
                        new View(VIEW_PATH . '/skillmatching/widget/evaluation.html.php', array('skillmatching' => $project, 'evaluation' => $evaluation));
                        break;

                    case 'home':
                    
                    default:
                        echo
                            new View(VIEW_PATH . '/skillmatching/widget/summary.h_ttl.html.php', array('skillmatching' => $project)),
                            new View(VIEW_PATH . '/skillmatching/widget/support.html.php', array('skillmatching' => $project)),
                            new View(VIEW_PATH . '/skillmatching/widget/gallery.html.php', array('skillmatching' => $project)),
                            new View(VIEW_PATH . '/skillmatching/widget/summary.html.php', array('skillmatching' => $project));
                        break;
                }
                ?>
             </div>

            <div class="side">
            <?php echo new View(VIEW_PATH . '/user/widget/user.html.php', array('user' => $project->user, 'projectType' => 'skillmatching')); ?>
            </div>

            <?php $printSendMsg = false; ?>

			<?php
				if($printSendMsg){
					 echo new View(VIEW_PATH . '/skillmatching/widget/sendMsg.html.php',array('skillmatching' => $project));
				}
            ?>

        </div>

        <?php include VIEW_PATH . '/footer.html.php' ?>
		<?php include VIEW_PATH . '/epilogue.html.php' ?>
