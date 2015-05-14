<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$table_id = "t_item_convert";      // Table Identifier
//
//ログインチェック
checkLogin();


$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}


$client_id = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_NUMBER_INT);
$item_code = filter_input(INPUT_POST, 'item_code', FILTER_SANITIZE_STRING);
$client_item_code = trim(filter_input(INPUT_POST, 'client_item_code', FILTER_SANITIZE_STRING));
$client_item_name = trim(filter_input(INPUT_POST, 'client_item_name', FILTER_SANITIZE_STRING));
$item_count = filter_input(INPUT_POST, 'item_count', FILTER_SANITIZE_NUMBER_INT);
$label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);

$status = 1;    // STATUS 1 means 'ENABLE'

if($click == "新規登録"){
//レコード追加
    if ($dbi->connect_errno == 0){
        if( $remark == "" ){
            $remark = "-";
        }
        date_default_timezone_set('Asia/Tokyo');
				
        $command  = "INSERT INTO t_item_convert (client_id, item_code, client_item_code, client_item_name, item_count, label_code, remark) ";
        $command .= "VALUES ({$client_id},'{$item_code}','{$client_item_code}','{$client_item_name}',{$item_count},'{$label}','{$remark}');";
        
//echo $command."<BR>";
        if (!$dbi->query($command)){
            echo "INSERT ERROR :".$dbi->errno;
        }
    }
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>商品番号変換テーブル管理｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css" title="currentStyle">
			@import "./css/demo_page.css";
			@import "./css/demo_table.css";
</style>
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8">
$(function() {
    $('#datacontaints').dataTable( {
        "bProcessing": true,
        "bServerSide": true,
        "bFilter":false,
        "bStateSave":true,
        "sAjaxSource": "functions/item_conversiondata.php",
        "sServerMethod": "GET",
        "fnServerData": function( sSource, aoData, fnCallback, oSettings ){
            $.getJSON( sSource, aoData, function(json){
                $(json.aaData).each(function(){ this[0] = '<a href="item_conversion_update.php?id='+this[0]+'">'+this[0]+'</a>'} );
                fnCallback(json);
            });

        }
    } );
} );
$(function(){ 
    $("#clientlist").load("./functions/clientlist.php", {type: 2});
    $("#itemlist").load("./functions/itemlist.php", { });
});

function itemlookup(){
   var itemcode = $("#itemcode").val();
   $("span#itemname").load("functions/itemlookup.php", {itemcode:itemcode});
}

function checkform(){
    var eflug = true;
    var estr = "";
    if ($('#clientlist').val() == ""){
        eflug = false;
        estr += "アイテムコード \n";
    }
    if ($('#itemlist').val() == ""){
        eflug = false;
        estr += "商品名 \n";
    }
    if ($('#citemcode').val() == ""){
        eflug = false;
        estr += "カスタマ商品番号 \n";
    }
    if ($('#citemname').val() == ""){
        eflug = false;
        estr += "カスタマ商品名 \n";
    }
    if ($('#itemcnt').val() == ""){
        eflug = false;
        estr += "商品数量 \n";
    }
    if (!eflug){
        alert(estr + "が入力されていません。\n");
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
<h2 class="h2item">商品番号変換テーブル管理</h2>

<p>※英数字は<strong>半角</strong>、*マークは入力必須</p>
  <form method="post" action="item_conversion.php" onSubmit="return checkform()">
  <div id="inputitems">
  <table width="820">
    <tr>
        <td width="150"> *カスタマ：</td>
        <td><select name="client_id" id="clientlist" style="width:150px;"></select></td>
    </tr>
    <tr>
        <td>*KMY商品：</td>
        <td><select name="item_code" id="itemlist" style="width:400px;"></select></td>
    </tr>
    <tr>
        <td>*カスタマ商品番号：</td>
        <td><input id="citemcode" type="text" name="client_item_code" size="20" style="ime-mode: inactive" /></td>
    <tr>
    <tr>
        <td>*カスタマ商品名：</td>
        <td><input id="citemname" type="text" name="client_item_name" size="50" style="ime-mode: active" />※セット商品の場合、セットとなる商品名を入力。</td>
    <tr>
    <tr>
        <td>*商品数量：</td>
        <td><input id="itemcnt" type="text" name="item_count" size="10" style="ime-mode: inactive" value="1" />※カスタマ毎受注1個あたりの商品数量</td>
    <tr>
    <tr>
        <td>ラベル番号：</td>
        <td><input type="text" name="label" size="20" style="ime-mode: inactive" /></td>
    <tr>
    <tr>
      <td>備考：</td>
      <td><input name="remark" type="text" value="-" size="80" style="ime-mode: active"></td>
    </tr>
    <tr>
      <td colspan="2"><input name="click" type="submit"value="新規登録"></td>
    </tr>
  </table>
  </div>
  </form>
<br />
<br />

<table cellpadding="0" cellspacing="0" border="0" class="display" id="datacontaints">
    <thead>
        <tr>
            <th width="5%">ID</th>
            <th width="10%">顧客</th>
            <th width="8%">顧客商品コード</th>
            <th width="30%">顧客商品名</th>
            <th width="8%">KMY商品コード</th>
            <th width="30%">KMY商品名</th>
            <th width="9%">輸入者シール</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5" class="dataTables_empty">Loading data from server</td>
        </tr>
    </tbody>
</table>
<br />
<br />
<a href='converttableupload.php' target='_self'>変換データ一括アップロード</a>

</div>
<?php
footer_out();
?>
    
</body>
</html>