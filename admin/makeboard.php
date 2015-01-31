<?php
require 'passcheck.php';
#====================================================
#　各種ＰＡＴＨ生成
#====================================================
if (!isset($_GET['bbs'])) $_GET['bbs'] = '';
if (!isset($_GET['mode'])) $_GET['mode'] = '';
$PATH = "../" . $_GET['bbs'] . '/';
$DATPATH = $PATH . "dat/";
$TEMPPATH = $PATH . "html/";
$INDEXFILE = $PATH . "index.html";
$SUBFILE = $PATH . "subback.html";
$IMODEFILE = $PATH."i/index.html";
if ($_GET['bbs']) require $PATH.'config.php';
#====================================================
#　掲示板新規作成
#====================================================
if ($_GET['mode'] == 'make') {
/*
	if (is_dir($PATH)) disperror("ＥＲＲＯＲ！", "同じ名前のディレクトリが存在します");
	mkdir($PATH, 0755);
	mkdir($DATPATH, 0755);
	mkdir($TEMPPATH, 0755);
	mkdir($PATH."kako", 0755);
	mkdir($PATH."temp", 0755);
	mkdir($PATH."i", 0755);
	mkdir($PATH."img", 0755);
	mkdir($PATH."img2", 0755);
	mkdir($PATH."0thello", 0755);
	copy("SETTING.TXT", $PATH."SETTING.TXT");
	copy("kako_index.txt", $PATH."kako/index.php");
	copy("config.php", $PATH."config.php");
	touch($PATH."kako/kako.txt");
	touch($PATH."head.txt");
	touch($PATH."subject.txt");
	touch($PATH."hostlog.cgi");
	touch($PATH."uerror.cgi");
	touch($PATH."timecheck.cgi");
	touch($PATH."RIP.cgi");
	touch($PATH."threadconf.cgi");
	header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/setboard.php?bbs=$_GET[bbs]");
	exit;
*/
}
#====================================================
#　index.htmlを作り直す
#====================================================
elseif ($_GET['mode'] == 'remake') {
	#====================================================
	#　初期情報の取得（設定ファイル）
	#====================================================
	#設定ファイルを読む
	$set_pass = $PATH . "SETTING.TXT";
	if (is_file($set_pass)) {
		$set_str = file($set_pass);
		foreach ($set_str as $tmp){
			$tmp = trim($tmp);
			list ($name, $value) = explode("=", $tmp);
			$SETTING[$name] = $value;
		}
	}
	else disperror("ＥＲＲＯＲ！", "ＥＲＲＯＲ：ユーザー設定が消失しています！");
	if (!is_dir($PATH)) disperror("ＥＲＲＯＲ！","そんな板ないです！");
	if (isset($_GET['check']) and $_GET['check'] == 'check') {
		# サブジェクトファイルを読み込む
		$PAGEFILE = array();
		$subjectfile = $PATH."subject.txt";
		$subr = @file($subjectfile);
		$subbak = '';
		if ($subr) {
			foreach($subr as $tmp){
				$tmp = chop($tmp);
				list ($file, $value) = explode("<>", $tmp);
				if (!$file) break;
				$filename = "$DATPATH/$file";
				if (is_file($filename)){ # datが存在する場合順番に追加
					array_push($PAGEFILE,$file);
					$subbak .= $tmp."\n";
					$SUBJECT[$file] = $value;
				}
			}
			$fp = fopen($PATH."subject.txt", "w");
			fputs($fp, $subbak);
			fclose($fp);
		}
		$NOWTIME = time();
		if (GD_VERSION) {
			$enctype = 'multipart/form-data';
			$file_form = '<input type="file" name="file" size="50" /><br>';
		}
		else {
			$enctype = 'application/x-www-form-urlencoded';
			$file_form = '';
		}
		if ($SETTING['BBS_TITLE_PICTURE']) $bbs_title = '<a href="'.$SETTING['BBS_TITLE_LINK'].'"><img src="'.$SETTING['BBS_TITLE_PICTURE'].'" border="0"></a>';
		else $bbs_title = '<h1><font color="'.$SETTING['BBS_TITLE_COLOR'].'">'.$SETTING['BBS_TITLE'].'</font></h1>';
		$set_cookie = '';
		$HOST = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		require('../test/make_html.php');
		exit;
	}
	else {
		?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>index.html作成</title>
<link rel="stylesheet" href="main.css" type="text/css" />
</head>
<body>
<h1 class="title"><?php echo $SETTING['BBS_TITLE']?></h1>
<h3>index.html作成</h3>
<hr />
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="GET">
<input type="hidden" name="mode" value="remake" />
<input type="hidden" name="bbs" value="<?php echo $_GET['bbs']?>" />
<input type="hidden" name="check" value="check" />
<p>index.htmlを作り直します。<br />
よろしいですか。</p>
<input type="submit" value="作り直す" />
</form>
</body></html>
<?php
		exit;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>掲示板作成</title>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" href="main.css" type="text/css">
</head>
<body>
<h3>掲示板作成</h3>
<hr>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
<input type="hidden" name="mode" value="make">
<p>新しい掲示板のディレクトリ名を決めてください。<br />
（エラーが出たり、ファイルが作れないときは、手動でファイルをアップしてください。）</p>
<input type="text" name="bbs" size="10" />
<input type="submit" value="決定" />
</form>
</body></html>
