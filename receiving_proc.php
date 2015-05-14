<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

//ログインチェック
checkLogin();

date_default_timezone_set("Asia/Tokyo");

$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}

// 呼出時処理 - 注文ID
$order_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
if (!isset($order_id)){
    $order_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);    
}


if ($order_id){
    if ($dbi->connect_errno == 0){  // ERR_NORM
        $command  = "SELECT t1.order_code as order_code, t1.item_code as item_code, t1.item_qty as item_qty, t1.importer_id as cid, t1.remark, t1.user, t1.udate ";
        $command .= "FROM t_purchaseorder t1 WHERE t1.order_code = '$order_id' ";
        if (!$resulp = $dbi->query($command)){
            echo "SELECT t_purchaseorder Query failed : ".$dbi->errno;
        }else{
            $rowpurchase = $resulp->fetch_array(MYSQLI_BOTH);
        }
    }
}

//   入荷登録
$receiveing_no = filter_input(INPUT_POST, 'receiveing_no', FILTER_SANITIZE_STRING);
$item_code = filter_input(INPUT_POST, 'item_code', FILTER_SANITIZE_STRING);
$item_qty = filter_input(INPUT_POST, 'item_qty', FILTER_SANITIZE_NUMBER_INT);
$expire = filter_input(INPUT_POST, 'expire', FILTER_SANITIZE_STRING);
$wh_id = filter_input(INPUT_POST, 'wh_id', FILTER_SANITIZE_NUMBER_INT);     // Default Value
$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$importer_id = filter_input(INPUT_POST, 'importer_id', FILTER_SANITIZE_NUMBER_INT);  // Default Value
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);

$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);
if($click == "登録"){
//レコード追加
    if ($dbi->connect_errno == 0){
	if ($receiveing_no == ""){
            echo "Receiving No. is MUST!<br>";
            exit();
        }
        if ($item_code == ""){
            $item_code = "-";
        }
        if( $remark == "" ){
            $remark = "-";
        }
        date_default_timezone_set('Asia/Tokyo');
        $udate=date('Y/m/d-H:i');
        $nameid=$_SESSION['login'];
		
        $command  = "INSERT INTO t_receiving (receiving_code, item_code, item_qty, expire, wh_id, location, importer_id, user, udate) ";
        $command .= "VALUES ('{$receiveing_no}','{$item_code}',{$item_qty},'{$expire}',{$wh_id},'{$location}',{$importer_id},'{$nameid}','{$udate}');";
        
//echo "INSERT QUERY : ".$command."<BR>";
        if (!$dbi->query($command)){
            echo "INSERT t_receiving Error :".$dbi->errno."<br />\n";
        } else {    // Update Inventory Table
            $que = "INSERT INTO t_inventory (item_code, wh_id, location, item_qty, expire) ";
            $que .= "VALUES ('{$item_code}',{$wh_id}, '{$location}', {$item_qty}, '{$expire}')";

            if (!$dbi->query($que)){
                echo "INSERT Inventory Error :".$dbi->errno."<br />\n";                
            }else{
                $que = "UPDATE t_purchaseorder SET done=TRUE WHERE order_code='$receiveing_no' ";
                if (!$dbi->query($que)){
                    echo "UPDATE t_purchaseorder Error :".$dbi->errno."<br />\n";
                }else{
                    header("location:receiving.php");
                }
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
<title>在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/smoothness/jquery-ui.css" />
<style type="text/css" title="currentStyle">
			@import "./css/demo_table.css";
</style>
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery-ui-1.10.2.custom.js"></script>
<script>

$(function(){
    var icode = "<?php echo $rowpurchase['item_code']; ?>";
    var cid = <?php echo $rowpurchase['cid']; ?>;
    $("#itemlist").load("./functions/itemlist.php", {itemid:icode});
    $("#warehouse").load("./functions/wh_list.php", {wh_id:0});
    $("#importer").load("./functions/clientlist.php", {type:1,id:cid});
    $("#datepicker").datepicker( {
        dateFormat: "yy-mm-dd"
    }); 
});

function checkform(){
    var eflug = true;
    var estr = "";
    if ($('#location').val() == ""){
        eflug = false;
        estr += "ロケーション \n";
    }
    if ($('#datepicker').val() == ""){
        eflug = false;
        estr += "賞味期限 \n";
    }
    if ($('#warehouse').val() == ""){
        eflug = false;
        estr += "倉庫 \n";
    }
    if (!eflug){
        alert(estr + "が入力されていません。\n");
    }
    return eflug;
}

function confirmation(){
    if ( confirm("削除します。よろしいですか？")){
        location.href='receivingdel.php?table=t_purchaseorder&colname=order_code&id=<?php print($rowpurchase['order_code']); ?>';
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
<h2 class="h2nyk">入荷処理</h2>

<p>※英数字は<strong>半角</strong>、*マークは入力必須</p>
  <form method="post" action="receiving_proc.php" onsubmit="return checkform()">
  <table width="820">
    <tr>
        <td width="300">
            伝票番号：<input id="receiveno" name="receiveing_no" type="text" size="20" value="<?php echo $rowpurchase['order_code']; ?>" style="ime-mode: inactive"><p id="receive_chk" style="font-color:red;"></p>
        </td>
        <td>*取引先：<select id="importer" name="importer_id" style="width:150px;"></td>
    </tr>

    <tr>
      <td colspan="2">*商品：
      <select name="item_code" id="itemlist" style="width:400px;"></select>
    </tr>
  </table>
  <br />
  <div id="inputitems">
  <table width="820">
    <tr>
      <td>*数量：<input type="text" name="item_qty" size="20" value="<?php echo $rowpurchase['item_qty']; ?>" style="ime-mode: inactive"></td>
      <td>*賞味期限：<input type="text" name="expire" size="20" id="datepicker"></td>
    </tr>
    <tr>
      <td>*倉庫：<select id="warehouse" name="wh_id" style="width:150px;"></td>
      <td>*ロケーション：<input type="text" name="location" size="20" id="location"></td>
      </td>
    </tr>
    <tr>
      <td colspan="2">備考：<input name="remark" type="text" value="-" size="60" style="ime-mode: active"></td>
    </tr>
      <td colspan="2"><input type="submit" name="click" value="登録" /> <input type="button" name="delete" value="削除" onclick="confirmation();" /></td>
    </tr>
  </table>
  </div>
  </form>
<br />
<br />
				
</div>
<?php
footer_out();
?>
</body>
</html>