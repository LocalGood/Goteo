REPLACE INTO `template` VALUES (1,'問い合わせ通知（未使用）','general','CONTACTメニューからの問い合わせ','お問い合わせ : %SUBJECT%','<p>こんにちは<span class=\"message-highlight-red\">%OWNERNAME%</span>!</p>\n<p>お問い合わせフォームより、下記メッセージが送信されましたのでご連絡します。</p>\n<p><span class=\"message-highlight-blue\">%USEREMAIL%</span></p>\n<blockquote>%MESSAGE%</blockquote>\n');
REPLACE INTO `template` VALUES (2,'PJオーナーから支援者へのメッセージ','general','支援者に対するPJオーナーからのメッセージテンプレート（マイページから送信）','【%PROJECTNAME%】プロジェクトのオーナーからメッセージです','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">%PROJECTNAME%</a></span>　のプロジェクトオーナーより以下のメッセージが届いています。</p>\r\n\r\n<blockquote>%MESSAGE%</blockquote>\r\n\r\n<p>注意: このメールに直接返信しても、プロジェクトオーナーには届きませんのでご注意ください。</p>\r\n\r\n<p>温かいご支援に感謝いたします！今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\r\n');
REPLACE INTO `template` VALUES (3,'支援者からプロジェクトオーナーへの応援メッセージ','general','支援者が寄付完了後にプロジェクトにメッセージを送信した際のメッセージ','【%PROJECTNAME%】プロジェクトの支援者からメッセージです','<p>こんにちは <span class=\"message-highlight-red\">%OWNERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-red\">%USERNAME%</span>様から<span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトに寄付完了後、以下のメッセージを頂きました。</p>\r\n\r\n<blockquote>%MESSAGE%</blockquote>\r\n\r\n<p>注意: このメールに直接返信しても%USERNAME%様には届きませんのでご注意ください。<br />\r\n返信をする場合は、マイページより行ってください。<br />\r\nプロジェクト達成まで頑張りましょう！</p>\r\n');
REPLACE INTO `template` VALUES (4,'メッセージ','general','メッセージ','%USERNAME% さんへメッセージ ','<p><span class=\"message-highlight-red\">%TONAME%</span>さんこんにちは！</p>\r\n<p>送信したメッセージ　<span class=\"message-highlight-red\">%USERNAME%</span>：</p>\r\n<blockquote>%MESSAGE%</blockquote>\r\n\r\n<p><span class=\"message-highlight-red\">%USERNAME%</span> さんへにメッセージを送信するには<span class=\"message-highlight-blue\"><a href=\"%RESPONSEURL%\">ここをクリック</a></span></p>\r\n<p>あなたのプロフィールに接触している人たち：</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROFILEURL%\">%PROFILEURL%</a></span></p>\r\n\r\n<p>LOCAL GOOD</p>');
REPLACE INTO `template` VALUES (5,'新規ユーザー登録確認','general','ユーザー登録確認メッセージ','LOCAL GOOD ユーザー登録ありがとうございます','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p>LOCAL GOODサイトより、新規ユーザー登録リクエストを頂きました。</p>\r\n<p>あなたのアカウントは正常に作成されました。</p>\r\n\r\n<p>こちらの電子メールから以下のリンクをクリックし、登録を完了させてください。<br />\r\n※クリックできない場合は、リンクをブラウザにコピーして開いてください。</p>\r\n\r\n<a href=\"%ACTIVATEURL%\">%ACTIVATEURL%</a>\r\n\r\n<p>アカウントが有効化されますと、利用できるメニューが広がり、プロジェクト支援が可能になります。 </p>\r\n');
REPLACE INTO `template` VALUES (6,'ユーザー向けパスワードの再設定','general','パスワードの再設定を要求するメッセージのテンプレート','LOCAL GOODパスワード再設定メール','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p>LOCAL GOODサイトより、パスワードリセットのリクエストを頂きました。<br />\r\nパスワードを再設定するには以下のリンクをクリックしてください。</p>\r\n\r\n<p><span class=\"message-highlight-blue\"><a href=\"%RECOVERURL%\">%RECOVERURL%</a></span></p>\r\n\r\n<p>※クリックできない場合はリンクをブラウザにコピーして開いてください。<br />\r\n※このメールに見覚えが無い場合は無視してください。</p>\r\n');
REPLACE INTO `template` VALUES (7,'メールアドレス変更確認','general','メールアドレス変更を完了させるためのメッセージ','メールアドレス変更手続きメール','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\n\n<p>LOCAL GOODサイトより、メールアドレス変更リクエストを頂きました。変更処理を完了させるには、次のリンク先をクリックしてください。:<br />\n<span class=\"message-highlight-blue\"><a href=\"%CHANGEURL%\">%CHANGEURL%</a></span></p>※クリックできない場合はリンクをブラウザにコピーして開いてください。</p>\n<p>※URLをクリックされない場合、手続きは完了しませんのでご注意ください。</p>\n');
REPLACE INTO `template` VALUES (8,'オーナー向け新規プロジェクトの提出確認','general','PJオーナー権限をもつユーザーが新規PJ原稿を提出した際のオーナーへの確認メッセージ','LOCAL GOOD プロジェクト申請ありがとうございます','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様<br />\r\nLOCAL GOODサイトより、新規プロジェクトのリクエストを頂きました。</p>\r\n\r\n<p>内容を確認させて頂き、運営側からご連絡させていただきます。<br />\r\n事前にヒアリングさせて頂いておりますプロジェクト内容と差異がある場合、修正頂く可能性がございます。<br />\r\n最終確認が完了しましたらサイトに掲載開始となります。</p>\r\n');
REPLACE INTO `template` VALUES (9,'ユーザー退会確認','general','ユーザーが退会処理を完了させるためのメッセージ','LOCAL GOOD ユーザー退会手続き確認メール','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p>LOCAL GOODサイトより、ユーザー退会リクエストを頂きました。<br />\r\n退会処理を完了させるには、次のリンク先をクリックしてください。\r\n\r\n<a href=\"%URL%\">%URL%</a>\r\n\r\n<p>※クリックできない場合はリンクをブラウザにコピーして開いてください。</p>\r\n\r\n<p>またLOCALGOODをご利用頂けることを、心よりお待ちしています。</p>\r\n');
REPLACE INTO `template` VALUES (10,'支援者向け寄付感謝メール（1stラウンド/お礼あり）','general','支援者への寄付感謝メッセージ（1stラウンド/お礼ありユーザー向け）','【%PROJECTNAME%】プロジェクトに支援頂きありがとうございました！（1stラウンド）','<p>こんにちは <span class=\"message-highlight-red\">%USERNAME%</span>様</p>\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトに寄付を頂き誠にありがとうございました。</p>\n<p>お礼を選択頂きましたので、プロジェクトが必要資金を集めることができましたら、プロジェクトごとに定める期間において、下記の指定された住所に郵送させて頂きます。</p>\n<p>%USERNAME%様が選択したお礼：%REWARDS%<br />\nお礼の郵送先：%ADDRESS%</p>\n\n<p>※プロジェクトが1stラウンドで募集している最低必要金額を達成できなければ、AXES決済処理は実行されません。</p>\n\n<p>温かいご支援に感謝いたします！今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\n');
REPLACE INTO `template` VALUES (11,'管理者からのお知らせテンプレート','general','管理者からのお知らせ','管理者からのお知らせ','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n\r\n<blockquote>%CONTENT%</blockquote>\r\n');
REPLACE INTO `template` VALUES (12,'オーナー向けユーザーからの返信通知','general','スキル・物品マッチングで返信があったことをオーナーに通知','【%PROJECTNAME%】プロジェクトのスキル・物品支援マッチングに返信がありました','<p>こんにちは<span class=\"message-highlight-red\">%OWNERNAME%</span>!</p>\n\n<p><span class=\"message-highlight-red\">%USERNAME%</span>さんより、<span class=\"message-highlight-blue\">%PROJECTNAME%</span>プロジェクトのスキル・物品支援マッチング掲示板に返信がありました。</p>\n\n<blockquote>%MESSAGE%</blockquote>\n<span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">%PROJECTURL%</a></span>\n\n<p>注意: このメールに直接返信してもユーザーには届きませんのでご注意ください。<br />\n返信をする場合は、プロジェクトページのスキル・物品支援マッチングより行ってください。<br />\nプロジェクト達成まで頑張りましょう!</p>');
REPLACE INTO `template` VALUES (13,'オーナー通知（終了まで残り8日）','general','掲載終了まで残り8日をオーナーに通知','【%PROJECTNAME%】プロジェクト掲載終了まで残り8日です','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%様</span></p>\n\n<p>【%PROJECTNAME%】プロジェクトは、募集終了まで残り8日となりましたが、まだ最低必要金額を達成していません。<br />\n<span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">＜\"該当プロジェクトへのリンク先\"＞</a></span><br />\n最低必要金額を達成できなかった場合は、支援金は支払われません。ご注意ください。</p>\n\n<p>支援者のみなさんにこのプロジェクトの価値を改めてアピールし、プロジェクトが成立するよう最後まで頑張りましょう！<br />\nブログの投稿はマイページより行うことができます。</p>\n');
REPLACE INTO `template` VALUES (14,'オーナー通知（終了まで残り1日）','general','掲載終了まで残り1日をオーナーに通知','【%PROJECTNAME%】プロジェクト掲載終了まで残り1日です','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%様</span></p>\n\n<p>【%PROJECTNAME%】プロジェクトは、募集終了まで残り1日となりましたが、まだ最低必要金額を達成していません。<br />\n<span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">＜\"該当プロジェクトへのリンク先\"＞</a></span><br />\n最低必要金額を達成できなかった場合は、支援金は支払われません。ご注意ください。</p>\n\n<p>支援者のみなさんにこのプロジェクトの価値を改めてアピールし、プロジェクトが成立するよう最後まで頑張りましょう！<br />\nブログの投稿はマイページより行うことができます。</p>\n');
REPLACE INTO `template` VALUES (15,'支援者向け1stラウンド達成の感謝メール','general','プロジェクトが1stラウンド達成した場合に送られる、支援者への感謝メッセージ','【%PROJECTNAME%】プロジェクト1st ラウンド達成のお知らせ','<p>こんにちは <span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p>皆様のご支援のおかげで、<span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは 1stラウンドで募集した最低必要金額を達成しました！これで2ndラウンドの募集に進むことができます。</p>\r\n<p>このプロジェクトが目的としている地域課題を解決するためには、まだ完全ではありません。<br />\r\n引き続き、皆様のあたたかいご支援をお待ちしております。 </p>\r\n\r\n<p>温かいご支援に感謝いたします！今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\r\n<p>※このメールには直接返送頂けません。</p>');
REPLACE INTO `template` VALUES (16,'支援者向け2ndラウンド達成の感謝メール','general','プロジェクトが2ndラウンド達成した場合に送られる、支援者への感謝メッセージ','【%PROJECTNAME%】プロジェクト 2ndラウンド達成のお知らせ','<p>こんにちは <span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p>皆様のご支援のおかげで、<span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは 2ndラウンドの募集を終了しました！</p>\r\n<p>今後の活動は活動報告よりアップしていきます。</p>\r\n\r\n<p>温かいご支援に感謝いたします！今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\r\n<p>※このメールには直接返送頂けません。</p>');
REPLACE INTO `template` VALUES (17,'支援者向けプロジェクト未達成のご連絡','general','プロジェクトが1stラウンド募集期間を過ぎ最低必要金額を達成できなかった場合の連絡','【%PROJECTNAME%】プロジェクトは最低必要金額を達成できませんでした','<p>こんにちは <span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは、1stラウンド期間にて、最低必要金額を達成することができませんでした。</p>\r\n<p>せっかく貴重な支援を頂いたにも関わらず、最低必要金額を達成できず、大変申し訳ございませんでした。<br />\r\n支援頂いたことに、改めて感謝いたします。</p>\r\n<p>※AXESの決済はキャンセルされます。</p>\r\n<p>本プロジェクトは未達成となりましたが、他にも地域課題解決のため、支援を募集しているプロジェクトがあります！<br />今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\r\n');
REPLACE INTO `template` VALUES (18,'支援者向けプロジェクトの更新通知','general','支援者に対するプロジェクトページに更新があった際の通知','【%PROJECTNAME%】プロジェクトの掲載ページに更新がありました','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトの掲載ページに更新がありました。</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%UPDATEURL%\">こちら</a></span>から閲覧できます</p>\r\n<p>このプロジェクトの更新通知を受信したくない場合は、マイページから通知設定を変更できます。</p>\r\n\r\n<p>温かいご支援に感謝いたします！今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\r\n');
REPLACE INTO `template` VALUES (19,'オーナー通知（PJ掲載開始から20日経過）','general','プロジェクト掲載開始から20日経過をオーナーに通知','【%PROJECTNAME%】プロジェクトは掲載開始から20日が経過しました','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-blue\">【%PROJECTNAME%】</span>プロジェクトは、掲載開始から20日間が経過しました。<br />\r\nプロジェクトの掲載期間は40日間です。</p>\r\n\r\n<p>プロジェクトはマイページから更新できます。支援者に向けてブログ記事などの投稿ができます。<br />\r\n定期的に支援状況を確認し、残り半分の期間、プロジェクト達成までに寄付促進に繋がる活動を行いましょう。</p>\r\n<p>マイページへのリンクは<a href=\"%WIDGETURL%\"><span class=\"message-highlight-blue\">こちら</span></a></p>\r\n\r\n<p>プロジェクト達成まで頑張りましょう！</p>\r\n');
REPLACE INTO `template` VALUES (20,'オーナー通知（1stラウンド達成）','general','プロジェクト1stラウンド達成をオーナーに通知','【%PROJECTNAME%】プロジェクトは1stラウンドを達成しました！','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは、1stラウンドで最低必要金額の達成に成功しました。おめでとうございます！</p>\r\n\r\n<p><span class=\"message-highlight-blue\">【%PROJECTNAME%】</span>プロジェクトは、次の目標に向けて、2nd ラウンドに突入しました。ここからが本番です！<br />\r\n設定した目標金額達成に向けて、募集期間が40日間追加されました。<br />\r\nより多くの支援をいただけるよう、引き続きアピールしていきましょう！</p>\r\n\r\n<p>プロジェクトの支援状況は<span class=\"message-highlight-blue\"><a href=\"%WIDGETURL%\">マイページ</a></span>から確認できます。</p>\r\n\r\n<p>2nd ラウンドもがんばりましょう！</p>\r\n');
REPLACE INTO `template` VALUES (21,'オーナー向けプロジェクト未達成のご連絡','general','プロジェクトの不成立をオーナーに通知','【%PROJECTNAME%】プロジェクトは最低必要金額を達成できませんでした','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは、1stラウンド募集期間において、最低必要金額を達成できませんでした。<br />\r\nプロジェクトが達成されず大変残念ですが、またチャレンジ頂ければと思います。</p>\r\n\r\n<p>最低必要金額が達成されませんでしたので、支援金の支払処理はされません。</p>\r\n');
REPLACE INTO `template` VALUES (22,'オーナー通知（2ndラウンド終了）','general','プロジェクト2ndラウンド終了をオーナーに通知','【%PROJECTNAME%】プロジェクトは2ndラウンドを終了しました！','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは、2ndラウンドの掲載を終了しました。おめでとうございます！</p>\r\n<p>プロジェクトの支援状況はマイページから確認できます。<br />\r\nお礼の発送準備、プロジェクトの実行準備を始めましょう。<br />\r\nマイページは<span class=\"message-highlight-blue\"><a href=\"%REWARDSURL%\">こちら</a></span></p>\r\n\r\n<p>集まった支援金は手数料を差し引き、オーナー様に入金されます。<br />\r\n今後、集まった資金を利用して課題解決プロジェクトを実行しましたら、定期的に活動報告をサイトから行ってください。</p>\r\n<p>マイページは<span class=\"message-highlight-blue\"><a href=\"%REWARDSURL%\">こちら</a></span></p>\r\n\r\n<p>プロジェクト成立おめでとうございます！</p>\r\n');
REPLACE INTO `template` VALUES (23,'オーナー通知（3ヶ月間ブログ未更新）','general','過去3ヶ月ブログ更新が無い場合プロジェクトをオーナーにリマインド','【%PROJECTNAME%】ブログ/活動報告の更新がありません','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは、この３か月間、ブログ／活動報告の更新がないようです。</p>\r\n\r\n<p>プロジェクトのブログ記事を書いて、支援者のみなさんに活動を報告しましょう。</p>\r\n<p>マイページは<span class=\"message-highlight-blue\"><a href=\"%UPDATESURL%\">こちら</a></span></p>\r\n');
REPLACE INTO `template` VALUES (24,'オーナー通知（3ヶ月間未活動）','general','過去3ヶ月活動履歴の無いプロジェクトオーナーにリマインド','【%PROJECTNAME%】プロジェクトに関する活動履歴がありません','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは、この３か月間、活動履歴がないようです。</p>\r\n<p>プロジェクトは、プロジェクトが実行され、事後評価を受けるまで完了にはなりません。<br />\r\n支援者との交流をきちんと行えているか確認しましょう。<br />\r\nまた、プロジェクトのブログ記事を書いて支援者のみなさんに活動を報告しましょう。</p>\r\n<p>マイページは<span class=\"message-highlight-blue\"><a href=\"%UPDATESURL%\">こちら</a></span></p>\r\n');
REPLACE INTO `template` VALUES (25,'オーナー通知（PJ達成から2ヶ月経過）','general','プロジェクト達成から2ヶ月経過をオーナーに通知','【%PROJECTNAME%】プロジェクトは成立から2ヶ月が経過しました','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは、クラウドファンディング成立から２か月が経過しました。</p>\r\n<p>プロジェクトの実行は順調ですか？支援者のみなさんに活動報告やお礼の送付を忘れずに行いましょう。</p>\r\n<p>ブログ／活動報告の更新、支援者の管理は、<span class=\"message-highlight-blue\"><a href=\"%REWARDSURL%\">マイページ</a></span>より行うことができます。</p>\r\n');
REPLACE INTO `template` VALUES (26,'プロジェクトの翻訳','general','プロジェクトが翻訳できるようになりました','これで【%PROJECTNAME%】プロジェクトを翻訳することができます。','<p><span class=\"message-highlight-red\">%OWNERNAME%</span>さんこんにちは！</p>\r\n<p>【%PROJECTNAME%】プロジェクトは翻訳することができるようになりました。</p>\r\n<p>マイページから確認できます：</p>\r\n<p><span class=\"message-highlight-blue\"><a href=\"%SITEURL%/dashboard/translates\">%SITEURL%/dashboard/translates</a></span></p>');
REPLACE INTO `template` VALUES (28,'支援者向け寄付感謝メール（1stラウンド/お礼無し）','general','支援者への寄付感謝メッセージ（1stラウンド/お礼無しユーザー向け）','【%PROJECTNAME%】プロジェクトに支援頂きありがとうございました！（1stラウンド）','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトに寄付を頂き誠にありがとうございました。</p>\n\n<p>※プロジェクトが1stラウンドで募集している最低必要金額を達成できなければ、AXES決済処理は実行されません。</p>\n\n<p>温かいご支援に感謝いたします！今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\n');
REPLACE INTO `template` VALUES (29,'オーナー向けプロジェクト寄付通知','general','プロジェクトに寄付があったときPJオーナーに通知するメッセージ','【%PROJECTNAME%】に寄付を頂きました！','<p>こんにちは <span class=\"message-highlight-red\">%OWNERNAME%</span>様</p>\n<p>あなたの <span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span> プロジェクト は <span class=\"message-highlight-blue\">%USERNAME%</span> 様から<span class=\"message-highlight-blue\">%AMOUNT%</span> 円の支援を受けました。</p>\n\n<p>プロジェクト達成まで頑張りましょう!</p>');
REPLACE INTO `template` VALUES (30,'オーナー向けプロジェクトへの投稿通知','general','ユーザーからプロジェクトに連絡があったことをオーナーに通知','【%PROJECTNAME%】プロジェクトのスキル・物品支援マッチングに新規投稿がありました','<p>こんにちは<span class=\"message-highlight-red\">%OWNERNAME%</span>様</p>\n\n<p><span class=\"message-highlight-red\">%USERNAME%</span>さんより、<span class=\"message-highlight-blue\">%PROJECTNAME%</span>プロジェクトのスキル・物品支援マッチング掲示板に新規投稿がありました。</p>\n<blockquote>%MESSAGE%</blockquote>\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">%PROJECTURL%</a></span></p>\n\n<p>注意: このメールに直接返信してもユーザーには届きませんのでご注意ください。<br />\n返信をする場合は、プロジェクトページのスキル・物品支援マッチングより行ってください。<br />\nプロジェクト達成まで頑張りましょう!</p>\n');
REPLACE INTO `template` VALUES (31,'オーナー向け新しいコメントに関するお知らせ','general','支援者がブログ/活動報告にコメントを投稿した際のPJオーナーに通知するメッセージ','【%PROJECTNAME%】へ新着コメントがあります','<p>こんにちは<span class=\"message-highlight-red\">%OWNERNAME%</span>様</p>\r\n\r\n<p><span class=\"message-highlight-red\">%USERNAME%</span>さんから、<span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">%PROJECTNAME%</a></span>プロジェクトのブログ記事へコメント投稿がありました。\r\n\r\n<blockquote>%MESSAGE%</blockquote> \r\n\r\n<p>注意: このメールに直接返信しても、%USERNAME%さんには届きませんのでご注意ください。<br />\r\n返信をする場合は、マイページ／マイプロジェクトの該当記事より行ってください。<br />\r\nプロジェクト達成まで頑張りましょう!</p>\r\n');
REPLACE INTO `template` VALUES (33,'ニュースレター','general','ニュースレター用テンプレート','LOCAL GOODからのお知らせ','<div style=\"width:590px;background-color:#ffffff;font-size:18px;padding:20px 20px 5px;\" >\r\n<span style=\"font-size:21px;font-weight:bold;\" >タイトル</span>\r\n<p>本文</p>\r\n</div>\r\n');
REPLACE INTO `template` VALUES (34,'支援者向け寄付感謝メール（2ndラウンド/お礼あり）','general','支援者への寄付感謝メッセージ（2ndラウンド/お礼ありユーザー向け）','【%PROJECTNAME%】プロジェクトに支援頂きありがとうございました！（2ndラウンド）','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトに寄付を頂き誠にありがとうございました。</p>\n<p>お礼を選択頂きましたので、プロジェクトの掲載が終了しましたら、プロジェクトごとに定める期間において、下記の指定された住所に郵送させて頂きます。</p>\n<p>%USERNAME%様が選択したお礼： %REWARDS%<br />\nお礼の郵送先：%ADDRESS%</p>\n\n<p>温かいご支援に感謝いたします！今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\n');
REPLACE INTO `template` VALUES (35,'支援者向けPJ不成立の通知','general','プロジェクト不成立を支援者に通知','【%PROJECTNAME%】プロジェクトに応援頂きありがとうございました','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトは、1stラウンド期間にて、最低必要金額を達成することができませんでした。<br />\r\nせっかく貴重な支援を頂いたにも関わらず、残念な結果になったことをお詫び申し上げます。<br />\r\n支援頂いたことに、改めて感謝いたします。<br />\r\n※AXESの決済はキャンセルされます。</p>\r\n\r\n<p>本プロジェクトは未達成となりましたが、ほかにも地域課題解決のため、支援を募集しているプロジェクトがあります！<br />\r\n今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\r\n');
REPLACE INTO `template` VALUES (36,'支援者向け寄付感謝メール（2ndラウンド/お礼無し）','general','支援者への寄付感謝メッセージ（2ndラウンド/お礼無しユーザー向け）','【%PROJECTNAME%】プロジェクト　に支援頂きありがとうございました！（2ndラウンド）','<p>こんにちは <span class=\"message-highlight-red\">%USERNAME%</span>様</p>\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトに寄付を頂き誠にありがとうございました。</p>\n\n<p>温かいご支援に感謝いたします！今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\n');
REPLACE INTO `template` VALUES (37,'AXES処理失敗の通知','invest','AXES決済処理の失敗通知','【%PROJECTNAME%】プロジェクトのAXES決済が失敗しました','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\n\n<p>【%PROJECTNAME%】プロジェクトに、%AMOUNT%円ご支援いただきありがとうございました。<br />\n<span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">＜\"該当プロジェクトへのリンク先\"＞</a></span><br />\nしかしながら、AXESの自動決済処理が正常に行われなかったようです。<br />\nAXESでのお支払いに決済エラーが出た際には、下記に当てはまる項目がないかご確認ください。</p>\n\n<ul>\n<li>AXESに登録しているクレジットカードの有効期限や利用限度額は正しいか</li>\n</ul>\n\n<p>今後ともLOCAL GOODプロジェクトへの応援をよろしくお願いします。</p>\n');
REPLACE INTO `template` VALUES (38,'Recordatorio donantes','massive','Para recordar algo a los donantes','Aviso: certificados de donativos','');
REPLACE INTO `template` VALUES (39,'オーナー向けスキルマッチング応募通知','general','スキルマッチングに応募があったときオー>ナーに通知するメッセージ','【%PROJECTNAME%】に応募がありました！','<p>こんにちは <span class=\"message-highlight-red\">%OWNERNAME%</span>様</p>\n<p>あなたのスキルマッチングプロジェクト <span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span> に <span class=\"message-highlight-blue\">%USERNAME%</span> 様からの応募がありました！</p>\n\n<p>\n-----<br>\n応募ユーザー名： %USERNAME%<br>\nメールアドレス： %USEREMAIL%<br>\n応募項目： %REWARD%<br>\n-----\n</p>\n\n<p>募集達成まで頑張りましょう!</p>');
REPLACE INTO `template` VALUES (40,'審査結果の通知（不合格）','contact','[審査不合格]押下後、プロジェクト管理からオーナーに自動送信','【%PROJECTNAME%】プロジェクトにご応募頂きありがとうございました','<p>こんにちは<span class=\"message-highlight-red\">%USERNAME%</span>様</p>\r\n\r\n<p><span class=\"message-highlight-blue\"><a href=\"%PROJECTURL%\">【%PROJECTNAME%】</a></span>プロジェクトを申請頂きありがとうございました。<br />\r\nLOCAL GOOD検討委員会により、慎重に検討いたしました結果、誠に遺憾ではございますが、掲載を見合わせることとなりました。<br />\r\n悪しからずご了承下さい。</p>\r\n\r\n<p>プロジェクトを掲載することができず大変残念ですが、またチャレンジ頂ければと思います。<br />\r\nプロジェクトの内容については、アドバイスも行っております。<br />\r\nご希望の方は以下のアドレスまで、お問い合わせください。<br />\r\n localgood@yokohamalab.jp\r\n</p>\r\n');
