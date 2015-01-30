<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<script type="text/javascript">
<!--
NameMail = "<input type=text name=FROM size=19 value='" + getCookie("NAME") + "'> E-mail：<input type=text name=mail size=19 value='" + getCookie("MAIL") + "'>";
function getCookie(key, tmp1, tmp2, xx1, xx2, xx3) {
	tmp1 = " " + document.cookie + ";";
	while(tmp1.match(/\+/)) {
		tmp1 = tmp1.replace("+", " ");
	}
	xx1 = xx2 = 0;
	len = tmp1.length;
	while (xx1 < len) {
		xx2 = tmp1.indexOf(";", xx1);
		tmp2 = tmp1.substring(xx1 + 1, xx2);
		xx3 = tmp2.indexOf("=");
		if (tmp2.substring(0, xx3) == key) {
			return(unescape(tmp2.substring(xx3 + 1, xx2 - xx1 - 1)));
		}
		xx1 = xx2 + 1;
	}
	return("");
}
// -->
</script>
<title><?=$SETTING['BBS_TITLE']?></title>
</head>
<body text="#000000" link="#0000FF" alink="#FF0000" vlink="#660099" bgcolor="<?=$SETTING['BBS_BG_COLOR']?>" background="<?=$SETTING['BBS_BG_PICTURE']?>">
<div align="center"><?=$bbs_title?></div><br>
<table border="1" cellspacing="7" cellpadding="3" width="95%" bgcolor="<?=$SETTING['BBS_MAKETHREAD_COLOR']?>" align="center">
<tr><td><form method="post" action="./bbs.php" enctype="<?=$enctype?>">
  <table border="0" cellpadding="1" width="100%">
  <tr><td nowrap colspan="3"><font size="+1"><b><?=$SETTING['BBS_TITLE']?></b></font><br></td></tr>
  <tr><td colspan="4"><?php readfile($PATH."head.txt"); ?><br></td></tr>
  <tr><td nowrap align="right">タイトル：</td><td><input type="text" name="subject" size="40"></td><td><input type="submit" value="新規スレッド作成" name="submit"></td></tr>
  <tr>
    <td nowrap align="right">名前：</td>
    <td nowrap colspan="2">
      <script type="text/javascript">
      <!--
      document.write(NameMail);
      // -->
      </script>
      <noscript>
      <input type=text name=FROM size=19> E-mail：<input type=text name=mail size=19>
      </noscript>
    </td>
  </tr>
  <tr>
    <td nowrap align="right" valign="top">内容：</td>
    <td colspan="3">
    <textarea rows="5" cols="60" wrap="off" name="MESSAGE"></textarea><br>
    <?=$file_form?>
    <input type="hidden" name="bbs" value="<?=$_POST['bbs']?>">
    <input type="hidden" name="time" value="<?=$NOWTIME?>">
    </td>
  </tr>
  </table>
</form></td></tr>
</table>
</body>
</html>
