<?php
define('VERSION', 'read.php ver2.5 2015/01/31');
require 'config_r.php';
$st = $to = 0;
$nofirst = '';
$ls = 0;
extract ($_GET);
#==================================================
#　時刻を設定
#==================================================
$NOW = time();
$today = getdate(); 
$JIKAN = $today['hours']; 
#==================================================
#　リクエスト解析
#==================================================
if ($_SERVER['REQUEST_METHOD'] != 'GET') DispError('そんな板orスレッドないです。');
if (!empty($_SERVER['PATH_INFO'])) {
	$pairs = explode('/',$_SERVER['PATH_INFO']);
	$bbs = $pairs[1];
	$key = $pairs[2];
	if (!empty($pairs[3])) {
		if (strstr($pairs[3], 'n')) {
			$nofirst = 'true';
			$pairs[3] = str_replace("n","",$pairs[3]);
		}
		if (substr($pairs[3], 0, 1) == 'l') {
			$ls = substr($pairs[3],1);
		}
		elseif (strstr($pairs[3], '-')) {
			list($st, $to) = explode('-',$pairs[3]);
			if (!$st) $st = 1;
		}
		else {
			$st = $pairs[3];
			$to = $pairs[3];
			$nofirst = 'true';
		}
	}
}
#==================================================
#　初期情報の取得（設定ファイル）
#==================================================
preg_match("/(.*)(\/test\/read\.php)(.*)/", $_SERVER['SCRIPT_NAME'], $match);
$URL = '//'.$_SERVER['HTTP_HOST'].$match[1];
$SCRIPT = $match[2];
$BASEURL = "$URL/$bbs/";
#設定ファイルを読む
$set_file = "../$bbs/SETTING.TXT";
if (is_file($set_file)) {
	$set_str = file($set_file);
	foreach ($set_str as $tmp){
		$tmp = trim($tmp);
		list ($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
}
if (!is_file("../$bbs/dat/$key.dat")) DispError('そんな板orスレッドないです。');
require "../$bbs/config.php";
#==================================================
#　表示範囲の決定
#==================================================
$LOG = file("../$bbs/dat/$key.dat");
$LINENUM = count($LOG);
$s = 1;
$mae = 0;
$END = $LINENUM;
if ($to and is_numeric($to) and $to < $LINENUM) $END = $to;
if ($st and is_numeric($st)) $s = ($st < $LINENUM) ? $st : $LINENUM;
if ($ls) {
	preg_match("/^(\d+)/", $ls, $match);
	$s = $LINENUM - $match[1] + 2;
	if ($nofirst == 'true') $s--;
}
if ($s < 1) $s = 1;
if ($s > 1) $mae = $s-1;
$fsize = (int)(filesize("../$bbs/dat/$key.dat") / 1024);
list(,,$tmp) = explode("<>", $LOG[$LINENUM-1]);
$stop = 0;
if (preg_match("/Over \d+ Thread|ストッパ|停止/", $tmp)) $stop = 1;
list(,,,,$subject) = explode("<>",$LOG[0]);
$subject = trim($subject);
header("Content-Type: text/html; charset=UTF-8");
if (GZ_FLAG) ob_start("ob_gzhandler");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<base href="<?php echo $BASEURL; ?>" />
<title><?php echo $subject?></title>
<script type="text/javascript">/*<![CDATA[*/
function l(e){
 var N=g("NAME"),M=g("MAIL"),i;
 with(document)
 for(i=0;i<forms.length;i++)
  if(forms[i].FROM&&forms[i].mail)with(forms[i]){FROM.value=N;mail.value=M;}
}
onload=l;
function g(key,tmp1,tmp2,xx1,xx2,xx3,len){
 tmp1=" "+document.cookie+";";
 xx1=xx2=0;
 len=tmp1.length;
 while(xx1<len){
  xx2=tmp1.indexOf(";",xx1);
  tmp2=tmp1.substring(xx1+1,xx2);
  xx3=tmp2.indexOf("=");
  if(tmp2.substring(0,xx3)==key)return unescape(tmp2.substring(xx3+1,xx2-xx1-1));
  xx1=xx2+1;
 }
 return "";
}
/*]]>*/</script>
<style type=text/css><!-- img {border:0;} --></style>
</head>
<body bgcolor="<?php echo $SETTING['BBS_BG_COLOR']?>" text="<?php echo $SETTING['BBS_TEXT_COLOR']?>" link="<?php echo $SETTING['BBS_LINK_COLOR']?>" alink="<?php echo $SETTING['BBS_ALINK_COLOR']?>" vlink="<?php echo $SETTING['BBS_VLINK_COLOR']; ?>" />
<?php readfile('headad.txt'); ?>
<a href="./">■掲示板に戻る■</a>
<?php
if(!JIKAN_KISEI or ($JIKAN > JIKAN_END and $JIKAN < JIKAN_START)) {
	echo "<a href=\"..$SCRIPT/$bbs/$key/\">全部</a>\n";
}
for ($iCnt = 1; $iCnt <= $LINENUM; $iCnt += 100){
	$iTo = $iCnt + 99;
	echo "<a href=\"..$SCRIPT/$bbs/$key/$iCnt-$iTo\">$iCnt-</a>\n";
}
$red_num = (int)(THREAD_RES*19/20);
$yellow_num = (int)(THREAD_RES*9/10);
$alert = '';
if ($LINENUM >=THREAD_RES) {
	$alert = '<p><table><tr><td bgcolor="red"><br /><br /><font color=white>レス数が '.THREAD_RES.' を超えています。残念ながら全部は表示しません。</font></td></tr></table>';
	$stop = 1;
}
elseif ($LINENUM >= $red_num) {
	$alert = '<p><table><tr><td bgcolor="red"><font color=white>レス数が '.$red_num.' を超えています。'.THREAD_RES.'を超えると表示できなくなるよ。</font></td></tr></table>';
}
elseif ($LINENUM >= $yellow_num) {
	$alert = '<p><table><tr><td bgcolor="yellow">レス数が '.$yellow_num.' を超えています。'.THREAD_RES.'を超えると表示できなくなるよ。</td></tr></table>';
}
elseif ($fsize >= 480){
	$alert = '<p><table><tr><td bgcolor="red"><font color="white">サイズが 480KB を超えています。500KB を超えると書きこめなくなるよ。</font></td></tr></table>';
}
?>
<a href="..<?php echo $SCRIPT."/".$bbs."/".$key?>/l50">最新50</a>
<?php echo $alert?>
<p><font size="+1" color="<?php echo $SETTING['BBS_SUBJECT_COLOR']; ?>" /><?php echo $subject?></font><dl>
<?php
$i = 0;
if ($nofirst != "true" or $st == 1) {
	$LOG[0] = trim($LOG[0]);
	list($name,$mail,$time,$message,$subject) = explode("<>",$LOG[0]);
	if (!$message) {
		$name='';
		$mail='';
		$time='[ここ壊れてます]';
		$message='[ここ壊れてます]';
	}
	$message = preg_replace("/(https?):\/\/([\w;\/\?:\@&=\+\$,\-\.!~\*'\(\)%#]+)/", "<a href=\"$1://$2\" target=\"_blank\">$1://$2</a>", $message);
	$mailto = $mail ? "<a href=\"mailto:$mail\"><b>$name</b></a>" : "<font color=\"$SETTING[BBS_NAME_COLOR]\"><b>$name</b></font>";
	echo "<dt>1 ：$mailto ： $time<br /><dd> $message <br /><br /><br />\n";
	if ($s == 1) $s++;
	if ($st == 1) $i++;
}
while ($s <= $END) {
	if (!isset($LOG[$s-1]) or !$LOG[$s-1]) break;
	$log = trim($LOG[$s-1]);
	list($name,$mail,$time,$message,$subject) = explode("<>", $log);
	if (!JIKAN_KISEI or ($JIKAN > JIKAN_END and $JIKAN < JIKAN_START)) {
		if (!$message) {
			$name='';
			$mail='';
			$time='[ここ壊れてます]';
			$message='[ここ壊れてます]';
		}
		$message = preg_replace("/(https?):\/\/([\w;\/\?:\@&=\+\$,\-\.!~\*'\(\)%#]+)/", "<a href=\"$1://$2\" target=\"_blank\">$1://$2</a>", $message);
		$mailto = $mail ? "<a href=\"mailto:$mail\"><b>$name</b></a>" : "<font color=\"$SETTING[BBS_NAME_COLOR]\"><b>$name</b></font>";
		echo "<dt>$s ：$mailto ： $time<br /><dd> $message <br /><br /><br />\n";
		$s++;
	 }
	 else {
		if ($i < 100) {
			if (!$message) {
				$name='';
				$mail='';
				$time='[ここ壊れてます]';
				$message='[ここ壊れてます]';
			}
			$message = preg_replace("/(https?|ftp):\/\/([\w;\/\?:\@&=\+\$,\-\.!~\*'\(\)%#]+)/", "<a href=\"$1://$2\" target=\"_blank\">$1://$2</a>", $message);
			$message = str_replace("../test/read.php/$bbs/$key/",'',$message);
			$mailto = $mail ? "<a href=\"mailto:$mail\"><b>$name</b></a>" : "<font color=\"$SETTING[BBS_NAME_COLOR]\"><b>$name</b></font>";
			echo "<dt>$s ：$mailto ： $time<br /><dd> $message <br /><br /><br />\n";
			$s++;
			$i++;
		}
		else break;
	}
}
echo "</dl><font color=\"red\" face=\"arial\"><b>$fsize KB</b></font><hr />\n";
if ($LINENUM <= 1000) {
	if ($LINENUM >= $s) echo "<center><a href=\"..$SCRIPT/$bbs/$key/$END-\">続きを読む</a></center><hr />\n";
	else echo "<center><a href=\"..$SCRIPT/$bbs/$key/$LINENUM-\">新着レスの表示</a></center><hr />\n";
}
$t = $s + 99;
$u = 1;
echo "<a href=\"./\">掲示板に戻る</a> <a href=\"..$SCRIPT/$bbs/$key/\">全部</a>\n";
if ($mae) {
	if ($mae == 1) echo "<a href=\"..$SCRIPT/$bbs/$key/1\">前100</a>\n";
	else {
		if ($mae > 100) $u = $mae-99;
		echo "<a href=\"..$SCRIPT/$bbs/$key/$u-$mae\">前100</a>\n";
	}
}
echo "<a href=\"..$SCRIPT/$bbs/$key/$s-$t\">次100</a> <a href=\"..$SCRIPT/$bbs/$key/l50\">最新50</a><br />\n";
if ($stop != 1) {
	$fp  = fopen("../$bbs/threadconf.cgi", "r");
	while ($vip = fgetcsv($fp, 1024)) {
		if ($vip[0] == $key) break;
		else $vip[9] = 0;
	}
	fclose($fp);
	if (UPLOAD or $vip[9]) {
		?>
<form method="post" action="../test/bbs.php" enctype="multipart/form-data">
<input type="submit" value="書き込む" name="submit">
名前： <input name="FROM" size="19">
E-mail<font size="1"> (省略可) </font>: <input name="mail" size="19" /><br />
<textarea rows="5" cols="70" wrap="off" name="MESSAGE"></textarea><br />
<input type="file" name="file" size="50">
<input type="hidden" name="bbs" value="<?php echo $bbs; ?>" />
<input type="hidden" name="key" value="<?php echo $key; ?>" />
<input type="hidden" name="time" value="<?php echo $NOW; ?>" />
</form>
<?php
	}
	else {
		?>
<form method="post" action="../test/bbs.php">
<input type="submit" value="書き込む" name="submit">
名前： <input name="FROM" size="19">
E-mail<font size="1"> (省略可) </font>: <input name="mail" size="19" /><br />
<textarea rows="5" cols="70" wrap="off" name="MESSAGE"></textarea><br />
<input type="hidden" name="bbs" value="<?php echo $bbs; ?>" />
<input type="hidden" name="key" value="<?php echo $key; ?>" />
<input type="hidden" name="time" value="<?php echo $NOW; ?>" />
</form>
<?php
	}
}
echo "<p>".VERSION."\n</body>\n</html>\n";
if (GZ_FLAG) ob_end_flush();
exit;
#===================
#　エラー表示
#===================
function DispError($topic) {
	global $URL, $bbs, $key;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title><?php echo $topic?></title>
<style type="text/css"><!--
TD.Type1 {color: #ffffff;text-align: left;}A.BigLine {color: #000000;text-decoration: none;}
--></style>
</head>
<body text="#000000" bgcolor="#efefef">
<? readfile("headad.txt") ?>
<b><font size="+1" color="#ff0000"><?php echo $topic?></font></b><br />
<dl>
<dt>1 名前：<font color="green"><b><?php echo VERSION?></b></font>投稿日：2001/04/12(木) 15:11
<dd><?php echo $topic?><br /><br /><br />
</dl>
<hr />
<font size=-2><?php echo VERSION?></font>
<hr />
<p>
<?php
	if (is_file("../$bbs/kako/$key.html")) {
		?>
隊長! 過去ログ倉庫で、<a target="_self" href="<?php echo "$URL/$bbs/kako/$key.html"; ?>">スレッド<?php echo $key?>.html</a> を発見しました。
<?php
	}
	else {
		?>
<a target="_self" href="<?php echo "$URL/$bbs/kako/"; ?>">過去ログ倉庫</a>にもありませんでした。<br />問い合わせても見つかる可能性はほとんどありません。
<?php
	}
	echo '</body></html>';
	if (GZ_FLAG) ob_end_flush();
	exit;
}
?>