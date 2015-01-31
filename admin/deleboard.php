<?php
require("passcheck.php");
#=============================================
if (!$_GET['bbs']) disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：板名をいれてちょ。。。");
if (!is_dir("../$_GET[bbs]")) disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：そんな板ないです。");
#====================================================
#　初期情報の取得（設定ファイル）
#====================================================
#設定ファイルを読む
$set_pass = "../$_GET[bbs]/SETTING.TXT";
if (is_file($set_pass)) {
	$set_str = file($set_pass);
	foreach ($set_str as $tmp){
		$tmp = rtrim($tmp);
		list($name, $value) = explode("=", $tmp);
		$SETTING[$name] = $value;
	}
}
else disperror("ＥＲＲＯＲ！","ＥＲＲＯＲ：ユーザー設定が消失しています！");
#=====================================
#　板まるごとあぼーん
#=====================================
if(isset($_GET['del']) and $_GET['del'] == 'ok') {
	$handle = opendir("../$_GET[bbs]");
	while (false !== ($file = readdir($handle))) { 
		if($file != '.' and $file != '..') {
			if (is_dir("../$_GET[bbs]/$file")) {
				$handle2 = opendir("../$_GET[bbs]/$file");
				while (false !== ($file2 = readdir($handle2))) { 
					if ($file2 != '.' and $file2 != '..') @unlink("../$_GET[bbs]/$file/$file2");
				}
				closedir($handle2);
				@rmdir("../$_GET[bbs]/$file");
			}
			else @unlink("../$_GET[bbs]/$file");
		}
	}
	closedir($handle);
	@rmdir("../$_GET[bbs]");
	?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>掲示板閉鎖</title>
<link rel="stylesheet" href="main.css" type="text/css" />
</head>
<body>
<h1 class="title"><?php echo $SETTING['BBS_TITLE']?></h1>
<h3>掲示板閉鎖</h3>
<hr />
削除しました<br />
<a class="item" href="admin.php?bbs=<?php echo $_GET['bbs']?>" target="_parent">ここ</a>からメニューの更新をしてください。
</body>
</html><?php
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>掲示板閉鎖</title>
<link rel="stylesheet" href="main.css" type="text/css" />
</head>
<body>
<h1 class="title"><?php echo $SETTING['BBS_TITLE']?></h1>
<h3>掲示板閉鎖</h3>
<hr />
ディレクトリ<q><?php echo $_GET['bbs']?></q>以下のファイルを全て削除します。<br />
<br />
<a class="item" href="<?php echo $_SERVER['PHP_SELF']?>?del=ok&amp;bbs=<?php echo $_GET['bbs']?>">削除</a>
<a class="item" href="admin.php?bbs=<?php echo $_GET['bbs']?>" target="_parent">やめる</a>
</body></html>
