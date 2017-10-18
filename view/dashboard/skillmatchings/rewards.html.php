<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundaciรณn Fuentes Abiertas (see README for details)
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
    Goteo\Library\Text,
    Goteo\Model\Skillmatching\Reward,
    Goteo\Model\Invest;

$icons = Reward::icons('individual');

$project = $this['skillmatching'];

$rewards = $this['rewards'];
// recompensas ordenadas por importe
uasort($rewards, function ($a, $b) {
        if ($a->amount == $b->amount) return 0;
        return ($a->amount > $b->amount) ? 1 : -1;
    }
);

$invests = $this['invests'];

$filter = $this['filter']; // al ir mostrando, quitamos los que no cumplan
// pending = solo los que tienen alguna recompensa pendientes
// fulfilled = solo los que tienen todas las recompensas cumplidas
// resign = solo los que hicieron renuncia a recompensa

$order = $this['order'];
// segun order:
switch ($order) {
    case 'date': // fecha aporte, mas reciente primero
        uasort($invests, function ($a, $b) {
                if ($a->invested == $b->invested) return 0;
                return ($a->invested > $b->invested) ? -1 : 1;
            }
        );
        break;
    case 'user': // nombre de usuario alfabetico
        uasort($invests, function ($a, $b) {
                if ($a->user->name == $b->user->name) return 0;
                return ($a->user->name > $b->user->name) ? 1 : -1;
            }
        );
        break;
    case 'reward': // importe de recompensa, mรกs bajo primero
        uasort($invests, function ($a, $b) {
                if (empty($a->rewards)) return 1;
                if ($a->rewards[0]->amount == $b->rewards[0]->amount) return 0;
                return ($a->rewards[0]->amount > $b->rewards[0]->amount) ? 1 : -1;
            }
        );
        break;
    case 'amount': // importe aporte, mรกs alto primero
    default:
        uasort($invests, function ($a, $b) {
                if ($a->amount == $b->amount) return 0;
                return ($a->amount > $b->amount) ? -1 : 1;
            }
        );
        break;
}


?>
<div class="widget gestrew">
    <div class="message">
        <?php echo Text::get('dashboard-rewards-notice'); ?>
    </div>
    <div class="rewards">
        <?php $num = 1;
        foreach ($rewards as $rewardId=>$rewardData) :
            $who = Invest::choosed($rewardData->id); ?>
            <div class="reward <?php if(($num % 4)==0)echo " last"?>">
                <div class="orden"><?php echo $num; ?></div>
                <span class="aporte"><?php echo Text::get('dashboard-rewards-management-amount'); ?><span class="num"><?php echo $rewardData->amount; ?></span> <span class="euro">&nbsp;</span></span>
                <span class="cofinanciadores"><?php echo Text::get('dashboard-rewards-management-confinanciadores'); ?><span class="num"><?php echo count($who); ?></span></span>
                <div class="tiporec"><ul><li class="<?php echo $rewardData->icon; ?>"><?php echo Text::shorten($rewardData->reward, 40); ?></li></ul></div>
                <div class="contenedorrecompensa">
                    <span class="recompensa"><strong style="color:#666;"><?php echo Text::get('dashboard-rewards-management-reward'); ?></strong><br/> <?php echo Text::shorten($rewardData->description, 100); ?></span>
                </div>
                <?php if (!empty($invests)): ?>
                    <a class="button green" onclick="msgto('<?php echo $rewardData->id; ?>')" ><?php echo Text::get('dashboard-rewards-management-message'); ?></a>
                <?php endif; ?>
            </div>
            <?php ++$num;
        endforeach; ?>
    </div>
</div>

