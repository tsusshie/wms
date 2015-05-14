<?php

//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$tmp_dir = "../uploads/";
$upload_dir = "./uploadfiles/";
$table_id = "item_master";      // Table Identifier
//
//ログインチェック
checkLogin();

$msg = "";
$fp = false;

$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);
if ($click){
    $tmp_fname = $_FILES['uploadfile']['tmp_name'];
    $fname = $upload_dir.$_FILES['uploadfile']['name'];
    if (move_uploaded_file($tmp_fname, $fname)){
        chmod($fname, 0644);
        echo "UPLOAD $fname SUCCESS.<br />";
    }
    echo "TMPFILE NAME:".$tmp_fname."<br />";
    if ($tmp_fname == ""){
        $msg .= "File is not specified.<br />\n";
    }else{
        $fp=fopen($fname, "r");
        if (!$fp){
            echo "CAN't OPEN UPLOADED FILE.<br />\n";
        }else{
            echo "UPLOADED FILE WAS OPENED!!<br />\n";
        }
    }
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex">
<title>情報修正｜在庫管理システム</title>
</head>
<body>
    <h1><font color="red">注意：この操作により商品データはすべて失われます。</font></h1>
    <form enctype="multipart/form-data" action="itemupload.php" method="POST">
        <input type="hidden" name="MAX_FILE_SIZE" value="50000" />
        ファイルを選択:<br/>
        <input type='file' name='uploadfile' size="50" /><br /><br />
        <input name="click" type='submit' value='アップロード' />
    </form>
    <p><a href='item.php'> BACK TO ITEMLIST </a></p><br />
<?php 
if ($fp){
    $dbi = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
    }
    // 全レコード削除
    $command = "TRUNCATE $table_id ;";
    echo "DELETE SQL :".$command."<br />";
    $dbi->query($command);
    if($dbi->errno){
        echo "DELETE ERROR : ".$dbi->errno."<br />";
    }
    
    date_default_timezone_set('Asia/Tokyo');
    $udate=date('Y/m/d-H:i');
    $status = 1; // This means "有効"
    while( $str = fgets($fp) ){
        $row = explode(",", $str);
        if ($row[0] == "" || $row[1] == ""){
            continue;
        }
        if ( !isset($row[2])){
            $remark = "";
        }else{
            $remark = mb_convert_encoding($row[2], "UTF-8");
        }
        $code = mb_convert_encoding($row[0], "UTF-8");
        $name = mb_convert_encoding($row[1], "UTF-8");

        $command  = "INSERT INTO $table_id (code, name, remark, status, udate) ";
        $command .= "VALUES ('{$code}','{$name}','{$remark}',{$status},'{$udate}');";
        $dbi->query($command);
        echo "QUERY :".$command."<br />";
    }
    
    $dbi->close();
    fclose($fp);
}
?>

</body>
</html>