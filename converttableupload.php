<?php

//セッション開始
session_start();

include "./functions/common_inc.php";
include "./functions/commonfunc.php";

$tmp_dir = "../uploads/";
$upload_dir = "./uploadfiles/";
$table_id = "t_item_convert";      // Table Identifier
//
//ログインチェック
checkLogin();

$msg = "";
$fFileUploaded = false;
$fp = false;

$click = filter_input(INPUT_POST, 'click', FILTER_SANITIZE_STRING);
if ($click){
    $tmp_fname = $_FILES['uploadfile']['tmp_name'];
    $fname = $upload_dir.$_FILES['uploadfile']['name'];
    if (move_uploaded_file($tmp_fname, $fname)){
        chmod($fname, 0644);
        echo "UPLOAD $fname SUCCESS.<br />\n";
    }
    echo "TMPFILE NAME:".$tmp_fname."<br />\n";
    if ($tmp_fname == ""){
        $msg .= "File is not specified.<br />\n";
    }else{
        $fp=fopen($fname, "r");
        if (!$fp){
            echo "CAN't OPEN UPLOADED FILE.<br />\n";
        }else{
            echo "UPLOADED FILE WAS OPENED!!<br />\n";
            $fFileUploaded = true;
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
    <h1><font color="red">注意：この操作により商品変換データはすべて失われます。</font></h1>
    <form enctype="multipart/form-data" action="converttableupload.php" method="POST">
        <input type="hidden" name="MAX_FILE_SIZE" value="50000" />
        ファイルを選択:<br />
        <input type='file' name='uploadfile' size="50" /><br /><br />
        <input name="click" type='submit' value='アップロード' />
    </form>
    <p><a href='item_conversion.php'> BACK TO ITEM_CONVERSION LIST </a></p><br />
<?php 
if ($fFileUploaded){
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
        if ($row[2] == ""){
            continue;
        }
        if ( !isset($row[6])){
            $remark = "''";
        }else{
            $remark = mb_convert_encoding($row[6], "UTF-8");
        }
        $client_id = mb_convert_encoding($row[0], "UTF-8");
        $item_code = mb_convert_encoding($row[1], "UTF-8");
        $client_item_code = mb_convert_encoding($row[2], "UTF-8");
        $client_item_name = mb_convert_encoding($row[3], "UTF-8");
        $item_count = mb_convert_encoding($row[4], "UTF-8");
        $label_code = mb_convert_encoding($row[5], "UTF-8");
        //$remark = mb_convert_encoding($row[6], "UTF-8");

        $command  = "INSERT INTO $table_id (client_id, item_code, client_item_code, client_item_name, item_count, label_code, remark) ";
        $command .= "VALUES ({$client_id},{$item_code},{$client_item_code},{$client_item_name},{$item_count},{$label_code},{$remark});";
        //$command .= "VALUES ({$client_id},'{$item_code}','{$client_item_code}','{$client_item_name}',{$item_count},'{$label_code}','{$remark}');";
        if ($dbi->query($command)){
            echo "QUERY :".$command."<br />\n";
        }else{
            echo "INSERT ERROR : ".$dbi->errno."<br />\n";
            echo "ERROR QUE :".$command."<br />\n";
        }
    }
    
    $dbi->close();
    fclose($fp);
}
?>

</body>
</html>