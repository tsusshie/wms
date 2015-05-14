<?php
//セッション開始
session_start();

include "./functions/common_inc.php";
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
</head>

<body>
<?php
header_out();
?>
<div id="main">
	
<?php
$memo1 = filter_input(INPUT_POST, 'memo1', FILTER_SANITIZE_STRING );
if($memo1){

    //メモ書き換え
    $fp=fopen("memo.txt", "w");
    if( $fp ) {
        $udate=date('Y/m/d-H:i')."<br>";
        $nameid=$_SESSION['login']."<br>";

        $command=$udate."\n".$nameid."\n".$memo1."\n";

        fwrite($fp,$command);
        fclose($fp);
    }else{
        echo "File 'memo.txt' was not able to open.<br>";
    }
}

 $fp=fopen("memo.txt", "r");
if ( $fp ){
?>
<table width="569" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="77"><b>MEMO</b></td>
    <td width="216"><p>更新日時 ： <?php print(fgets($fp)); ?></p></td>
    <td width="276"><p>更新者ID ： <?php print(fgets($fp)); ?></p></td>
  </tr>
</table>

<form method="post" action="home.php" />
<p><textarea name="memo1" cols="80" rows="15"><?php while($row=fgets($fp)){print($row);} ?></textarea></p>
<p><input type="submit"value="更新"></p>
</form>

<?php
	fclose($fp);
        
        footer_out();
}
?>

</body>
</html>