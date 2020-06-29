<?php
function DispError($msg) {
	echo "<HTML><BODY>$msg</BODY></HTML>";
	exit;
}
#====================================================
#　初期情報の取得（設定ファイル）
#====================================================
#設定ファイルを読む
$set_pass = "../SETTING.TXT";
if (is_file($set_pass)) {
	$set_str = file($set_pass);
	foreach ($set_str as $tmp){
		$tmp = chop($tmp);
		list ($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
}
else DispError("ＥＲＲＯＲ：ユーザー設定が消失しています！");

$kakolog = file("kako.txt");
@sort($kakolog);
@reset($kakolog);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title><php echo $SETTING['BBS_TITLE']; ?>　過去ログ倉庫</title>
</head>
<body>
<p><a href="..">■掲示板に戻る■</a></p>
<p>※新しいデータ形式(teriのタイプ)のスレッド</p>
<ol>
<?php
if ($kakolog) {
	foreach ($kakolog as $tmp) {
		echo '<li>' . $tmp . '</li>' . "\n";
	}
}
?>
</ol>
</body>
</html>