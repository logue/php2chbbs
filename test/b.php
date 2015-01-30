<?php
#　スレ立て用フォーム
$version = "b.php ver1.2 (2004/06/25)";
#==================================================
#　リクエスト解析
#==================================================
# PATH INFOからパラメータを取り出す。
if(!isset($_SERVER['PATH_INFO']) or !$_SERVER['PATH_INFO']){echo("ERR - $version");exit;}
$buffer = $_SERVER['PATH_INFO'];
$pairs = explode('/',$buffer);
$bbs = $pairs[1];
if (!is_dir("../$bbs")) { echo("ERR - $version");exit; }
#==================================================
#　フォーム出力
#==================================================
?>
<body><form method=POST action="../../bbs.php">ﾀｲﾄﾙ<input name=subject>NAME：<input name=FROM>MAIL：<input name=mail istyle=3><input type=hidden name=bbs value=<?=$bbs?>><input type=hidden name=time value=<?=time()?>><textarea name=MESSAGE></textarea><input type=submit value="かきこむ" name=submit></form><br><?=$version?></body>
