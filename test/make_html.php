<?php
define('VERSION', '2005/04/23');
# 過去ログメニューをかーくー
$fp = @fopen($SUBFILE, "w");
if (!$fp) exit;
fputs($fp, '<html><head><meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS"><meta http-equiv="pragma" content="no-cache"><meta http-equiv="Cache-Control" content="no-cache"></head><body><font size="2">');
$count = 0;
foreach ($PAGEFILE as $tmp) {
	$count++;
	$dat = str_replace(".dat", "", $tmp);
	fputs($fp, "<a href=\"../test/read.php/$_REQUEST[bbs]/$dat/l50\">$count: $SUBJECT[$tmp]</a><br>\n");
}
fputs($fp, '<div align="right"><a href="./kako/"><b>過去ログ倉庫はこちら</b></a></font>');
fputs($fp, "</body></html>\n");
fclose($fp);
#====================================================
#　本ＨＴＭＬ吐き処理
#====================================================
$fp = fopen($INDEXFILE, "w");
#--------ヘッダ＆上の広告
list($header, $footer) = explode('<CUT>', implode('', file("../test/index.txt")));
$header = str_replace("<BBS_TITLE>", $SETTING['BBS_TITLE'], $header);
$header = str_replace("<BBS_TEXT_COLOR>", $SETTING['BBS_TEXT_COLOR'], $header);
$header = str_replace("<BBS_MENU_COLOR>", $SETTING['BBS_MENU_COLOR'], $header);
$header = str_replace("<BBS_LINK_COLOR>", $SETTING['BBS_LINK_COLOR'], $header);
$header = str_replace("<BBS_ALINK_COLOR>", $SETTING['BBS_ALINK_COLOR'], $header);
$header = str_replace("<BBS_VLINK_COLOR>", $SETTING['BBS_VLINK_COLOR'], $header);
$header = str_replace("<BBS_BG_COLOR>", $SETTING['BBS_BG_COLOR'], $header);
$header = str_replace("<BBS_BG_PICTURE>", $SETTING['BBS_BG_PICTURE'], $header);
$header = str_replace("<BBS_TITLE_NAME>", $bbs_title, $header);
$head = implode('', file($PATH."head.txt"));
$header = str_replace("<GUIDE>", $head, $header);
$option = implode('', file("../test/option.txt"));
$header = str_replace("<OPTION>", $option, $header);
$putad = implode('', file("../test/putad.txt"));
$header = str_replace("<PUTAD>", $putad, $header);
fputs($fp, $header);
$headad = implode('', file("../test/headad.txt"));
if ($headad) {
	fputs($fp, '<br><table border="1" cellspacing="7" cellpadding="3" width="95%" bgcolor="'.$SETTING['BBS_MENU_COLOR']."\" align=\"center\">\n <tr>\n  <td>\n");
	fputs($fp, $headad);
	fputs($fp, "\n  </td>\n </tr>\n</table><br>\n");
}
#--------スレッド一覧
$menu = '<a name="menu"></a>
<table border="1" cellspacing="7" cellpadding="3" width="95%" bgcolor="'.$SETTING['BBS_MENU_COLOR'].'" align="center">
 <tr>
  <td><font size="2">
