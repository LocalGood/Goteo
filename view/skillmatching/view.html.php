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

$skillmatching = $this['skillmatching'];
$show    = $this['show'];
$step    = $this['step'];
$post    = $this['post'];
$blog    = $this['blog'];
$thread    = $this['thread'];

$evaluation = \Goteo\Library\Evaluation::get($skillmatching->id);

$user    = $_SESSION['user'];
$personalData = ($user instanceof User) ? User::getPersonal($user->id) : new stdClass();

$categories = Category::getNames($skillmatching->prefixed_id);

$skills = Skill::getNames($skillmatching->id);

if (!empty($skillmatching->investors)) {
    $supporters = ' (' . $skillmatching->num_investors . ')';
} else {
    $supporters = '';
}
if (!empty($skillmatching->messages)) {
    $messages = ' (' . $skillmatching->num_messages . ')';
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

$bodyClass = 'project-show skillmatching-show'; include 'view/prologue.html.php' ?>

<?php include 'view/header.html.php' ?>

        <div id="sub-header">
            <div class="project-header">
                <h2><span><?php echo htmlspecialchars($skillmatching->name) ?></span></h2>
                <div class="project-subtitle"><?php echo htmlspecialchars($skillmatching->subtitle) ?></div>

                <div class="project-header-bottom">
                    <?php
                    $_value = '/skillmatching/' . $skillmatching->id;
                    $_url = urldecode($_SERVER['REQUEST_URI']);
                    if(strstr($_url,$_value) && preg_match('/^\/skillmatching\/(.*)$/',$_url)):
                        echo new View('view/skillmatching/widget/share.html.php', array('skillmatching' => $skillmatching));
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
                <?php echo new View('view/skillmatching/view/menu.html.php',
                            array(
                                'skillmatching' => $skillmatching,
                                'show' => $show,
                                'supporters' => $supporters,
                                'messages' => $messages,
                                'updates' => $updates,
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
            echo
                new View('view/skillmatching/widget/support.html.php', array('skillmatching' => $skillmatching));
/*
            if ((!empty($skillmatching->investors) &&
                !empty($step) &&
                in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail')) )
                || $show == 'messages' ) {
                echo new View('view/skillmatching/widget/investors.html.php', array('skillmatching' => $skillmatching));
            }

            if (!empty($skillmatching->supports) && $show !='messages') {
                echo new View('view/skillmatching/widget/collaborations.html.php', array('skillmatching' => $skillmatching));
            }
*/
            if ($show != 'rewards') {
                echo new View('view/skillmatching/widget/rewards.html.php', array('skillmatching' => $skillmatching));
            }

            echo new View('view/user/widget/user.html.php', array('user' => $skillmatching->user,'projectType' => 'skillmatching'));

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
                                new View('view/skillmatching/widget/non-needs.html.php',
                                    array('skillmatching' => $skillmatching, 'types' => Support::types()));
                        } else {
                        echo
                            new View('view/skillmatching/widget/needs.html.php', array('skillmatching' => $skillmatching, 'types' => Cost::types())),
                            new View('view/skillmatching/widget/schedule.html.php', array('skillmatching' => $skillmatching)),
                            new View('view/skillmatching/widget/sendMsg.html.php', array('skillmatching' => $skillmatching));
                        }
                        break;
						
                    case 'supporters':

						// segun el paso de aporte
                        if (!empty($step) && in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail'))) {

                            switch ($step) {
                                case 'continue':
                                    echo
                                        new View('view/skillmatching/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        new View('view/skillmatching/widget/invest_redirect.html.php', array('skillmatching' => $skillmatching, 'personal' => $personalData, 'step' => $step, 'allowpp'=> $this['allowpp']));
                                    break;
									
                                case 'ok':
                                    echo
                                        new View('view/skillmatching/widget/investMsg.html.php', array('message' => $step, 'user' => $user));
                                        if(get_spOS() === 'iOS'){
                                            echo new View('view/skillmatching/widget/iosMsg.html.php',array('skillmatching' => $skillmatching));
                                        }
                                    echo
                                        new View('view/skillmatching/widget/spread.html.php',array('skillmatching' => $skillmatching));
                                        //sacarlo de div#center
                                        $printSendMsg=true;
                                    break;
									
                                case 'fail':
                                    echo
                                        new View('view/skillmatching/widget/investMsg.html.php', array('message' => $step, 'user' => User::get($_SESSION['user']->id))),
                                        new View('view/skillmatching/widget/invest.html.php', array('skillmatching' => $skillmatching, 'personal' => User::getPersonal($_SESSION['user']->id), 'allowpp'=> $this['allowpp']));
                                    break;
                                default:
                                    echo
                                        new View('view/skillmatching/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        new View('view/skillmatching/widget/invest.html.php', array('skillmatching' => $skillmatching, 'personal' => $personalData, 'step' => $step, 'allowpp'=> $this['allowpp']));
                                    break;
                            }
                        } else {
                            echo
                                new View('view/skillmatching/widget/supporters.html.php', $this),
                                new View('view/worth/legend.html.php');
                        }
                        break;
						
                    case 'messages':
                        echo
//                            new View('view/skillmatching/widget/collaborations_message.html.php', array('skillmatching' => $skillmatching,'thread' => $thread)),
                            new View('view/skillmatching/widget/messages.html.php', array('skillmatching' => $skillmatching,'thread' => $thread));
                        break;
                   
				    case 'rewards':
                        echo
                            new View('view/skillmatching/widget/rewards-summary.html.php', array('skillmatching' => $skillmatching));
                        break;
                    
                    case 'updates':
                        echo
                            new View('view/skillmatching/widget/updates.html.php', array('skillmatching' => $skillmatching, 'blog' => $blog, 'post' => $post));
                        break;
                    
                    case 'evaluation':
                        echo
                            new View('view/skillmatching/widget/evaluation.html.php', array('skillmatching' => $skillmatching, 'evaluation' => $evaluation));
                        break;
                    
					case 'home':
					
                    default:
                        echo
                            new View('view/skillmatching/widget/summary.html.php', array('skillmatching' => $skillmatching));
                        break;
                }
                ?>
             </div>

			<?php
//            var_dump($printSendMsg);
				if($printSendMsg){
					 echo new View('view/skillmatching/widget/sendMsg.html.php',array('skillmatching' => $skillmatching));
				}
            ?>

        </div>

        <?php include 'view/footer.html.php' ?>
		<?php include 'view/epilogue.html.php' ?>
