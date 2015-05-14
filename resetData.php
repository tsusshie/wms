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
<title>Reset All Tables</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
</head>
<body>

<?php
$val = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING );
if ( $val == NULL){
    ?>
    <h2>データベースを初期化します。よろしいですか？</h2>
    <table>
        <tr>
            <td><input type="button" value="OK" onclick="location.href='resetData.php?action=yes'" /></td>
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
/*
    $ret = resetitemtable();
    if ($ret) {
        echo "<h2>テーブル item_master を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル item_master の初期化に失敗しました。設定を確認してください。</h2><br />";
    }
    
    $ret = resetusertable();
    if ($ret) {
        echo "<h2>テーブル user_master を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル user_master の初期化に失敗しました。設定を確認してください。</h2><br />";
    }
    
    $ret = resetwarehousetable();
    if ($ret) {
        echo "<h2>テーブル warehouse_master を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル warehouse_master の初期化に失敗しました。設定を確認してください。</h2><br />";
    }
 
    $ret = resetclienttable();
    if ($ret) {
        echo "<h2>テーブル client_master を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル client_master の初期化に失敗しました。設定を確認してください。</h2><br />";
    }

    $ret = resetitem_statustable();
    if ($ret) {
        echo "<h2>テーブル status_master を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル status_master の初期化に失敗しました。設定を確認してください。</h2><br />";
    }
    
    $ret = resettypetable();
    if ($ret) {
        echo "<h2>テーブル type_master を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル type_master の初期化に失敗しました。設定を確認してください。</h2><br />";
    }
    
    $ret = resetroletable();
    if ($ret) {
        echo "<h2>テーブル role_master を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル role_master の初期化に失敗しました。設定を確認してください。</h2><br />";
    }
    
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

    $ret = resetitem_converttable();
    if ($ret) {
        echo "<h2>テーブル t_item_convert を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル t_item_convert の初期化に失敗しました。設定を確認してください。</h2><br />";
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
*/
    $ret = resetInventorytable();
    if ($ret) {
        echo "<h2>テーブル resetInventorytable を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル resetInventorytable の初期化に失敗しました。設定を確認してください。</h2><br />";
    }

    echo "<a href='home.php'>HOMEへ戻る</a><br />";
}

            
?>
</body>
</html>

<?php

$dbi->close();

// Create Database on Server
function createDBifexists()
{
    global $dbi, $DBNAME;
    $ret = FALSE;
    $query = "DROP DATABASE IF EXISTS ".$DBNAME;
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        $ret = FALSE;
    }
    
    $query = "CREATE DATABASE IF NOT EXISTS ".$DBNAME." CHARACTER SET utf8";
    if (!$dbi->query($query)){
        echo "CREATE ERROR:".$dbi->errno."<br />";
        $ret = FALSE;
    } else {
        $ret = TRUE;
    }
    return $ret;
}

//===============================
//   Item Table Reset Function
//    Create or Remake all tables.(Call ONLY when just installed)
//===============================
function resetitemtable()
{
    global $dbi;
    $query = "DROP TABLE IF EXISTS item_master";
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        $ret = FALSE;
    }
     
    $query = "CREATE TABLE item_master (
        id              int not null auto_increment,
        code		varchar(12) NOT NULL,
        name		varchar(128) NOT NULL,
        remark		text,
        status              int,
        udate		datetime,
        PRIMARY KEY (id)
    )";
    if ( $dbi->query($query)){
        $ret = TRUE;
    } else{
        $ret = FALSE;
    }
    echo "CREATE ERROR :".$dbi->errno."<br />";
    return $ret;
}

//   User Table Reset Function
function resetusertable()
{
    global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS user_master";
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        return $ret;
    }

    $query = "CREATE TABLE user_master (
        code	varchar(12) NOT NULL,
        name	varchar(64) NOT NULL,
        password	varchar(16),
        role	int,
        status  int,  
        udate	datetime,
        PRIMARY KEY (code)
    )";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
    return $ret;
}

//   Warehouse Table Reset Function
function resetwarehousetable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS warehouse_master";
    if (!$dbi->query($query)){
        return $ret;
    }
        
    $query = "CREATE TABLE warehouse_master (
        id	int NOT NULL AUTO_INCREMENT,
        name	varchar(64) not null,
        address	text,
        remark	text,
        udate	datetime,
        PRIMARY KEY (id)
    )";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
	return $ret;
}

//   Client Table Reset Function
function resetclienttable()
{
    global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS client_master";
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        return $ret;
    }
    $query = "CREATE TABLE client_master (
        id	int NOT NULL AUTO_INCREMENT,
        name	varchar(64) not null,
        address	text,
        tel	varchar(16),
        fax	varchar(16),
        type    int,
        remark	text,
        udate	datetime,
        PRIMARY KEY (id)
    ) ";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
    return $ret;
}



