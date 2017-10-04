<?php
use Goteo\Core\View,
    Goteo\Model\User;

$invest = $this['invest'];



#
#	AXES では、外部CGI 直リンクだったが、イプシロンは違うので、フローを変更。
#	epsilongo で、処理する。
#
$redirect = '/invest/epsilongo/' . $invest->id . '/2/';
$item_price = $invest->amount;


?>

<?php include VIEW_PATH . '/prologue.html.php';?>
<?php include VIEW_PATH . '/header.html.php' ?>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

    <div class="contents_wrapper">
        <div id="main" class="">
            <div class="widget invest-pre-info">
                <p><span class="project_name"><?php echo $invest->project_name ?></span>に<span class="amount"><?php echo $invest->amount;?></span>円寄付します。</p>


<?php print<<<FORM1
                <form method="post" action="$redirect">
                    <input type="hidden" name="amount" value="$item_price">

                    <button type="submit" id="submit" class="process pay-axes" name="method" value="epsilongo">前回のカードで決済を実行</button>
                    <!-- input type="submit" value="決済ページへ" -->

                    <input type="hidden" name="failure_str" value="back">
                    <input type="button" value="戻る" class="back" onClick='history.back();'>    

                </form>
                <div class="caution">
                    <br />
                	<p style="color:#ff3300;margin-bottom: 0;">前回ご利用のクレジットカードにて決済いたします。</p>
                	<p style="color:#ff3300;margin-bottom: 0;">初回のご利用の場合は、クレジットカード番号の入力が必要です。</p>
                    <br />
                    <br />
                    <br />
                    実際の決済はプロジェクト成立後に行われます。
                    <br />
                    <br />
                    <h3>【クレジットカード決済に関するご説明】</h3>
                    <p>決済システムは（株）ＧＭＯイプシロンを利用しています。<br />
                        クレジットカードの一括払いでのお支払となります。クレジットカード番号はローカルグッドに知らされることはございませんのでご安心ください。<br />
                        <a href="http://www.epsilon.jp/security.html" target="_blank">必ずお読みください</a><br /><br />
                    </p>
                    <h3>【カード決済に関するお問い合わせ】</h3>
                    <p>カスタマーサポート（平日 9:30 - 18:00)<br />
                        TEL：03-3464-6211<br />
                        <a href="mailto:support@epsilon.jp">support@epsilon.jp</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

FORM1;
?>

<?php include VIEW_PATH . '/footer.html.php' ?>
<?php include VIEW_PATH . '/epilogue.html.php' ?>
