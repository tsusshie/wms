<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/dbclass.php";
include "./functions/commonfunc.php";

//ログインチェック
checkLogin();

$itemsperpage = 30;     //Display Item count per page.
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

//   入荷登録
$receiveing_no = filter_input(INPUT_POST, 'receiveing_no', FILTER_SANITIZE_STRING);
$item_code = filter_input(INPUT_POST, 'item_code', FILTER_SANITIZE_STRING);
$item_qty = filter_input(INPUT_POST, 'item_qty', FILTER_SANITIZE_NUMBER_INT);
$expire = filter_input(INPUT_POST, 'expire', FILTER_SANITIZE_STRING);
$wh_id = filter_input(INPUT_POST, 'wh_id', FILTER_SANITIZE_NUMBER_INT);     // Default Value
$importer_id = filter_input(INPUT_POST, 'importer_id', FILTER_SANITIZE_NUMBER_INT);  // Default Value
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);


if($receiveing_no){

//レコード追加
    if ($dbi->connect_errno == ERR_NORM){
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
		
        $command  = "INSERT INTO t_receiving (receiving_code, item_code, item_qty, expire, wh_id, importer_id, user, udate) ";
        $command .= "VALUES ('{$receiveing_no}','{$item_code}',{$item_qty},'{$expire}',{$wh_id},{$importer_id},'{$nameid}','{$udate}');";
        
// echo $command."<BR>";
        if (!$dbi->query($command)){
            echo "INSERT Query Error :".$dbi->errno;
        } else {    // Update Inventory Table
            $command = "SELECT id,item_qty FROM t_inventory WHERE item_code='{$item_code}' AND wh_id={$wh_id}";
            $result = $dbi->query($command);
            if ($result->num_rows != 0 ){
                $inventory = $result->fetch_array(MYSQLI_BOTH);
                $qty = $inventory['item_qty'] + $item_qty;
                $que = "UPDATE t_inventory SET item_qty = ".$qty;
            } else {
                $que = "INSERT INTO t_inventory (item_code, item_qty, wh_id) VALUES ('{$item_code}',{$item_qty},{$wh_id})";
            }
            if (!$dbi->query($que)){
                echo "UPDATE Inventory Error :".$dbi->errno;                
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
    $("#warehouse").load("./functions/wh_list.php");
    $("#importer").load("./functions/clientlist.php");
});
$(function(){
   $("#datepicker").datepicker( {
       dateFormat: "yy-mm-dd"
   }); 
});
$(function() {
    $('#datacontaints').dataTable( {
        "bProcessing": true,
        "bServerSide": true,
        "bFilter":false,
        "sAjaxSource": "functions/receivingdata.php",
        "sServerMethod": "GET",
        "fnServerData": function( sSource, aoData, fnCallback, oSettings ){
            $.getJSON( sSource, aoData, function(json){
                $(json.aaData).each(function(){ this[0] = '<a href="receivingupdate.php?code='+this[0]+'">'+this[0]+'</a>'} );
                fnCallback(json);
            });

        }
    } );
} );

function itemlookup(){
   var itemcode = $("#itemcode").val();
   $("span#itemname").load("functions/itemlookup.php", {itemcode:itemcode});
}

</script>
</head>

<body>
<?php
header_out();
?>

<div id="main">
<h2 class="h2nyk">出荷商品選択</h2>

<p>※英数字は<strong>半角</strong>、*マークは入力必須</p>
  <form method="post" action="receiving.php">
  <div id="inputitems">
  <table width="820">
    <tr>
        <td width="300">*伝票番号：<input id="receiveno" name="receiveing_no" type="text" size="20" style="ime-mode: inactive"><p id="receive_chk" style="font-color:red;"></p></td>
        <td>*入荷元：<select id="importer" name="importer_id" style="width:150px;"></td>
    </tr>

    <tr>
      <td>*品番：
      <input id="itemcode" type="text" name="item_code" size="20" style="ime-mode: inactive" onchange="itemlookup()" ></td>
      <td>商品名： <span id="itemname"></span></td>
    </tr>
    <tr>
      <td>*数量：
      <input type="text" name="item_qty" size="20" style="ime-mode: inactive"></td>
      <td>*賞味期限：
      <input type="text" name="expire" size="20" id="datepicker"></td>
      </td>
    </tr>
    <tr>
      <td colspan="2">*倉庫：<select id="warehouse" name="wh_id" style="width:150px;"></td>
    </tr>
    <tr>
      <td colspan="2">備考：<input name="remark" type="text" value="-" size="60" style="ime-mode: active"></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit"value="登録"></td>
    </tr>
  </table>
  </div>
  </form>
<br />
<br />
<table cellpadding="0" cellspacing="0" border="0" class="display" id="datacontaints">
    <thead>
        <tr>
            <th width="10%">code</th>
            <th width="30%">name</th>
            <th width="30%">qty</th>
            <th width="10%">expire</th>
            <th width="20%">udate</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5" class="dataTables_empty">Loading data from server</td>
        </tr>
    </tbody>
</table>
				
</div>
<?php
footer_out();
?>
</body>
</html>