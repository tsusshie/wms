<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

//ログインチェック
checkLogin();

// RETURN PAGE
$base_url = "receiving.php";

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$table = filter_input(INPUT_GET, 'table', FILTER_SANITIZE_STRING);
if ($id == NULL || $table == NULL ) {
    header("location:home.php");
    exit();
}

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
<title>在庫管理｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
</head>

<body>

<?php
if ($id == "zero" && $table == "t_inventory") {
    $que = "DELETE FROM $table WHERE item_qty=0";
}else {
    $que = "DELETE FROM $table WHERE order_code='". $id."'";
}
if (!$dbi->query( $que )){
    echo "レコードの削除に失敗しました。<br />";
    echo "<input type='button' name='delete' value='戻る' onclick='history.back();' />";
}else{
    header("location:".$base_url );
}
?>

</body>
</html>