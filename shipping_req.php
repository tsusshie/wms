<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$thisfile = "salesorder_detail.php";
$itemsperpage = 30;     //Display Item count per page.

//ログインチェック
checkLogin();

$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}

// 呼出時処理 - 注文ID
$param = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
if (!isset($param)){
    $param = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);    
}
//echo "order_id:".$param."<BR>\n";

if ($param){
    if ($dbi->connect_errno == 0){  // ERR_NORM
        $command  = "SELECT t1.order_id as order_id, t2.name as cname, t1.client_id as cid, t1.remark, t1.user, t1.udate, t1.wh_id as wid ";
        $command .= "FROM t_sales_order t1 INNER JOIN client_master t2 ON t1.client_id=t2.id ";
        $command .= "WHERE order_id='$param' ;";
        if (!$rowresult = $dbi->query($command)){
            echo "SELECT t_sales_order Query failed : ".$dbi->errno;
        }else{
            $rowsales = $rowresult->fetch_array(MYSQLI_BOTH);
            
            $command  = "SELECT 
                        t1.order_id as order_id, 
                        t2.item_code as itemcode, 
                        (t1.item_qty * t2.item_count) as item_qty, 
                        t3.name as item_name ";
            $command .= "FROM t_sales_order_item t1 INNER JOIN (t_item_convert t2, item_master t3) ";
            $command .= "ON t1.client_itemcode = t2.client_item_code AND t2.item_code = t3.code ";
            $command .= "WHERE t1.order_id='$param' ;";
// echo $command."<BR>";
            if (!$resultitem = $dbi->query($command)){
                echo "SELECT t_sales_order_item Query failed : ".$dbi->errno;
            }else{
                //echo "SELECT t_sales_order_item Query OK. <br />\n";
            }
        }
    }
}
$order_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$client_id = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_NUMBER_INT);
$wh_id = filter_input(INPUT_POST, 'wh_id', FILTER_SANITIZE_NUMBER_INT);     // Default Value
$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$importer_id = filter_input(INPUT_POST, 'importer_id', FILTER_SANITIZE_NUMBER_INT);  // Default Value
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);

$nameid = mb_convert_encoding($_SESSION['login'], "utf8");
$remark = mb_convert_encoding($remark, "utf8");