function resetReceivingtable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS t_receiving";
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        return $ret;
    }
        
    $query = "CREATE TABLE t_receiving (
        receiving_code    VARCHAR(12),
        item_code       VARCHAR(12) not null,
        item_qty	INT,
        expire          DATE,
        wh_id           INT,
        location        VARCHAR(12),
        importer_id	INT,
        user            VARCHAR(64),
        udate           datetime,
        PRIMARY KEY (receiving_code)
    )";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
	return $ret;
}

function resetInventorytable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS t_inventory";
    if (!$dbi->query($query)){
        return $ret;
    }
    $query = "CREATE TABLE t_inventory (
        id              int NOT NULL AUTO_INCREMENT,
        item_code	varchar(12) NOT NULL,
        wh_id           int,
        location        varchar(12),
        item_qty        int,
        expire          DATE,
        udate          DATE,
        PRIMARY KEY (id)
    ) ";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
    return $ret;
}

function resetitem_converttable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS t_item_convert";
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        return $ret;
    }
        
    $query = "CREATE TABLE t_item_convert (
        id              INT NOT NULL AUTO_INCREMENT,
        client_id       INT NOT NULL,
        item_code	VARCHAR(12) NOT NULL,
        client_item_code       VARCHAR(12) NOT NULL,
        client_item_name        VARCHAR(128) NOT NULL,
        item_count      INT NOT NULL,
        label_code      VARCHAR(16),
        remark      text,
        PRIMARY KEY (id)
    ) ";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
    return $ret;
}


//   Warehouse Table Reset Function
function resetshippingtable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS t_shipping";
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        return $ret;
    }
    $query = "CREATE TABLE t_shipping (
        id	int NOT NULL AUTO_INCREMENT,
        shipping_code	varchar(12) NOT NULL,
        client_master_id     int,
        wh_id   int,
        user        varchar(64),
        remark      text,
        udate       datetime,
        PRIMARY KEY (id)
    )";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
	return $ret;
}
//   Warehouse Table Reset Function
function resetshippingItemtable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS t_shipping_item";
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        return $ret;
    }
        
    $query = "CREATE TABLE t_shipping_item (
        id                      int NOT NULL AUTO_INCREMENT,
        shipping_req_id         int NOT NULL,
        inventory_id            int NOT NULL,
        item_qty                int,
        PRIMARY KEY (id)
    ) ";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
	return $ret;
}

function resetSalesOrdertable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS t_sales_order";
    if (!$dbi->query($query)){
        return $ret;
    }
    $query = "CREATE TABLE t_sales_order (
        order_id	varchar(12) NOT NULL,
        client_id	int NOT NULL,
        remark          text,
        wh_id           int,
        user            varchar(64),
        done            boolean,
        udate           datetime,
        PRIMARY KEY (order_id)
    ) ";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
    return $ret;
}

function resetSalesOrderItemtable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS t_sales_order_item";
    if (!$dbi->query($query)){
        return $ret;
    }
    $query = "CREATE TABLE t_sales_order_item (
        id              int NOT NULL AUTO_INCREMENT,
        order_id	varchar(12) NOT NULL,
        client_itemcode   varchar(12),
        item_qty        int,
        remark          text,
        PRIMARY KEY (id)
    ) ";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
    return $ret;
}


function resetitem_statustable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS status_master";
    if (!$dbi->query($query)){
        return $ret;
    }
    $query = "create table status_master (
	id	INT NOT NULL,
	status	VARCHAR(16),
	PRIMARY KEY(id)
    ) ";
    if ( $dbi->query($query)){
        $query = "INSERT INTO status_master (id, status) VALUES (0, '無効'),(1, '有効'),(2, '終了')";
        if ($dbi->query($query)){
            $ret = TRUE;
        }
    }
    return $ret;
}

function resettypetable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS type_master";
    if (!$dbi->query($query)){
        return $ret;
    }
    $query = "create table type_master (
	id	INT NOT NULL,
	type	VARCHAR(16),
	PRIMARY KEY(id)
    )";
    if ( $dbi->query($query)){
        $query = "INSERT INTO type_master (id, type) VALUES (0, '無効'),(1, '輸入者'),(2, '顧客')";
        if ($dbi->query($query)){
            $ret = TRUE;
        }
    }
    return $ret;
}


function resetroletable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS role_master";
    if (!$dbi->query($query)){
        return $ret;
    }
    $query = "create table role_master (
	id	INT NOT NULL,
	role	VARCHAR(16),
	PRIMARY KEY(id)
    ) ";
    if ( $dbi->query($query)){
        $query = "INSERT INTO role_master (id, role) VALUES (0, '管理者'),(1, '一般'),(2, 'ゲスト')";
        if ($dbi->query($query)){
            $ret = TRUE;
        }
    }
    return $ret;
}


function resetPurchaseOrdertable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS t_purchaseorder";
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        return $ret;
    }
        
    $query = "CREATE TABLE t_purchaseorder (
        order_code    VARCHAR(12),
        item_code       VARCHAR(12) not null,
        item_qty	INT,
        expire          DATE,
        importer_id	INT,
        user            VARCHAR(64),
        remark          TEXT,
        done            BOOLEAN,
        udate           datetime,
        PRIMARY KEY (order_code)
    )";  // Added 'done' on Aug.12.2013
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
	return $ret;
}


?>