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

$categories = Category::getNames($skillmatching->id);

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
                <a href="/user/<?php echo $skillmatching->owner; ?>"><img src="<?php echo $skillmatching->user->avatar->getLink(56,56, true) ?>" /></a>
                <h2><span><?php echo htmlspecialchars($skillmatching->name) ?></span></h2>
                <div class="project-subtitle"><?php echo htmlspecialchars($skillmatching->subtitle) ?></div>
                <?/*
                <div class="wants-skills">
                    スキル: <?php
                        // スキル表示
                        if (!empty($skills)):
                            foreach( $skills as $_skill_id => $_skill_name):
                                ?>
                                <a href=""><?php echo $_skill_name ?></a>
                                <?
                            endforeach;
                        endif;
                    ?>
                </div>
                */?>

                <div class="project-by"><a href="/user/<?php echo $skillmatching->owner; ?>"><?php echo Text::get('regular-by') ?> <?php echo $skillmatching->user->name; ?></a></div>
                <?
                $_value = '/skillmatching/' . $skillmatching->id;
                $_url = urldecode($_SERVER['REQUEST_URI']);
                if(strstr($_url,$_value) && preg_match('/^\/project\/((?!\/).)*$/',$_url)):
                    ?>
                    <div id="social_bookmark">
                        <div id="twitter">
                            <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
                            <script>
                                !function(d,s,id){
                                    var js,fjs=d.getElementsByTagName(s)[0];
                                    if(!d.getElementById(id)){
                                        js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";
                                        fjs.parentNode.insertBefore(js,fjs);
                                    }
                                }(document,"script","twitter-wjs");
                            </script>
                        </div>
                        <div id="facebook">
                            <div class="fb-like" data-href="<?= $ogmeta['url']; ?>" data-layout="button_count" data-action="recommend" data-show-faces="false" data-share="true"></div>
                        </div>

                        <div class="g-plusone" data-size="medium" data-width="60"></div>
                        <script type="text/javascript">
                            window.___gcfg = {lang: 'ja'};

                            (function() {
                                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                                po.src = 'https://apis.google.com/js/platform.js';
                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                            })();
                        </script>

                        <div style="clear:both"></div>
                    </div><!-- #social_bookmark -->
                <?
                endif;
                ?>
                <br clear="both" />

                <div class="categories"><h3><?php echo Text::get('project-view-categories-title'); ?></h3>
                    <?php $sep = ''; foreach ($categories as $key=>$value) :
                        echo $sep.'<a href="/discover/results/'.$key.'">'.htmlspecialchars($value).'</a>';
                    $sep = ', '; endforeach; ?>
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

            if ((!empty($skillmatching->investors) &&
                !empty($step) &&
                in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail')) )
                || $show == 'messages' ) {
                echo new View('view/skillmatching/widget/investors.html.php', array('skillmatching' => $skillmatching));
            }

            if (!empty($skillmatching->supports) && $show !='messages') {
                echo new View('view/skillmatching/widget/collaborations.html.php', array('skillmatching' => $skillmatching));
            }

//            if ($show != 'rewards') {
//                echo new View('view/skillmatching/widget/rewards.html.php', array('skillmatching' => $skillmatching));
//            }

            echo new View('view/user/widget/user.html.php', array('user' => $skillmatching->user));

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
            var_dump($printSendMsg);
				if($printSendMsg){
					 echo new View('view/skillmatching/widget/sendMsg.html.php',array('skillmatching' => $skillmatching));
				}
            ?>

        </div>

        <?php include 'view/footer.html.php' ?>
		<?php include 'view/epilogue.html.php' ?>
