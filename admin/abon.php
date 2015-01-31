<?php
require("passcheck.php");
$method = "POST";
#一度に表示する
$thread = 50;
$res = 30;
$comment = '';
$stopper_array = array('(￣ー￣)ﾆﾔﾘｯ','(｀･ω･´) ｼｬｷｰﾝ','（´・ω・｀）ﾓｷｭ');
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
require("../$_REQUEST[bbs]/config.php");
if (!empty($_POST['key'])) {
	$fp  = fopen("../$_POST[bbs]/threadconf.cgi", "r");
	while ($vip = fgetcsv($fp, 1024)) {
		if ($vip[0] == $_POST['key']) break;
		else $vip[0] = 0;
	}
	fclose($fp);
}
#==================================================
#　ファイル操作（サブジェクトファイル読み込み）
#==================================================
#サブジェクトファイル更新
#サブジェクトファイルを読み込む
$subfile = "../$_REQUEST[bbs]/subject.txt";
$SUBJECTLIST = @file($subfile);
#サブジェクト内容をハッシュに格納
$PAGEFILE = array();
foreach($SUBJECTLIST as $tmp){
	$tmp = rtrim($tmp);
	list($file, $value) = explode("<>", $tmp);
	$filename = "../$_REQUEST[bbs]/dat/$file";
	if(is_file($filename)){
		#datが存在する場合のみ最後に追加
		$file = str_replace('.dat', '', $file);;
		array_push($PAGEFILE,$file);
		$SUBJECT[$file] = $value;
	}
}
#==================================================
#　レスあぼーん
#==================================================
if (isset($_POST['mode']) and $_POST['mode'] == "res_del" and isset($_POST['del'])) {
	if (!is_file("../$_POST[bbs]/dat/$_POST[key].dat")) disperror("ＥＲＲＯＲ！", "そんな板orスレッドないです。");
	$datafile = "../$_POST[bbs]/dat/$_POST[key].dat";
	if (!is_writable($datafile)) disperror("ＥＲＲＯＲ！", "このスレッドには書き込めません！");
	$temp = file($datafile);
	$num = count($temp);
	function res_num($num, $del) {
		if (strstr($num, '-')) {
			list($num1, $num2) = explode('-', $num);
			if ($num1 > $del) $num1--;
			if ($num2 > $del) $num2--;
			return $num1.'-'.$num2;
		}
		else return ($num <= $del) ? $num : --$num;
	}
	foreach ($_POST['del'] as $del) {
		$imgnum = sprintf("%04d", $del);
		$del--;
		if (!$_POST['mes']) $line = '';
		else $line = "$_POST[mes]<>$_POST[mes]<>$_POST[mes]<>$_POST[mes]<>$_POST[mes]\n";
		$temp[$del] = $line;
		if (is_file("../$_POST[bbs]/img/$_POST[key]$imgnum.jpg")) {
			unlink("../$_POST[bbs]/img/$_POST[key]$imgnum.jpg");
			@unlink("../$_POST[bbs]/img2/$_POST[key]$imgnum.jpg");
		}
		elseif (is_file("../$_POST[bbs]/img/$_POST[key]$imgnum.gif")) {
			unlink("../$_POST[bbs]/img/$_POST[key]$imgnum.gif");
			@unlink("../$_POST[bbs]/img2/$_POST[key]$imgnum.jpg");
		}
		elseif (is_file("../$_POST[bbs]/img/$_POST[key]$imgnum.png")) {
			unlink("../$_POST[bbs]/img/$_POST[key]$imgnum.png");
			@unlink("../$_POST[bbs]/img2/$_POST[key]$imgnum.jpg");
		}
		if (!$_POST['mes']) {
			foreach ($temp as $key=>$line) {
				$line = preg_replace("!<a href=\"\.\./test/read\.php/$_POST[bbs]/$_POST[key]/([\d|\-]+)\" target=\"_blank\">&gt;&gt;([\d|\-]+)</a>!e", "'<a href=\"../test/read.php/$_POST[bbs]/$_POST[key]/'.res_num('$1',$del).'\" target=\"_blank\">&gt;&gt;'.res_num('$2',$del).'</a>'", $line);
				$temp[$key] = $line;
			}
		}
	}
	$fp = fopen($datafile, "w");
	foreach ($temp as $tmp) fputs($fp, $tmp);
	fclose($fp);
	require '../test/make_work.php';
	$sub_txt = MakeWorkFile($_POST['bbs'], $_POST['key']);
	if (!$_POST['mes']) {
		$fp = fopen("../$_POST[bbs]/subject.txt", "w");
		foreach ($PAGEFILE as $line) {
			if ($line == $_POST['key']) {
				$SUBJECT[$line] = $sub_txt;
			}
			fputs($fp, "$line.dat<>$SUBJECT[$line]\n");
		}
		fclose($fp);
	}
	$comment = "レスあぼーんしました。メニューの<b>index.htmlを作り直す</b>をクリックしてください。<br>";
}
#==================================================
#　レス表示
#==================================================
if(isset($_REQUEST['mode']) and ($_REQUEST['mode'] == "view" or $_REQUEST['mode'] == "res_del")) {
	if (!is_file("../$_REQUEST[bbs]/dat/$_REQUEST[key].dat")) disperror("ＥＲＲＯＲ！", "そんな板orスレッドないです。");
	$datafile = "../$_REQUEST[bbs]/dat/$_REQUEST[key].dat";
	$temp = file($datafile);
	$num = count($temp);
	if (!isset($_GET['page']) or !$_GET['page']) $_GET['page'] = 1;
	$st = ($_GET['page'] - 1) * $res;
	$total_page = (int)(($num+$res-1)/$res);
	list($name,$mail,$date,$message,$subject) = explode("<>",$temp[0]);
	$subject = trim($subject);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="main.css" type="text/css" />
<title>あぼーん</title>
</head>
<body>
<h1 class="title"><?php echo $SETTING['BBS_TITLE']?></h1>
<h3>あぼーん</h3>
<hr />
スレッド ： <a class="item" href="../test/read.php/<?php echo $_REQUEST['bbs']?>/<?php echo $_REQUEST['key']?>/">#<?php echo $_REQUEST['bbs'].$_REQUEST['key']?></a><br>
タイトル ： <font color="red"><b><?php echo $subject?></b></font><br />
<br />
<font color="red"><?php echo $comment?></font>
<font size="-1">削除メッセージは変更できます。空欄にすると透明削除になりレスそのものが削除されます。<br />
その場合、同一スレッド内のレスアンカーリンクは修正されますが他からのリンクがあった場合レス番号がずれることがあります。<br />
また画像投稿時に不具合が起こる可能性がありますので、画像投稿許可の場合は出来るだけ通常削除をおすすめします。<br />
削除メッセージの規定値は<b>管理メニュー</b>の<b>設定変更</b>で変更できます。</font><br />
<br />
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="<?php echo $method?>" />
<input type=hidden name="bbs" value="<?php echo $_REQUEST['bbs']?>" />
<input type=hidden name="key" value="<?php echo $_REQUEST['key']?>" />
<input type=hidden name="mode" value="res_del" />
削除メッセージ　：　<input type=text name="mes" value="<?php echo $SETTING['BBS_DELETE_NAME']?>" />
<input type=submit value="実行"></td></tr>
</table>
<?
	echo "page：$_GET[page]<br>\n";
	for ($i = 1; $i <= $total_page; $i++) {
		if ($i == $_GET['page']) echo " $i \n";
		else echo " <a class=\"item\" href=\"$_SERVER[PHP_SELF]?bbs=$_REQUEST[bbs]&amp;key=$_REQUEST[key]&amp;mode=view&amp;page=$i\">$i</a> \n";
	}
	?>
<table border="1" cellspacing="0" cellpadding="2">
<tr><td>　</td><td>番号</td><td>名前</td><td>投稿日</td><td>内容</td><td>画像</td></tr>
<?php
	$n = $st;
	for ($i = $st; $i < $st + $res; $i++) {
		if (!isset($temp[$i])) break;
		$tmp = $temp[$i];
		list($name,$mail,$date,$message,$subject) = explode("<>", $tmp);
		$date = preg_replace("/ ID:(.+)$/", "", $date);
		preg_match("|<a href=\"(.+)\"><img src=\"(.+)\" width=\"(\d+)\" height=\"(\d+)\"|", $message, $match);
		if ($match) {
			$image = '<a href="'.$match[1].'"><img src="'.$match[2].'" width="'.$match[3].'" height="'.$match[4].'">';
		}
		else $image = '画像なし';
		$message = strip_tags($message);
		$message = substr($message,0,30);
		$n++;
		echo "<tr><td><input type=\"checkbox\" name=\"del[]\" value=\"$n\"></td><td align=\"center\">$n</td><td><font color=\"$SETTING[BBS_NAME_COLOR]\"><b>$name</b></font>[$mail]</td><td>$date</td><td>$message </td><td>$image</td></tr>\n";
	}
	echo "</table></form>\n";
	for ($i = 1; $i <= $total_page; $i++) {
		if ($i == $_GET['page']) echo " $i \n";
		else echo " <a class=\"item\" href=\"$_SERVER[PHP_SELF]?bbs=$_REQUEST[bbs]&amp;key=$_REQUEST[key]&amp;mode=view&amp;page=$i\">$i</a> \n";
	}
	echo "</body></html>";
	exit;
}
#==================================================
#　スレッドストッパー
#==================================================
elseif (isset($_POST['mode']) and $_POST['mode'] == "stop") {
	if (!is_file("../$_POST[bbs]/dat/$_POST[key].dat")) disperror("ＥＲＲＯＲ！", "そんな板orスレッドないです。");
	$dattemp = "../$_POST[bbs]/dat/$_POST[key].dat";
	$workfile = "../$_POST[bbs]/html/$_POST[key].html";
	if (!is_writable($dattemp)) disperror("ＥＲＲＯＲ！", "すでに書きこみ出来ません。");
	else {
		$fp = fopen($dattemp, "a");
		$stopper = $stopper_array[$_POST['stopper']];
		fputs($fp, "停止しました。。。<>停止<>停止<>真・スレッドストッパー。。。$stopper<>\n");
		fclose($fp);
		chmod($dattemp, 0444);
		require '../test/make_work.php';
		MakeWorkFile($_POST['bbs'], $_POST['key']);
		$comment = "スレッドストップしました。メニューの<b>index.htmlを作り直す</b>をクリックしてください。";
	}
}
#==================================================
#　
#==================================================
if (!isset($_GET['page']) or !$_GET['page']) $_GET['page'] = 1;
$st = ($_GET['page'] - 1) * $thread;
$total = count($PAGEFILE)+$thread-1;
$total_page = (int)($total/$thread);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="main.css" type="text/css">
<title>スレッドストップ</title>
</head>
<body>
<h1 class="title"><?php echo $SETTING['BBS_TITLE']?></h1>
<h3>スレッドストップ</h3>
<hr>
<font color="red"><?php echo $comment?></font><br>
スレッドストッパーの<b>ストップ</b>ボタンを押すとそのスレッドは書き込めなくなります。<br />
レスを削除したいときは、<b>レス表示</b>ボタンを押して削除処理画面に移動してください。<br />
<br />
page：<?php echo $_GET['page']; ?><br />
<?php
for ($i = 1; $i <= $total_page; $i++) {
	if ($i == $_GET['page']) echo " $i \n";
	else echo " <a class=\"item\" href=\"$_SERVER[PHP_SELF]?bbs=$_REQUEST[bbs]&amp;page=$i\">$i</a> \n";
}
?>
<table border="1" cellspacing="0" cellpadding="2">
<tr><th>スレッドキー</th><th>タイトル（レス数）</th><th>スレッドストッパー</th><th>　</th></tr>
<?php
for ($i = $st; $i < $st+$thread; $i++) {
	if (!isset($PAGEFILE[$i])) break;
	$tmp = $PAGEFILE[$i];
	?><tr><td> <a class="item" href="../test/read.php/<?php echo $_REQUEST['bbs']."/".$tmp?>/l50">#<?php echo $_REQUEST['bbs'].$tmp?></a> </td><td><?php echo $SUBJECT[$tmp]?></td>
<td>
<?php
clearstatcache();
if (is_writable("../$_REQUEST[bbs]/dat/$tmp.dat")) {
?> <form action="<?php echo $_SERVER['PHP_SELF']?>" method="<?php echo $method?>">
<select name="stopper">
<?php foreach($stopper_array as $key=>$stopper){ echo '<option value="' . $key . '">'.$stopper."\n"; } ?>
</select>で
<input type="submit" value="ストップ" />
<input type="hidden" name="bbs" value="<?php echo $_REQUEST['bbs']?>" />
<input type="hidden" name="key" value="<?php echo $tmp?>" />
<input type="hidden" name="mode" value="stop" />
</form>
<?php
}
else echo "<s>ストップ</s>";
?></td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="<?php echo $method?>">
<input type="submit" value="レス表示" />
<input type="hidden" name="bbs" value="<?php echo $_REQUEST['bbs']?>" />
<input type="hidden" name="key" value="<?php echo $tmp?>" />
<input type="hidden" name="mode" value="view" />
</form>
</td>
</tr>
<?php
}
echo "</table>\n";
for ($i = 1; $i <= $total_page; $i++) {
	if ($i == $_GET['page']) echo " $i \n";
	else echo " <a class=\"item\" href=\"$_SERVER[PHP_SELF]?bbs=$_REQUEST[bbs]&amp;page=$i\">$i</a> \n";
}
echo "</body></html>";
exit;
