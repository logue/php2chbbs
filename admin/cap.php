<?php
#====================================================
#　キャップ
#====================================================
require("passcheck.php");
#=============================================
$cap_file = "../test/caps.cgi";
$list = @file($cap_file);
$fll = 0;
if (!isset($_POST['name'])) $_POST['name'] = '';
if (!isset($_POST['password'])) $_POST['password'] = '';
if (!isset($_POST['passold'])) $_POST['passold'] = '';
if (get_magic_quotes_gpc()) {
	$_POST['name'] = stripslashes($_POST['name']);
	$_POST['password'] = stripslashes($_POST['password']);
}
#---------------追加
if (isset($_POST['mode']) and $_POST['mode'] == "add") {
	$fll = 1;
	if (!$_POST['name']) disperror("ＥＲＲＯＲ！","名前が入力されていません！");
	if(!$_POST['password']) disperror("ＥＲＲＯＲ！","パスが入力されていません！");
	$time = time();
	if ($list) {
		foreach ($list as $tmp) {
			$tmp = trim($tmp);
			list($id2,$name2,$pass2) = explode("<>", $tmp);
			if ($_POST['name'] == $name2) disperror("ＥＲＲＯＲ！","その名前は既に使用されています！");
			if (crypt($_POST['password'], $pass2) == $pass2) disperror("ＥＲＲＯＲ！","そのパスは既に使用されています！");
		}
	}
	$_POST['password'] = crypt($_POST['password']);
	$fp = @fopen($cap_file, "a");
	fputs($fp, "$time<>$_POST[name]<>$_POST[password]<>$_POST[color]\n");
	fclose($fp);
}
#---------------編集
elseif (isset($_POST['mode']) and $_POST['mode'] == "変更"){
	$fll = 1;
	$caplist = '';
	foreach ($list as $tmp){
		list($id2,$name2,$pass2) = explode("<>", $tmp);
		if ($_POST['id'] == $id2) {
			if (crypt($_POST['passold'], $pass2) != $pass2) disperror("ＥＲＲＯＲ！","パスワードが違います！");
			if ($_POST['passnew']) $pass2 = crypt($_POST['passnew']);
			$tmp="$_POST[id]<>$_POST[name]<>$pass2<>$_POST[color]\n";
		}
		$caplist .= $tmp;
	}
	$fp = fopen($cap_file, "w");
	fputs($fp, $caplist);
	fclose($fp);
}
#---------------削除
elseif (isset($_POST['mode']) and $_POST['mode'] == "削除"){
	$fll = 1;
	$caplist = '';
	foreach ($list as $tmp){
		list($id2,$name2,$pass2) = explode("<>", $tmp);
		if($_POST['id'] == $id2){
			if (crypt($_POST['passold'], $pass2) != $pass2) disperror("ＥＲＲＯＲ！","パスワードが違います！");
			else continue;
		}
		$caplist .= $tmp;
	}
	$fp = fopen($cap_file, "w");
	fputs($fp, $caplist);
	fclose($fp);
}
#---------------それ以外
if($fll == 1){
	header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]");
	exit;
}
#########キャップ管理メニュー
?>
<html>
<head>
<title>キャップ管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" href="main.css" type="text/css">
</head>
<body>
<h3>キャップ管理</h3>
<hr>
投稿時にメール欄に#に続けてパスワードを入力すると、ここで登録した名前がキャップマーク★付きで表示されます。<br>
パスワードがabcdだったらメール欄に#abcdと記入します。<br>
sage#abcdのようにsage機能と併用も出来ます。script@s16.xrea.com#abcdとメールアドレスも書けます。<br>
投稿時には名前欄は無記入でも登録したキャップ名が表示されますが、名前を記入して投稿すると<b>名前＠キャップ名 ★</b>と表示されます。<br>
色はhtmlで使える形で書いてください。#C06000のように#も記入します。red,green,blueなどのブラウザが対応している一般的な色名も使えます。<br>
<hr>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<input type="hidden" name="mode" value="add">
新規キャップ登録<br>
名前：<input type="text" name="name">
パスワード：<input type="password" name="password">
色：<input type="text" name="color">
<input type="submit" value="追加">
</form>
<hr>
キャップ変更削除<font size="-1">（登録時のパスワードが必要です）</font><br>
<table border=1 cellspacing=2 cellpadding=3>
<tr><th>キャップID</th><th>名前</th><th>パスワード（必須）</th><th>色</th><th>パスワード変更</th><th>　</th><th>　</th></tr>
<?php
if ($list) {
	foreach ($list as $tmp) {
		$tmp = chop($tmp);
		list($id2,$name2,$pass2,$color) = explode("<>", $tmp);
?><form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<tr><td><?=$id2?></td>
<td>
<input type="text" name="name" value="<?=$name2?>">
</td>
<td>
<input type="password" name="passold" value="">
<input type="hidden" name="id" value="<?=$id2?>">
</td>
<td>
<input type="text" name="color" value="<?=$color?>">
</td>
<td>
<input type="password" name="passnew" value="">
</td>
<td>
<input type="submit" name="mode" value="変更">
</td>
<td>
<input type="submit" name="mode" value="削除">
</td>
</tr>
</form>
<?php
	}
}
?></table>
<hr>
</body></html>
<?php
exit;
?>
