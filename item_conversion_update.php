<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$table_id = "t_item_convert";      // Table Identifier

//ログインチェック
checkLogin();

$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
//$client_id = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_NUMBER_INT);
$item_code = filter_input(INPUT_POST, 'item_code', FILTER_SANITIZE_STRING);
$client_item_code = trim(filter_input(INPUT_POST, 'client_item_code', FILTER_SANITIZE_STRING));
$client_item_name = trim(filter_input(INPUT_POST, 'client_item_name', FILTER_SANITIZE_STRING));
$item_count = filter_input(INPUT_POST, 'item_count', FILTER_SANITIZE_NUMBER_INT);
$label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
$status = 1;    // STATUS 1 means 'ENABLE'

if (!isset($id)){
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
}

$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);

if($click == "修正"){
//レコード追加
    if ($dbi->connect_errno == 0){
        if( $remark == "" ){
            $remark = "-";
        }
        date_default_timezone_set('Asia/Tokyo');
				
        $command  = "UPDATE t_item_convert SET item_code='{$item_code}', client_item_code='{$client_item_code}', client_item_name='{$client_item_name}', item_count = '{$item_count}', label_code='{$label}', remark='{$remark}' ";
         $command .= "WHERE id = {$id};";
        
//echo $command."<BR>";
        if (!$dbi->query($command)){
            echo "UPDATE ERROR :".$dbi->errno;
        }else{
            header("location:item_conversion.php");
        }
    }
} 

$que = "SELECT t1.id as id, t1.client_id as client_id, t2.name as cname, t1.client_item_code as citemcode, t1.client_item_name as citemname, t1.item_count as icount, t1.item_code as icode, t3.name as iname, t1.label_code as label, t1.remark as remark";
$que .= " FROM t_item_convert t1 LEFT JOIN (client_master t2, item_master t3) ON ( t1.client_id=t2.id AND t1.item_code=t3.code ) ";
$que .= "WHERE t1.id=".$id;
$result = $dbi->query($que);
if ($result){
    //カウント
    $count = $result->num_rows;
    $row = $result->fetch_array(MYSQLI_BOTH);

}else{
    echo "SELECT Query Failed: $dbi->errno<br>";
    echo "QUERY :".$que."<br /";
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
<script>
//jQuery( function() {
//    jQuery('select#statuslist').load(
//    "functions/statuslist.php",{}
//    )
//});
$(function(){ 
    var icode = "<?php echo $row['icode']; ?>";
    $("#itemlist").load("./functions/itemlist.php", {itemid:icode });
    var cid = "<?php echo $row['client_id']; ?>";
    $("#clientlist").load("./functions/clientlist.php", {type: 2, id:cid});
});

function confirmation(){
    if ( confirm("削除します。よろしいですか？")){
        location.href='item_conversion_del.php?id=<?php print($row['id']); ?>';
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
<h2 class="h2item">商品番号変換テーブル 情報修正</h2>
<p>
</p>

<form method="post" action="item_conversion_update.php">
<table width="800" border="1" cellpadding="3" cellspacing="1">
    <tr bgcolor="#AAAAAA">
        <th colspan="2"><p align="center">情報修正</p></th>
    </tr>
    <tr>
        <td width="150"> *カスタマ：<input type="hidden" name="id" value="<?php print($row['id']); ?>" /></td>
        <td><select name="client_id" id="clientlist" style="width:150px;"></select></td>
    </tr>
    <tr>
        <td>*商品番号：</td>
        <td><select id="itemlist" name="item_code" value="<?php print($row['icode']); ?>" style="width:450px"></select></td>
    </tr>
    <tr>
        <td>*カスタマ商品番号：</td>
        <td><input name="client_item_code" type="text" value="<?php print($row['citemcode']); ?>" size="20" /></td>
    <tr>
    <tr>
        <td>*カスタマ商品名：</td>
        <td><input type="text" name="client_item_name" value="<?php print($row['citemname']); ?>" size="50" style="ime-mode: active" />※セット商品の場合、セットとなる商品名を入力。</td>
    <tr>
    <tr>
        <td>*商品数量：</td>
        <td><input type="text" name="item_count" value="<?php print($row['icount']); ?>" size="10" style="ime-mode: inactive" />※カスタマ毎受注1個あたりの商品数量</td>
    <tr>
    <tr>
        <td>ラベル番号：</td>
        <td><input type="text" name="label" value="<?php print($row['label']); ?>" size="20" style="ime-mode: inactive" /></td>
    <tr>
    <tr>
      <td>備考：</td>
      <td><input name="remark" type="text" value="<?php print($row['remark']); ?>" size="80" style="ime-mode: active"></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit" name="click" value="修正" /><input type="button" name="delete" value="削除" onclick="confirmation();" /></td>
    </tr>
</table>
</form>
</div>
<?php
if ($dbi != false){ 
	$dbi->close();
}

footer_out();
?>
</body>
</html>