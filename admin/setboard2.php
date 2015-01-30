<?php
require("passcheck.php");
if (!isset($_POST['mode'])) $_POST['mode'] = '';
if(get_magic_quotes_gpc()) {
	$_POST = array_map("stripslashes", $_POST);
}
if (!isset($_REQUEST['bbs']) or !$_REQUEST['bbs']) disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：板名をいれてちょ。。。");
if (!is_dir("../$_REQUEST[bbs]")) disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：そんな板ないです。");
$set_pass = "../$_REQUEST[bbs]/config.php";
$comment = '<br>';
#====================================================
#　設定ファイルの書込み
#====================================================
if ($_POST['mode'] == 'set') {
	$_POST['THREAD_MAX_MSG'] = str_replace(array("\r\n", "\r", "\n"), '<br>', $_POST['THREAD_MAX_MSG']);
	$setvalue = <<<EOF
<?php
# 設定ファイル（"SETTING.TXT"以外の設定項目）
# ログファイル保持数（システム設定）
define('KEEPLOGCOUNT', $_POST[KEEPLOGCOUNT]);
# 1スレッドに投稿できるレス数の上限
define('THREAD_RES', $_POST[THREAD_RES]);
# レスオーバー時のメッセージ
define('THREAD_MAX_MSG', '$_POST[THREAD_MAX_MSG]');
# 1スレッドの上限（バイト）
define('THREAD_BYTES', $_POST[THREAD_BYTES]);
# ファイルアップ許可
define('UPLOAD', $_POST[UPLOAD]);
# GDバージョン
define('GD_VERSION', $_POST[GD_VERSION]);
# アップロード上限（バイト）
define('MAX_BYTES', $_POST[MAX_BYTES]);
# サムネイル画像の幅
define('MAX_W', $_POST[MAX_W]);
# サムネイル画像の高さ
define('MAX_H', $_POST[MAX_H]);
# おみくじ機能
define('OMIKUJI', $_POST[OMIKUJI]);
# 野球機能
define('BASEBALL', $_POST[BASEBALL]);
# どこ誰何機能
define('WHO_WHERE', $_POST[WHO_WHERE]);
# 壷機能（未実装）
define('TUBO', $_POST[TUBO]);
# 等幅フォント機能
define('TELETYPE', $_POST[TELETYPE]);
# スレッド内名無し名変更機能
define('NAME_774', $_POST[NAME_774]);
# 名無しへ強制変更機能
define('FORCE_774', $_POST[FORCE_774]);
# IDなし機能
define('FORCE_NO_ID', $_POST[FORCE_NO_ID]);
# sage強制機能
define('FORCE_SAGE', $_POST[FORCE_SAGE]);
# レス要キャップ機能
define('FORCE_STARS', $_POST[FORCE_STARS]);
# スレッド内VIP機能解除
define('FORCE_NORMAL', $_POST[FORCE_NORMAL]);
# 名前入力強制機能
define('FORCE_NAME', $_POST[FORCE_NAME]);
# 0thelo機能
define('ZEROTHELO', $_POST[ZEROTHELO]);
# アップロード機能
define('FORCE_UP', $_POST[FORCE_UP]);
?>

EOF;
	$fp = fopen($set_pass, "w");
	fputs($fp, $setvalue);
	fclose($fp);
	$comment = '<font color="red">設定を更新しました。</font><br>';
}
#====================================================
#　初期情報の取得（設定ファイル）
#====================================================
#設定ファイルを読む
if (is_file("../$_REQUEST[bbs]/SETTING.TXT")) {
	$set_str = file("../$_REQUEST[bbs]/SETTING.TXT");
	foreach ($set_str as $tmp){
		$tmp = rtrim($tmp);
		list($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
}
else disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：ユーザー設定が消失しています！");
#設定ファイル2を読む
if (is_file($set_pass)) {
	require $set_pass;
}
else disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：ユーザー設定２が消失しています！");
if (!is_file("../$_REQUEST[bbs]/index.html")) {
	$comment = '<font color=red>まず各設定項目を変更後に<b>設定更新</b>ボタンを押し、<a href="admin.php?bbs='.$_REQUEST['bbs'].'" target="_parent">ここ</a>からメニューの更新をして、次にメニューの<b>index.htmlを作り直す</b>をクリックしてください。</font><br>';
}
$maxmsg = str_replace('<br>', "\n", THREAD_MAX_MSG);
#====================================================
#　掲示板の設定
#====================================================
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" href="main.css" type="text/css">
<title>VIP設定変更</title>
</head>
<body>
<h1 class="title"><?=$SETTING['BBS_TITLE']?></h1>
<h3>VIP設定変更</h3>
<hr>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<?=$comment?>
<input type="submit" value="設定更新">
<input type="hidden" name="mode" value="set">
<input type="hidden" name="bbs" value="<?=$_REQUEST['bbs']?>">
<table border="2">
<tr>
<th>ログファイル保持数</th>
<td><input type="text" size="10" name="KEEPLOGCOUNT" value="<?=KEEPLOGCOUNT?>"></td>
</tr>
<tr>
<th>1スレッドに投稿できるレス数の上限</th>
<td><input type="text" size="10" name="THREAD_RES" value="<?=THREAD_RES?>"></td>
</tr>
<tr>
<th>レスオーバー時のメッセージ<br>レス数の表示部分は<br>&lt;NUM&gt;<br>と書いてください<br></th>
<td><textarea cols="60" rows="5" name="THREAD_MAX_MSG"><?=$maxmsg?></textarea></td>
</tr>
<tr>
<th>1スレッドの上限（バイト）</th>
<td><input type="text" size="10" name="THREAD_BYTES" value="<?=THREAD_BYTES?>"></td>
</tr>
<tr>
<th>画像アップロード</th>
<td><input type="radio" name="UPLOAD" value="1"<? if (UPLOAD) echo " checked"?>>あり　<input type="radio" name="UPLOAD" value="0"<? if (!UPLOAD) echo " checked"?>>なし</td>
</tr>
<tr>
<th>サムネイル作成</th>
<td><font size=2>
<input type="radio" name="GD_VERSION" value="0"<? if (GD_VERSION == 0) echo " checked"?>>なし |
<input type="radio" name="GD_VERSION" value="1"<? if (GD_VERSION == 1) echo " checked"?>>あり(GD Ver.1) |
<input type="radio" name="GD_VERSION" value="2"<? if (GD_VERSION == 2) echo " checked"?>>あり(GD Ver.2)
</font></td>
</tr>
<tr>
<th>アップロード上限（バイト）</th>
<td><input type="text" size="10" name="MAX_BYTES" value="<?=MAX_BYTES?>">php.iniの設定（普通2M）以上にしてもだめですから</td>
</tr>
<tr>
<th>サムネイル画像の幅</th>
<td><input type="text" size="10" name="MAX_W" value="<?=MAX_W?>"></td>
</tr>
<tr>
<th>サムネイル画像の高さ</th>
<td><input type="text" size="10" name="MAX_H" value="<?=MAX_H?>"></td>
</tr>
</table>
VIP機能<br>
<table border="2">
<tr>
<th>おみくじ機能</th>
<td><input type="radio" name="OMIKUJI" value="1"<? if (OMIKUJI) echo " checked"?>>あり　<input type="radio" name="OMIKUJI" value="0"<? if (!OMIKUJI) echo " checked"?>>なし</td>
</tr>
<tr>
<th>野球機能</th>
<td><input type="radio" name="BASEBALL" value="1"<? if (BASEBALL) echo " checked"?>>あり　<input type="radio" name="BASEBALL" value="0"<? if (!BASEBALL) echo " checked"?>>なし</td>
</tr>
<tr>
<th>誰がどこで機能</th>
<td><input type="radio" name="WHO_WHERE" value="1"<? if (WHO_WHERE) echo " checked"?>>あり　<input type="radio" name="WHO_WHERE" value="0"<? if (!WHO_WHERE) echo " checked"?>>なし</td>
</tr>
<tr>
<th>壷機能（もってないので未実装）</th>
<td><input type="radio" name="TUBO" value="1"<? if (TUBO) echo " checked"?>>あり　<input type="radio" name="TUBO" value="0"<? if (!TUBO) echo " checked"?>>なし</td>
</tr>
<tr>
<th>等幅フォント機能</th>
<td><input type="radio" name="TELETYPE" value="1"<? if (TELETYPE) echo " checked"?>>あり　<input type="radio" name="TELETYPE" value="0"<? if (!TELETYPE) echo " checked"?>>なし</td>
</tr>
</table>
スレ立て時につけられる機能<br>
<table border="2">
<tr>
<th>スレッド内名無し名変更機能「!774格さん!3」</th>
<td><input type="radio" name="NAME_774" value="1"<? if (NAME_774) echo " checked"?>>あり　<input type="radio" name="NAME_774" value="0"<? if (!NAME_774) echo " checked"?>>なし</td>
</tr>
<tr>
<th>名無しへ強制変換機能「!774!force格さん!3」</th>
<td><input type="radio" name="FORCE_774" value="1"<? if (FORCE_774) echo " checked"?>>あり　<input type="radio" name="FORCE_774" value="0"<? if (!FORCE_774) echo " checked"?>>なし</td>
</tr>
<tr>
<th>IDなし機能「!774!force!noid!3」</th>
<td><input type="radio" name="FORCE_NO_ID" value="1"<? if (FORCE_NO_ID) echo " checked"?>>あり　<input type="radio" name="FORCE_NO_ID" value="0"<? if (!FORCE_NO_ID) echo " checked"?>>なし</td>
</tr>
<tr>
<th>強制sage機能「!774!force!sage!3」</th>
<td><input type="radio" name="FORCE_SAGE" value="1"<? if (FORCE_SAGE) echo " checked"?>>あり　<input type="radio" name="FORCE_SAGE" value="0"<? if (!FORCE_SAGE) echo " checked"?>>なし</td>
</tr>
<tr>
<th>レス時キャップ必須機能「!774!force!stars!3」</th>
<td><input type="radio" name="FORCE_STARS" value="1"<? if (FORCE_STARS) echo " checked"?>>あり　<input type="radio" name="FORCE_STARS" value="0"<? if (!FORCE_STARS) echo " checked"?>>なし</td>
</tr>
<tr>
<th>スレッド内VIP機能解除「!774!normal!3」</th>
<td><input type="radio" name="FORCE_NORMAL" value="1"<? if (FORCE_NORMAL) echo " checked"?>>あり　<input type="radio" name="FORCE_NORMAL" value="0"<? if (!FORCE_NORMAL) echo " checked"?>>なし</td>
</tr>
<tr>
<th>名前入力強制機能「!774!name!3」</th>
<td><input type="radio" name="FORCE_NAME" value="1"<? if (FORCE_NAME) echo " checked"?>>あり　<input type="radio" name="FORCE_NAME" value="0"<? if (!FORCE_NAME) echo " checked"?>>なし</td>
</tr>
<tr>
<th>0thelo機能「!774!0thello!3#tripkey」</th>
<td><input type="radio" name="ZEROTHELO" value="1"<? if (ZEROTHELO) echo " checked"?>>あり　<input type="radio" name="ZEROTHELO" value="0"<? if (!ZEROTHELO) echo " checked"?>>なし</td>
</tr>
<tr>
<th>アップロード機能「!774!force!up!3」</th>
<td><input type="radio" name="FORCE_UP" value="1"<? if (FORCE_UP) echo " checked"?>>あり　<input type="radio" name="FORCE_UP" value="0"<? if (!FORCE_UP) echo " checked"?>>なし</td>
</tr>
</table>
<input type="submit" value="設定更新"><br>
</form>
<br>
</body></html>
