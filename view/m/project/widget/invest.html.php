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
    Goteo\Core\ACL,
    Goteo\Library\Worth,
    Goteo\Model\User,
    Goteo\Library\Text,
    Goteo\Model\License;

$project = $this['project'];
$personal = $this['personal'];

$user = $_SESSION['user'];
if ($user != NULL) {
    $usedepslion = $user->getUsedEpsilon ();
    $repeatf = ($usedepslion['usedcnt'] != 0) ? 1 : 0;
} else {
    $repeatf = 0;
}



// cantidad de aporte
if (isset($_SESSION['invest-amount'])) {
    $amount = $_SESSION['invest-amount'];
    unset($_SESSION['invest-amount']);
} elseif (!empty($_GET['amount'])) {
    $amount = $_GET['amount'];
} else {
    $amount = 0;
}
$step = $this['step'];

$level = (int) $this['level'] ?: 3;

$worthcracy = Worth::getAll();

$licenses = array();

foreach (License::getAll() as $l) {
    $licenses[$l->id] = $l;
}
$action = ($step == 'start') ? '/user/login' : '/invest/' . $project->id;
?>
<div class="widget project-invest project-invest-amount">
    <h<?php echo $level+1 ?> class="title"><?php echo Text::get('invest-amount') ?></h<?php echo $level+1 ?>>
    
    <form method="post" action="<?php echo $action; ?>">

    <label><input type="text" id="amount" name="amount" class="amount" value="<?php echo $amount ?>" /><?php echo Text::get('invest-amount-tooltip') ?></label>
</div>

    
<div class="widget project-invest project-invest-individual_rewards">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('invest-individual-header') ?></h<?php echo $level ?>>

    <div class="project-widget-box">
        <div class="individual">
            <h<?php echo $level+1 ?> class="title"><?php echo Text::get('project-rewards-individual_reward-title'); ?></h<?php echo $level+1 ?>>
            <ul>
                <li>
                    <label class="resign">
                        <input class="individual_reward" type="radio" id="resign_reward" name="selected_reward" value="0" amount="0"/><?php echo Text::get('invest-resign') ?>
                    </label>
                </li>
            <?php foreach ($project->individual_rewards as $individual) : ?>
                <li class="<?php echo $individual->icon ?><?php if ($individual->none) echo ' disabled' ?>">
                    <label class="amount" for="reward_<?php echo $individual->id; ?>">
                        <input type="radio" name="selected_reward" id="reward_<?php echo $individual->id; ?>" value="<?php echo $individual->id; ?>" amount="<?php echo $individual->amount; ?>" class="individual_reward" title="<?php echo htmlspecialchars($individual->reward) ?>" <?php if ($individual->none) echo 'disabled="disabled"' ?>/>
                        <span class="amount"><?php echo $individual->amount; ?> <?php echo Text::get('invest-currency-unit'); ?></span>
                        <h<?php echo $level + 2 ?> class="name"><?php echo htmlspecialchars($individual->reward) ?></h<?php echo $level + 2 ?>>
                        <?php
                        if (!empty($individual->image)):
                            $img_src = $individual->image->getLink(200,200,false);
                            ?>
                            <img src="<?php echo $img_src ?>" alt="<?php echo htmlspecialchars($individual->reward) ?>" />
                            <?php
                        endif;
                        ?>
                        <p><?php echo htmlspecialchars($individual->description)?></p>
                        <?php if ($individual->none) : // no quedan ?>
                            <span class="left"><?php echo Text::get('invest-reward-none') ?></span>
                        <?php elseif (!empty($individual->units)) : // unidades limitadas ?>
                        <?/*<strong><?php echo Text::get('project-rewards-individual_reward-limited'); ?></strong><br />
                    <?php $units = ($individual->units - $individual->taken); // resto
                    echo Text::html('project-rewards-individual_reward-units_left', $units); ?><br />*/?>
                        <?php endif; ?>
                    </label>
                </li>
            <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>

<?php
// si es el primer paso, mostramos el botÃ³n para ir a login
if ($step == 'start') : ?>
<div class="widget project-invest method">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('user-login-required-to_invest') ?></h<?php echo $level ?>>

    <div class="buttons">
        <button type="submit" class="button red" name="go-login" value=""><?php echo Text::get('imperative-register'); ?></button>
    </div>

    <div class="reminder"><span id="amount-reminder"><?php echo $amount ?></span> <?php echo Text::get('invest-alert-investing') ?></div>

</div>
<?php else : ?>
<a name="continue"></a>
<div class="widget project-invest address">
    <h<?php echo $level ?> class="beak" id="address-header"><?php echo Text::get('invest-address-header'); ?></h<?php echo $level ?>>
    <table>
        <tr>
            <td>
                <label for="fullname"><?php echo Text::get('invest-address-name-field') ?></label><br />
                <input type="text" id="fullname" name="fullname" value="<?php echo $personal->contract_name; ?>" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="address"><?php echo Text::get('invest-address-address-field') ?></label><br />
                <input type="text" id="address" name="address" value="<?php echo $personal->address; ?>" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="zipcode"><?php echo Text::get('invest-address-zipcode-field') ?></label><br />
                <input type="text" id="zipcode" name="zipcode" value="<?php echo $personal->zipcode; ?>" />
            </td>
        </tr>
    </table>
    <p>
        <label><input type="checkbox" name="anonymous" value="1" /><span class="chkbox"></span><?php echo Text::get('invest-anonymous') ?></label>
    </p>
