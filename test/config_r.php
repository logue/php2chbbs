<?php
# read.php用　設定ファイル（"SETTING.TXT"以外の設定項目）
# gzip圧縮をする（TRUE=する、FALSE=しない）広告が自動で挿入されるサーバでは使えません
define('GZ_FLAG', FALSE);
# 時間規制する（TRUE=する、FALSE=しない）
define('JIKAN_KISEI', FALSE);
# 規制開始時間（0-23）
define('JIKAN_START', 22);
# 規制終了時間（0-23）
define('JIKAN_END', 2);
?>