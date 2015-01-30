<?php
# 0thello 処理
$fp = fopen($PATH.'0thello/'.$_POST['key'].'.dat', "r+");
flock($fp, LOCK_EX);
if ($_POST['subject']) {
	// スレ立てた人のトリップを登録
	if ($trip) fwrite($fp, $trip."\n");
	// トリップ付け忘れならデータファイル削除
	else {
		fclose($fp);
		unlink($PATH.'0thello/'.$_POST['key'].'.dat');
		DispError("ＥＲＲＯＲ！", "VIP-0THELLO: トリップをつけてください ");
	}
}
elseif ($trip and $white = fgets($fp, 1024) and strstr($_POST['FROM'], '!0thello')) {
	$white = rtrim($white);
	$game_board = array();
	$tmp_board = array();
	// 黒も登録されている
	$black = rtrim(fgets($fp, 1024));
	if ($black) {
		// 次の手番のトリップ
		list($game_count, $now) = fgetcsv($fp, 1024);
		if ($trip == $now) {
			// 盤面データの読み込み
			for ($i = 0; $i < 8; $i++) {
				$game_board[$i] = explode('｜', rtrim(fgets($fp, 1024)));
			}
			$game_count++;
			if (preg_match("/(.*)!0thello!x(\d+)!y(\d+)<\/b>◆/", $_POST['FROM'], $match)) {
				if ($match[2] < 1 or $match[2] > 9) DispError("ＥＲＲＯＲ！", "VIP-0THELLO: X軸を指定してください ");
				if ($match[3] < 1 or $match[3] > 9) DispError("ＥＲＲＯＲ！", "VIP-0THELLO: Y軸を指定してください ");
				$next = ($trip == $white) ? $black : $white;
				if ($next == $white) {
					$p1 = "●";
					$p2 = "○";
				}
				else {
					$p1 = "○";
					$p2 = "●";
				}
				$x = $match[2] - 1;
				$y = $match[3] - 1;
				if ($game_board[$y][$x] != "　") DispError ("ＥＲＲＯＲ！","VIP-0THELLO：そこに駒を置くことはできません");
				function reverse(&$board, $board2, $x, $y, $dx, $dy, $p1, $p2) {
					$count = 0;
					$temp = $board;
					$x += $dx;
					$y += $dy;
					while ($x >= 0 and $x < 8 and $y >= 0 and $y < 8 and $board2[$y][$x] == $p2) {
						$board[$y][$x] = $p1;
						$x += $dx;
						$y += $dy;
						$count++;
					}
					if ($count and $board2[$y][$x] != $p1) {
						$board = $temp;
						$count = 0;
					}
					return $count;
				}
				$tmp_board = $game_board;
				$reverse  = reverse($game_board, $tmp_board, $x, $y, -1, -1, $p1, $p2);
				$reverse += reverse($game_board, $tmp_board, $x, $y, -1, 0, $p1, $p2);
				$reverse += reverse($game_board, $tmp_board, $x, $y, -1, 1, $p1, $p2);
				$reverse += reverse($game_board, $tmp_board, $x, $y, 0, -1, $p1, $p2);
				$reverse += reverse($game_board, $tmp_board, $x, $y, 0, 1, $p1, $p2);
				$reverse += reverse($game_board, $tmp_board, $x, $y, 1, -1, $p1, $p2);
				$reverse += reverse($game_board, $tmp_board, $x, $y, 1, 0, $p1, $p2);
				$reverse += reverse($game_board, $tmp_board, $x, $y, 1, 1, $p1, $p2);
				if ($reverse == 0) DispError ("ＥＲＲＯＲ！","VIP-0THELLO：そこに駒を置くことはできません");
				$game_board[$y][$x] = $p1;
				fseek($fp, 0, SEEK_SET);
				fwrite($fp, $white."\n".$black."\n".$game_count.",".$next."\n");
				for ($i = 0; $i < 8; $i++) {
					fwrite($fp, implode('｜', $game_board[$i])."\n");
				}
				ftruncate($fp, ftell($fp));
				$_POST['MESSAGE'] .= '<pre>------------------------------------- <br> ';
				$_POST['MESSAGE'] .= '　８　７　６　５　４　３　２　１ <br> ';
				$_POST['MESSAGE'] .= '┌―┬―┬―┬―┬―┬―┬―┬―┐ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[0])).'｜一　　○先手(◆'.$white.') <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[1])).'｜二　　●後手(◆'.$black.') <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[2])).'｜三　　'.$p1.$game_count.": ".$match[2].", ".$match[3].' <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[3])).'｜四　　次は'.$p2.'の番です <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[4])).'｜五 <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[5])).'｜六 <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[6])).'｜七 <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[7])).'｜八 <br> ';
				$_POST['MESSAGE'] .= '└―┴―┴―┴―┴―┴―┴―┴―┘ </pre> ';
				$_POST['FROM'] = $match[1]."</b>◆".$trip."<b>";
			}
			// パス
			elseif (preg_match("/(.*)!0thello!pass<\/b>◆/", $_POST['FROM'], $match)) {
				fseek($fp, 0, SEEK_SET);
				fwrite($fp, $white."\n".$black."\n".$game_count.",".$next."\n");
				for ($i = 0; $i < 8; $i++) {
					fwrite($fp, implode('｜', $game_board[$i])."\n");
				}
				ftruncate($fp, ftell($fp));
				$_POST['MESSAGE'] .= '<pre>------------------------------------- <br> ';
				$_POST['MESSAGE'] .= '　８　７　６　５　４　３　２　１ <br> ';
				$_POST['MESSAGE'] .= '┌―┬―┬―┬―┬―┬―┬―┬―┐ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[0])).'｜一　　○先手(◆'.$white.') <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[1])).'｜二　　●後手(◆'.$black.') <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[2])).'｜三　　'.$p1.$game_count.': パス <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[3])).'｜四　　次は'.$p2.'の番です <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[4])).'｜五 <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[5])).'｜六 <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[6])).'｜七 <br> ';
				$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
				$_POST['MESSAGE'] .= '｜'.implode('｜', array_reverse($game_board[7])).'｜八 <br> ';
				$_POST['MESSAGE'] .= '└―┴―┴―┴―┴―┴―┴―┴―┘ </pre> ';
				$_POST['FROM'] = $match[1]."</b>◆".$trip."<b>";
			}
		}
	}
	// 白のみ登録の場合黒の登録と次の手番（白）を書き込み
	else {
		fwrite($fp, $trip."\n"."0,".$white."\n");
		// 盤の初期化
		for ($i = 0; $i < 8; $i++) {
			for ($j = 0; $j < 8; $j++) {
				$game_board[$i][$j] = '　';
			}
		}
		$game_board[3][3] = "●";
		$game_board[4][4] = "●";
		$game_board[3][4] = "○";
		$game_board[4][3] = "○";
		for ($i = 0; $i < 8; $i++) {
			fwrite($fp, implode('｜', $game_board[$i])."\n");
		}
		$_POST['MESSAGE'] .= '<pre>------------------------------------- <br> ';
		$_POST['MESSAGE'] .= '　８　７　６　５　４　３　２　１ <br> ';
		$_POST['MESSAGE'] .= '┌―┬―┬―┬―┬―┬―┬―┬―┐ <br> ';
		$_POST['MESSAGE'] .= '｜　｜　｜　｜　｜　｜　｜　｜　｜一　　○先手(◆'.$white.') <br> ';
		$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
		$_POST['MESSAGE'] .= '｜　｜　｜　｜　｜　｜　｜　｜　｜二　　●後手(◆'.$trip.') <br> ';
		$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
		$_POST['MESSAGE'] .= '｜　｜　｜　｜　｜　｜　｜　｜　｜三　　次は○の番です <br> ';
		$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
		$_POST['MESSAGE'] .= '｜　｜　｜　｜○｜●｜　｜　｜　｜四　　 <br> ';
		$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
		$_POST['MESSAGE'] .= '｜　｜　｜　｜●｜○｜　｜　｜　｜五 <br> ';
		$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
		$_POST['MESSAGE'] .= '｜　｜　｜　｜　｜　｜　｜　｜　｜六 <br> ';
		$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
		$_POST['MESSAGE'] .= '｜　｜　｜　｜　｜　｜　｜　｜　｜七 <br> ';
		$_POST['MESSAGE'] .= '├―┼―┼―┼―┼―┼―┼―┼―┤ <br> ';
		$_POST['MESSAGE'] .= '｜　｜　｜　｜　｜　｜　｜　｜　｜八 <br> ';
		$_POST['MESSAGE'] .= '└―┴―┴―┴―┴―┴―┴―┴―┘ </pre> ';
	}
}
fclose($fp);
$_POST['FROM'] = str_replace('!0thello', '', $_POST['FROM'])
?>
