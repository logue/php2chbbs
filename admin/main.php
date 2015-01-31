<?php
require("passcheck.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>トップページ</title>
<link rel="stylesheet" href="main.css" type="text/css" />
</head>
<body>
<h3>トップ</h3>
この画面です。<br />
<br>
<?php /*
if (!ini_get("safe_mode")) {
	?>
<h3>掲示板作成</h3>
新しく掲示板を自動設置します。PHPの設定でSafe modeがOnの場合は使用できません。<br>
<br />
<?php
}*/
?>
<h3>キャップ管理</h3>
<p>ここでパスワードと名前を登録すると、メール欄に”#パスワード”と入力することで★マークをつけることが出来ます。</p>
<h3>掲示板管理</h3>
<p>javascriptが有効であればディレクトリの部分をクリックすることで表示を切り替えられます。</p>
<dl>
	<dt>設定変更</dt>
	<dd>掲示板の背景や文字の色などの各種設定を行います。</dd>
	<dt>VIP機能設定</dt>
	<dd>VIP機能の各種設定を行います。</dd>
	<dt>VIP設定変更</dt>
	<dd>スレッド毎VIP機能の変更を行います。</dd>
	<dt>あぼーん/スレスト</dt>
	<dd>あぼーんは投稿記事削除のことです。スレストはそのスレッドを書込み禁止にします。</dd>
	<dt>スレッド削除/移動</dt>
	<dd>スレッド全体を削除、または過去ログに移動します。</dd>
	<dt>掲示板閉鎖</dt>
	<dd>掲示板のデータを全て消去します。</dd>
	<dt>画像削除</dt>
	<dd>投稿画像の削除処理を行います。</dd>
	<dt>ホストログ管理</dt>
	<dd>投稿した人のホスト名、IPアドレスが確認できます。</dd>
	<dt>アク禁処理</dt>
	<dd>アクセス禁止、解除の処理を行います。</dd>
	<dt>テキスト編集</dt>
	<dd>テキストファイルの編集をウェブ上から行えます。</dd>
	<dt>index.htmlを作り直す</dt>
	<dd>変更の内容をindex.htmlに反映させます。</dd>
</dl>
</body>
</html>