</div>
<?php if (ACL::check('/admin')) : ?>
<div class="widget project-invest display-name">
    <h<?php echo $level ?> class="beak" id="address-header"><?php echo Text::get('invest-display-message') ?></h<?php echo $level ?>>

    <table>
        <tr>
            <td>
                <label for="disp_name"><?php echo Text::get('invest-display-name') ?></label><br />
                <input type="text" id="disp_name" name="disp_name" value="" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="investor_email"><?php echo Text::get('invest-display-address') ?></label><br />
                <input type="text" id="investor_email" name="investor_email" value="" />
            </td>
        </tr>
    </table>
</div>
<?php endif; ?>

<div class="widget project-invest method">

<?php if ( defined('AXESON')) : ?>
    <h<?php echo $level ?> class="beak"><?php echo Text::get('project-invest-continue') ?>
    <p style="color:#ff3300;margin-bottom: 0;"><?php echo Text::get('invest-axeson-caution') ?></p></h<?php echo $level ?>>
<?php endif; ?>

<?php if ( defined('EPSILONON')) : ?>
    <h<?php echo $level ?> class="beak">
    <p style="color:#ff3300;margin-bottom: 0;"><?php echo Text::get('invest-axeson-caution-second') ?></p></h<?php echo $level ?>>
<?php endif; ?>



<input type="hidden" id="paymethod"  />

<?php if (ACL::check('/admin')) : ?>
<p><button type="submit" class="process pay-cash" name="method" value="cash"><?php echo Text::get('invest-cash') ?></button></p>
<?php endif; ?>
<!--<p><button type="submit" class="process pay-paypal" name="method"  value="paypal">PAYPAL</button></p>-->

<?php if ( defined('AXESON')) : ?>
<p><button type="submit" class="process pay-axes" name="method"  value="axes"><?php echo Text::get('invest-credit-card') ?></button></p>
<?php endif; ?>

<?php if ( defined('EPSILONON')) : ?>
<p><button type="submit" class="process pay-axes" name="method"  value="epsilon"><?php echo Text::get('invest-new-credit-card') ?></button></p>
<?php if ( $repeatf != 0) : ?>
<div>
    <button type="submit" class="process pay-axes" name="method"  value="epsilonrepeat"><?php echo Text::get('invest-previous-credit-card') ?></button><br />
    <p style="color:#ff3300;margin-bottom: 0;"><?php echo Text::get('invest-previous-credit-card-message') ?></p>
</div>
<?php endif; ?>
<?php endif; ?>

<?php if ( defined('EPSILONCONVENI')) : ?>
    <p><button type="submit" class="process pay-axes" name="method"  value="conveni"><?php echo Text::get('invest-conveni') ?></button></p>
<?php endif; ?>

</div>
<?php endif; ?>
</form>

