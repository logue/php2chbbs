<?php
$HOST = gethostbyaddr($_SERVER['REMOTE_ADDR']);
#====================================================
#　日付・時刻を設定
#====================================================
$NOWTIME = time();
$wday = array('日','月','火','水','木','金','土');
$today = getdate($NOWTIME);
$JIKAN = $today['hours'];
$DATE = date("Y/m/d(", $NOWTIME).$wday[$today['wday']].date(") H:i:s", $NOWTIME);
#====================================================
#　各種ＰＡＴＨ生成
#====================================================
$PATH		= "../".$_POST['bbs']."/";
$DATPATH	= $PATH."dat/";
$TEMPPATH	= $PATH."html/";
$INDEXFILE	= $PATH."index.html";
$SUBFILE	= $PATH."subback.html";
$IMODEFILE	= $PATH."i/index.html";
$IMGPATH	= $PATH."img/";
$IMGPATH2	= $PATH."img2/";
if (!isset($_POST['subject'])) $_POST['subject'] = '';
if (!isset($_POST['FROM'])) $_POST['FROM'] = '';
if (!isset($_POST['mail'])) $_POST['mail'] = '';
if (!isset($_POST['bbs'])) $_POST['bbs'] = '';
if (!isset($_POST['key'])) $_POST['key'] = '';
if (!isset($_POST['MESSAGE'])) $_POST['MESSAGE'] = '';
#====================================================
#　初期情報の取得（設定ファイル）
#====================================================
#設定ファイルを読む
$set_file = $PATH . "SETTING.TXT";
if (is_file($set_file)) {
	$set_str = file($set_file);
	foreach ($set_str as $tmp){
		$tmp = trim($tmp);
		list ($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
}
#設定ファイルがない（ERROR)
else DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：ユーザー設定が消失しています！$PATH");
require $PATH.'config.php';
#====================================================
#　入力情報を取得（ＰＯＳＴ）
#====================================================
if ($_SERVER['REQUEST_METHOD'] != 'POST') DispError ("ＥＲＲＯＲ！","ＥＲＲＯＲ：不正な投稿です！");
if (get_magic_quotes_gpc()) $_POST = array_map("stripslashes", $_POST);
$_POST['subject'] = str_replace('"', "&quot;", $_POST['subject']);
$_POST['subject'] = str_replace("<", "&lt;", $_POST['subject']);
$_POST['subject'] = str_replace(">", "&gt;", $_POST['subject']);
$_POST['subject'] = str_replace("'", "&#039;", $_POST['subject']);
$_POST['subject'] = str_replace(array("\r\n","\r","\n"), " ", $_POST['subject']);
$_POST['FROM'] = htmlspecialchars($_POST['FROM']);
$_POST['FROM'] = str_replace(array("\r\n","\r","\n"), " ", $_POST['FROM']);
$_POST['mail'] = htmlspecialchars($_POST['mail'], ENT_QUOTES);
$_POST['mail'] = str_replace(array("\r\n","\r","\n"), " ", $_POST['mail']);
$_POST['bbs'] = str_replace(array(".","/","|"), "", $_POST['bbs']);
$_POST['key'] = str_replace(array(".","/","|"), "", $_POST['key']);
$_POST['MESSAGE'] = rtrim($_POST['MESSAGE']);
$_POST['MESSAGE'] = str_replace('"', "&quot;", $_POST['MESSAGE']);
$_POST['MESSAGE'] = str_replace("<", "&lt;", $_POST['MESSAGE']);
$_POST['MESSAGE'] = str_replace(">", "&gt;", $_POST['MESSAGE']);
$_POST['MESSAGE'] = str_replace(array("\r\n","\r","\n"), " <br> ", $_POST['MESSAGE']);
# ユニコード変換
if($SETTING['BBS_UNICODE'] == "change"){
	$_POST['subject'] = preg_replace("/\&\#\d+\;/", "？", $_POST['subject']);
	$_POST['MESSAGE'] = preg_replace("/\&\#\d+\;/", "？", $_POST['MESSAGE']);
}
$_POST['MESSAGE'] = str_replace("'", "&#039;", $_POST['MESSAGE']);
# ＮＧワード
#$_POST['FROM'] = str_replace("管理", '"管理"', $_POST['FROM']);
#$_POST['FROM'] = str_replace("管直", '"管直"', $_POST['FROM']);
#$_POST['FROM'] = str_replace("菅直", '"菅直"', $_POST['FROM']);
#$_POST['FROM'] = str_replace("削除", '"削除"', $_POST['FROM']);
#$_POST['FROM'] = str_replace("sakujyo", '"sakujyo"', $_POST['FROM']);
# 偽キャップ、偽トリップ変換
$_POST['FROM'] = str_replace("★", "☆", $_POST['FROM']);
$_POST['FROM'] = str_replace("◆", "◇", $_POST['FROM']);
# 全角＃のパス漏れ防止
#香美バグのためmb_ereg_replaceにしようかとも考えたが中止
#$_POST['FROM'] = str_replace("＃", "#", $_POST['FROM']);
#$_POST['mail'] = str_replace("＃", "#", $_POST['mail']);
#====================================================
#　ホストの判定
#====================================================
$PROXY = '';
if (isset($_SERVER['HTTP_VIA']) and preg_match("/.*\s(\d+)\.(\d+)\.(\d+)\.(\d+)/", $_SERVER['HTTP_VIA'], $match)) {
	$PROXY = "$match[1].$match[2].$match[3].$match[4]";
}
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match("/^(\d+)\.(\d+)\.(\d+)\.(\d+)(\D*).*/", $_SERVER['HTTP_X_FORWARDED_FOR'], $match)) {
	$PROXY = "$match[1].$match[2].$match[3].$match[4]";
}
if (isset($_SERVER['HTTP_FORWARDED']) and preg_match("/.*\s(\d+)\.(\d+)\.(\d+)\.(\d+)/", $_SERVER['HTTP_FORWARDED'], $match)) {
	$PROXY = "$match[1].$match[2].$match[3].$match[4]";
}
if ($PROXY) {
	$PROXY = gethostbyaddr($PROXY);
	$HOST .= "<$PROXY>";
}
#==================================================
#　アクセス規制
#==================================================
# 不正ＰＲＯＸＹ使用ですか、、、？
# プロキシ制限の実施
/**** 2ch BBQ
if(gethostbyname(join('.',array_reverse(explode( ".", $_SERVER['REMOTE_ADDR'])).'.niku.2ch.net') == "127.0.0.2") {
	$PROXY = $_SERVER['REMOTE_ADDR'];
}
#*******/
if ($PROXY and ($SETTING['BBS_PROXY_CHECK'] == "checked")) {
	DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：ＰＲＯＸＹ規制中！");
}
if ($SETTING['BBS_OVERSEA_PROXY'] == "checked" and !preg_match("/\.jp</i", $HOST)) {
	DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：ＰＲＯＸＹ規制中！");
}
if ($SETTING['BBS_OVERSEA_THREAD'] == "checked" and $_POST['subject'] and !preg_match("/\.jp$/i", $HOST) and !preg_match("/\.bbtec\.net$/", $HOST)) {
	DispError("ＥＲＲＯＲ！","jpドメインからスレッド立ててください");
}
#-------------------------------アクセス拒否リスト
if (is_file($PATH."uerror.cgi")){
	$IN = file($PATH."uerror.cgi");
	foreach ($IN as $tmp){
		$tmp = trim($tmp);
		if (stristr($HOST, $tmp)) DispError("ＥＲＲＯＲ！","ユーザー設定が異常です！");
		if (stristr($_SERVER['REMOTE_ADDR'], $tmp)) DispError("ＥＲＲＯＲ！","ユーザー設定が異常です！");
	}
}
#====================================================
#　新規スレッド画面
#====================================================
$enctype = 'application/x-www-form-urlencoded';
$file_form = '';
if (UPLOAD) {
	$enctype = 'multipart/form-data';
	$file_form = '<input type=file name=file size=50><br>';
}
if ($SETTING['BBS_TITLE_PICTURE']) $bbs_title = '<a href="'.$SETTING['BBS_TITLE_LINK'].'"><img src="'.$SETTING['BBS_TITLE_PICTURE'].'" border="0"></a>';
else $bbs_title = '<h1><font color="'.$SETTING['BBS_TITLE_COLOR'].'">'.$SETTING['BBS_TITLE'].'</font></h1>';
if (isset($_POST['new']) and $_POST['new'] == "thread") {
	header("Content-Type: text/html; charset=Shift_JIS");
	require('new_thread.php');
	exit;
}
#====================================================
#　クッキー発行
#====================================================
#有効期限をつくる
$exptime = 24 * 60 * 60;
$exptime *= 90;	#有功日数を乗じる
$exptime += $NOWTIME;
$exp = date("D, j-M-Y H:i:s ", $exptime).'GMT';
$set_cookie = '<script type="text/javascript"><!-- 
';
if ($SETTING['BBS_NAMECOOKIE_CHECK']) {
	$set_cookie .= 'cookname = escape("'.addslashes($_POST['FROM']).'"); document.cookie = "NAME="+cookname+"; expires='.$exp.'; path=/"; ';
}
if ($SETTING['BBS_MAILCOOKIE_CHECK']) {
	$set_cookie .= 'cookmail = escape("'.addslashes($_POST['mail']).'"); document.cookie = "MAIL="+cookmail+"; expires='.$exp.'; path=/"; ';
}
$set_cookie .= '//--></script>';
#====================================================
#　新規スレッドと普通の書き込みの情報チェック
#====================================================
# 画像用のカウンタ
$imgnum = 1;
if ($_POST['subject']) {
	do {
		#サブジェクトがあれば新規スレなのでキーを現在に設定
		$_POST['key'] = time();
		#.datファイルの設定
		$DATAFILE = $DATPATH.$_POST['key'].".dat";
	} while (is_file($DATAFILE)) ;
}
elseif ($_POST['key']) {
	#キーが数字じゃない場合ばいばい！
	if (preg_match("/\D/", $_POST['key'])) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：キー情報が不正です！");
	#.datファイルの設定
	$DATAFILE = $DATPATH.$_POST['key'].".dat";
	#.datが存在してないか書けないならばいばい
	if (!is_writable($DATAFILE)) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：このスレッドには書けません！");
	#.datのサイズが大きすぎる時はばいばい
	if (filesize($DATAFILE) > THREAD_BYTES) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：このスレッドは ".(int)(THREAD_BYTES/1024)."k を超えているので書けません！");
	# レスの総数を画像用カウンタに
	$fp = fopen($DATAFILE, "r");
	while ($tmp = fgets($fp)) $imgnum++;
	fclose($fp);
}
#サブジェクトもキーも存在しないならばいばい
else DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：サブジェクトが存在しません！");
#====================================================
#　フィールドサイズの判定
#====================================================
if (strlen($_POST['MESSAGE']) == 0) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：本文がありません！");
if (strlen($_POST['mail']) > $SETTING['BBS_MAIL_COUNT']) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：メールアドレスが長すぎます！");
$msg = explode("<br>", $_POST['MESSAGE']);
if (count($msg) > 50) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：改行が多すぎます！");
foreach ($msg as $tmp) {
	if (strlen($tmp) > 256) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：長すぎる行があります！");
}
if (strlen($_POST['MESSAGE']) > $SETTING['BBS_MESSAGE_COUNT']) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：本文が長すぎます！");
if (preg_match_all("/&gt;&gt;[0-9]/", $_POST['MESSAGE'], $matches) > 16) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：レスアンカーリンクが多すぎます！");
if (strlen($_POST['FROM']) > $SETTING['BBS_NAME_COUNT']) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：名前が長すぎます！");
if (strlen($_POST['subject']) > $SETTING['BBS_SUBJECT_COUNT']) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：サブジェクトが長すぎます！");
#====================================================
#　書き込み情報のチェック＆補完
#====================================================
#monazilla からの書きこみ
#refererチェック＆携帯からの書きこみ
if (!isset($_SERVER['HTTP_REFERER']) or !$_SERVER['HTTP_REFERER']){
	#refererが無い場合は携帯からの書きこみチェック
	if (!strstr($_SERVER['HTTP_USER_AGENT'], 'DoCoMo') and
	!strstr($_SERVER['HTTP_USER_AGENT'], 'J-PHONE') and
	!strstr($_SERVER['HTTP_USER_AGENT'], 'UP.Browser'))
	DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：リファラぐらい送ってちょ。");
}
/*
else {
	if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){
		DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：ブラウザ変ですよん。");
	}
	if ($_SERVER['HTTP_HOST'] != $_SERVER['SERVER_NAME']){
		DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：ブラウザ変ですよん。");
	}
}
*/
#====================================================
#　エラーレスポンス（普通のエラーはまとめてばいばい）
#====================================================
#ＰＯＳＴ情報
if ($_POST['submit'] != "書き込む" and $_POST['submit'] != "新規スレッド作成" and $_POST['submit'] != "かきこむ" and $_POST['submit'] != "上記全てを承諾して書き込む") {
	DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：ユーザー設定が消失しています！");
}
#時間が読み込めなかったらばいばい
if (!$_POST['time']) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：フォーム情報が不正です！");
#==================================================
#　スレッド立てすぎチェック
#==================================================
if ($_POST['subject'] and $SETTING['BBS_THREAD_TATESUGI'] >= 2) {
	# スレ立て者のホスト名記録ファイルを読み込む（BBS_THREAD_TATESUGI個記録されている）
	$file = $PATH."RIP.cgi";
	$IP = array();
	if (is_file($file)) {
		$IP = file($file);
		foreach ($IP as $tmp) {
			$tmp = rtrim($tmp);
			# 記録ファイル内に同一ホストがあればエラー。
			if ($HOST == $tmp) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：スレッド立てすぎです。。。");
		}
	}
	array_unshift($IP, "$HOST\n");
	# 記録ファイル内のホスト数を BBS_THREAD_TATESUGI 個以内に調整して保存
	while (count($IP) > $SETTING['BBS_THREAD_TATESUGI']) array_pop($IP);
	$fp = @fopen($file, "w");
	foreach($IP as $tmp) fputs($fp, "$tmp");
	fclose($fp);
}
#==================================================
#　クッキー食いチェック
#==================================================
if (!$_COOKIE and 
	!strstr($_SERVER['HTTP_USER_AGENT'], 'DoCoMo') and
	!strstr($_SERVER['HTTP_USER_AGENT'], 'J-PHONE') and
	!strstr($_SERVER['HTTP_USER_AGENT'], 'UP.Browser'))
	DispError("投稿確認");
#==================================================
#　連続投稿規制
#==================================================
if ($SETTING['timecount'] >= 2) {
	# 投稿者のホスト名記録ファイルを読み込む（timecount個記録されている）
	$file = $PATH."timecheck.cgi";
	$IP = array();
	$count = 0;
	if (is_file($file)) {
		$IP = file($file);
		foreach($IP as $tmp){
			$tmp = rtrim($tmp);
			list($time1,$host1) = explode("<>", $tmp);
			if ($HOST == $host1) {
				# 同じ投稿フォームからの場合は2重カキコとしてエラー
				if ($_POST['time'] == $time1) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：２重カキコですか？？");
				# ホスト名が同じ投稿の数をカウント
				$count++;
			}
		}
	}
	# timecount 個の投稿内にtimeclose 個以上の投稿があればエラー
	if ($count >= $SETTING['timeclose']) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：連続投稿ですか？？");
	array_unshift($IP, "$_POST[time]<>$HOST\n");
	# 記録ファイル内のホスト数を timecount 個以内に調整して保存
	while (count($IP) > $SETTING['timecount']) array_pop($IP);
	$fp = @fopen($file, "w");
	foreach($IP as $tmp) fputs($fp, $tmp);
	fclose($fp);
}
#==================================================
#　キャップ、トリップ
#==================================================
# ＩＤを生成する
$idnum = substr($_SERVER['REMOTE_ADDR'], 8); 
$idcrypt = substr(crypt($idnum * $idnum, substr($DATE, 8, 2)), -8); 
$ID = " ID:".$idcrypt;
# ID強制表示じゃない場合でmail欄に記入があればIDを隠す
if ($_POST['mail'] and $SETTING['BBS_FORCE_ID'] != "checked") $ID = " ID:???";
# キャップ、トリップはクッキー食いチェックが終わってから変換すること
# トリップ
# $trip は0thelloに使用
$trip = '';
if (preg_match("/([^\#]*)\#(.+)/", $_POST['FROM'], $match)) {
	$salt = substr($match[2]."H.", 1, 2);
	$salt = preg_replace("/[^\.-z]/", ".", $salt);
	$salt = strtr($salt,":;<=>?@[\\]^_`","ABCDEFGabcdef");
	$trip = substr(crypt($match[2], $salt),-10);
	$_POST['FROM'] = $match[1]."</b>◆".$trip."<b>";
}
# キャップ
if (preg_match("/([^\#]*)\#(.+)/", $_POST['mail'], $cap)) {
	if (is_file("caps.cgi")) {
		$fp = fopen("caps.cgi", "r");
		while ($cap_data = fgets($fp, 1024)) {
			$cap_data = rtrim($cap_data);
			list($id1,$name1,$pass1,$color1) = explode("<>", $cap_data);
			if (crypt($cap[2], $pass1) == $pass1) {
				if ($_POST['FROM']) $_POST['FROM'] .= "＠$name1 ★";
				else $_POST['FROM'] = "$name1 ★";
				if ($color1) $_POST['FROM'] = "<font color=\"$color1\">$_POST[FROM]</font>";
				$ID = " ID:???";
				break;
			}
		}
		fclose($fp);
	}
	$_POST['mail'] = $cap[1];
}
#####################################################
#　板ごとの罠をかけるならここへ。。。
#####################################################
$sage = 0;
$stars = 0;
@include 'bbs2.php';
if ($stars and !strpos($_POST['FROM'], '★')) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：キャップがないとレスできません。。。");
# 日付欄にIDを加える
$DATE_ID = $DATE;
# 'BBS_NO_ID', 'NANASHI_CHECK', 'BBS_NONAME_NAME' は"bbs2.php"で変更されている可能性あり
if ($SETTING['BBS_NO_ID'] != "checked") $DATE_ID .= $ID;
# ホスト表示の場合ホスト名を付加
if ($SETTING['BBS_DISP_IP'] == "checked") $DATE_ID .=" <font size=1>[ $HOST ]</font>";
# fusianasanでホスト表示
$_POST['FROM'] = str_replace("fusianasan", " </b>$HOST<b>", $_POST['FROM']);
# 名前入力チェックと補完
if ($SETTING['NANASHI_CHECK'] and !$_POST['FROM']) DispError("ＥＲＲＯＲ！","ＥＲＲＯＲ：名前いれてちょ。。。");
if (!$_POST['FROM']) $_POST['FROM'] = $SETTING['BBS_NONAME_NAME'];
#====================================================
#　レスポンスアンカー（本文）
#====================================================
$_POST['MESSAGE'] = preg_replace("/&gt;&gt;([0-9]+)(?![-\d])/", "<a href=\"../test/read.php/$_POST[bbs]/$_POST[key]/$1\" target=\"_blank\">&gt;&gt;$1</a>", $_POST['MESSAGE']);
$_POST['MESSAGE'] = preg_replace("/&gt;&gt;([0-9]+)\-([0-9]+)/", "<a href=\"../test/read.php/$_POST[bbs]/$_POST[key]/$1-$2\" target=\"_blank\">&gt;&gt;$1-$2</a>", $_POST['MESSAGE']);
#====================================================
#　ファイル操作（ホスト記録）
#====================================================
# .datファイルはmonazilla関係で丸見えなので別にホスト記録用ログファイルを用意
$fp = fopen($PATH."hostlog.cgi", "a");
flock($fp, 2);
fwrite($fp, "$_POST[FROM]<>$_POST[mail]<>$DATE $idcrypt<>".substr(strip_tags($_POST['MESSAGE']), 0, 30)."<>$_POST[subject]<>$HOST<>$_SERVER[REMOTE_ADDR]<>\n");
fclose($fp);
#====================================================
#　ファイル操作（ＤＡＴファイル更新）
#====================================================
$outdat = "$_POST[FROM]<>$_POST[mail]<>$DATE_ID <> $_POST[MESSAGE] <>$_POST[subject]\n";
# $outdatの追加とhtmlファイルの作成（戻り値は"サブジェクト名 (レスの総数)"）
require 'make_work.php';
$subtt = MakeWorkFile($_POST['bbs'], $_POST['key'], $outdat);
#====================================================
#　ファイル操作（subject.txt）
#====================================================
$subjectfile = $PATH."subject.txt";
$keyfile = $_POST['key'].".dat";
$PAGEFILE = array();
# サブジェクトファイルを読み込む
# スレッドキー.dat<>タイトル (レスの数)\n
# $PAGEFILE = array('スレッドキー.dat',・・・)
# $SUBJECT = array('スレッドキー.dat'=>'タイトル (レスの数)',・・・)
$subr = @file($subjectfile);
if ($subr) {
	foreach ($subr as $tmp){
		$tmp = rtrim($tmp);
		list($file, $value) = explode("<>", $tmp);
		if (!$file) break;
		$filename = $DATPATH . $file;
		array_push($PAGEFILE,$file);
		$SUBJECT[$file] = $value;
	}
}
# サブジェクト数を取得
$FILENUM = count($PAGEFILE);
# 新規スレッドの場合は1個追加
if ($_POST['subject']) $FILENUM++;
# ログを定数に揃える
if ($FILENUM > KEEPLOGCOUNT) {
	for ($start = KEEPLOGCOUNT; $start < $FILENUM; $start++) {
		$delfile = $DATPATH . $PAGEFILE[$start];
		# datファイル削除
		unlink($delfile);
		$key = str_replace('.dat', '', $PAGEFILE[$start]);
		$delfile = $TEMPPATH . $key . ".html";
		# htmlファイル削除
		@unlink($delfile);
		if ($dir = @opendir($IMGPATH)) {
			while (($file = readdir($dir)) !== false) {
				# 画像ファイル削除
				if (strpos($file, $key) === 0) unlink($IMGPATH.$file);
			}  
			closedir($dir);
		}
		if ($dir = @opendir($IMGPATH2)) {
			while (($file = readdir($dir)) !== false) {
				# サムネイル画像ファイル削除
				if (strpos($file, $key) === 0) unlink($IMGPATH2.$file);
			}  
			closedir($dir);
		}
	}
	$FILENUM = KEEPLOGCOUNT;
	$PAGEFILE = array_slice($PAGEFILE, 0, $FILENUM);
}
$subtm = "$keyfile<>$subtt";
# サブジェクトハッシュを書き換える
$SUBJECT[$keyfile] = $subtt;
# サブジェクトテキストを開く
$fp = @fopen($subjectfile, "w");
#一括書き込み
# sageの時は上がらない
if (!$_POST['subject'] and ($sage or strstr($_POST['mail'], 'sage'))) {
	foreach ($PAGEFILE as $tmp){
		fputs($fp, "$tmp<>$SUBJECT[$tmp]\n");
	}
}
else {
	# 上がるキーは一番最初に持ってくる
	$temp[0] = $keyfile;
	$i = 1;
	fputs($fp, "$subtm\n");
	foreach ($PAGEFILE as $tmp) {
		# keyfileは現在書き込みしたスレッドキー（上がっている）
		if ($tmp != $keyfile) {
			$temp[$i] = $tmp;
			$i++;
			fputs($fp, "$tmp<>$SUBJECT[$tmp]\n");
		}
	}
	$PAGEFILE = $temp;
}
fclose($fp);
#====================================================
#　本ＨＴＭＬ吐き処理
#====================================================
require 'make_html.php';
exit;
#====================================================
#　エラー画面（エラー処理）と書き込み確認画面
#====================================================
#DispError(TITLE,TOPIC);
function DispError($title, $topic = "") {
	global $HOST, $NOWTIME;
	setcookie("PON", $HOST, $NOWTIME+3600*24*90, "/");
	header("Content-Type: text/html; charset=Shift_JIS");
	# $topicが無い場合は書き込み確認画面
	if (!$topic) {
		$sbj = $_POST['subject'];
		$frm = $_POST['FROM'];
		$mml = $_POST['mail'];
		$msg = $_POST['MESSAGE'];
		$sbj = str_replace ("&amp;", "&", $sbj);
		$sbj = str_replace (" ", "\x1F", $sbj);
		$frm = str_replace ("&amp;", "&", $frm);
		$frm = str_replace (" ", "\x1F", $frm);
		$mml = str_replace ("&amp;", "&", $mml);
		$mml = str_replace (" ", "\x1F", $mml);
		$msg = str_replace (" <br> ", "\n", $msg);
		$msg = str_replace (" ", "\x1F", $msg);
		?>
<html><!-- 2ch_X:cookie --><head><title>■ 書き込み確認 ■</title><meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS"></head><body bgcolor="#EEEEEE">
<font size="+1" color="#FF0000"><b>書きこみ＆クッキー確認</b></font><ul><br><br><b><?=$_POST['subject']?> </b><br>名前：<?=$_POST['FROM']?> <br>E-mail：<?=$_POST['mail']?> <br>内容：<br><?=$_POST['MESSAGE']?><br><br></ul>
<b>
<?=$title?><br>
・投稿者は、投稿に関して発生する責任が全て投稿者に帰すことを承諾します。<br>
・投稿者は、話題と無関係な広告の投稿に関して、相応の費用を支払うことを承諾します<br>
・投稿者は、投稿された内容について、掲示板運営者がコピー、保存、引用、転載等の利用することを許諾します。また、掲示板運営者に対して、著作者人格権を一切行使しないことを承諾します。<br>
・投稿者は、掲示板運営者が指定する第三者に対して、著作物の利用許諾を一切しないことを承諾します。
<br>

</b>
  <form method="post" action="../test/bbs.php" enctype="<?=$GLOBALS['enctype']?>">
    <input type="hidden" name="subject" value="<?=$sbj?>">
    <input type="hidden" NAME="FROM"  value="<?=$frm?>">
    <input type="hidden" NAME="mail"  value="<?=$mml?>">
    <input type="hidden" name="MESSAGE" value="<?=$msg?>"></ul>
    <input type="hidden" name="bbs" value="<?=$_POST['bbs']?>">
    <input type="hidden" name="time" value="<?=$_POST['time']?>">
    <input type="hidden" name="key" value="<?=$_POST['key']?>">

<br>
<?
if (isset($_FILES['file']['name']) and $_FILES['file']['name']) {
	echo "もう一度ファイルの指定を行ってください。<br>\n";
	echo '<input type="file" name="file" size="50"><br>';
}
?>
<input type="submit" value="上記全てを承諾して書き込む" name="submit"><br>
</form>
変更する場合は戻るボタンで戻って書き直して下さい。<br><br>
<font size="-1">(cookieを設定するとこの画面はでなくなります。)</font><br>
</body></html>
<?
	}
	# $topicがあるときはエラー画面表示
	else {
		?>
<html><head><title><?=$title?></title><meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS"></head><body bgcolor="#FFFFFF">
<font size="+1" color="#FF0000"><b><?=$topic?></b></font>
<ul><br>ホスト<b><?=$HOST?></b><br><b><?=$_POST['subject']?> </b><br>名前： <?=$_POST['FROM']?><br>E-mail：<?=$_POST['mail']?> <br>内容：<br><?=$_POST['MESSAGE']?><br><br></ul>
<center>こちらでリロードしてください。<a href="../<?=$_POST['bbs']?>/"> GO! </a></center></body></html>
<?
	}
	exit();
}
?>