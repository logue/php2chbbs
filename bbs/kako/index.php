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
<html>
<head>
<title><?=$SETTING['BBS_TITLE']?>　過去ログ倉庫</title>
</head>
<body>
<a href="..">■掲示板に戻る■</a><p>
※新しいデータ形式(teriのタイプ)のスレッド
<p>
<?php
if ($kakolog) {
	foreach ($kakolog as $tmp) {
		echo $tmp;
	}
}
?>
</body>
</html>