<?php


//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$thisfile = "receiveshiphistory.php";
$table_id = "t_sales_order";      // Table Identifier
//
//ログインチェック
checkLogin();
$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>入出荷履歴｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/smoothness/jquery-ui.css" />
<style type="text/css" title="currentStyle">
			@import "./css/demo_page.css";
			@import "./css/demo_table.css";
</style>
<style type="text/css">
    #output {
        font-family: sans-serif;
        font-size: 10pt;
    }
    #itemheader {
        background-color: #CCCCCC;
    }
    #item_form {
        font-size: 10pt;
        font-weight: 100;
    }
</style>
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery-ui-1.10.2.custom.js"></script>
<script type="text/javascript" charset="utf-8">
$(function(){
    $("#itemlist").load("./functions/itemlist.php", {itemid:0});
    $("#warehouse").load("./functions/wh_list.php");

    $("#datefrom").datepicker( {
        dateFormat: "yy-mm-dd"
    }); 
    $("#dateto").datepicker( {
        dateFormat: "yy-mm-dd"
    }); 
    
    $("#fromdate").change(function(){
        $("#todate").val($("#fromdate").val() );
    });
});

function loadTable()
{
    var type = $("select[name=ordertype]").val();
    var item = $("select[name=item_code]").val();
    var warehouse = $("select[name=warehouse]").val();
    var datefrom = $("#datefrom").val();
    var dateto = $("#dateto").val();
    
    $("#contents").load("./functions/historylist.php", {
        "datefrom": datefrom,
        "dateto": dateto,
        "type": type,
        "item": item,
        "warehouse": warehouse
    });
}
</script>

</head>

<body>
<div id="wrapper">
<?php
header_out();
?>
<div id="main">
<h2 class="h2item">商品別入出荷履歴</h2>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="sortitems">
    <thead>
        <tr>
            <th width="30%">対象商品選択</th>
            <th width="13%"> 出荷/入荷</th>
            <th width="7%">倉庫</th>
            <th>登録日時(範囲指定)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <select name="item_code" id="itemlist" style="width:300px;"></select>
            </td>
            <td>
                <select name="ordertype" id="ordertype" style="width: 50px">
                    <option value="0" selected>出荷</option>
                    <option value="1">入荷</option>
                <select />
            </td>
            <td><select name="warehouse" id="warehouse" style="width: 120px" /></td>
            <td>FROM<input type="text" name="datefrom" id="datefrom"" style="width: 80px" />
            ～TO<input type="text" name="dateto" id="dateto" style="width: 80px" /></td>
        </tr>
    </tbody>
</table>
<input type="button" value="表示" width="80" onclick="loadTable()" />

    <div id="contents">
        <!-- Result output space  -->
    </div>

</div>

<?php
footer_out();
?>
</div> 
</body>
</html>