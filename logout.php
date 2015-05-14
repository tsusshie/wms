<?php
// セッションの初期化
session_start();

// セッション変数を全て解除する
$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// 最終的に、セッションを破壊する
session_destroy();
?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ログアウト</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link href="style.css" rel="stylesheet" type="text/css" />

</head>
<body>

<div id="login">
<h2 class="title">ログアウト完了 </h2>

<p class="logout"><a href="index.html">&rarr;ログイン画面</a></p>
</div>

</body>
</html>