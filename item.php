<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$table_id = "item_master";      // Table Identifier
//
//ログインチェック
checkLogin();

$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}


//改ページ
$offset = filter_input(INPUT_GET, 'offset', FILTER_SANITIZE_NUMBER_INT );
if($offset==""){
    $offset=0;
}


$code = trim(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING));
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);

$status = 1;    // STATUS 1 means 'ENABLE'

if($click == "新規登録"){
//レコード追加
    if ($dbi->connect_errno == ERR_NORM){
        // コード入力チェック
        $result = $dbi->query("SELECT code FROM item_master WHERE code=".$code);
        if ($result->num_rows != 0){
            $eflug = true;           
            $estr .= "アイテムコード<br />\n";
        }
	if ($code == ""){
            $eflug = true;
            $estr .= "アイテムコード<br />\n";
        }
        if ($name == ""){
            $eflug = true;
            $estr .= "商品名<br />\n";
        }
        if( $remark == "" ){
            $remark = "-";
        }
        
        if ($eflug){
            echo "<h1>ERROR!</h1>\n";
            echo "下記の入力に誤りがあります。<br />\n";
            echo $estr;
            echo "<input type='button' value='戻る' onClick='history.go(-1);' />";
            exit();
        }else{
            date_default_timezone_set('Asia/Tokyo');
            $udate=date('Y/m/d-H:i');
		
            $nameid=$_SESSION['login'];
		
            $command  = "INSERT INTO item_master (code, name, remark, status, udate) ";
            $command .= "VALUES ('{$code}','{$name}','{$remark}',{$status},'{$udate}');";
        
//echo $command."<BR>";
            $dbi->query($command);
        }
    }
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>商品管理｜在庫管理システム</title>
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
        "bStateSave":true,
        "bFilter":false,
        "sAjaxSource": "functions/itemdata.php",
        "sServerMethod": "GET",
        "fnServerData": function( sSource, aoData, fnCallback, oSettings ){
            $.getJSON( sSource, aoData, function(json){
                $(json.aaData).each(function(){ this[0] = '<a href="itemupdate.php?code='+this[0]+'">'+this[0]+'</a>'} );
                fnCallback(json);
            });

        }
    } );
} );

function checkform(){
    var eflug = true;
    var estr = "";
    if ($('#itemcode').val() == ""){
        eflug = false;
        estr += "アイテムコード \n";
    }
    if ($('#itemname').val() == ""){
        eflug = false;
        estr += "商品名 \n";
    }
    if (!eflug){
        alert(estr + "が入力されていません。\n");
    }
    return eflug;
}
</script>
</head>

<body>
<div id="wrapper">
<?php
header_out();
?>
<div id="main">
<h2 class="h2item">商品管理</h2>

<p>※英数字は<strong>半角</strong>、*マークは入力必須</p>
  <form method="post" action="item.php" onSubmit="return checkform()">
  <div id="inputitems">
  <table width="820">
    <tr>
      <td width="150">*品番：</td>
      <td><input id="itemcode" type="text" name="code" size="20" style="ime-mode: inactive" /></td>
    <tr>
        <td>*商品名：</td>
        <td><input id="itemname" type="text" name="name" size="60" style="ime-mode: active" /></td>
    <tr>
      <td>商品備考：</td>
      <td><input name="remark" type="text" value="-" size="60" style="ime-mode: active"></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit" name="click" value="新規登録" /></td>
    </tr>
  </table>
  </div>
  </form>

    
<table cellpadding="0" cellspacing="0" border="0" class="display" id="datacontaints">
    <thead>
        <tr>
            <th width="10%">code</th>
            <th width="30%">name</th>
            <th width="30%">remark</th>
            <th width="10%">status</th>
            <th width="20%">udate</th>
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
<a href='itemupload.php' target='_self'>アイテムデータ一括アップロード</a>
<?php
footer_out();
?>
</div> 
</body>
</html>