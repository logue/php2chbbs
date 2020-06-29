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
?><!DOCTYPE html><html><head><meta charset="UTF-8" /><base href="<?php echo $url.'/test/r.php/'.$bbs?>" /><title><?php echo $bbs?> スレッド一覧</title></head><body><a href=../../p.php/<?php echo $bbs?>/<?php echo $mae?>>前</a> <a href=../../p.php/<?php echo $bbs?>/<?php echo $tugi?>>次</a><hr /><?php
for ($i = $st; $i < $tugi; $i++) {
	list($id, $sub) = explode("<>", $th_titles[$i-1]);
	$id = str_replace(".dat", "", $id);
	echo $i,': <A href=',$id,'/>',$sub,'</A><BR>';
}
?><hr /><a href=../../p.php/<?php echo $bbs?>/<?php echo $mae?>>前</a> <a href=../../p.php/<?php echo $bbs?>/<?php echo $tugi?>>次</a><hr /><?php echo $version?></body></html>