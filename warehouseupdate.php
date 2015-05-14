<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$itemsperpage = 30;     //Display Item count per page.
$table_id = "warehouse_master";      // Table Identifier

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

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);
if (!isset($id)){
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
}
if($click == "修正"){
//レコード追加
    if ($dbi->connect_errno == 0){
        if ($name == ""){
            $name = "-";
        }
        if( $remark == "" ){
            $remark = "-";
        }
        date_default_timezone_set('Asia/Tokyo');
        $udate=date('Y/m/d-H:i');
		
        $nameid=$_SESSION['login'];
		
        $command  = "UPDATE warehouse_master SET name='{$name}', address='{$address}', remark='{$remark}', udate='{$udate}' ";
        $command .= " WHERE id='{$id}';";
        
//echo $command."<BR>";
        if (!$dbi->query($command)){
            echo "Update Query Error : ".$dbi->errno;
        }
        header("location:warehouse.php");
    }
}

$que = "SELECT t1.id as id, t1.name as name, t1.address as address, t1.remark as remark ";
$que .= "FROM warehouse_master t1 ";
$que .= "WHERE id=".$id;
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
<script type="text/javascript" src="http://barcode-coder.com/js/jquery-ui-1.7.custom.min.js"></script>
<script type="text/javascript" src ="./scripts/jquery-barcode.min.js"></script>
<script>
//jQuery( function() {
//    jQuery('select#statuslist').load(
//    "functions/statuslist.php",{}
//    )
//});
function confirmation(){
    if ( confirm("削除します。よろしいですか？")){
        location.href='warehousedelete.php?id=<?php print($row['id']); ?>';
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
<h2 class="h2item">倉庫情報修正</h2>
<p>
</p>
<div id="test"></div>
<table width="909" border="1" cellpadding="3" cellspacing="0">
<tr bgcolor="#AAAAAA">
<th width="300"><p>名前</p></th>
<th width="300"><p>住所</p></th>
<th width="300"><p>備考</p></th>

</tr>

<tr>
<td><p align="left"><?php print($row['name']); ?></p></td>
<td><p align="left"><?php print($row['address']); ?></p></td>
<td><p align="left"><?php print($row['remark']); ?></p></td>
</tr>
</table>
※　種別　1:輸入者、2:顧客
<br /><br />
<table width="690" border="1" cellpadding="3" cellspacing="1">
<tr bgcolor="#AAAAAA">
    <th width="680"><p align="center">情報修正</p></th>
</tr>
<tr>
    <td>
        <form method="post" action="warehouseupdate.php" >
        ID：
        <b><?php echo $row['id']; ?></b>
        <br /><br />
        <input type="hidden" name="id" value="<?php print($row['id']); ?>" />
        名前：
        <input name="name" type="text" value="<?php print($row['name']); ?>" size="70" />
        <br /><br />
        住所：
        <input name="address" type="text" value="<?php print($row['address']); ?>" size="70" />
        <br /><br />
        備考：
        <input name="remark" type="text" value="<?php print($row['remark']); ?>" size="100" />
        <br /><br />
        <input type="submit" name="click" value="修正" />
        <input type="button" name="delete" value="削除" onclick="confirmation()" />
        <div id="bctarget"></div>
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