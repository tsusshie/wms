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

//ページ設定
$offset = filter_input(INPUT_GET, 'offset', FILTER_SANITIZE_NUMBER_INT );
if($offset==""){
    $offset=0;
}

//   発注登録
$order_code = filter_input(INPUT_POST, 'order_code', FILTER_SANITIZE_STRING);
$item_code = filter_input(INPUT_POST, 'item_code', FILTER_SANITIZE_STRING);
$item_qty = filter_input(INPUT_POST, 'item_qty', FILTER_SANITIZE_NUMBER_INT);
$importer_id = filter_input(INPUT_POST, 'importer_id', FILTER_SANITIZE_NUMBER_INT);  // Default Value
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);


if($order_code){

//レコード追加
    if ($dbi->connect_errno == 0){
	if ($order_code == ""){
            echo "Receiving No. is required!<br>";
            exit();
        }
        if( $remark == "" ){
            $remark = "-";
        }
        
        if (isRecordDouble("t_purchaseorder", "order_code", $order_code)){
            echo "Order_code is already exists. <br />\n";
            echo "<input type='button' value='BACK' onclick='history.back()' >";
            exit();
        }
        
        date_default_timezone_set('Asia/Tokyo');
        $done = false;
        $udate=date('Y/m/d-H:i');
        $nameid=$_SESSION['login'];
		
        $command  = "INSERT INTO t_purchaseorder (order_code, item_code, item_qty, importer_id, user, remark, done, udate) ";
        $command .= "VALUES ('{$order_code}','{$item_code}',{$item_qty},{$importer_id},'{$nameid}','{$remark}',FALSE, '{$udate}');";
        
//echo "INSERT PurchaseOrder : ".$command."<BR>";
        if (!$dbi->query($command)){
            echo "INSERT PurchaseOrder Error :".$dbi->errno;
        }else{
            header("location:receiving.php");
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
    $("#itemlist").load("./functions/itemlist.php", {itemid:0});
    $("#warehouse").load("./functions/wh_list.php", {wh_id:0});
    $("#importer").load("./functions/clientlist.php", {type:1});
   $("#datepicker").datepicker( {
       dateFormat: "yy-mm-dd"
   }); 
});


</script>
</head>

<body>
<?php
header_out();
?>

<div id="main">
<h2 class="h2nyk">商品発注登録</h2>

<p>※英数字は<strong>半角</strong>、*マークは入力必須</p>
  <form method="post" action="purchaseorder.php">
  <div id="inputitems">
  <table width="820">
    <tr>
        <td width="300">*伝票番号：<input id="receiveno" name="order_code" type="text" size="20" style="ime-mode: inactive"><p id="receive_chk" style="font-color:red;"></p></td>
        <td>*取引先：<select id="importer" name="importer_id" style="width:150px;"></td>
    </tr>

    <tr>
      <td colspan="2">*商品：
      <select name="item_code" id="itemlist" style="width:400px;"></select>
    </tr>
    <tr>
      <td>*数量：<input type="text" name="item_qty" size="20" style="ime-mode: inactive"></td>
    </tr>
    <tr>
      <td colspan="2">備考：<input name="remark" type="text" value="-" size="100" style="ime-mode: active"></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit"value="登録"></td>
    </tr>
  </table>
  </div>
  </form>
<a href="receiving.php">「入荷処理」ページに戻る</a>
<br />
<br />
			
</div>
<?php
footer_out();
?>
</body>
</html>

<?php
// Check record double function.
function isRecordDouble($table, $item, $val)
{
    $ret = false;   // default return value
    global $dbi;
    
    $questr = "SELECT * FROM $table WHERE $item='".$val."'";
echo "CHECK DOUBLE :".$questr."<br />\n";
    $result = $dbi->query($questr);
    if ($result){
        if( $result->num_rows > 0 ){
            $ret = true;
        }
    }
    return $ret;
}
?>