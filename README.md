PHP 2ch風BBS（仮）
======================
2ch風掲示板のPHP実装。鏡の国とMitsのコードをマージし、Shift_JISだったものをUTF-8化したもの。ということになってます。

外部からのパラメータをそのまま変数として使ってるあたり使えたもんじゃありません。
良い子は、おとなしく[ぜろちゃんねるプラス](http://zerochplus.sourceforge.jp/)を使いましょう。

設置方法
------
adminディレクトリの名前は不正アクセス予防のため分かりにくい名前に変えた方が良いでしょう。
ファイル名は変えないでください。
```
php2ch(755)/┬admin(755)/┬admin.php(644) 
            │           ├abon.php(644)
            │           ├cap.php(644)
            │           ├deleboard.php(644)
            │           ├deny.php(644)
            │           ├edit.php(644)
            │           ├hostlog.php(644)
            │           ├image.php(644)
            │           ├main.php(644)
            │           ├makeboard.php(644)
            │           ├menu.php(644)
            │           ├passcheck.php(644)
            │           ├setboard.php(644)
            │           ├setboard2.php(644)
            │           ├threadm.php(644)
            │           ├vip.php(644)
            │           ├main.css(644)
            │           ├menu.css(644)
            │           └passfile.cgi(666)
            │
            ├test(755)/ ┬bbs.php(644)
            │           ├bbs2.php(644)
            │           ├read.php(644)
            │           ├make_html.php(644)
            │           ├make_work.php(644)
            │           ├new_thread.php(644)
            │           ├config_r.php(644)
            │           ├b.php(644)
            │           ├p.php(644)
            │           ├r.php(644)
            │           ├index.txt(644)
            │           ├form.txt(644)
            │           ├caps.cgi(666)
            │           ├headad.txt(666)
            │           ├putad.txt(666)
            │           ├option.txt(666)
            │           └omikuji(755)/┬omikuji.txt(644)
            │                          ├base.txt(644)
            │                          ├who.txt(644)
            │                          ├where.txt(644)
            │                          ├do.txt(644)
            │                          └food.txt(644)
            │
            ├2ch.gif(644)
            ├ba.gif(644)
            │
            └bbs(755)/┬dat(777)/
                       ├html(777)/
                       ├kako(777)/┬index.php(644)
                       │          └kako.txt(666)
                       ├i(755)/─index.html(666)
                       ├img(777)/
                       ├img2(777)/
                       ├0thello(777)/
                       ├NGWord.cgi(644)
                       ├SETTING.TXT(666)
                       ├config.php(666)
                       ├subject.txt(666)
                       ├subback.html(666)
                       ├index.html(666)
                       ├head.txt(666)
                       ├RIP.cgi(666)
                       ├timecheck.cgi(666)
                       ├hostlog.cgi(666)
                       ├uerror.cgi(666)
                       └threadconf.cgi(666)
```
bbsディレクトリは自分の好きな名前に変更します。これが掲示板のディレクトリ名になります。
同様にして掲示板を複数設置できます（普通は1個で十分ですが）。
設置したらadmin/admin.phpにアクセスして、パスワードの設定と板の設定変更画面からindex.htmlの作り直しをしてください。

今後の予定
--------
* 自己満足以外の何物でもないオブジェクト思考化
* 動作が重くなるZend2導入
* 無駄にi18n対応
* 流行だからってBootstrap準拠
* 実用に乏しいajax対応
* せめてもの抵抗CAPTCHA対応

ライセンス
--------
[好きにしやがれ、ボケがパブリックライセンス](WTFPL "http://www.wtfpl.net/)

関連情報
--------
1. [オリジナル版配布元](http://script.s16.xrea.com/ "鏡の国")
2. [Mits氏改造版](http://www.mits-jp.com/2ch/)
3. [ぜろちゃんねるプラス](http://zerochplus.sourceforge.jp/)