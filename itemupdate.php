<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$itemsperpage = 30;     //Display Item count per page.
$table_id = "item_master";      // Table Identifier
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

$code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT);
if (!isset($code)){
    $code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
}

if(isset($name)){
//レコード追加
    if ($dbi->connect_errno == 0){
        if ($name == ""){
            $name = "-";
        }
        if( $remark == "" ){
            $remark = "-";
        }
        if( $status == "" ){
            $status = 1;
        }
        date_default_timezone_set('Asia/Tokyo');
        $udate=date('Y/m/d-H:i');
		
        $nameid=$_SESSION['login'];
		
        $command  = "UPDATE item_master SET name='{$name}', remark='{$remark}', status={$status}, udate='{$udate}' ";
        $command .= " WHERE code='{$code}';";
        
//echo $command."<BR>";
        if (!$dbi->query($command)){
            echo "Update Query Error : ".$dbi->errno;
        }
        header("location:item.php");
    }
}

$que = "SELECT t1.code as code, t1.name as name, t2.status as status, t1.remark as remark, t1.status as status_id ";
$que .= "FROM item_master t1 LEFT JOIN status_master t2 ON t1.status=t2.id ";
$que .= "WHERE code='".$code."'";
$result = $dbi->query($que);
if ($dbi->real_query($que)){
    //カウント
    $count = $result->num_rows;
    $row = $result->fetch_array(MYSQLI_BOTH);

}else{
    echo "SELECT Query Failed.<br>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css" title="currentStyle">
			@import "./css/demo_page.css";
			@import "./css/demo_table.css";
</style>
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript" src ="./scripts/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="http://barcode-coder.com/js/jquery-ui-1.7.custom.min.js"></script>
<script type="text/javascript" src ="./scripts/jquery-barcode.min.js"></script>
<script>
//jQuery( function() {
//    jQuery('select#statuslist').load(
//    "functions/statuslist.php",{}
//    )
//});
$(function(){
    var status = <?php echo $row['status_id']; ?>;
    //alert(status);
    $("#statuslist").load("functions/statuslist.php",{status:status});
});
$(function(){
     $("#bctarget").barcode({value:'12345670', btype:"code128"});
});

function confirmation(){
    if ( confirm("削除します。よろしいですか？")){
        location.href='itemdel.php?colname=code&id=<?php print($row['code']); ?>';
    } else {
        return false;
    }
};

</script>
<title>情報修正｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />

</head>

<body>
<?php
header_out();
?>
<div id="main">
<h2 class="h2item">商品情報修正</h2>
<p>
</p>
<div id="test"></div>
<table width="909" border="1" cellpadding="3" cellspacing="0">
<tr bgcolor="#AAAAAA">
<th width="55"><p>品番</p></th>
<th width="214"><p>商品名</p></th>
<th width="196"><p>ステータス</p></th>
<th width="196"><p>備考</p></th>

</tr>

<tr>
<td><p align="left"><?php print($row['code']); ?></p></td>
<td><p align="left"><?php print($row['name']); ?></p></td>
<td><p align="left"><?php print($row['status']); ?></p></td>
<td><p align="left"><?php print($row['remark']); ?></p></td>  
</tr>
</table>

<table width="690" border="1" cellpadding="3" cellspacing="1">
  <tr bgcolor="#AAAAAA">
<th width="680"><p align="center">情報修正</p></th>

</tr>


<tr>
<td>
  <form method="post" action="itemupdate.php"/>
     品　番：
     <b><?php echo $row['code']; ?></b>     <br /><br />
     <input type="hidden" name="code" value="<?php print($row['code']); ?>"></input>
     商品名：
    <input name="name" type="text" value="<?php print($row['name']); ?>" size="70">
    <br /><br />
    ステータス：
    <select id="statuslist" name="status" value="<?php print($row['status']); ?>" style="width:150px">
    </select>
    <br /><br />
    備　考：
    <input name="remark" type="text" value="<?php print($row['remark']); ?>" size="70">
    <br /><br />
    <input type="submit"value="修正" /> <input type="button" name="delete" value="削除" onclick="confirmation();" />
    </p>
  </form>
<?php
if ($dbi != false){ 
	$dbi->close();
}
?>
</td></tr></table>

</div>
<?php
footer_out();
?>
</body>
></html>