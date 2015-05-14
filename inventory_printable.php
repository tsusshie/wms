<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$thisfile = "shipping_detail.php";
$itemsperpage = 30;     //Display Item count per page.

//ログインチェック
checkLogin();

if ( isset($_GET['wh_id']) ) {
    $wh = $_GET['wh_id'];
//echo "WH:".$wh."<br />\n";
} else {
    $wh = 0;
}


$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}

if (isset($wh)){
    if ($dbi->connect_errno == 0){  // ERR_NORM
        $command     = "SELECT 
                        t1.id as id, 
                        t1.item_code as item_code, 
                        t2.name as item_name, 
                        t1.wh_id as wh_id, 
                        t1.item_qty as item_qty, 
                        t1.location as location, 
                        t1.expire as expire ";
        $command    .= "FROM t_inventory t1 INNER JOIN item_master t2 ON t1.item_code=t2.code ";
        $command    .= "WHERE t1.item_qty>0 ";
        $command    .= "ORDER BY wh_id ";
//echo "QUERY: ".$command."<br />\n";
        if (!$rowresult = $dbi->query($command)){
            echo "SELECT t_inventory Query failed : ".$dbi->errno;
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>在庫チェック表（印刷用）</title>
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
        <h2>在庫チェック表</h2>
    </div>
    <div id="contents">
        <table width="925" border="1" cellpadding="3" cellspacing="0">
            <tr>
                <th width="7%">在庫ID</th>
                <th width="13%">商品番号</th>
                <th width="30%">商品名</th>
                <th width="7%">倉庫ID</th>
                <th width="8%">数量</th>
                <th width="8%">ロケーション</th>
                <th width="8%">賞味期限</th>
            </tr>
<?php while ($rowitem = $rowresult->fetch_array(MYSQLI_BOTH)) { ?>
            <tr id="item_form">
                <td><?php echo $rowitem['id']; ?></td>
                <td><?php echo $rowitem['item_code']; ?></td>
                <td><?php echo $rowitem['item_name']; ?></td>
                <td><?php echo $rowitem['wh_id']; ?></td>
                <td><?php echo $rowitem['item_qty']; ?></td>
                <td><?php echo $rowitem['location']; ?></td>
                <td><?php echo $rowitem['expire']; ?></td>
            </tr>
<?php } ?>
        </table>    
    </div>
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
