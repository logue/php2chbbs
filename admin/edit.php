<?php
require("passcheck.php");
#=====================================
#　テキスト編集
#=====================================
if (!is_dir("../$_REQUEST[bbs]")) disperror("ＥＲＲＯＲ！", "そんな板orスレッドないです。");
#====================================================
#　初期情報の取得（設定ファイル）
#====================================================
#設定ファイルを読む
$set_pass = "../$_REQUEST[bbs]/SETTING.TXT";
if (is_file($set_pass)) {
	$set_str = file($set_pass);
	foreach ($set_str as $tmp){
		$tmp = chop($tmp);
		list ($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
}
else disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：ユーザー設定が消失しています！");
# index.txtの読み込み
list($header,) = explode('<CUT>', implode('', file("../test/index.txt")));
$header = str_replace("<BBS_TITLE>", $SETTING['BBS_TITLE'], $header);
$header = str_replace("<BBS_TEXT_COLOR>", $SETTING['BBS_TEXT_COLOR'], $header);
$header = str_replace("<BBS_MENU_COLOR>", $SETTING['BBS_MENU_COLOR'], $header);
$header = str_replace("<BBS_LINK_COLOR>", $SETTING['BBS_LINK_COLOR'], $header);
$header = str_replace("<BBS_ALINK_COLOR>", $SETTING['BBS_ALINK_COLOR'], $header);
$header = str_replace("<BBS_VLINK_COLOR>", $SETTING['BBS_VLINK_COLOR'], $header);
$header = str_replace("<BBS_BG_COLOR>", $SETTING['BBS_BG_COLOR'], $header);
$header = str_replace("<BBS_BG_PICTURE>", $SETTING['BBS_BG_PICTURE'], $header);
$header = str_replace("<BBS_TITLE_NAME>", '<h1 class="title">'.$SETTING['BBS_TITLE'].'</h1>
<h3>テキスト編集</h3>', $header);
$headad = implode('', file("../test/headad.txt"));
if (isset($_REQUEST['mode']) and $_REQUEST['mode'] == 'view') {
	$head = implode('', file("../$_REQUEST[bbs]/head.txt"));
	$header = str_replace("<GUIDE>", $head, $header);
	$option = implode('', file("../test/option.txt"));
	$header = str_replace("<OPTION>", $option, $header);
	$putad = implode('', file("../test/putad.txt"));
	$header = str_replace("<PUTAD>", $putad, $header);
	echo $header;
	$headad = implode('', file("../test/headad.txt"));
	if ($headad) {
		echo '<table border="1" cellspacing="7" cellpadding="3" width="95%" bgcolor="'.$SETTING['BBS_MENU_COLOR']."\" align=\"center\">\n <tr>\n  <td>\n";
		echo $headad;
		echo "\n  </td>\n </tr>\n</table><br>\n";
	}
	exit;
}
if (isset($_REQUEST['file'])) {
	if ($_REQUEST['file'] == 'option' or $_REQUEST['file'] == 'putad' or $_REQUEST['file'] == 'headad')
			$file_name = "../test/$_REQUEST[file].txt";
	elseif ($_REQUEST['file'] == 'head') $file_name = "../$_REQUEST[bbs]/head.txt";
	else disperror("ＥＲＲＯＲ！", "ファイル名が不正です。");
	if (!is_file($file_name)) disperror("ＥＲＲＯＲ！", "ファイル（".$file_name."）がありません。ファイルをアップロードしてください。");
	if (!is_writable($file_name)) disperror("ＥＲＲＯＲ！", "ファイル（".$file_name."）に書き込み属性がありません。パーミッションを606か666にしてください。");
	$comment = '';
	if (isset($_POST['text']) and $_POST['mode'] == 'write') {
		if (get_magic_quotes_gpc()) $_POST['text'] = stripslashes($_POST['text']);
		$fp = fopen($file_name, "w");
		fputs($fp, $_POST['text']);
		fclose($fp);
		$comment = "ファイルを書き換えました。メニューの<b>index.htmlを作り直す</b>をクリックしてください";
	}
	$text = implode('', file($file_name));
	?>
<html>
<head>
<title>テキスト編集</title>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" href="main.css" type="text/css">
</head>
<body>
<h1 class="title"><?=$SETTING['BBS_TITLE']?></h1>
<h3>テキスト編集</h3>
<hr>
<font color="red"><?=$comment?></font><br>
<b><?=$file_name?></b>を編集しています　　　　　
<a class="item" href="edit.php?bbs=<?=$_REQUEST['bbs']?>">編集メニュー</a>　　　　　<a class="item" href="edit.php?mode=view&amp;bbs=<?=$_REQUEST['bbs']?>" target="_blank">確認</a><br>
<br>
<form action="./edit.php" method="post">
<input type="hidden" name="mode" value="write">
<input type="hidden" name="bbs" value="<?=$_REQUEST['bbs']?>">
<input type="hidden" name="file" value="<?=$_REQUEST['file']?>">
<input type="submit" name="submit" value="変更">
<input type="reset" name="reset" value="リセット"><br>
<textarea rows="30" cols="80" name="text"><?=$text?></textarea>
</form>
</body>
</html>
<?php
	exit;
}
$form = '<form action="edit.php" method="post"><input type="hidden" name="bbs" value="'.$_REQUEST['bbs'].'"><input type="hidden" name="file" value="head"><input type="submit" value="head.txtを編集する"></form>';
$header = str_replace("<GUIDE>", $form, $header);
$form = '<form action="edit.php" method="post"><input type="hidden" name="bbs" value="'.$_REQUEST['bbs'].'"><input type="hidden" name="file" value="option"><input type="submit" value="option.txtを編集する"></form>';
$header = str_replace("<OPTION>", $form, $header);
$form = '<form action="edit.php" method="post"><input type="hidden" name="bbs" value="'.$_REQUEST['bbs'].'"><input type="hidden" name="file" value="putad">　　　　<input type="submit" value="putad.txtを編集する"></form>';
$header = str_replace("<PUTAD>", $form, $header);
$css = '<link rel="stylesheet" href="main.css" type="text/css">
</head>
';
$header = str_replace("</head>", $css, $header);
echo $header;
?>
<table border="1" cellspacing="7" cellpadding="3" width="95%" bgcolor="<?=$SETTING['BBS_MENU_COLOR']?>" align="center">
 <tr>
  <td>
<form action="edit.php" method="post"><input type="hidden" name="bbs" value="<?=$_REQUEST['bbs']?>"><input type="hidden" name="file" value="headad"><input type="submit" value="headad.txtを編集する"></form>
  </td>
 </tr>
</table><br>
掲示板を複数設置している場合option.txt,putad.txt,headad.txtは全ての掲示板で同じものが使用されます。<br>
head.txtのみが掲示板ごとに変更できます。<br>
<a class="item" href="edit.php?mode=view&amp;bbs=<?=$_REQUEST['bbs']?>" target="_blank">確認</a><br>
</body></html>
