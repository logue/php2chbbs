<?php
$version = "p.php ver1.3 (2005/03/28)";
$th_count = 5; // 1画面に表示するスレッドの数。
#==================================================
#　リクエスト解析
#==================================================
$url = preg_replace("/(.*)\/test\/.*/", "http://$_SERVER[HTTP_HOST]$1", $_SERVER['SCRIPT_NAME']);
$bbs = '';
$st = 1;
extract($_GET);
// PATH INFOからパラメータを取り出す。
if(!empty($_SERVER['PATH_INFO'])){
	$pairs = explode('/',$_SERVER['PATH_INFO']);
	$bbs = $pairs[1];
	if(!is_dir("../$bbs")) {echo("そんな板ないです。");exit;}
	$st = $pairs[2];
	if (!preg_match("/^\d+$/", $st)) {$st = 1;}
}
if (!is_file("../".$bbs."/subject.txt")) {echo("そんな板ないです。");exit;}
$th_titles = file("../".$bbs."/subject.txt");
$end = count($th_titles);
if ($st > $end) {$st = $end;}
$mae = $st - $th_count;
if ($mae <= 0) {$mae = 1;}
$tugi = $st + $th_count;
if ($tugi > $end + 1) {$tugi = $end + 1;}
?><HTML><HEAD><BASE href=<?=$url.'/test/r.php/'.$bbs?>/><TITLE><?=$bbs?> スレッド一覧</TITLE></HEAD><BODY><A href=../../p.php/<?=$bbs?>/<?=$mae?>>前</A> <A href=../../p.php/<?=$bbs?>/<?=$tugi?>>次</A><HR><?php
for ($i = $st; $i < $tugi; $i++) {
	list($id, $sub) = explode("<>", $th_titles[$i-1]);
	$id = str_replace(".dat", "", $id);
	echo $i,': <A href=',$id,'/>',$sub,'</A><BR>';
}
?><HR><A href=../../p.php/<?=$bbs?>/<?=$mae?>>前</A> <A href=../../p.php/<?=$bbs?>/<?=$tugi?>>次</A><HR><?=$version?></BODY></HTML>