<?php
require("passcheck.php");
if (!is_dir("../$_GET[bbs]")) disperror("ＥＲＲＯＲ！", "そんな板orスレッドないです。");
#====================================================
#　初期情報の取得（設定ファイル）
#====================================================
#設定ファイルを読む
$set_pass = "../$_GET[bbs]/SETTING.TXT";
if (is_file($set_pass)) {
	$set_str = file($set_pass);
	foreach ($set_str as $tmp){
		$tmp = trim($tmp);
		list($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
}
else disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：ユーザー設定が消失しています！");
#==================================================
#　アクセス制限
#==================================================
if(isset($_GET['mode']) and $_GET['mode'] == "deny") {
	$_GET['list'] = str_replace("　", " ", $_GET['list']);
	$_GET['list'] = preg_replace("/\s+/", " ", $_GET['list']);
	$_GET['list'] = trim($_GET['list']);
	$deny_array = explode(' ', $_GET['list']);
	$fp = fopen("../$_GET[bbs]/uerror.cgi", "a");
	foreach ($deny_array as $deny) {
		if ($deny) fputs($fp, $deny."\n");
	}
	fclose($fp);
}
#アクセス制限リスト読み込み
if (is_file("../$_GET[bbs]/uerror.cgi")) $deny_array = file("../$_GET[bbs]/uerror.cgi");
else $deny_array = array();
$deny_array = array_map("trim", $deny_array);
#==================================================
#　アクセス解除
#==================================================
if(isset($_GET['mode']) and $_GET['mode'] == "allow") {
	if (!isset($_GET['allow'])) $_GET['allow'] = array();
	$deny_array = array_diff($deny_array, $_GET['allow']);
	$fp = fopen("../$_GET[bbs]/uerror.cgi", "w");
	foreach ($deny_array as $deny) fputs($fp, $deny."\n");
	fclose($fp);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="main.css" type="text/css" />
<title>アク禁処理</title>
</head>
<body>
<h1 class="title"><?php echo $SETTING['BBS_TITLE']?></h1>
<h3>アク禁処理</h3>
<hr />
<p><small>アクセス禁止するホスト名またはIPアドレス（全部または一部）を記入してください。<br />
複数設定する場合はスペースで区切ってください<br />
例：　<b>127.0.0.1　201.105　.go.jp　YahooBB123456789</b></small></p>
<form name="form1" action="<?php echo $_SERVER['PHP_SELF']?>" method="GET">
	<input type="hidden" name="bbs" value="<?php echo $_GET['bbs']?>" />
	<input type="hidden" name="mode" value="deny" />
	<input type="text" name="list" size="50" />
	<input type="submit" value="アク禁" />
</form>
<hr />
アクセス禁止を解除したい場合はチェックボックスにチェックして<b>解除</b>ボタンを押してください。
<form name="form1" action="<?php echo $_SERVER['PHP_SELF']?>" method="GET">
	<input type="hidden" name="bbs" value="<?php echo $_GET['bbs']?>" />
	<input type="hidden" name="mode" value="allow" />
	<input type="submit" value="解除" />
<table border="1" cellspacing="0" cellpadding="2">
<?php
foreach ($deny_array as $deny) {
	echo '<tr><td><input type="checkbox" name="allow[]" value="'.$deny.'"></td><td>'.$deny."</td></tr>\n";
}
?>
</table>
</form>
</body>
</html>
