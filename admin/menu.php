<?php
require("passcheck.php");
if (!isset($_GET['bbs'])) $_GET['bbs'] = '';
#=====================================
#　管理メニュー
#=====================================
$board = array();
$handle = opendir('../');
while (false !== ($file = readdir($handle))) { 
    if($file != 'admin' and $file != 'test' and $file != '.' and $file != '..') {
    	if (is_dir("../$file") and is_file("../$file/SETTING.TXT") and !is_file("../$file/admin.php")) array_push($board, $file);
    }
}
closedir($handle);
$make_board = '';
#if (!ini_get("safe_mode")) $make_board = '<a class="menu" href="makeboard.php"><b>掲示板作成</b></a><br>'."\n<hr>\n";
if (isset($_GET['bbs'])and $_GET['bbs']) $func = "func('$_GET[bbs]');";
else $func = '';
?>
<html>
<head>
<meta chaset="UTF-8" />
<title>管理メニュー</title>
<link rel="stylesheet" href="menu.css" type="text/css" />
<script type="text/javascript">/* <![CDATA[ */
function init() {
  if (!document.getElementsByTagName) { return; }
  var objs = document.getElementsByTagName("div");
  for (i = 0; i < objs.length; i++) {
    if (objs[i].className == "titem") {
      objs[i].style.display = "none";
    }
  }
}
function func(id) {
  if (!document.getElementsByTagName) { return false; }
  var obj = document.getElementById(id);
  if (obj.style.display == "block") {
    obj.style.display = "none";
  } else {
    obj.style.display = "block";
  }
  return false;
}
/* ]]> */</script>
<base target="main">
</head>
<body onload="init();<?php echo $func; ?>">
<div class="menu"><b>管理メニュー</b></div>
<hr />
<a class="menu" href="main.php"><b>トップ</b></a><br>
<hr />
<?php echo $make_board; ?>
<a class="menu" href="cap.php"><b>キャップ管理</b></a><br>
<hr />
<?php
$i = 0;
foreach ($board as $dir) {
	#====================================================
	#　初期情報の取得（設定ファイル）
	#====================================================
	#設定ファイルを読む
	$set_pass = "../$dir/SETTING.TXT";
	$set_str = file($set_pass);
	foreach ($set_str as $tmp){
		$tmp = chop($tmp);
		list ($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
	$i++;
?>
<a class="title" href="../<?php echo $dir; ?>/"><b><?php echo $SETTING['BBS_TITLE']; ?></b></a><br>
<a class="dir" href="#" target="menu" onclick="return func('<?php echo $dir; ?>')">ディレクトリ：<?php echo $dir; ?></a><br>
<div class="titem" id="<?php echo $dir; ?>">
  <ul>
    <li><a class="item" href="setboard.php?bbs=<?php echo $dir; ?>"> 設定変更 </a></li>
    <li><a class="item" href="setboard2.php?bbs=<?php echo $dir; ?>"> VIP設定変更 </a></li>
    <li><a class="item" href="vip.php?bbs=<?php echo $dir; ?>"> VIP機能変更 </a></li>
    <li><a class="item" href="abon.php?bbs=<?php echo $dir; ?>"> あぼーん/スレスト </a></li>
    <li><a class="item" href="threadm.php?bbs=<?php echo $dir; ?>"> スレッド削除/移動 </a></li>
    <li><a class="item" href="deleboard.php?bbs=<?php echo $dir; ?>"> 掲示板閉鎖 </a></li>
    <li><a class="item" href="image.php?bbs=<?php echo $dir; ?>"> 画像削除 </a></li>
    <li><a class="item" href="hostlog.php?bbs=<?php echo $dir; ?>"> ホストログ管理 </a></li>
    <li><a class="item" href="deny.php?bbs=<?php echo $dir; ?>"> アク禁処理 </a></li>
    <li><a class="item" href="edit.php?bbs=<?php echo $dir; ?>"> テキスト編集 </a></li>
    <li><a class="item" href="makeboard.php?mode=remake&bbs=<?php echo $dir; ?>">index.htmlを作り直す</a></li>
  </ul>
</div>
<hr class="sub">
<?php
}
?>
</body></html>