<?php echo new View(VIEW_PATH . '/project/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $_SESSION['user']->worth)) ?>

<a name="commons"></a>
<div class="widget project-invest">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('invest-social-header') ?></h<?php echo $level ?>>

    <div class="social">
        <h<?php echo $level + 1 ?> class="title"><?php echo Text::get('project-rewards-social_reward-title'); ?></h<?php echo $level + 1 ?>>
        <ul>
        <?php foreach ($project->social_rewards as $social) : ?>
            <li class="<?php echo $social->icon ?>">
                <h<?php echo $level + 2 ?> class="name"><?php echo htmlspecialchars($social->reward) ?></h<?php echo $level + 2 ?>
                <p><?php echo htmlspecialchars($social->description)?></p>
                <?php if (!empty($social->license) && array_key_exists($social->license, $licenses)): ?>
                <div class="license <?php echo htmlspecialchars($social->license) ?>">
                    <h<?php echo $level + 3 ?>><?php echo Text::get('regular-license'); ?></h<?php echo $level + 3 ?>>
                    <a href="<?php echo htmlspecialchars($licenses[$social->license]->url) ?>" target="_blank">
                        <strong><?php echo htmlspecialchars($licenses[$social->license]->name) ?></strong>

                    <?php if (!empty($licenses[$social->license]->description)): ?>
                    <p><?php echo htmlspecialchars($licenses[$social->license]->description) ?></p>
                    <?php endif ?>
                    </a>
                </div>
                <?php endif ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>

<script type="text/javascript">
    
    $(function () {
        
        var update = function () {

            var $reward = null;
            var val = parseFloat($('#amount').val());

            $('div.widget.project-invest-individual_rewards input.individual_reward').each(function (i, cb) {
               var $cb = $(cb);
               $cb.closest('li').removeClass('chosed');
               // importe de esta recompensa
               var rval = parseFloat($cb.attr('amount'));
               if (rval > 0 && rval <= val) {
                   // si aun quedan
                   if ($cb.attr('disabled') != 'disabled') {
                       // nos quedamos con esta y seguimos
                       $reward = $cb;
                   }
               }

               if ($reward != null) {
                 $reward.click();
                 $reward.closest('li').addClass('chosed');
               } else {
                 $('#resign_reward').click();
                 $('#resign_reward').closest('li').addClass('chosed');
               }
            });
        };    

        var reset_reward = function (chosen) {

            $('div.widget.project-invest-individual_rewards input.individual_reward').each(function (i, cb) {
               var $cb = $(cb);
               $cb.closest('li').removeClass('chosed');

               if ($cb.attr('id') == chosen) {
                 $cb.closest('li').addClass('chosed');
               }
            });
        };

        // funcion comparar valores
        var greater = function (a, b) {
            if (parseFloat(a) > parseFloat(b)) {
                return true;
            } else {
                return false;
            }
        };

        // funcion resetear inpput de cantidad
        var reset_amount = function (preset) {
            $('#amount').val(preset);
            update();
        };

        // funcion resetear copy de cantidad
        var reset_reminder = function (amount) {
            var euros = parseFloat(amount);
            if (isNaN(euros)) {
                euros = 0;
            }

            $('#amount').val(euros);
            $('#amount-reminder').html(euros);
        };

/* Actualizar el copy */
        $('#amount').bind('paste', function () {reset_reminder($('#amount').val());update()});

        $('#amount').change(function () {reset_reminder($('#amount').val());update()});


/* Si estan marcando o quitando el renuncio */
        $(':radio').bind('change', function () {
            var curr = $('#amount').val();
            var a = $(this).attr('amount');
            var i = $(this).attr('id');

            <?php if ($step == 'start') : ?>
                reset_reward(i);
            <?php else : ?>
            // si es renuncio
            if ($('#resign_reward').prop('checked')) {
                $("#address-header").html('<?php echo Text::slash('invest-donation-header') ?>');
                /*$("#donation-data").show();*/
                reset_reward(i);
            } else {
                $("#address-header").html('<?php echo Text::slash('invest-address-header') ?>');
                /*$("#donation-data").hide();*/
                reset_reward(i);
            }
            <?php endif; ?>
            
            if (greater(a, curr)) {
                reset_reminder(a);
            }
        });

/* Verificacion, no tenemos en cuenta el paso porque solo son los botones de pago en el paso confirm */
        $('button.process').click(function () {

            var amount = $('#amount').val();
            var rest = $('#rest').val();

            if (parseFloat(amount) == 0 || isNaN(amount)) {
                alert('<?php echo Text::slash('invest-amount-error') ?>');
                $('#amount').focus();
                return false;
            }

            /* Renuncias pero no has puesto tu NIF para desgravar el donativo */
            if ($('#resign_reward').prop('checked')) {
                if ($('#nif').val() == '' && !confirm('<?php echo Text::slash('invest-alert-renounce') ?>')) {
                    $('#nif').focus();
                    return false;
                }
            } else {
                var reward = '';
                var chosen = 0;
                /* No has marcado ninguna recompensa, renuncias? */
                $('input.individual_reward').each(function (i, cb) {
                   var prize = $(this).attr('amount');
                   if (greater(prize, 0) && $(this).prop('checked')) {
                       reward = $(this).attr('title');
                       chosen = prize;
                   }
                });

               if (greater(chosen, amount)) {
                   alert('<?php echo Text::slash('invest-alert-lackamount') ?>');
                   return false;
               }

                if (reward == '') {
                    if (confirm('<?php echo Text::slash('invest-alert-noreward') ?>')) {
//                        if (confirm('<?php //echo Text::slash('invest-alert-noreward_renounce') ?>//')) {
                            $("#address-header").html('<?php echo Text::slash('invest-donation-header') ?>');
                            /*$("#donation-data").show();*/
                            $('#resign_reward').click();
//                            $('#nif').focus();
                            return false;
//                        }
                    } else {
//                        $('#nif').focus();
                        return false;
                    }
                } else {
                    //When the supporter has selected a reward　--add 141030
                    //var reward = $('#resign_reward').val();
                    var name = $('#fullname').val();
                    var add = $('#address').val();

                    if (reward == 0 && (name == '' || add == '')) {
                        alert('<?php echo Text::slash('invest-recipient-error') ?>');
                        return false;
                    }


                    /* Has elegido las siguientes recompensas */
                    if (!confirm( reward+' <?php echo Text::slash('invest-alert-rewards') ?> ')) {
                        return false;
                    }
                }
            }

            if (rest > 0 && greater(amount, rest)) {
                if (!confirm('<?php echo Text::slash('invest-alert-lackdrop') ?> '+rest+' EUR, ok?')) {
                    return false;
                }
            }

            return confirm(amount+'<?php echo Text::slash('invest-alert-investing') ?>');
        });

/* Seteo inicial por url */
        reset_amount('<?php echo $amount ?>');

    });    
    
</script>
