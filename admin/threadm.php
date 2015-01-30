<?php
$thread=20;
require("passcheck.php");
$comment = '';
#====================================================
#　初期情報の取得（設定ファイル）
#====================================================
#設定ファイルを読む
$set_pass = "../$_REQUEST[bbs]/SETTING.TXT";
if (is_file($set_pass)) {
	$set_str = file ($set_pass);
	foreach ($set_str as $tmp){
		$tmp = chop($tmp);
		list ($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
}
else disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：ユーザー設定が消失しています！");
#==================================================
#　ファイル操作（サブジェクトファイル読み込み）
#==================================================
#サブジェクトファイルを読み込む
$subfile = "../$_REQUEST[bbs]/subject.txt";
#サブジェクトファイルを読み込む
$SUBJECTLIST = @file($subfile);
#サブジェクト内容をハッシュに格納
$PAGEFILE = array();
if ($SUBJECTLIST) {
	foreach ($SUBJECTLIST as $tmp) {
		$tmp = rtrim($tmp);
		list($file, $value) = explode("<>", $tmp);
		$filename = "../$_REQUEST[bbs]/dat/$file";
		if (is_file($filename)) {
			#datが存在する場合のみ最後に追加
			preg_match("/(\d+)/", $file, $match);
			$file = $match[1];
			array_push($PAGEFILE,$file);
			$SUBJECT[$file] = $value;
		}
	}
}
#==================================================
#　スレッド削除
#==================================================
if (isset($_POST['mode']) and $_POST['mode'] == "del") {
	if (!is_file("../$_POST[bbs]/dat/$_POST[key].dat")) disperror("ＥＲＲＯＲ！", "そんな板orスレッドないです。");
	if (!isset($_POST['chk']) or $_POST['chk'] != "ok") {
		preg_match("/(.*)(\(\d+\))$/", $SUBJECT[$_POST['key']], $match);
		?>
<html>
<head>
<title>スレッド削除</title>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" href="main.css" type="text/css">
</head>
<body>
<h1 class="title"><?=$SETTING['BBS_TITLE']?></h1>
<h3>スレッド削除</h3>
<hr>
スレッド：<a class="item" href="../test/read.php/<?=$_POST['bbs']."/".$_POST['key']?>/l50">#<?=$_POST['bbs'].$_POST['key']?></a><br>
タイトル：<font color="<?=$SETTING['BBS_SUBJECT_COLOR']?>"><b><?=$match[1]?></b></font><br>
レス数：<?=$match[2]?><br><br><br>
このスレッドを削除します。<br><br>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
 <input type=hidden name="bbs" value="<?=$_POST['bbs']?>">
 <input type=hidden name="key" value="<?=$_POST['key']?>">
 <input type=hidden name="mode" value="del">
 <input type=hidden name="chk" value="ok">
 <input type=submit value="消しちゃえ！">
</form>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
 <input type=hidden name="bbs" value="<?=$_POST['bbs']?>">
 <input type=submit value="ヤッパやめる">
</form>
</body></html>
<?php
		exit;
	}
	else {
		$delfile = "../$_POST[bbs]/dat/$_POST[key].dat";
		@chmod($delfile, 0644);
		unlink($delfile);
		@unlink("../$_POST[bbs]/html/$_POST[key].html");
		#サブジェクト内容を更新
		$PAGEFILE = array();
		$new = '';
		if ($SUBJECTLIST) {
			foreach($SUBJECTLIST as $tmp){
				$tmp = rtrim($tmp);
				list($file, $value) = explode("<>", $tmp);
				if ("$_POST[key].dat" != $file) $new .= $tmp."\n";
				$filename = "../$_POST[bbs]/dat/$file";
				if (is_file($filename)) {
					#datが存在する場合のみ最後に追加
					if (preg_match("/(\d+)\.dat$/", $file, $match)) {
						array_push($PAGEFILE,$match[1]);
						$SUBJECT[$match[1]] = $value;
					}
				}
			}
		}
		$fp = fopen($subfile, "w");
		fputs($fp, $new);
		fclose($fp);
		$dir = opendir("../$_POST[bbs]/img");
		while (false !== ($file = readdir($dir))) {
			if ($file != "." and $file != "..") {
				// 拡張子が固定でないのでキーで選択
				if (strstr($file, $_POST['key'])) {
					unlink ("../$_POST[bbs]/img/$file");
				}
			}
		}
		closedir($dir);
		$dir = opendir("../$_POST[bbs]/img2");
		while (false !== ($file = readdir($dir))) {
			if ($file != "." and $file != "..") {
				if (strstr($file, $_POST['key'])) {
					unlink ("../$_POST[bbs]/img2/$file");
				}
			}
		}
		closedir($dir);
		#0thello ファイル削除
		@unlink("../$_POST[bbs]/0thello/$_POST[key].dat");
		#threadconf ログ削除
		$threadconf = file("../$_POST[bbs]/threadconf.cgi");
		$fp = fopen("../$_POST[bbs]/threadconf.cgi", "w");
		foreach($threadconf as $key=>$val) {
			if (!strstr($val, $_POST['key'])) fwrite($fp, $val);
		}
		fclose($fp);
		$comment = '<font color="red">スレッドを削除しました。メニューの<b>index.htmlを作り直す</b>をクリックしてください。</font><br>';
	}
}
#==================================================
#　スレッド移動
#==================================================
if (isset($_POST['mode']) and $_POST['mode'] == "mov") {
	if (!is_file("../$_POST[bbs]/dat/$_POST[key].dat")) disperror("ＥＲＲＯＲ！", "そんな板orスレッドないです。");
	if (!isset($_POST['chk']) or $_POST['chk'] != "html") {
		preg_match("/(.*)(\(\d+\))$/", $SUBJECT[$_POST['key']], $match);
		?>
<html>
<head>
<title>スレッド移動</title>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" href="main.css" type="text/css">
</head>
<body>
<h1 class="title"><?=$SETTING['BBS_TITLE']?></h1>
<h3>スレッド移動</h3>
<hr>
スレッド：<a class="item" href="../test/read.php/<?=$_POST['bbs']."/".$_POST['key']?>/l50">#<?=$_POST['bbs'].$_POST['key']?></a><br>
タイトル：<font color="<?=$SETTING['BBS_SUBJECT_COLOR']?>"><b><?=$match[1]?></b></font><br>
レス数：<?=$match[2]?><br><br><br>
このスレッドを移動します。<br><br>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
 <input type=hidden name="bbs" value="<?=$_POST['bbs']?>">
 <input type=hidden name="key" value="<?=$_POST['key']?>">
 <input type=hidden name="mode" value="mov">
 <input type=hidden name="chk" value="html">
 <input type=submit value="HTML化して過去ログ倉庫へ">
</form>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
 <input type=hidden name="bbs" value="<?=$_POST['bbs']?>">
 <input type=submit value="ヤッパやめる">
</form>
</body></html>
<?php
		exit;
	#=========================
	#　過去ログ倉庫
	#=========================
	}
	else {
		$log = file("../$_POST[bbs]/dat/$_POST[key].dat");
		list(,,,,$subject) = explode("<>", $log[0]);
		$html = <<<EOF
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<title>$subject</title>
<style type=text/css><!--
img {border:0;}
--></style>
</head>
<body text="$SETTING[BBS_TEXT_COLOR]" bgcolor="$SETTING[BBS_THREAD_COLOR]" link="$SETTING[BBS_LINK_COLOR]" alink="$SETTING[BBS_ALINK_COLOR]" vlink="$SETTING[BBS_VLINK_COLOR]">
<a href="../">■掲示板に戻る■</a>
<dl>
<font size=+1 color="$SETTING[BBS_SUBJECT_COLOR]">$subject</font></b>
EOF;
		$fp = @fopen("../$_POST[bbs]/kako/$_POST[key].html", "w");
		fputs($fp, $html);
		$s = 1;
		foreach($log as $tmp) {
			$tmp = chop($tmp);
			list($name,$mail,$time,$message,$subject) = explode("<>", $tmp);
			if (!$message) {
				$name='';
				$mail='';
				$time='[ここ壊れてます]';
				$message='[ここ壊れてます]';
			}
			$message = preg_replace("/(https?|ftp):\/\/([\x21-\x7E]+)/",'<a href="$1://$2" target="_blank">$1://$2</a>',$message);
			$message = str_replace("../test/read.php/$_POST[bbs]/$_POST[key]/",'#', $message);
			$message = preg_replace("|<([^>]+\"\.\./)$_POST[bbs]/|", "<$1", $message);
			$mailto = $mail ? "<a href=\"mailto:$mail\"><b> $name </b></a>" : "<font color=\"$SETTING[BBS_NAME_COLOR]\"><b>$name</b></font>";
			fputs($fp, "<dt><a name=\"$s\">$s</a> 名前： $mailto 投稿日： $time<br><dd> $message <br><br><br>\n");
			$s++;
		}
		fputs($fp, "</dl>\n<p>\n<hr>\n</body>\n</html>");
		fclose($fp);
		@chmod("../$_POST[bbs]/dat/$_POST[key].dat", 0644);
		unlink("../$_POST[bbs]/dat/$_POST[key].dat");
		@unlink("../$_POST[bbs]/html/$_POST[key].html");
		$fp = fopen("../$_POST[bbs]/kako/kako.txt", "a");
		fputs($fp, "$_POST[key] ※ <a href=\"$_POST[key].html\">".$SUBJECT[$_POST['key']]."</a><br>\n");
		fclose($fp);
		#サブジェクト内容を更新
		$PAGEFILE = array();
		$new = '';
		if ($SUBJECTLIST) {
			foreach($SUBJECTLIST as $tmp) {
				$tmp = rtrim($tmp);
				list($file, $value) = explode("<>", $tmp);
				if ("$_POST[key].dat" != $file) $new .= $tmp."\n";
				$filename = "../$_POST[bbs]/dat/$file";
				if (is_file($filename)) {
					#datが存在する場合のみ最後に追加
					if (preg_match("/(\d+)\.dat$/", $file, $match)) {
						array_push($PAGEFILE,$match[1]);
						$SUBJECT[$match[1]] = $value;
					}
				}
			}
		}
		#サブジェクトファイル更新
		$fp = fopen($subfile, "w");
		fputs($fp, $new);
		fclose($fp);
		#0thello ファイル削除
		@unlink("../$_POST[bbs]/0thello/$_POST[key].dat");
		$threadconf = file("../$_POST[bbs]/threadconf.cgi");
		foreach($threadconf as $key=>$val) {
			if (strstr($val, $_POST['key'])) unset($threadconf[$key]);
		}
		$fp = fopen("../$_POST[bbs]/threadconf.cgi", "w");
		fwrite($fp, implode("\n", $threadconf));
		fclose($fp);
		$comment = '<font color="red">スレッドを移動しました。メニューの<b>index.htmlを作り直す</b>をクリックしてください。</font><br>';
	}
}
#==================================================
#　メニュー
#==================================================
if (!isset($_GET['page']) or !$_GET['page']) $_GET['page'] = 1;
$st = ($_GET['page'] - 1) * $thread;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" href="main.css" type="text/css">
<title>スレッド削除/移動</title>
</head>
<body>
<h1 class="title"><?=$SETTING['BBS_TITLE']?></h1>
<h3>スレッド削除/移動</h3>
<hr>
<?=$comment?>
<b>スレッド削除</b>はそのスレッドを全て削除します。<br>
<b>スレッド移動</b>はスレッドをHTML化して過去ログディレクトリに移動します。<br>
<br>
page：<?=$_GET['page']?><br>
<?
$total = count($PAGEFILE) + $thread - 1;
$total_page = (int)($total/$thread);
for ($i = 1; $i <= $total_page; $i++) {
	if ($i == $_GET['page']) echo " $i \n";
	else echo " <a class=\"item\" href=\"$_SERVER[PHP_SELF]?bbs=$_REQUEST[bbs]&amp;page=$i\">$i</a> \n";
}
?>
<table border="1" cellspacing="0" cellpadding="2">
<tr><th>スレッドキー</th><th>タイトル</th><th>　</th><th>　</th></tr>
<?php
for ($i = $st; $i < $st+$thread; $i++) {
	if (!isset($PAGEFILE[$i])) break;
	$tmp = $PAGEFILE[$i];
	?>
<tr><td> <a class="item" href="../test/read.php/<?=$_REQUEST['bbs']."/".$tmp?>/l50">#<?=$_REQUEST['bbs'].$tmp?></a> </td><td><?=$SUBJECT[$tmp]?></td>
<td>
 <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
 <input type=hidden name="bbs" value="<?=$_REQUEST['bbs']?>">
 <input type=hidden name="key" value="<?=$tmp?>">
 <input type=hidden name="mode" value="del">
 <input type=submit value="スレッド削除">
 </form>
</td>
<td>
 <form action="threadm.php" method="POST">
 <input type=hidden name="bbs" value="<?=$_REQUEST['bbs']?>">
 <input type=hidden name="key" value="<?=$tmp?>">
 <input type=hidden name="mode" value="mov">
 <input type=submit value="スレッド移動">
 </form>
</td>
<?php
}
echo "</table>\n";
for ($i = 1; $i <= $total_page; $i++) {
	if ($i == $_GET['page']) echo " $i \n";
	else echo " <a class=\"item\" href=\"$_SERVER[PHP_SELF]?bbs=$_REQUEST[bbs]&amp;page=$i\">$i</a> \n";
}
echo "</body></html>";
exit;
?>