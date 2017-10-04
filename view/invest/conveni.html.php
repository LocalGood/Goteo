<?php
use Goteo\Core\View,
    Goteo\Model\User;

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
                <p><span class="project_name"><?php echo $invest->project_name ?></span>に<span class="amount"><?php echo $invest->amount;?></span>円寄付します。</p>

<?php print<<<FORM1
                <form method="post" action="$redirect">
                    <input type="hidden" name="amount" value="$item_price">

                    <button type="submit" class="process pay-axes" name="method" value="convenigo">コンビニ決済ページへ</button>

                    <input type="hidden" name="failure_str" value="back">
                    <input type="button" value="戻る" class="back" onClick='history.back();'>

                </form>
                <div class="caution">
                    <br />
					コンビニエンスストアでお支払い後、寄付金として反映されます。<br />
                    <br />
                    <br />
                    <h3>【コンビニ決済に関するご説明】</h3>
                    <p>決済システムは（株）ＧＭＯイプシロンを利用しています。<br />
                        コンビニエンスストアでのお支払となります。<br />
                        コンビニ決済の場合、プロジェクトが成立しない場合も、返金処理がございません。<br />
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

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>
