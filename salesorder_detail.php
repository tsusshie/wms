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

$order_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
// Need below 2 params for Delete Record.
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);

if ($action == "del") {
    if ($dbi->connect_errno == 0){  // ERR_NORM
        $command = "DELETE FROM t_sales_order WHERE order_id='".$code."'";
        if (!$rowresult = $dbi->query($command)){
            echo "DELETE t_sales_order Query failed : ".$dbi->errno;
        }else{
            $command = "DELETE FROM t_sales_order_item WHERE order_id='".$code."'";
            if (!$resultitem = $dbi->query($command)){
                echo "DELETE t_sales_order_item Query failed : ".$dbi->errno;
            }else{
                header("Location:salesorder.php");
            }
        }
    }
}

if ($order_id){
    if ($dbi->connect_errno == 0){  // ERR_NORM
        $command  = "SELECT t1.client_id as cid,t1.order_id as order_id, t2.name as cname, t1.remark, t1.user, t1.done, t1.udate ";
        $command .= "FROM t_sales_order t1 LEFT JOIN client_master t2 ON t1.client_id=t2.id WHERE order_id='$order_id' ;";
        if (!$rowresult = $dbi->query($command)){
            echo "SELECT t_sales_order Query failed : ".$dbi->errno;
        }else{
            $rowsales = $rowresult->fetch_array();
            
            $command  = "SELECT DISTINCT t1.order_id as order_id, t1.client_itemcode as client_itemcode, t1.item_qty as item_qty, t2.client_item_name as item_name ";
            $command .= "FROM t_sales_order_item t1 INNER JOIN t_item_convert t2 ";
            $command .= "ON t1.client_itemcode = t2.client_item_code ";
            $command .= "WHERE t1.order_id='$order_id' ;";
//echo $command."<BR>";
            if (!$resultitem = $dbi->query($command)){
                echo "SELECT t_sales_order_item Query failed : ".$dbi->errno;
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

function confirmation(){
    if ( confirm("削除します。よろしいですか？")){
        location.href='<?php echo $thisfile; ?>?action=del&code=<?php echo $order_id; ?>';
    } else {
        return false;
    }
};

</script>

</head>

<body>
<?php
header_out();
?>
<div id="main">
<h2 class="h2item">商品注文詳細</h2>

<div id="inputitems">
    <div width="150">注文番号：<?php echo $rowsales['order_id']; ?></div><br />
    <div width="150">カスタマ：<?php echo $rowsales['cname']; ?></div><br />
    <div width="450">備 考：<?php echo $rowsales['remark']; ?></div>
</div>
<table width="925" border="1" cellpadding="3" cellspacing="0">
<tbody>
    <tr bgcolor="#AAAAAA">
    <th width="120"><p>顧客商品番号</p></th>
    <th width="450"><p>商品名</p></th>
    <th width="60"><p>数量</p></th>
</tr>
<?php while ($rowitem = $resultitem->fetch_array()) { ?>
<tr id="item_form">
    <td><p align="left"><?php echo $rowitem['client_itemcode']; ?></p></td>
    <td><p align="left"><?php echo $rowitem['item_name']; ?></p></td>
    <td><p align="left"><?php echo $rowitem['item_qty']; ?></p></td>
</tr>
<?php } ?>
</tbody>
</table>    
<input type="button" name="delete" value="削除" onclick="confirmation();" />
</div>
<?php
//データベース終了
if ($dbi != false){ 
	$dbi->close();
}
?>

</div>

<?php
footer_out();
?>
    
</body>
</html>

<?php
function itemdisplay($client_id, $citemcode)
{
    global $dbi;
    $command  = "SELECT t2.name as item_name ";
    $command .= "FROM t_item_convert t1 LEFT JOIN item_master t2 ";
    $command .= "ON  t1.item_code = t2.code ";
    $command .= "WHERE t1.client_id='$client_id' AND t1.client_item_code='$citemcode' ;";
    if ( $result = $dbi->query($command) ){
        while($row = $result->fetch_array(MYSQLI_BOTH)){
            echo $row['item_name']."<br />\n";
        }
    }
}
?>