<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$table_id = "user_master";      // Table Identifier
$parent_page = "user.php";

//ログインチェック
checkLogin();

$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}

$code = trim(filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING));

if ($code != ""){
    $query = "DELETE FROM $table_id WHERE code='".$code."'";
}
if ( $dbi->query($query) ){
    $message = "削除しました。";
}else {
    $message = "削除に失敗しました。設定を確認してください。";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="3;<?php echo $parent_page;?>" />
<meta name="robots" content="noindex">
<link href="style.css" rel="stylesheet" type="text/css" />
<title> 削除｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <h2><?php echo $message;?></h2>
    <input type="button" value="一覧ページに戻る" onclick="location.href='<?php echo $parent_page;?>'" />
</body>
</html>