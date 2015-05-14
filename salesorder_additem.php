<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/dbclass.php";
include "./functions/commonfunc.php";

$thisfile = "salesorder_additem.php";
$itemsperpage = 30;     //Display Item count per page.
$table_id = "t_sales_order_item";      // Table Identifier
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

$order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_STRING);
$client_id = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_NUMBER_INT);
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
$wh_id = filter_input(INPUT_POST, 'wh_id', FILTER_SANITIZE_NUMBER_INT);
$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);

//$citemcode = filter_input_array(INPUT_POST, "citemcode", FILTER_SANITIZE_STRING);
//$itemqty = filter_input_array(INPUT_POST, "itemqty", FILTER_SANITIZE_NUMBER_INT);
$citemcode = $_REQUEST['citemcode'];
$itemqty = $_REQUEST['itemqty'];


$order_id = trim(mb_convert_encoding($order_id, "utf8"));
$remark = mb_convert_encoding($remark, "utf8");
$nameid = mb_convert_encoding($_SESSION['login'], "utf8");
$status = 1;    // STATUS 1 means 'ENABLE'

if($click == "新規登録"){
    if ($dbi->connect_errno == 0){
        date_default_timezone_set('Asia/Tokyo');
        $udate=date('Y/m/d-H:i');
	//注文レコード追加
        $command  = "INSERT INTO t_sales_order (order_id, client_id, remark, wh_id, user, done, udate) ";
        $command .= "VALUES ('{$order_id}',{$client_id},'{$remark}',{$wh_id},'{$nameid}',FALSE, '{$udate}');";
//echo "t_sales_order:".$command."<BR>";
        if (!$dbi->query($command)){
            echo "INSERT t_sales_order Query failed : ".$dbi->errno;
        }else{
            $i = 0;
            while($citemcode[$i] != ""){
                $citem=$citemcode[$i];
                $cqty=$itemqty[$i];
                $i++;
                $que = "INSERT INTO t_sales_order_item (order_id, client_itemcode, item_qty) ";
                $que .= "VALUES ('{$order_id}','{$citem}',{$cqty}); ";
//echo "t_sales_order_item:".$que."<BR>";
                if (!$dbi->query($que)){
                    echo "INSERT t_sales_order_item Query failed : ".$dbi->errno;
                }
            }
            header("location:salesorder.php");
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>注文登録｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript">
$(function(){
    $('#add_btn').click(function(){
        //alert("Test");
        $('tbody').append("<tr><td><p align='left'><select id='ccode_1' name='citemcode[]'  style='width:530'></select></p></td><td><p align='left'><input type='text' name='itemqty[]' size='15' style='ime-mode: inactive' id='cqty_1' /></p></td></tr>\n");
        var clientId = $("#client").val();
        $("[id=ccode_1]:last").load("functions/clientitemlist.php", {ccode:clientId});    
    });
    
    $("#orderid").blur(function(){
        var regstr = /^[0-9]+$/;
        var id = $("#orderid").val();
        if ( !regstr.test(id) ){
            alert("注文番号は数字のみ入力してください。");
            //$("#orderid").focus();
        }
    });
});
$(function(){
    $("#client").load("./functions/clientlist.php");
    $("#warehouse").load("./functions/wh_list.php");
});

function fillItemlist(){
    var clientcode = $("#client").val();
    $("[id=ccode_1]").load("functions/clientitemlist.php", {ccode:clientcode});    
}

function checkform(){
    var eflug = true;
    var estr = "";
    var itemerr = false;
    var i = 0;
    if ($('#orderid').val() == ""){
        eflug = false;
        estr += "注文番号 \n";
    }
    if ($('#client').val() == ""){
        eflug = false;
        estr += "カスタマ \n";
    }
    if ($('#warehouse').val() == ""){
        eflug = false;
        estr += "倉庫 \n";
    }
    $("input[name='citemcode[]']").each(function(){
        alert("CONFIRM:"+ $("[name='citemcode[]']").eq[i].val() );
        if ( $("[name='citemcode[]']").eq[i].val() != "" ) {
            itemerr = true;
        }
        if ( $("[name='itemqty[]']").eq[i].val() != "" ) {
            itemerr = true;
        }
        i++;
    });
    if (!eflug){
        alert(estr + "が入力されていません。\n");
    }
    if (itemerr){
        eflug = false;
        estr += "商品の入力にエラーがあります。 \n";
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
<h2 class="h2item">商品注文登録</h2>

<p>※英数字は<strong>半角</strong>、*マークは入力必須、最初に顧客を選択してください。</p>
<form method="post" action="<?php echo $thisfile;?>" onSubmit="return checkform();">
  <div id="inputitems">
        <div width="150">*顧客：<select id="client" name="client_id" style="width:150px;" onchange="fillItemlist();"></select></div><br />
        <div width="150">*注文番号：<input id="orderid" type="text" name="order_id" size="30" style="ime-mode: inactive"></div><br />
        <div width="150">*倉庫：<select id="warehouse" name="wh_id" style="width:150px;"></select></div><br />
        <div width="450">備 考：<input type="text" name="remark" size="90" style="ime-mode: active"></div>
<br />
<table width="925" border="1" cellpadding="3" cellspacing="0">
<tbody>
    <tr bgcolor="#AAAAAA">
    <th width="450"><p>顧客商品番号</p></th>
    <th width="60"><p>数量</p></th>
</tr>
<tr id="item_form">
    <td><p align="left"><select id="ccode_1" name="citemcode[]"  style="width:430"></select></p></td>
    <td><p align="left"><input type='text' name='itemqty[]' size='15' style='ime-mode: inactive' id='cqty_1' /></p></td>
</tr>
<tr>
    <td><p align="left"><select id="ccode_1" name="citemcode[]"  style="width:430"></select></p></td>
    <td><p align="left"><input type='text' name='itemqty[]' size='15' style='ime-mode: inactive' id='cqty_1' /></p></td>
</tr>
<tr>
    <td><p align="left"><select id="ccode_1" name="citemcode[]"  style="width:430"></select></p></td>
    <td><p align="left"><input type='text' name='itemqty[]' size='15' style='ime-mode: inactive' id='cqty_1' /></p></td>
</tr>
</tbody>
</table>    
<input type='button' value='商品を追加' id='add_btn' />
</div>
<p align='center'><input name="click" type="submit" value="新規登録" /></p>
</div>
</form>

<?php
//データベース終了
if ($dbi != false){ 
	$dbi->close();
}
?>

</div>

<?php
footer_out();
?>
    
</body>
</html>