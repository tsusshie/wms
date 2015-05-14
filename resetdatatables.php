<?php

include "./functions/common_inc.php";
include "./functions/dbclass.php";


//データベース準備
$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
} else {
    $dbi->select_db($DBNAME);
}

?>

<html>
<head>
<title>Reset Data Tables</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
</head>
<body>
    <div id="main">
<?php
$val = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING );
if ( $val == NULL){
    ?>
    <h2>データベース上の入出荷データおよび在庫データを初期化します。よろしいですか？</h2>
    <h3>なお、この操作により入荷、出荷　在庫などマスタデータ以外の全データが失われます。</h3>
    <table>
        <tr>
            <td><input type="button" value="OK" onclick="location.href='resetdatatables.php?action=yes'" /></td>
            <td><input type="button" value="HOME" onClick="location.href='index.html'"></td>
        </tr>
    </table>
    <?php
} else if ($val == "yes") {
/*    $ret = createDBifexists();
    if ($ret) {
        $dbi->select_db($DBNAME);
        echo "<h2>データベース ".$DBNAME." を初期化しました。</h2><br />";
    } else {
        echo "<h2>データベース ".$DBNAME." の初期化に失敗しました。設定を確認してください。</h2><br />";
        exit();
    }
*/ 

    $ret = resetreceivingtable();
    if ($ret) {
        echo "<h2>テーブル t_receiving を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル t_receiving の初期化に失敗しました。設定を確認してください。</h2><br />";
    }

    sleep(1);

    $ret = resetshippingtable();
    if ($ret) {
        echo "<h2>テーブル t_shipping_req を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル t_shipping_req の初期化に失敗しました。設定を確認してください。</h2><br />";
    }

    $ret = resetshippingItemtable();
    if ($ret) {
        echo "<h2>テーブル t_shipping_item を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル t_shipping_item の初期化に失敗しました。設定を確認してください。</h2><br />";
    }

    $ret = resetSalesOrdertable();
    if ($ret) {
        echo "<h2>テーブル t_sales_order を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル t_sales_order の初期化に失敗しました。設定を確認してください。</h2><br />";
    }

    $ret = resetSalesOrderItemtable();
    if ($ret) {
        echo "<h2>テーブル t_sales_order_item を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル t_sales_order_item の初期化に失敗しました。設定を確認してください。</h2><br />";
    }

    $ret = resetInventorytable();
    if ($ret) {
        echo "<h2>テーブル t_inventory を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル t_inventory の初期化に失敗しました。設定を確認してください。</h2><br />";
    }

    $ret = resetPurchaseOrdertable();
    if ($ret) {
        echo "<h2>テーブル t_purchaseorder を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル t_purchaseorder の初期化に失敗しました。設定を確認してください。</h2><br />";
    }


    echo "<a href='home.php'>HOMEへ戻る</a><br />";
}

            
?>
    </div>
</body>
</html>

<?php

$dbi->close();



function resetReceivingtable()
{
    global $dbi;
    $ret = FALSE;
    $query = "TRUNCATE TABLE t_receiving";
    if (!$dbi->query($query)){
        echo "RESET ERROR:".$dbi->errno."<br />";
    } else {
	$ret = TRUE;
    }
    return $ret;
}

function resetInventorytable()
{
    global $dbi;
    $ret = FALSE;
    $query = "TRUNCATE TABLE t_inventory";
    if (!$dbi->query($query)){
        echo "RESET ERROR:".$dbi->errno."<br />";
    } else {
	$ret = TRUE;
    }
    return $ret;
}


//   Warehouse Table Reset Function
function resetshippingtable()
{
    global $dbi;
    $ret = FALSE;
    $query = "TRUNCATE TABLE t_shipping";
    if (!$dbi->query($query)){
        echo "RESET ERROR:".$dbi->errno."<br />";
    } else {
	$ret = TRUE;
    }
    return $ret;
}

//   Warehouse Table Reset Function
function resetshippingItemtable()
{
    global $dbi;
    $ret = FALSE;
    $query = "TRUNCATE TABLE t_shipping_item";
    if (!$dbi->query($query)){
        echo "RESET ERROR:".$dbi->errno."<br />";
    } else {
	$ret = TRUE;
    }
    return $ret;
}

function resetSalesOrdertable()
{
    global $dbi;
    $ret = FALSE;
    $query = "TRUNCATE TABLE t_sales_order";
    if (!$dbi->query($query)){
        echo "RESET ERROR:".$dbi->errno."<br />";
    } else {
	$ret = TRUE;
    }
    return $ret;
}

function resetSalesOrderItemtable()
{
    global $dbi;
    $ret = FALSE;
    $query = "TRUNCATE TABLE t_sales_order_item";
    if (!$dbi->query($query)){
        echo "RESET ERROR:".$dbi->errno."<br />";
    } else {
	$ret = TRUE;
    }
    return $ret;
}


function resetPurchaseOrdertable()
{
    global $dbi;
    $ret = FALSE;
    $query = "TRUNCATE TABLE t_purchaseorder";
    if (!$dbi->query($query)){
        echo "RESET ERROR:".$dbi->errno."<br />";
    } else {
	$ret = TRUE;
    }
    return $ret;
}


?>