<?php if (!empty($invests)) : ?>
    <script type="text/javascript">
        function set(what, which) {
            document.getElementById('invests-'+what).value = which;
            document.getElementById('invests-filter-form').submit();
            return false;
        }
    </script><div class="widget gestrew">
        <h2 class="title"><?php echo Text::_("Gestionar retornos"); ?></h2>
        <a name="gestrew"></a>
        <form id="invests-filter-form" name="filter_form" action="<?php echo '/dashboard/projects/rewards/filter#gestrew'; ?>" method="post">
            <input type="hidden" id="invests-filter" name="filter" value="<?php echo $filter; ?>" />
            <input type="hidden" id="invests-order" name="order" value="<?php echo $order; ?>" />
        </form>
        <div class="filters">
            <label><?php echo Text::get('dashboard-rewards-management-contributions'); ?>: </label>
            <ul>
                <li<?php if ($order == 'amount' || $order == '') echo ' class="current"'; ?>><a href="#" onclick="return set('order', 'amount');"><?php echo Text::get('dashboard-rewards-management-ammount'); ?></a></li>
                <li>|</li>
                <li<?php if ($order == 'date') echo ' class="current"'; ?>><a href="#" onclick="return set('order', 'date');"><?php echo Text::get('dashboard-rewards-management-date'); ?></a></li>
                <li>|</li>
                <li<?php if ($order == 'user') echo ' class="current"'; ?>><a href="#" onclick="return set('order', 'user');"><?php echo Text::get('dashboard-rewards-management-user'); ?></a></li>
                <li>|</li>
                <li<?php if ($order == 'reward') echo ' class="current"'; ?>><a href="#" onclick="return set('order', 'reward');"><?php echo Text::get('dashboard-rewards-management-reward'); ?></a></li>
                <li>|</li>
                <li<?php if ($filter == 'pending') echo ' class="current"'; ?>><a href="#" onclick="return set('filter', 'pending');"><?php echo Text::get('dashboard-rewards-management-pending'); ?></a></li>
                <li>|</li>
                <li<?php if ($filter == 'fulfilled') echo ' class="current"'; ?>><a href="#" onclick="return set('filter', 'fulfilled');"><?php echo Text::get('dashboard-rewards-management-fulfilled'); ?></a></li>
                <li>|</li>
                <li<?php if ($filter == 'resign') echo ' class="current"'; ?>><a href="#" onclick="return set('filter', 'resign');"><?php echo Text::get('dashboard-rewards-management-resign'); ?></a></li>
                <li>|</li>
                <li<?php if ($filter == '') echo ' class="current"'; ?>><a href="#" onclick="return set('filter', '');"><?php echo Text::get('dashboard-rewards-management-all'); ?></a></li>
            </ul>
        </div>

        <div id="invests-list">
            <form name="invests_form" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/process'; ?>" method="post">
                <input type="hidden" name="filter" value="<?php echo $filter; ?>" />
                <input type="hidden" name="order" value="<?php echo $order; ?>" />
                <?php foreach ($invests as $investId=>$investData) :

                    $address = $investData->address;
                    $cumplida = true; //si nos encontramos una sola no cumplida, pasa a false
                    $estilo = "disabled";
                    foreach ($investData->rewards as $reward) {
                        if ($reward->fulfilled != 1) {
                            $estilo = "";
                            $cumplida = false;
                        }
                    }

                    // filtro
                    if ($filter == 'pending' && $cumplida != false) continue;
                    if ($filter == 'fulfilled' && $cumplida != true) continue;
                    if ($filter == 'resign' && $investData->resign != true) continue;

                    //匿名判定
                    if ($investData->anonymous):
                        $invest_name = Text::get('regular-anonymous');
                    else:
                        $invest_name = Text::shorten($investData->user->name,15);
                    endif;
                    ?>

                    <div class="investor">

                        <div class="left">
                            <a href="/user/<?php echo $investData->user->id; ?>"><img src="<?php echo $investData->user->avatar->getLink(45, 45, true); ?>" /></a>
                        </div>

                        <div class="left username">
                            <span><a href="/user/<?php echo $investData->user->id; ?>"><?php echo $invest_name; ?></a></span>
                            <label class="amount"><?php echo Text::get('invests-list-contribution'); ?><?php if ($investData->anonymous) echo ' <strong>'.  Text::get('regular-anonymous').'</strong>'; ?></label>
                            <span class="amount">&yen; <?php echo $investData->amount; ?></span>
                            <span class="date"><?php echo date('Y-m-d', strtotime($investData->invested)); ?></span>
                        </div>

                        <div class="left recompensas">
                            <span class="<?php echo $estilo;?>"><strong><?php echo Text::get('invests-list-expected-rewards'); ?>:</strong></span>
                            <?php foreach ($investData->rewards as $reward) : ?>
                                <div class="<?php echo $estilo;?>">
                                    <input type="checkbox"  id="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>" name="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>" value="1" <?php if ($reward->fulfilled == 1) echo ' checked="checked" disabled';?>  />
                                    <label for="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>"><?php echo Text::shorten($reward->reward, 40); ?></label>
                                </div>
                            <?php endforeach; ?>

                        </div>

                        <div class="left name">
                            <span><strong>氏名(本名)</strong></span>
                            <span><?php echo $address->name; ?></span>
                        </div>

                        <div class="left address">
                            <span class="<?php echo $estilo;?>"><strong><?php echo Text::get('invests-list-delivery-address'); ?>: </strong></span>
                            <span class="<?php echo ' '.$estilo;?>">
                            〒<?php echo $address->zipcode; ?></span>
                            <span><?php echo $address->address; ?></span>
                        </div>

                        <div class="left">
                            <span class="status"><?php echo $cumplida ? '<span class="cumplida">' . Text::get('invests-list-accomplished') . '</span>' : '<span class="pendiente">' . Text::get('invests-list-pending') . '</span>'; ?></span>
                            <span class="profile"><a href="/user/profile/<?php echo $investData->user->id ?>" target="_blank"><?php echo Text::get('profile-widget-button'); ?></a> </span>
                            <span class="contact"><a href="/user/profile/<?php echo $investData->user->id ?>/message" target="_blank"><?php echo Text::get('regular-send_message'); ?></a></span>
                        </div>

                    </div>

                <?php endforeach; ?>

                <?php if ($project->amount >= $project->mincost) : ?>
                    <input type="submit" name="process" value="<?php echo Text::get('application-marked'); ?>" class="save" onclick="return confirm('<?php echo Text::get('application-marked-confirm'); ?>')"/>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="widget projects" id="colective-messages">
        <a name="message"></a>
        <h2 class="title"><?php echo Text::get('colective-messages'); ?></h2>

        <form name="message_form" method="post" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/message'; ?>">
            <div id="checks">
                <input type="hidden" name="filter" value="<?php echo $filter; ?>" />
                <input type="hidden" name="order" value="<?php echo $order; ?>" />

                <p>
                    <input type="checkbox" id="msg_all" name="msg_all" value="1" onclick="alert('全ての支援者に送信します');" />
                    <label for="msg_all">全ての支援者に送信</label>
                </p>

                <p>
                    <?php echo Text::_('Send it to reward seekers');_?>: <br />
                    <?php foreach ($rewards as $rewardId => $rewardData) : ?>
                        <input type="checkbox" id="msg_reward-<?php echo $rewardId; ?>" name="msg_reward-<?php echo $rewardId; ?>" value="1" />
                        <label for="msg_reward-<?php echo $rewardId; ?>">&yen;<?php echo $rewardData->amount; ?>  (<?php echo Text::shorten($rewardData->reward, 40); ?>)</label>
                    <?php endforeach; ?>

                </p>
            </div>
            <div id="comment">
                <script type="text/javascript">
                    // Mark DOM as javascript-enabled
                    jQuery(document).ready(function ($) {
                        //change div#preview content when textarea lost focus
                        $("#message_reward").blur(function(){
                            $("#preview").html($("#message_reward").val());
                        });

                        //add fancybox on #a-preview click
                        $("#a-preview").fancybox({
                            'titlePosition'		: 'inside',
                            'transitionIn'		: 'none',
                            'transitionOut'		: 'none'
                        });
                    });
                </script>
                <div id="bocadillo"></div>
                <input type="hidden" name="username" value="<?php echo $investData->user->name; ?>" />
                <?php /* todo: ボタンを押すと遷移するはずだが出てない。出たら#messageを直す */?>
                <textarea rows="5" cols="50" name="message" id="message_reward"></textarea>
                <a class="preview" href="#preview" id="a-preview" target="_blank"><?php echo Text::get('regular-preview'); ?></a>
                <div style="display:none">
                    <div style="width:400px;height:300px;overflow:auto;" id="preview"></div>
                </div>
                <button type="submit" class="green"><?php echo Text::get('project-messages-send_message-button'); ?></button>
            </div>
        </form>
    </div>

<?php endif; ?>
<script type="text/javascript">
    function msgto(reward) {
        document.getElementById('msg_reward-'+reward).checked = 'checked';
        document.location.href = '#message_reward';
        $("#message_reward").focus();
    }
</script>