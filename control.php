<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/dbclass.php";
include "./functions/commonfunc.php";

date_default_timezone_set("Asia/Tokyo");
//ログインチェック
checkLogin();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>HOME｜在庫管理システム</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
</head>

<body>
<?php
header_out();
?>

<div id="main">
<h2 class="h2syk">データベース管理(HOME)</h2>
	
<table width="569" border="1" cellpadding="5" cellspacing="5">
  <tr>
      <td><strong><a href="item.php">商品管理</a></strong></td>
  </tr>
  <tr>
      <td><strong><a href="user.php">ユーザ管理</a></strong></td>
  </tr>
  <tr>
      <td><strong><a href="warehouse.php">倉庫管理</a></strong></td>
  </tr>
  <tr>
      <td><strong><a href="client.php">取引先管理</a></strong></td>
  </tr>
  <tr>
      <td><strong><a href="item_conversion.php">商品番号変換テーブル管理</a></strong></td>
  </tr>
  <tr>
      <td><strong><a href="orderhistory.php">購入履歴</a></strong></td>
  </tr>
  <tr>
      <td><strong><a href="receivinghistory.php">入荷履歴</a></strong></td>
  </tr>
  <tr>
      <td><strong><a href="shippinghistory.php">出荷履歴</a></strong></td>
  </tr>
  <tr>
      <td><strong><a href="inventory.php">在庫チェック</a></strong></td>
  </tr>
  <tr>
      <td><strong><a href="receiveshiphistory.php">入出荷履歴</a></strong></td>
  </tr>
</table>
</div>
    
<div id="addition">
    <a href="resetdatatables.php">一部データのリセット</a>
</div>
    
<?php
footer_out();
 ?>
</body>

</html>