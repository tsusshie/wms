<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/dbclass.php";
include "./functions/commonfunc.php";

$thisfile = "warehouse.php";
$itemsperpage = 30;     //Display Item count per page.
$table_id = "warehouse_master";      // Table Identifier
//
//ログインチェック
checkLogin();


//データベース準備
//$db = new dbconnect($DBHOST, $DBUSER, $DBPASS, $DBNAME);
//if ($db->err != ERR_NORM){
//	echo "Database Error. code=".$db->err."<br>";
//	exit();
//}

$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}


//改ページ
$offset = filter_input(INPUT_GET, 'offset', FILTER_SANITIZE_NUMBER_INT );
if($offset==""){
    $offset=0;
}


$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);

if($click == "新規登録"){
//レコード追加
    if ($dbi->connect_errno == ERR_NORM){
        date_default_timezone_set('Asia/Tokyo');
        $udate=date('Y/m/d-H:i');
				
        $command  = "INSERT INTO warehouse_master (name, address, remark, udate) ";
        $command .= "VALUES ('{$name}','{$address}','{$remark}','{$udate}');";
        
//echo $command."<BR>";
        if (!$dbi->query($command)){
            echo "INSERT Query failed : ".$dbi->errno;
        }
    }
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>倉庫管理｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css" title="currentStyle">
			@import "./css/demo_page.css";
			@import "./css/demo_table.css";
</style>
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8">
$("#rolelist").load("./functions/rolelist.php", { });
$(function() {
    $('#datacontaints').dataTable( {
        "bProcessing": true,
        "bServerSide": true,
        "bFilter":false,
        "sAjaxSource": "functions/warehousedata.php",
        "sServerMethod": "GET",
        "fnServerData": function( sSource, aoData, fnCallback, oSettings ){
            $.getJSON( sSource, aoData, function(json){
                $(json.aaData).each(function(){ this[0] = '<a href="warehouseupdate.php?id='+this[0]+'">'+this[0]+'</a>'} );
                fnCallback(json);
            });

        }
    } );
} );

$(function(){
    $("#rolelist").load("./functions/rolelist.php");
});
</script>

</head>

<body>
<?php
header_out();
?>
<div id="main">
<h2 class="h2item">倉庫管理</h2>


<p>※英数字は<strong>半角</strong>、*マークは入力必須</p>
  <form method="post" action="<?php echo $thisfile;?>">
  <div id="inputitems">
  <table width="820">
    <tr>
        <td width="150">*名前： </td>
        <td><input type="text" name="name" size="30" style="ime-mode: active"></td>
    </tr>
    <tr>
        <td>住所： </td>
        <td><input type="text" name="address" size="90" style="ime-mode: active"></td>
    </tr>
    <tr>
        <td>備考： </td>
        <td><input type="text" name="remark" size="90" style="ime-mode: active"></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit" name="click" value="新規登録" /></td>
    </tr>
  </table>
  </div>
  </form>

<br />
<br />
<table cellpadding="0" cellspacing="0" border="0" class="display" id="datacontaints">
    <thead>
        <tr>
            <th width="7%">id</th>
            <th width="20%">name</th>
            <th width="33%">Address</th>
            <th width="20%">Remark</th>
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
<br />
<br />

<?php
footer_out();
?>
    
</body>
</html>