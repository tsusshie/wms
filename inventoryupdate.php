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

$code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
$item_code = filter_input(INPUT_POST, 'item_code', FILTER_SANITIZE_STRING);
$item_qty = filter_input(INPUT_POST, 'item_qty', FILTER_SANITIZE_NUMBER_INT);
$expire = filter_input(INPUT_POST, 'expire', FILTER_SANITIZE_STRING);
$wh_id = filter_input(INPUT_POST, 'wh_id', FILTER_SANITIZE_NUMBER_INT);     // Default Value
$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
$update = filter_input(INPUT_POST, 'update', FILTER_SANITIZE_STRING);
if (!isset($id)){
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
}

if($update == "修正"){
//レコード修正
    if ($dbi->connect_errno == 0){
        if( $remark == "" ){
            $remark = "-";
        }
        date_default_timezone_set('Asia/Tokyo');
        $udate=date('Y/m/d-H:i');
				
        $command  = "UPDATE t_inventory SET item_code='{$item_code}', item_qty={$item_qty}, expire='{$expire}', wh_id={$wh_id}, location='{$location}', udate='{$udate}' ";
        $command .= "WHERE id={$code}";
        
//echo $command."<BR>";
        if (!$dbi->query($command)){
            echo "Update Query Error : ".$dbi->errno."<br />\n";
        }
        header("location:inventory.php");
    }
}

$que = "SELECT * ";
$que .= "FROM t_inventory t1 ";
$que .= "WHERE id=".$id;
$result = $dbi->query($que);
if ($dbi->real_query($que)){
    //カウント
    $count = $result->num_rows;
    $row = $result->fetch_array(MYSQLI_BOTH);

}else{
    echo "SELECT t_inventory Failed.<br>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/smoothness/jquery-ui.css" />
<style type="text/css" title="currentStyle">
			@import "./css/demo_page.css";
			@import "./css/demo_table.css";
</style>
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery-ui-1.10.2.custom.js"></script>
<script>
$(function(){
    var icode = "<?php echo $row['item_code']; ?>";
    var wid = "<?php echo $row['wh_id']; ?>";
    $("#itemlist").load("./functions/itemlist.php", {itemid:icode});
    $("#warehouse").load("./functions/wh_list.php", {wh_id:wid});
    $("#importer").load("./functions/clientlist.php", {type:1});
    $("#datepicker").datepicker( {
       dateFormat: "yy-mm-dd"
   }); 
});

function confirmation(){
    if ( confirm("削除します。よろしいですか？")){
        location.href='itemdel.php?table=t_inventory&id=<?php print($row['id']); ?>';
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
<h2 class="h2item">在庫修正</h2>
<p>
</p>
<div id="test"></div>
<table width="820" border="1" cellpadding="3" cellspacing="0">
<tr bgcolor="#AAAAAA">
<th width="10%"><p>ID</p></th>
<th width="40%"><p>商品名</p></th>
<th width="20%"><p>数量</p></th>
<th width="30%"><p>備考</p></th>

</tr>

<tr>
<td><p align="left"><?php print($row['id']); ?></p></td>
<td><p align="left"><?php print($row['item_code']); ?></p></td>
<td><p align="left"><?php print($row['item_qty']); ?></p></td>
<td><p align="left"><?php print($row['expire']); ?></p></td>  
</tr>
</table>

<table width="690" border="1" cellpadding="3" cellspacing="1">
  <tr bgcolor="#AAAAAA">
<th width="680"><p align="center">情報修正</p></th>

</tr>


<tr>
<td>
  <form method="post" action="inventoryupdate.php"/>
  <table width="820">

    <tr>
      <td colspan="2">*商品：
      <select name="item_code" id="itemlist" style="width:400px;"></select>
    </tr>
    <tr>
      <td>*数量：<input type="text" name="item_qty" size="20" value="<?php print($row['item_qty']); ?>" style="ime-mode: inactive"></td>
      <td>*賞味期限：<input type="text" name="expire" size="20" value="<?php print($row['expire']); ?>" id="datepicker"></td>
    </tr>
    <tr>
      <td>*倉庫：<select id="warehouse" name="wh_id" style="width:150px;"></td>
      <td>*ロケーション：<input type="text" name="location" size="20" value="<?php print($row['location']); ?>"></td>
      </td>
    </tr>
    <tr>
      <td colspan="2">備考：<input name="remark" type="text" value="-" size="60" style="ime-mode: active"></td>
    </tr>
  </table>



    <input type="submit" name="update" value="修正" /> <input type="button" name="delete" value="削除" onclick="confirmation();" />
    <input type="hidden" name="code" value="<?php echo $row['id']; ?>" />
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
</html>