';
fputs($fp, $menu);
$i = 1;
foreach ($PAGEFILE as $tmp){
	$tmpkey = str_replace(".dat", "", $tmp);
	if ($i <= $SETTING['BBS_THREAD_NUMBER']) {
		fputs($fp, "   <a href=\"../test/read.php/$_REQUEST[bbs]/$tmpkey/l50\" target=\"body\">$i:</a> <a href=\"#$i\">$SUBJECT[$tmp]</a>　\n");
	}
	elseif ($i <= $SETTING['BBS_MAX_MENU_THREAD']) {
		fputs($fp, "   <a href=\"../test/read.php/$_REQUEST[bbs]/$tmpkey/l50\" target=\"body\">$i: $SUBJECT[$tmp]</a>　\n");
	}
	else break;
	$i++;
}
$count_end = --$i;
fputs($fp, "   <div align=\"right\"><a href=\"subback.html\"><b>スレッド一覧はこちら</b></a></div>\n  </td>\n </tr>\n</table><br>\n");
#--------一覧下の広告
#--------スレッド表示
$i = 1;
$form_txt = implode('', file("../test/form.txt"));
$fp2  = fopen($PATH."threadconf.cgi", "r");
$array = array();
while ($list = fgetcsv($fp2, 1024)) {
	$vip[$list[0]] = $list;
}
fclose($fp2);
foreach ($PAGEFILE as $tmp){
	$tmpkey = str_replace(".dat", "", $tmp);
	$enctype = 'application/x-www-form-urlencoded';
	$file_form = '';
	if (UPLOAD or $vip[$tmpkey][9]) {
		$enctype = 'multipart/form-data';
		$file_form = '<input type=file name=file size=50><br>';
	}
#	if(!is_file("$TEMPPATH$tmpkey.html")) MakeWorkFile($tmpkey);
	$log = file($TEMPPATH.$tmpkey.".html");
	if ($i == 1) {
		$j = ($count_end <= $SETTING['BBS_THREAD_NUMBER']) ? $count_end : $SETTING['BBS_THREAD_NUMBER'];
	}
	else $j = $i - 1;
	if (count($PAGEFILE) == $i) $k = 1;
	elseif ($i >=  $SETTING['BBS_THREAD_NUMBER']) $k = 1;
	else $k = $i + 1;
	$first = array_shift($log);
	$first = str_replace('$ANCOR', $i, $first);
	$first = str_replace('$FRONT', $j, $first);
	$first = str_replace('$NEXT', $k, $first);
	fputs($fp, $first);
	foreach ($log as $loglist) {
		fputs($fp, $loglist);
	}
	$form = str_replace('<BBS>', $_REQUEST['bbs'], $form_txt);
	$form = str_replace('<KEY>', $tmpkey, $form);
	$form = str_replace('<TIME>', $NOWTIME, $form);
	$form = str_replace('<PATH>', $PATH, $form);
	$form = str_replace('<ENCTYPE>', $enctype, $form);
	$form = str_replace('<FILE_FORM>', $file_form, $form);
	fputs($fp, $form);
	if (++$i >  $SETTING['BBS_THREAD_NUMBER']) break;
}
#--------新規作成画面＆一番下の広告＆バージョン表示
$footer = str_replace('<BBS_MAKETHREAD_COLOR>', $SETTING['BBS_MAKETHREAD_COLOR'], $footer);
$footer = str_replace('<BBS>', $_REQUEST['bbs'], $footer);
$footer = str_replace('<VERSION>', VERSION, $footer);
fputs($fp, $footer);
fclose($fp);
# i-mode用index
$th_titles = file($subjectfile);
$end = count($th_titles);
$data = "<html><head><title>$_REQUEST[bbs] スレッド一覧</title></head><body>$SETTING[BBS_TITLE]<hr>";
for ($i = 1; $i < 11; $i++) {
	if(!isset($th_titles[$i-1]) or !$th_titles[$i-1]) break;
	list($id, $sub) = explode("<>", $th_titles[$i-1]);
	$id = str_replace(".dat", "", $id);
	$data .= $i.": <a href=../../test/r.php/$_REQUEST[bbs]/$id/>".rtrim($sub).'</a><br>';
}
$data .= "<hr><a href=../../test/p.php/$_REQUEST[bbs]/$i>続き</a> <a href=../../test/b.php/$_REQUEST[bbs]/>新ｽﾚ</a></body></html>\n";
$fp = fopen ($IMODEFILE, "w");
fputs($fp, $data);
fclose($fp);
#--------書きこみ終了画面
if(strstr($_SERVER['HTTP_USER_AGENT'], 'DoCoMo') or
strstr($_SERVER['HTTP_USER_AGENT'], 'J-PHONE') or
strstr($_SERVER['HTTP_USER_AGENT'], 'UP.Browser')) {
	header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['SCRIPT_NAME']))."/$_REQUEST[bbs]/i/");
	exit;
}
setcookie ("PON", $HOST, $NOWTIME+3600*24*90, "/");
header("Content-Type: text/html; charset=Shift_JIS");
?>
<html>
<head>
<title>書きこみました。</title>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<?=$set_cookie?>
<meta http-equiv="refresh" content="1;URL=<?=$INDEXFILE?>?">
</head>
<body>書きこみが終わりました。<br>
<br>
画面を切り替えるまでしばらくお待ち下さい。<br>
<br><br><br>
</body>
</html>
<?
exit;
?>