// フォーム送信時処理 - t_shipping に追加
$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);
//echo "CLICK: ".$click."<br />\n";
if($click == "選択完了"){
//echo "CUSTOMER NAME: ".filter_input(INPUT_POST, 'cname', FILTER_SANITIZE_STRING)."<br />\n";
    if ($dbi->connect_errno == 0){
        date_default_timezone_set('Asia/Tokyo');
        $udate=date('Y/m/d-H:i');
	//注文レコード追加
        $command  = "INSERT INTO t_shipping (shipping_code, client_master_id, wh_id, user, remark, udate) ";
        $command .= "VALUES ('{$order_id}',{$client_id},{$wh_id},'{$nameid}','{$remark}', '{$udate}');";
//echo "t_shipping_req:".$command."<BR>\n";
        if (!$dbi->query($command)){
            echo "INSERT t_shipping Query failed : ".$dbi->errno;
        }else{
            $i = 0;
            $inventory = $_REQUEST['inventory'];
            $export_qty = $_REQUEST['exqty'];
            while( $i < count($inventory)){
                if ($export_qty[$i] == ""){
                    continue;
                }
                try {
                    $que = "INSERT INTO t_shipping_item (shipping_req_id, inventory_id, item_qty) ";
                    $que .= "VALUES ('{$order_id}',{$inventory[$i]},'{$export_qty[$i]}'); ";
//echo "INSERT t_shipping_item:".$que."<BR>\n";
                    if (!$dbi->query($que)){
                        throw new Exception("INSERT t_shipping_item Query failed : ".$dbi->errno);
                    }
                
                    $que = "UPDATE t_inventory SET item_qty = item_qty - $export_qty[$i]  WHERE id=$inventory[$i]";
//echo "UPDATE t_inventory:".$que."<BR>\n";
                    if (!$dbi->query($que)){
                        throw new Exception( "UPDATE t_inventory Query failed : ".$dbi->errno);
                    }
                    $que = "UPDATE t_sales_order SET done=1 WHERE order_id='".$order_id."'";
//echo "UPDATE t_sales_order:".$que."<BR>\n";
                    if (!$dbi->query($que)){
                        throw new Exception( "UPDATE t_sales_order Query failed : ".$dbi->errno);
                    }
                } catch( exception $e) {
                    // Error Occured
                    echo $e->getMessage()."<br />\n";
                    break;
                }
                $i++;
            }
            header("location:shipping.php");
        }
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>出荷依頼｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript">
$(function(){
    $(":checkbox").click(function(){
        var idx = $(":checkbox").index(this);
        if ( $(this).is(':checked') ) {
            $(":text:eq("+ idx +")").attr('disabled', false);
//            $("#qty:eq("+ idx +")").removeAttr('disabled');
        } else {
            $(":text:eq("+ idx +")").val("");
            $(":text:eq("+ idx +")").attr('disabled', true);
        }
    });
});
$(function(){
    $("#client").load("./functions/clientlist.php");
});

function checkform(){
    var eflug = true;
    var estr = "";
    
    var cb = $(":checkbox:checked").length; // チェック済チェックボックス数
    var tb = $(":text:enabled").length;     // 有効テキストボックス数
    if ( cb !== tb ){
        alert("チェックボックスと入力数を確認してください。");
        eflug = false;
    }
    return eflug;
}

</script>
</head>

<body>
<?php
header_out();
?>
<div id="main">
<h2 class="h2item">出荷依頼フォーム</h2>
<form method="post" action="shipping_req.php" onSubmit="return checkform()">
<div id="inputitems">
    <div width="150">注文番号：<?php echo $rowsales['order_id']; ?></div><br />
    <div width="150">カスタマ：<?php echo $rowsales['cname']; ?></div><br />
    <div width="450">備 考：<?php echo $rowsales['remark']; ?></div>
</div>
    <input type="hidden" name="id" value="<?php echo $rowsales['order_id']; ?>" />
    <input type="hidden" name="cname" value="<?php echo $rowsales['cname']; ?>" />
    <input type="hidden" name="client_id" value="<?php echo $rowsales['cid']; ?>" />
    <input type="hidden" name="wh_id" value="<?php echo $rowsales['wid']; ?>" />
    <input type="hidden" name="remark" value="<?php echo $rowsales['remark']; ?>" />
<table width="925" border="1" cellpadding="3" cellspacing="0">
        <tr bgcolor="#AAAAAA">
        <th width="40%"><p>商品番号 - 商品名</p></th>
        <th width="5%"><p>数量</p></th>
        <th width="55%"><p>倉庫 - ロケーション(在庫数量)</p></th>
    </tr>
<?php while ($rowitem = $resultitem->fetch_array(MYSQLI_BOTH)) { ?>
    <tr id="item_form">
        <td><p align="left"><?php echo $rowitem['itemcode']." - ".$rowitem['item_name']; ?></p></td>
        <td><p align="left"><?php echo $rowitem['item_qty']; ?></p></td>
        <td><p align="left">
                <?php InventoryList($rowitem['itemcode'], $rowsales['wid'], $rowitem['item_qty'] ); ?>
            </p>
        </td>
    </tr>
<?php
        }
        //データベース終了
        if ($dbi != false){ 
            $dbi->close();
        }
?>
</table>
<br /><br />
<input type="submit" name="click" value="選択完了"  />
</form>
</div>
<?php
footer_out();
?>
    
</body>
</html>
<?php
function InventoryList( $icode, $wh, $qty ){
    global $dbi;
    //$wh = 1;
    $fResult = false;
    $que = "SELECT t1.id as id, t1.location as location, t1.item_qty as item_qty, t2.name as name, t1.expire as expire
            FROM t_inventory t1 LEFT JOIN warehouse_master t2 ON t1.wh_id=t2.id
            WHERE t1.item_code='$icode' AND t1.wh_id = $wh AND t1.item_qty > 0;";
    $result = $dbi->query($que);
    //echo "WAREHOUSE QUE:".$que;
    if (!$result){
        echo "SQL SELECT ERROR:".$dbi->errno;
        return;
    }
    while( $row = $result->fetch_array(MYSQLI_BOTH) ){
        echo '<input id="cb" type="checkbox" name="inventory[]" value="'.$row["id"].'" />';
        echo $row["name"]." - ".$row["location"]." (".$row["item_qty"].") 賞味期限 ".$row["expire"];
        echo ' 数量<input id="qty" type="text" name="exqty[]" size="4" disabled />';
        echo " <br />\n";
        $fResult = true;
    }
    if ( !$fResult ){
        echo "在庫がありません";
    }
}
?>