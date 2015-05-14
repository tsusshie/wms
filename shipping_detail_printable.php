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
        $command     = "SELECT 
                        t1.shipping_code as shipping_code, 
                        t1.client_master_id as cid, 
                        t2.name as cname, 
                        t1.wh_id as wh_id, 
                        t1.user, 
                        t1.udate, 
                        t2.address as caddress ";
        $command    .= "FROM t_shipping t1 INNER JOIN client_master t2 ON t1.client_master_id=t2.id ";
        $command    .= "WHERE t1.shipping_code='$order_id' ;";
        if (!$rowresult = $dbi->query($command)){
            echo "SELECT t_shipping Query failed : ".$dbi->errno;
        }else{
            $rowsales = $rowresult->fetch_array();
            
            $command = "SELECT DISTINCT 
                        t2.location as location,
                        t2.item_code as item_code,
                        t1.item_qty as item_qty,
                        t3.name as iname, 
                        t2.expire as expire,
                        t3.remark as remark,
                        t4.label_code as label, 
                        (t1.item_qty / t4.item_count) as item_count ";
            $command .= "FROM t_shipping_item t1 INNER JOIN ( t_inventory t2, item_master t3, t_item_convert t4) ";
            $command .= " ON t1.inventory_id = t2.id 
                          AND t2.item_code = t3.code 
                          AND t3.code = t4.item_code 
                          AND t4.client_id = ".$rowsales['cid'];
            $command .= " WHERE t1.shipping_req_id=$order_id 
                          AND t1.item_qty > 0;";
            
            
            
            if (!$resultitem = $dbi->query($command)){
                echo $command."<br />\n";
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
<title>出庫依頼表（印刷用）</title>
<link href="printstyle.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript">
$(function(){
    $("#client").load("./functions/clientlist.php");
});

</script>

</head>

<body>
<div id="main">
    <div id="header">
        <h2>出庫依頼表</h2>
        <table width="925" border="1" cellpadding="3" cellspacing="0">
            <tr>
                <th width="7%">倉庫番号</th>
                <th width="13%">取引先番号</th>
                <th width="30%">取引先名</th>
                <th width="50%">注文番号</th>
            </tr>
            <tr id="item_form">
                <td><?php echo $rowsales['wh_id']; ?></td>
                <td><?php echo $rowsales['cid']; ?></td>
                <td><?php echo $rowsales['cname']; ?></td>
                <td><?php echo $rowsales['shipping_code']; ?></td>
            </tr>
        </table>    
    </div>
    <div id="contents">
        <table width="925" border="1" cellpadding="3" cellspacing="0">
            <tr>
                <th width="10%"><p>棚番号</p></th>
                <th width="13%"><p>商品番号</p></th>
                <th width="23%"><p>商品名</p></th>
                <th width="10%"><p>賞味期限</p></th>
                <th width="7%"><p>数量</p></th>
                <th width="7%"><p>組数</p></th>
                <th width="10%"><p>輸入者シール</p></th>
                <th width="20%"><p>備考</p></th>
            </tr>
<?php while ($rowitem = $resultitem->fetch_array()) { ?>
            <tr id="item_form">
                <td><p align="center"><?php echo $rowitem['location']; ?></p></td>
                <td><p align="center"><?php echo $rowitem['item_code']; ?></p></td>
                <td><p align="left"><?php echo $rowitem['iname']; ?></p></td>
                <td><p align="center"><?php echo $rowitem['expire']; ?></p></td>
                <td><p align="center"><?php echo $rowitem['item_qty']; ?></p></td>
                <td><p align="center"><?php echo (int)$rowitem['item_count']; ?></p></td>
                <td><p align="center"><?php echo $rowitem['label']; ?></p></td>
                <td><p align="left"><?php echo $rowitem['remark']; ?></p></td>
            </tr>
<?php } ?>
        </table>    
    </div>
</div>


<?php
//データベース終了
if ($dbi != false){ 
	$dbi->close();
}
?>

</div>



</body>
</html>
