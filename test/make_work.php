<?php
#====================================================
#　ファイル操作（ＨＴＭＬ作成用作業ファイル更新）
#====================================================
#MakeWorkFile(KEY-NUMBER)
function MakeWorkFile($bbs, $key, $outdat="") {
	global $SETTING;
	$dattemp = "../$bbs/dat/$key.dat";
	$workfile = "../$bbs/html/$key.html";
	if (is_file($dattemp)) {
		$logopen = file($dattemp);
		$lognum = count($logopen);
		# 最後のレスの日付欄を取得（スレスト、Over threadチェックのため）
		list(,,$tmp) = explode("<>", end($logopen));
	}
	else {
		$logopen = array();
		$lognum = 0;
		$tmp = '';
	}
	# 書込み禁止で無い場合
	clearstatcache();
	if (is_writable($dattemp) or !is_file($dattemp)) {
		$fp = fopen($dattemp, "a");
		flock($fp, 2);
		if (!preg_match("/Over \d+ Thread|停止/", $tmp)) {
			if ($outdat and $lognum < THREAD_RES) {
				fputs($fp, $outdat);
				array_push($logopen, $outdat);
				$lognum++;
			}
			$stop = 0;
		}
		else $stop = 1;
		# １０００(THREAD_RES)オーバーの書きこみ禁止
		if ($lognum >= THREAD_RES) {
			if (!$stop) {
				# 全角数字に変更
				$maxnum = mb_convert_kana(THREAD_RES, "N", "SJIS");
				$maxplus = mb_convert_kana(++$lognum, "N", "SJIS");
				$maxmsg = "このスレッドは${maxnum}を超えました。 <br /> もう書けないので、新しいスレッドを立ててくださいです。。。 ";
				if (THREAD_MAX_MSG) {
					$maxmsg = str_replace('<NUM>', $maxnum, THREAD_MAX_MSG);
				}
				fputs($fp, "$maxplus<><>Over ".THREAD_RES." Thread<>$maxmsg<>\n");
				array_push($logopen, "$maxplus<><>Over ".THREAD_RES." Thread<>$maxmsg<>\n");
				$stop = 1;
			}
		}
		fclose($fp);
		if ($stop) chmod($dattemp, 0444);
	}
	# 1さんを取り出し
	$logfirst = array_shift($logopen);
	# 表示するレス数だけ取り出し
	$logopen = array_slice($logopen, -$SETTING['BBS_CONTENTS_NUMBER']);
	# 1の次に表示するレス番号
	$topnum = $lognum - count($logopen) + 1;
	#１つ目の要素を加工する
	$logfirst = rtrim($logfirst);
	list ($name,$mail,$date,$message,$subject) = explode ("<>", $logfirst);
	$logsub = $subject;
	#サブジェクトテーブルを吐き出す（ここは必ず１行にまとめること（処理効率））
	$logall = '<table border="1" cellspacing="7" cellpadding="3" width="95%" bgcolor="'.$SETTING['BBS_THREAD_COLOR'].'" align="center"><tr><td><dl><a name="$ANCOR"></a><div align="right"><a href="#menu">■</a><a href="#$FRONT">▲</a><a href="#$NEXT">▼</a></div><b>【$ANCOR:'.$lognum.'】<font size="5" color="'.$SETTING['BBS_SUBJECT_COLOR']."\">$subject</font></b>\n";
	#１つ目のリンクを作成
	$message = preg_replace("/(https?):\/\/([\w;\/\?:\@&=\+\$,\-\.!~\*'\(\)%#]+)/", "<a href=\"$1://$2\" target=\"_blank\">$1://$2</a>", $message);
	#名前欄の変換
	if ($mail) $mailto = "<a href=\"mailto:$mail \"><b>$name </b></a>";
	else $mailto = "<font color=\"$SETTING[BBS_NAME_COLOR]\"><b>$name </b></font>";
	#１つ目の要素を吐き出す
	$logall .= " <dt>1 名前：$mailto $date<dd>$message <br /><br /><br />\n";
	#残りのログを表示する
	foreach ($logopen as $tmp){
		#要素を加工する
		$tmp = rtrim($tmp);
		list ($name,$mail,$date,$message,$subject) = explode ("<>", $tmp);
		#リンクを作成
		$message = preg_replace("/(https?):\/\/([\w;\/\?:\@&=\+\$,\-\.!~\*'\(\)%#]+)/", "<a href=\"$1://$2\" target=\"_blank\">$1://$2</a>", $message);
		#名前欄の変換
		if ($mail) $mailto = "<a href=\"mailto:$mail \"><b>$name </b></a>";
		else $mailto = "<font color=\"$SETTING[BBS_NAME_COLOR]\"><b>$name </b></font>";
		#要素を吐き出す
		$logall .= " <dt>$topnum 名前：$mailto ：$date<dd>";
		// 0thelloスレッドは全部表示
		if ($GLOBALS['vip'][8]) $logall .= $message;
		else {
			$messx = explode ("<br />", $message);
			for ($i = 1; $i <= $SETTING['BBS_LINE_NUMBER']; $i++) {
				if ($messx) {
					$logall .= array_shift($messx);
					$logall .= "<br />";
				}
			}
			if ($messx) {
				$logall .= '<p style="color:' . $SETTING[BBS_NAME_COLOR] . '">（省略されました・・全てを読むには<a href="../test/read.php/' . $_POST['bbs'] . '/' . $key . '/' . $topnum . '" target="_blank">ここ</a>を押してください）</p>';
			}
		}
		$logall .= "<br />\n";
		$topnum++;
	}
	$fp = fopen($workfile, "w");
	fputs($fp, $logall);
	fclose($fp);
	return "$logsub ($lognum)";
}
?>
