<?php
require("passcheck.php");
if (!isset($_POST['mode'])) $_POST['mode'] = '';
if(get_magic_quotes_gpc()) {
	$_POST = array_map("stripslashes", $_POST);
}
if (!isset($_REQUEST['bbs']) or !$_REQUEST['bbs']) disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：板名をいれてちょ。。。");
if (!is_dir("../$_REQUEST[bbs]")) disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：そんな板ないです。");
$set_pass = "../$_REQUEST[bbs]/SETTING.TXT";
$comment = '<br>';
#====================================================
#　初期情報の書込み（設定ファイル）
#====================================================
if ($_POST['mode'] == 'set') {
	error_reporting(E_ALL ^ E_NOTICE);
	$setvalue = <<<EOF
BBS_TITLE=$_POST[BBS_TITLE]
BBS_TITLE_PICTURE=$_POST[BBS_TITLE_PICTURE]
BBS_TITLE_COLOR=$_POST[BBS_TITLE_COLOR]
BBS_TITLE_LINK=$_POST[BBS_TITLE_LINK]
BBS_BG_COLOR=$_POST[BBS_BG_COLOR]
BBS_BG_PICTURE=$_POST[BBS_BG_PICTURE]
BBS_NONAME_NAME=$_POST[BBS_NONAME_NAME]
BBS_MAKETHREAD_COLOR=$_POST[BBS_MAKETHREAD_COLOR]
BBS_MENU_COLOR=$_POST[BBS_MENU_COLOR]
BBS_THREAD_COLOR=$_POST[BBS_THREAD_COLOR]
BBS_TEXT_COLOR=$_POST[BBS_TEXT_COLOR]
BBS_NAME_COLOR=$_POST[BBS_NAME_COLOR]
BBS_LINK_COLOR=$_POST[BBS_LINK_COLOR]
BBS_ALINK_COLOR=$_POST[BBS_ALINK_COLOR]
BBS_VLINK_COLOR=$_POST[BBS_VLINK_COLOR]
BBS_THREAD_NUMBER=$_POST[BBS_THREAD_NUMBER]
BBS_CONTENTS_NUMBER=$_POST[BBS_CONTENTS_NUMBER]
BBS_LINE_NUMBER=$_POST[BBS_LINE_NUMBER]
BBS_MAX_MENU_THREAD=$_POST[BBS_MAX_MENU_THREAD]
BBS_SUBJECT_COLOR=$_POST[BBS_SUBJECT_COLOR]
BBS_PASSWORD_CHECK=$_POST[BBS_PASSWORD_CHECK]
BBS_UNICODE=$_POST[BBS_UNICODE]
BBS_DELETE_NAME=$_POST[BBS_DELETE_NAME]
BBS_NAMECOOKIE_CHECK=$_POST[BBS_NAMECOOKIE_CHECK]
BBS_MAILCOOKIE_CHECK=$_POST[BBS_MAILCOOKIE_CHECK]
BBS_SUBJECT_COUNT=$_POST[BBS_SUBJECT_COUNT]
BBS_NAME_COUNT=$_POST[BBS_NAME_COUNT]
BBS_MAIL_COUNT=$_POST[BBS_MAIL_COUNT]
BBS_MESSAGE_COUNT=$_POST[BBS_MESSAGE_COUNT]
BBS_NEWSUBJECT=$_POST[BBS_NEWSUBJECT]
BBS_THREAD_TATESUGI=$_POST[BBS_THREAD_TATESUGI]
BBS_AD2=$_POST[BBS_AD2]
SUBBBS_CGI_ON=$_POST[SUBBBS_CGI_ON]
NANASHI_CHECK=$_POST[NANASHI_CHECK]
timecount=$_POST[timecount]
timeclose=$_POST[timeclose]
BBS_PROXY_CHECK=$_POST[BBS_PROXY_CHECK]
BBS_OVERSEA_THREAD=$_POST[BBS_OVERSEA_THREAD]
BBS_OVERSEA_PROXY=$_POST[BBS_OVERSEA_PROXY]
BBS_RAWIP_CHECK=$_POST[BBS_RAWIP_CHECK]
BBS_SLIP=$_POST[BBS_SLIP]
BBS_DISP_IP=$_POST[BBS_DISP_IP]
BBS_FORCE_ID=$_POST[BBS_FORCE_ID]
BBS_NO_ID=$_POST[BBS_NO_ID]

EOF;
	$fp = fopen($set_pass, "w");
	fputs($fp, $setvalue);
	fclose($fp);
	$comment = '<font color="red">設定を更新しました。メニューの<b>index.htmlを作り直す</b>をクリックしてください。</font><br>';
}
#====================================================
#　初期情報の取得（設定ファイル）
#====================================================
#設定ファイルを読む
if (is_file($set_pass)) {
	$set_str = file($set_pass);
	foreach ($set_str as $tmp){
		$tmp = rtrim($tmp);
		list($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
}
else disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：ユーザー設定が消失しています！");
if (!is_file("../$_REQUEST[bbs]/index.html")) {
	$comment = '<font color=red>まず各設定項目を変更後に<b>設定更新</b>ボタンを押し、<a href="admin.php?bbs='.$_REQUEST['bbs'].'" target="_parent">ここ</a>からメニューの更新をして、次にメニューの<b>index.htmlを作り直す</b>をクリックしてください。</font><br>';
}
$sel_pass = $sel_change = "";
if ($SETTING['BBS_UNICODE'] == "pass") $sel_pass = "selected";
if ($SETTING['BBS_UNICODE'] == "change") $sel_change = "selected";
#====================================================
#　掲示板の設定
#====================================================
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="main.css" type="text/css" />
<title>設定変更</title>
</head>
<body>
<h1 class="title"><?php echo $SETTING['BBS_TITLE']?></h1>
<h3>設定変更</h3>
<hr />
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
<?php echo $comment?>
<input type="submit" value="設定更新" />
<input type="hidden" name="mode" value="set" />
<input type="hidden" name="bbs" value="<?php echo $_REQUEST['bbs']?>" />
<table border="2">
<tr>
<th>掲示板のタイトル</th>
<td><input type="text" size="30" name="BBS_TITLE" value="<?php echo $SETTING['BBS_TITLE']?>" /></td>
</tr>
<tr>
<th>タイトル画像のパス</th>
<td><input type="text" size="30" name="BBS_TITLE_PICTURE" value="<?php echo $SETTING['BBS_TITLE_PICTURE']?>" /></td>
</tr>
<tr>
<th>タイトルのリンクURL</th>
<td><input type="text" size="30" name="BBS_TITLE_LINK" value="<?php echo $SETTING['BBS_TITLE_LINK']?>" /></td>
</tr>
<tr>
<th>背景画像のパス</th>
<td><input type="text" size="30" name="BBS_BG_PICTURE" value="<?php echo $SETTING['BBS_BG_PICTURE']?>" /></td>
</tr>
<tr>
<th>名無しさんの名前</th>
<td><input type="text" size="30" name="BBS_NONAME_NAME" value="<?php echo $SETTING['BBS_NONAME_NAME']?>" /></td>
</tr>
<tr>
<th>削除メッセージ</th>
<td><input type="text" size="30" name="BBS_DELETE_NAME" value="<?php echo $SETTING['BBS_DELETE_NAME']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_TITLE_COLOR']?>">■</font>タイトルの色</th>
<td><input type="color" size="30" name="BBS_TITLE_COLOR" value="<?php echo $SETTING['BBS_TITLE_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_BG_COLOR']?>">■</font>背景色</th>
<td><input type="color" size="30" name="BBS_BG_COLOR" value="<?php echo $SETTING['BBS_BG_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_MAKETHREAD_COLOR']?>">■</font>スレッド新規作成画面の色</th>
<td><input type="color" size="30" name="BBS_MAKETHREAD_COLOR" value="<?php echo $SETTING['BBS_MAKETHREAD_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_MENU_COLOR']?>">■</font>スレメニュー表示部の背景色</th>
<td><input type="color" size="30" name="BBS_MENU_COLOR" value="<?php echo $SETTING['BBS_MENU_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_THREAD_COLOR']?>">■</font>スレッド表示部の背景色</th>
<td><input type="color" size="30" name="BBS_THREAD_COLOR" value="<?php echo $SETTING['BBS_THREAD_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_SUBJECT_COLOR']?>">■</font>スレタイトルの色</th>
<td><input type="color" size="30" name="BBS_SUBJECT_COLOR" value="<?php echo $SETTING['BBS_SUBJECT_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_TEXT_COLOR']?>">■</font>投稿文の色</th>
<td><input type="color" size="30" name="BBS_TEXT_COLOR" value="<?php echo $SETTING['BBS_TEXT_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_NAME_COLOR']?>">■</font>名前の色</th>
<td><input type="color" size="30" name="BBS_NAME_COLOR" value="<?php echo $SETTING['BBS_NAME_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_LINK_COLOR']?>">■</font>LINKの色</th>
<td><input type="color" size="30" name="BBS_LINK_COLOR" value="<?php echo $SETTING['BBS_LINK_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_ALINK_COLOR']?>">■</font>ALINKの色</th>
<td><input type="color" size="30" name="BBS_ALINK_COLOR" value="<?php echo $SETTING['BBS_ALINK_COLOR']?>" /></td>
</tr>
<tr>
<th><font color="<?php echo $SETTING['BBS_VLINK_COLOR']?>">■</font>VLINKの色</th>
<td><input type="color" size="30" name="BBS_VLINK_COLOR" value="<?php echo $SETTING['BBS_VLINK_COLOR']?>" /></td>
</tr>
<tr>
<th>index.html に表示するスレッド数</th>
<td><input type="text" size="30" name="BBS_THREAD_NUMBER" value="<?php echo $SETTING['BBS_THREAD_NUMBER']?>" /></td>
</tr>
<tr>
<th>1スレッドに表示するレス数</th>
<td><input type="text" size="30" name="BBS_CONTENTS_NUMBER" value="<?php echo $SETTING['BBS_CONTENTS_NUMBER']?>" /></td>
</tr>
<tr>
<th>1レスに表示する行数</th>
<td><input type="text" size="30" name="BBS_LINE_NUMBER" value="<?php echo $SETTING['BBS_LINE_NUMBER']?>" /></td>
</tr>
<tr>
<th>メニューに表示するスレッド数</th>
<td><input type="text" size="30" name="BBS_MAX_MENU_THREAD" value="<?php echo $SETTING['BBS_MAX_MENU_THREAD']?>" /></td>
</tr>
<tr>
<th>スレタイトルの最大文字数（バイト）</th>
<td><input type="text" size="30" name="BBS_SUBJECT_COUNT" value="<?php echo $SETTING['BBS_SUBJECT_COUNT']?>" /></td>
</tr>
<tr>
<th>名前の最大文字数（バイト）</th>
<td><input type="text" size="30" name="BBS_NAME_COUNT" value="<?php echo $SETTING['BBS_NAME_COUNT']?>" /></td>
</tr>
<tr>
<th>メールの最大文字数（バイト）</th>
<td><input type="text" size="30" name="BBS_MAIL_COUNT" value="<?php echo $SETTING['BBS_MAIL_COUNT']?>" /></td>
</tr>
<tr>
<th>本文の最大文字数（バイト）</th>
<td><input type="text" size="30" name="BBS_MESSAGE_COUNT" value="<?php echo $SETTING['BBS_MESSAGE_COUNT']?>" /></td>
</tr>
<tr>
<th>UNICODE処理</th>
<td><select name="BBS_UNICODE"><option value="pass" <?php echo $sel_pass?>>pass<option value="change" <?php echo $sel_change?>>change</select></td>
</tr>
<tr>
<th>ホスト名表示</th>
<td>する<input type="checkbox" name="BBS_DISP_IP" value="checked" <?php echo $SETTING['BBS_DISP_IP']?> /></td>
</tr>
<tr>
<th>ID表示</th>
<td>しない<input type="checkbox" name="BBS_NO_ID" value="checked" <?php echo $SETTING['BBS_NO_ID']?> /></td>
</tr>
<tr>
<th>ID強制表示</th>
<td>する<input type="checkbox" name="BBS_FORCE_ID" value="checked" <?php echo $SETTING['BBS_FORCE_ID']?> />↑ID表示しないがチェックされているとそちらが優先されます</td>
</tr>
<tr>
<th>名前入力必須</th>
<td>する<input type="checkbox" name="NANASHI_CHECK" value="checked" <?php echo $SETTING['NANASHI_CHECK']?> /></td>
</tr>
<tr>
<th>スレッド立てすぎ</th>
<td><input type="text" size="3" name="BBS_THREAD_TATESUGI" value="<?php echo $SETTING['BBS_THREAD_TATESUGI']?>" /> 個間隔</td>
</tr>
<tr>
<th>連続投稿制限</th>
<td><input type="text" size="3" name="timecount" value="<?php echo $SETTING['timecount']?>" />回中<input type="text" size="3" name="timeclose" value="<?php echo $SETTING['timeclose']?>" />回で制限</td>
</tr>
<tr>
<th>Cookie(NAME)作成</th>
<td>する<input type="checkbox" name="BBS_NAMECOOKIE_CHECK" value="checked" <?php echo $SETTING['BBS_NAMECOOKIE_CHECK']?> /></td>
</tr>
<tr>
<th>Cookie(MAIL)作成</th>
<td>する<input type="checkbox" name="BBS_MAILCOOKIE_CHECK" value="checked" <?php echo $SETTING['BBS_MAILCOOKIE_CHECK']?> /></td>
</tr>
<tr>
<th>PROXY制限</th>
<td>する<input type="checkbox" name="BBS_PROXY_CHECK" value="checked" <?php echo $SETTING['BBS_PROXY_CHECK']?> />環境変数で判断するので匿名PROXYは制限できません</td>
</tr>
<tr>
<th>海外PROXY制限</th>
<td>する<input type="checkbox" name="BBS_OVERSEA_PROXY" value="checked" <?php echo $SETTING['BBS_OVERSEA_PROXY']?> /></td>
</tr>
<tr>
<th>.JPドメイン以外からのスレ立て拒否</th>
<td>する<input type="checkbox" name="BBS_OVERSEA_THREAD" value="checked" <?php echo $SETTING['BBS_OVERSEA_THREAD']?> />bbtec.net(YahooBB)は拒否されません</td>
</tr>
</table>
<input type="submit" value="設定更新"><br>
</form>
<br>
</body></html>
