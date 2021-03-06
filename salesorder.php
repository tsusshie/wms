<?php


//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$thisfile = "salesorder.php";
$table_id = "t_sales_order";      // Table Identifier
//
//ログインチェック
checkLogin();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>注文登録｜在庫管理システム</title>
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
        "sAjaxSource": "functions/salesorderdata.php",
        "sServerMethod": "GET",
        "fnServerData": function( sSource, aoData, fnCallback, oSettings ){
            $.getJSON( sSource, aoData, function(json){
                $(json.aaData).each(function(){ 
                    this[0] = '<a href="salesorder_detail.php?id='+this[0]+'">'+this[0]+'</a>'
                } );
                $(json.aaData).each(function(){ 
                    if (this[3] == 0 ){
                        this[3] = "未";
                    } else{
                        this[3] = "済";
                    }
                } );
                fnCallback(json);
            });

        }
    } );
} );

//$("#rolelist").load("./functions/rolelist.php", { });
</script>

</head>

<body>
<div id="wrapper">
<?php
header_out();
?>
<div id="main">
<h2 class="h2item">注文登録</h2>

<input type="button" value="新規注文登録" width="80" onclick="location.href='salesorder_additem.php'" />
<br />
<br />
<table cellpadding="0" cellspacing="0" border="0" class="display" id="datacontaints">
    <thead>
        <tr>
            <th width="10%">ID</th>
            <th width="30%">顧客</th>
            <th width="20%">倉庫</th>
            <th width="10%">処理済</th>
            <th width="20%">登録日時</th>
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
</div> 
</body>
</html>