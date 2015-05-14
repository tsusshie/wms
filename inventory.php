<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

//ログインチェック
checkLogin();

$itemsperpage = 30;     //Display Item count per page.
date_default_timezone_set("Asia/Tokyo");

$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
}


//   入荷登録
$receiveing_no = filter_input(INPUT_POST, 'receiveing_no', FILTER_SANITIZE_STRING);
$item_code = filter_input(INPUT_POST, 'item_code', FILTER_SANITIZE_STRING);
$item_qty = filter_input(INPUT_POST, 'item_qty', FILTER_SANITIZE_NUMBER_INT);
$expire = filter_input(INPUT_POST, 'expire', FILTER_SANITIZE_STRING);
$wh_id = filter_input(INPUT_POST, 'wh_id', FILTER_SANITIZE_NUMBER_INT);     // Default Value
$importer_id = filter_input(INPUT_POST, 'importer_id', FILTER_SANITIZE_NUMBER_INT);  // Default Value
$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);


if($receiveing_no){

//レコード追加
    if ($dbi->connect_errno == ERR_NORM){
	if ($receiveing_no == ""){
            echo "Receiving No. is MUST!<br>";
            exit();
        }
        if ($item_code == ""){
            $item_code = "-";
        }
        if( $remark == "" ){
            $remark = "-";
        }
        date_default_timezone_set('Asia/Tokyo');
        $udate=date('Y/m/d-H:i');
        $nameid=$_SESSION['login'];
		
        $command  = "INSERT INTO t_receiving (receiving_code, item_code, item_qty, expire, wh_id, importer_id, user, udate) ";
        $command .= "VALUES ('{$receiveing_no}','{$item_code}',{$item_qty},'{$expire}',{$wh_id},{$importer_id},'{$nameid}','{$udate}');";
        
//echo $command."<BR>";
        if (!$dbi->query($command)){
            echo "INSERT Query Error :".$dbi->errno;
        } else {    // Update Inventory Table
            $command = "SELECT id,item_qty FROM t_inventory WHERE item_code='{$item_code}' AND wh_id={$wh_id}";
            $result = $dbi->query($command);
            if ($result->num_rows != 0 ){
                $inventory = $result->fetch_array(MYSQLI_BOTH);
                $qty = $inventory['item_qty'] + $item_qty;
                $que = "UPDATE t_inventory SET item_qty = ".$qty;
            } else {
                $que = "INSERT INTO t_inventory (item_code, item_qty, wh_id) VALUES ('{$item_code}',{$item_qty},{$wh_id})";
            }
            if (!$dbi->query($que)){
                echo "UPDATE Inventory Error :".$dbi->errno;                
            }
        }
    }
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/smoothness/jquery-ui.css" />
<style type="text/css" title="currentStyle">
			@import "./css/demo_table.css";
</style>
<script type="text/javascript" src="./scripts/jquery191.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/jquery-ui-1.10.2.custom.js"></script>
<script type="text/javascript" language="javascript" src="./scripts/dataTables.fnReloadAjax.js"></script>
<script>

$(function(){
    $("#warehouselist").load("./functions/wh_list.php", {"wh_id":0});
    
    var wh = $("select[name=wh_id]").val();
    if (!wh){
        wh = 0;
    }
    wh = String(wh);
//alert("WH_ID:"+wh);
    cTables = $('#datacontaints').dataTable( {
        "bProcessing": true,
        "bServerSide": true,
        "bRetrieve": true,
        "bStateSave": true,
        "bFilter":false,
        "sAjaxSource": "functions/inventorydata.php?wh_id="+wh+"&dispz=0",
        "sServerMethod": "GET",
        //"fnServerParams": function( aoData ){
        //    aoData.push({"name":"wh_id","value":wh });
        //},
        "fnDraw": function(){
        },
        "fnServerData": function( sSource, aoData, fnCallback, oSettings ){
            $.getJSON( sSource, aoData, function(json){
                $(json.aaData).each(function(){ this[0] = '<a href="inventoryupdate.php?id='+this[0]+'">'+this[0]+'</a>'} );
                fnCallback(json);
            });

        }
    } );
    //cTables.fnReloadAjax();
});

function reloadTable(){
    var wh = $("select[name=wh_id]").val();
    if (!wh){
        wh = 0;
    }
    wh = String(wh);
    cTables.fnReloadAjax("functions/inventorydata.php?wh_id="+wh);
}

function confirmation(){
    if ( confirm("削除します。よろしいですか？")){
        //location.href='itemdel.php?table=t_inventory&id=zero';
    } else {
        return false;
    }
};

</script>
</head>

<body>
<?php
header_out();
?>

<div id="main">
<h2 class="h2nyk">在庫チェック</h2>

  <form method="post" action="receiving.php">
  <table width="820">
    <tr>
      <td colspan="2">
          *倉庫：<select id="warehouselist" name="wh_id" style="width:150px;" onchange="reloadTable()"></select>
      </td>
    </tr>
  </table>
  </form>
<br />
<br />
<table cellpadding="0" cellspacing="0" border="0" class="display" id="datacontaints">
    <thead>
        <tr>
            <th width="5%">ID</th>
            <th width="10%">商品番号</th>
            <th width="35%">商品名</th>
            <th width="10%">倉庫ID</th>
            <th width="10%">数量</th>
            <th width="20%">ロケーション</th>
            <th width="20%">賞味期限</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5" class="dataTables_empty">Loading data from server</td>
        </tr>
    </tbody>
</table>
				
</div>

    <div id="addition">
        在庫数量0のデータを削除します<input type="button" name="delete" value="削除" onclick="confirmation();" />
    </div>
    <div id="addition">
    <input type="button" value="在庫チェック（プリント用）表示" width="160" onclick="window.open('inventory_printable.php'); return false;" />
    </div>

<?php
footer_out();
?>
</body>
</html>

<?php
function WarehouseList( $cur_wh ){
    global $dbi;
    //$wh = 1;
    $que = "SELECT t1.id as id, t1.name as name
            FROM warehouse_master t1 ;";
    $result = $dbi->query($que);
    //echo "WAREHOUSE QUE:".$que;
    if (!$result){
        echo "SQL SELECT ERROR:".$dbi->errno;
        return;
    }
    echo "<option value='' selected>すべての倉庫</option>\n";
    while( $row = $result->fetch_array(MYSQLI_BOTH) ){
        echo '<option value="'.$row["id"].'">';
        echo $row["name"];
        echo "</option>\n";
    }
}
?>