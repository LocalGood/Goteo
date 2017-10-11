<?php
use Goteo\Core\View,
    Goteo\Model\User;

$invest = $this['invest'];
?>

<?php include 'view/prologue.html.php';?>
<?php include 'view/header.html.php' ?>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

    <div class="contents_wrapper">
        <div id="main" class="axes">
            <div class="widget">
                <p><span class="project_name"><?php echo $invest->project_name ?></span>に<span class="amount"><?php echo $invest->amount;?></span>円寄付します。</p>
                <form method="post" action="https://gw.axes-payment.com/cgi-bin/credit/order.cgi">
                <input type="hidden" name="clientip" value="<?php echo AXES_CLIENTIP; ?>">
                <input type="hidden" name="money" value="0">
                <input type="hidden" name="sendid" value="<?php echo$invest->id?>">
                <input type="hidden" name="sendpoint" value="">
                <input type="hidden" name="success_url" value="<?php echo$invest->urlOK?>">
                <input type="hidden" name="success_str" value="back">
                <input type="hidden" name="failure_url" value="<?php echo$invest->urlNOK?>">
                <input type="hidden" name="failure_str" value="back">
                <input type="button" value="戻る" class="back" onClick='history.back();'>
                <input type="submit" value="決済ページへ">
                </form>
                <div class="caution">
                    <br />
                    実際の決済はプロジェクト成立後に行われます。手続き上、今回の決済は0円でお支払いが表示されます。
                    <br />
                    <br />
                    <h3>【クレジットカード決済に関するご説明】</h3>
                    <p>決済システムは（株）AXES Paymentを利用しています<br />
                        クレジットカード（Visa、JCB、MasterCard）の一括払いでのお支払となります。クレジットカード番号は弊社に知らされることはございませんのでご安心ください。<br />
                        <a href="https://gw.axes-payment.com/cgi-bin/pc_exp.cgi?clientip=1011003702" target="_blank">必ずお読みください</a><br /><br />
                    </p>
                    <h3>【カード決済に関するお問い合わせ】</h3>
                    <p>カスタマーサポート（24時間365日)<br />
                        TEL：0570-03-6000（03-3498-6200<br />
                        <a href="mailto:creditinfo@axes-payment.co.jp">creditinfo@axes-payment.co.jp</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>
