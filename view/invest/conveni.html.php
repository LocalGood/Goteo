<?php
use Goteo\Core\View,
    Goteo\Model\User,
    Goteo\Library\Text;

$invest = $this['invest'];
$bodyClass = 'invest';



#
#	AXES では、外部CGI 直リンクだったが、イプシロンは違うので、フローを変更。
#	epsilongo で、処理する。
#
$redirect = '/invest/convenigo/' . $invest->id . '/';
$item_price = $invest->amount;


?>

<?php include 'view/prologue.html.php';?>
<?php include 'view/header.html.php' ?>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

    <div class="contents_wrapper">
        <div id="main" class="">
            <div class="widget invest-pre-info">
                <p><span class="project_name"><?php echo $invest->project_name ?></span><?php echo Text::get('invest-amount-to') ?><span class="amount"><?php echo $invest->amount;?></span><?php echo Text::get('invest-price') ?></p>
                <form method="post" action="<?php echo $redirect; ?>">
                    <input type="hidden" name="amount" value="<?php echo $item_price; ?>">

                    <button type="submit" class="process pay-axes" name="method" value="convenigo"><?php echo Text::get('invest-to-conveni') ?></button>

                    <input type="hidden" name="failure_str" value="back">
                    <input type="button" value="戻る" class="back" onClick='history.back();'>

                </form>
                <div class="caution">
                    <p class="first_text"><?php echo Text::get('invest-conveni-description') ?></p>
                    <h3><?php echo Text::get('invest-conveni-about-payment-ttl') ?></h3>
                    <p><?php echo Text::get('invest-conveni-about-payment-desc') ?></p>
                    <h3><?php echo Text::get('invest-about-payment-ttl') ?></h3>
                    <p><?php echo Text::get('invest-about-payment-desc') ?></p>
                </div>
            </div>
        </div>
    </div>

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>
