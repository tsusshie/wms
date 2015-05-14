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

$receiveing_no = filter_input(INPUT_POST, 'receiveing_no', FILTER_SANITIZE_STRING);
$item_code = filter_input(INPUT_POST, 'item_code', FILTER_SANITIZE_STRING);
$item_qty = filter_input(INPUT_POST, 'item_qty', FILTER_SANITIZE_NUMBER_INT);
$expire = filter_input(INPUT_POST, 'expire', FILTER_SANITIZE_STRING);
$wh_id = filter_input(INPUT_POST, 'wh_id', FILTER_SANITIZE_NUMBER_INT);     // Default Value
$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$importer_id = filter_input(INPUT_POST, 'importer_id', FILTER_SANITIZE_NUMBER_INT);  // Default Value
if (!isset($code)){
    $code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
}
$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);

if($click == "修正"){
//レコード修正
    if ($dbi->connect_errno == 0){
        date_default_timezone_set('Asia/Tokyo');
        $udate=date('Y/m/d-H:i');
		
        $nameid=$_SESSION['login'];
		
        $command  = "UPDATE t_receiving SET item_code='{$item_code}', item_qty={$item_qty}, expire='{$expire}', wh_id={$wh_id}, location='{$location}', importer_id={$importer_id}, udate='{$udate}' ";
        $command .= " WHERE receiving_code='{$receiveing_no}'";
        
//echo $command."<BR>";
        if (!$dbi->query($command)){
            echo "Update Query Error : ".$dbi->errno;
        }
        header("location:receiving.php");
    }
}

$que = "SELECT t1.receiving_code as code, t1.item_code as item_code, t1.item_qty as item_qty, t1.expire as expire, t1.importer_id as iid, t1.wh_id as wh_id, t1.location as location ";
$que .= "FROM t_receiving t1 ";
$que .= "WHERE receiving_code='".$code."'";
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
    var iid = <?php echo $row['iid']; ?>;

    $("#itemlist").load("./functions/itemlist.php", {itemid:icode});
    $("#warehouse").load("./functions/wh_list.php", {wh_id:wid});
    $("#importer").load("./functions/clientlist.php", {type:1,id:iid});
    $("#datepicker").datepicker( {
       dateFormat: "yy-mm-dd"
   }); 
});

function confirmation(){
    if ( confirm("削除します。よろしいですか？")){
        location.href='receivingdel.php?table=t_receiving&id=<?php print($row['code']); ?>';
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
<h2 class="h2item">入荷履歴修正</h2>
<p>
</p>
<div id="test"></div>
<table width="690" border="1" cellpadding="3" cellspacing="0">
<tr bgcolor="#AAAAAA">
<th width="10%"><p>伝票番号</p></th>
<th width="20%"><p>商品番号</p></th>
<th width="10%"><p>数量</p></th>
<th width="20%"><p>賞味期限</p></th>

</tr>

<tr>
<td><p align="left"><?php print($row['code']); ?></p></td>
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
  <form method="post" action="receivingupdate.php"/>
  <table width="820">
    <tr>
        <td width="300">*伝票番号：<input id="receiveno" name="receiveing_no" type="text" size="20" value="<?php print($row['code']); ?>" style="ime-mode: inactive"><p id="receive_chk" style="font-color:red;"></p></td>
        <td>*入荷元：<select id="importer" name="importer_id" style="width:150px;"></select></td>
    </tr>

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
  </table>



    <input type="submit" name='click' value='修正' /> <input type="button" name="delete" value="削除" onclick="confirmation();" />
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
</html>