<?php
#一度に表示するリスト
$inum = 10;
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
#　ファイル操作（ホストファイル名読み込み）
#==================================================
$log_file = "../$_GET[bbs]/hostlog.cgi";
if (!is_file($log_file)) disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：$log_file がありません");
if (!is_writable($log_file)) disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：$log_file に書き込み属性がありません");
$host_log = file($log_file);
#==================================================
#　アクセス制限
#==================================================
if(isset($_GET['mode']) and $_GET['mode'] == "ban") {
	list(,,,,,,$ipaddr) = explode('<>', $host_log[$_GET['id']]);
	$fp = fopen("../$_GET[bbs]/uerror.cgi", "a");
	fputs($fp, $ipaddr."\n");
	fclose($fp);
}
#アクセス制限リスト読み込み
if (is_file("../$_GET[bbs]/uerror.cgi")) $deny_array = file("../$_GET[bbs]/uerror.cgi");
else $deny_array = array();
#==================================================
#　ログ削除
#==================================================
if(isset($_GET['mode']) and $_GET['mode'] == "log_del") {
	if (isset($_GET['del']) and $_GET['del']) {
		foreach ($_GET['del'] as $del) unset($host_log[$del]);
	}
	$fp = fopen($log_file, "w");
	foreach ($host_log as $log) fputs($fp,$log);
	fclose($fp);
	$host_log = file($log_file);
}
#==================================================
#　ログ表示
#==================================================
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="main.css" type="text/css" />
<title>ホストログ管理</title>
</head>
<body>
<h1 class="title"><?php echo $SETTING['BBS_TITLE']?></h1>
<h3>ホストログ管理</h3>
<hr />
<p>ログを削除する場合は、削除したいログのチェックボックスをチェックして<b>削除</b>ボタンを押してください。<br />
<b>アク禁</b>をクリックするとその投稿者のIPアドレスが投稿拒否リストに追加されます。<br />
アク禁を解除する場合はIPアドレスを確認してメニューの<b>アク禁処理</b>から解除してください。<br />
</p>
<form name="form1" action="<?php echo $_SERVER['PHP_SELF']?>" method="GET">
<input type="hidden" name="bbs" value="<?php echo $_GET['bbs']?>" />
<input type="hidden" name="mode" value="log_del" />
<input type="submit" value="削除" />
<?php
if (!isset($_GET['page']) or !$_GET['page']) $_GET['page'] = 1;
echo "page：$_GET[page]<br />\n";
$st = ($_GET['page'] - 1) * $inum;
$num = count($host_log);
$total_page = (int)(($num+$inum-1)/$inum);
for ($i = 1; $i <= $total_page; $i++) {
	if ($i == $_GET['page']) echo " $i \n";
	else echo " <a class=\"item\" href=\"$_SERVER[PHP_SELF]?bbs=$_GET[bbs]&amp;page=$i\">$i</a> \n";
}
?>
<table border="1" cellspacing="0" cellpadding="2">
<?php
for ($i = $st; $i < $st + $inum; $i++) {
	if (!isset($host_log[$i])) break;
	list($name,$mail,$date,$comment,$subject,$host,$ipaddr) = explode('<>', $host_log[$i]);
	if (!$mail) $mail = '&nbsp;';
	if (!$subject) $subject = '&nbsp;';
	$comment = htmlspecialchars($comment);
	$deny_flag = '<a class="item" href="'.$_SERVER['PHP_SELF'].'?bbs='.$_GET['bbs'].'&amp;page='.$_GET['page'].'&amp;mode=ban&amp;id='.$i.'">アク禁</a>';
	foreach ($deny_array as $deny) {
		$deny = trim($deny);
		if (stristr($host, $deny)) {
			$host = str_replace($deny, '<font color="red"><b>'.$deny.'</b></font>', $host);
			$deny_flag = 'アク禁済';
			break;
		}
		if (stristr($ipaddr, $deny)) {
			$ipaddr = str_replace($deny, '<font color="red"><b>'.$deny.'</b></font>', $ipaddr);
			$deny_flag = 'アク禁済';
			break;
		}
	}
	?>
<tr>
	<td rowspan="2"><input type="checkbox" name="del[]" value="<?php echo $i++?>" /></td>
	<td rowspan="2" align="center"><?php echo $i--?></td>
	<td colspan="2">
		<font color="<?php echo $SETTING['BBS_NAME_COLOR']?>"><b><?php echo $name?></b></font>
		[<?php echo $mail?>] (<?php echo $date?>)　
		<font color="<?php echo $SETTING['BBS_SUBJECT_COLOR']?>"><?php echo $subject?></font>
	</td>
	<td rowspan="2"><?php echo $deny_flag?></td>
</tr>
<tr>
	<td><?php echo $comment?> </td>
	<td><?php echo $host?> (<?php echo $ipaddr?>)</td>
</tr>
<?php
}
?>
</table>
<?php
for ($i = 1; $i <= $total_page; $i++) {
	if ($i == $_GET['page']) echo " $i \n";
	else echo '<a class="item" href="' . $_SERVER[PHP_SELF] . '?bbs=' . $_GET[bbs] . '&amp;page=' . $i . '">$i</a>' . "\n";
}
?>
<br />
<input type="submit" value="削除" />
</form>
</body></html>
<?php
exit;
?>