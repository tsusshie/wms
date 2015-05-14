<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$thisfile = "shipping_detail.php";
$itemsperpage = 30;     //Display Item count per page.

//ログインチェック
checkLogin();

$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}

$order_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

if ($order_id){
    if ($dbi->connect_errno == 0){  // ERR_NORM
        $command  = "SELECT t1.shipping_code as shipping_code, t1.client_master_id as cid, t2.name as cname, t1.wh_id as wh_id, t1.user, t1.udate ";
        $command .= "FROM t_shipping t1 INNER JOIN client_master t2 ON t1.client_master_id=t2.id WHERE t1.shipping_code='$order_id' ;";
        if (!$rowresult = $dbi->query($command)){
            echo "SELECT t_shipping Query failed : ".$dbi->errno;
        }else{
            $rowsales = $rowresult->fetch_array();
            
            $command  = "SELECT DISTINCT t1.shipping_req_id as order_id, t2.item_code as item_code, t1.item_qty as item_qty, t3.name as iname ";
            $command .= "FROM t_shipping_item t1 INNER JOIN (t_inventory t2, item_master t3) ";
            $command .= "ON t1.inventory_id = t2.id AND t2.item_code = t3.code ";
            $command .= "WHERE t1.shipping_req_id='$order_id' "
                    . " AND t1.item_qty > 0";
//echo $command."<BR>";
            if (!$resultitem = $dbi->query($command)){
                echo "SELECT t_shipping_item Query failed : ".$dbi->errno;
            }else{
                $command = "SELECT t2.name FROM t_sales_order_item t1 LEFT JOIN item_master t2 ON t1.";
            }
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>注文登録｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript">
$(function(){
    $("#client").load("./functions/clientlist.php");
});

</script>

</head>

<body>
<?php
header_out();
?>
<div id="main">
<h2 class="h2item">出荷処理済注文詳細</h2>

<div id="inputitems">
    <div width="150">注文番号：<?php echo $rowsales['shipping_code']; ?></div><br />
    <div width="150">カスタマ：<?php echo $rowsales['cname']; ?></div><br />
    <div width="450">倉庫番号：<?php echo $rowsales['wh_id']; ?></div>
</div>
<table width="925" border="1" cellpadding="3" cellspacing="0">
<tbody>
    <tr bgcolor="#AAAAAA">
    <th width="120"><p>商品番号</p></th>
    <th width="450"><p>商品名</p></th>
    <th width="60"><p>数量</p></th>
</tr>
<?php while ($rowitem = $resultitem->fetch_array()) { ?>
<tr id="item_form">
    <td><p align="left"><?php echo $rowitem['item_code']; ?></p></td>
    <td><p align="left"><?php echo $rowitem['iname']; ?></p></td>
    <td><p align="left"><?php echo $rowitem['item_qty']; ?></p></td>
</tr>
<?php } ?>
</tbody>
</table>    
<br />
<input type="button" value="出庫依頼表（プリント用）表示" width="160" onclick="window.open('shipping_detail_printable.php?id=<?php echo $order_id; ?>'); return false;" />
</div>

<?php
//データベース終了
if ($dbi != false){ 
	$dbi->close();
}

footer_out();
?>
    
</body>
</html>

