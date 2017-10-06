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

$bodyClass = 'project-show'; include 'view/prologue.html.php' ?>

<?php include 'view/header.html.php' ?>

        <div id="sub-header">
            <div class="project-header">
                <h2><span><?php echo htmlspecialchars($project->name) ?></span></h2>
                <div class="project-subtitle"><?php echo htmlspecialchars($project->subtitle) ?></div>

                <div class="project-header-bottom">
                    <?
                    $_value = '/project/' . $project->id;
                    $_url = urldecode($_SERVER['REQUEST_URI']);
                    if(strstr($_url,$_value) && preg_match('/^\/project\/(.*)$/',$_url)):
                        echo new View('view/project/widget/share.html.php', array('project' => $project));
                    endif;
                    ?>
                    <?php if (!empty($categories)): ?>
                        <div class="categories">
                            <?php $sep = ''; foreach ($categories as $key=>$value) :
                                echo $sep.'<a href="/discover/results/'.$key.'">'.htmlspecialchars($value).'</a>';
                            $sep = ', '; endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sub-menu">
                <?php echo new View('view/project/view/menu.html.php',
                            array(
                                'project' => $project,
                                'show' => $show,
                                'messages' => $messages,
                                'updates' => $updates,
                                'supporters' => $supporters,
                                'evaluation' => $ev_label
                            )
                    );
                ?>
            </div>

        </div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>


        <div id="main" class="threecols">

            <div class="side">
            <?php
            // el lateral es diferente segun el show (y el invest)
            echo new View('view/project/widget/support.html.php', array('project' => $project));

            echo new View('view/user/widget/user.html.php', array('user' => $project->user));

            if ((!empty($project->investors) &&
                !empty($step) &&
                in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail')) )
                || $show == 'messages' ) {
                echo new View('view/project/widget/investors.html.php', array('project' => $project));
            }

            if (!empty($project->supports) && $show !='messages') {
                echo new View('view/project/widget/collaborations.html.php', array('project' => $project));
            }

            if ($show != 'rewards') {
                echo new View('view/project/widget/rewards.html.php', array('project' => $project));
            }

            ?>
            </div>

            <?php $printSendMsg = false; ?>
            <div class="center <?php echo $show; ?>">
			<?php
                // los modulos centrales son diferentes segun el show
                switch ($show) {
                    case 'needs':
                        if ($this['non-economic']) {
                            echo
                                new View('view/project/widget/non-needs.html.php',
                                    array('project' => $project, 'types' => Support::types()));
                        } else {
                        echo
                            new View('view/project/widget/needs.html.php', array('project' => $project, 'types' => Cost::types())),
                            new View('view/project/widget/schedule.html.php', array('project' => $project)),
                            new View('view/project/widget/sendMsg.html.php', array('project' => $project));
                        }
                        break;
						
                    case 'supporters':

						// segun el paso de aporte
                        if (!empty($step) && in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail'))) {

                            switch ($step) {
                                case 'continue':
                                    echo
                                        new View('view/project/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        new View('view/project/widget/invest_redirect.html.php', array('project' => $project, 'personal' => $personalData, 'step' => $step, 'allowpp'=> $this['allowpp']));
                                    break;
									
                                case 'ok':
                                    echo
                                        new View('view/project/widget/investMsg.html.php', array('message' => $step, 'user' => $user));
                                        if(get_spOS() === 'iOS'){
                                            echo new View('view/project/widget/iosMsg.html.php',array('project' => $project));
                                        }
                                    echo
                                        new View('view/project/widget/spread.html.php',array('project' => $project));
                                        //sacarlo de div#center
                                        $printSendMsg=true;
                                    break;
									
                                case 'fail':
                                    echo
//                                        new View('view/project/widget/investMsg.html.php', array('message' => $step, 'user' => User::get($_SESSION['user']->id))),
                                        new View('view/project/widget/invest.html.php', array('project' => $project, 'personal' => User::getPersonal($_SESSION['user']->id), 'allowpp'=> $this['allowpp']));
                                    break;
                                default:
                                    echo
//                                        new View('view/project/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        new View('view/project/widget/invest.html.php', array('project' => $project, 'personal' => $personalData, 'step' => $step, 'allowpp'=> $this['allowpp']));
                                    break;
                            }
                        } else {
                            echo
                                new View('view/project/widget/supporters.html.php', $this),
                                new View('view/worth/legend.html.php');
                        }
                        break;

                    case 'messages':
                        echo
                            new View('view/project/widget/collaborations_message.html.php', array('project' => $project,'thread' => $thread)),
                            new View('view/project/widget/messages.html.php', array('project' => $project,'thread' => $thread));
                        break;
                   
				    case 'rewards':
                        echo
                            new View('view/project/widget/rewards-summary.html.php', array('project' => $project));
                        break;
                    
                    case 'updates':
                        echo
                            new View('view/project/widget/updates.html.php', array('project' => $project, 'blog' => $blog, 'post' => $post));
                        break;
                    
                    case 'evaluation':
                        echo
                            new View('view/project/widget/evaluation.html.php', array('project' => $project, 'evaluation' => $evaluation));
                        break;
                    
					case 'home':
					
                    default:
                        echo
                            new View('view/project/widget/summary.html.php', array('project' => $project));
                        break;
                }
                ?>
			<?php
			if($printSendMsg){
				echo new View('view/project/widget/sendMsg.html.php',array('project' => $project));
			}
			?>
             </div>

        </div>

        <?php include 'view/footer.html.php' ?>
		<?php include 'view/epilogue.html.php' ?>
