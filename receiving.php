<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

//ログインチェック
checkLogin();


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

$(function() {
    $('#datacontaints').dataTable( {
        "bProcessing": true,
        "bServerSide": true,
        "bFilter":false,
        "sAjaxSource": "functions/purchaseorderdata.php?done=0",
        "sServerMethod": "GET",
        "fnServerData": function( sSource, aoData, fnCallback, oSettings ){
            $.getJSON( sSource, aoData, function(json){
                $(json.aaData).each(function(){ 
                    this[0] = '<a href="receiving_proc.php?id='+this[0]+'">'+this[0]+'</a>'
                } );
                fnCallback(json);
            });

        }
    } );

    $('#datacontaints2').dataTable( {
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave":true,
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

</script>
</head>

<body>
<?php
header_out();
?>

<div id="main">
<h2 class="h2nyk">購入登録</h2>
<input type="button" value="新規購入登録" width="80" onclick="location.href='purchaseorder.php'" />

<h3 class="h3shk">未処理発注</h3>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="datacontaints">
    <thead>
        <tr>
            <th width="10%">購入番号</th>
            <th width="40%">商品</th>
            <th width="10%">数量</th>
            <th width="20%">取引先</th>
            <th width="20%">注文日時</th>
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
<h3 class="h3shk">入荷履歴</h3>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="datacontaints2">
    <thead>
        <tr>
            <th width="10%">伝票番号</th>
            <th width="40%">商品名</th>
            <th width="10%">数量</th>
            <th width="10%">倉庫ID</th>
            <th width="15%">ロケーション</th>
            <th width="15%">賞味期限</th>
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