<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

//ログインチェック
checkLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);



$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
</head>

<body>

<?php
$que = "DELETE FROM t_item_convert WHERE id='".  filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT)."'";
if (!$dbi->query( $que )){
    echo "レコードの削除に失敗しました。<br />";
    echo "<a href='item_conversion.php>変換テーブル - トップ</a>";
}else{
    header("location:item_conversion.php");
}
?>

</body>
</html>