<?php

include "./functions/common_inc.php";


//データベース準備
$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
} else {
    $dbi->select_db($DBNAME);
}

?>

<html>
<head>
<title>Reset All Tables</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./scripts/jquery191.js"></script>
</head>
<body>

<?php
$val = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING );
if ( $val == NULL){
    ?>
    <h2>tableを初期化します。よろしいですか？</h2>
    <table>
        <tr>
            <td><input type="button" value="OK" onclick="location.href='tableReset.php?action=yes'" /></td>
            <td><input type="button" value="HOME" onClick="location.href='index.html'"></td>
        </tr>
    </table>
    <?php
} else if ($val == "yes") {
    $ret = resettable();
    if ($ret) {
        echo "<h2>テーブル を初期化しました。</h2><br />";
    } else {
        echo "<h2>テーブル の初期化に失敗しました。設定を確認してください。</h2><br />";
    }
        $dbi->close();
}

//===============================
//   Item Table Reset Function
//    Create or Remake all tables.(Call ONLY when just installed)
//===============================
function resettable()
{
     global $dbi;
    $ret = FALSE;
    $query = "DROP TABLE IF EXISTS t_shipping_req";
    if (!$dbi->query($query)){
        echo "DROP ERROR:".$dbi->errno."<br />";
        return $ret;
    }
    $query = "CREATE TABLE t_shipping_req (
        id	int NOT NULL AUTO_INCREMENT,
        shipping_code	varchar(12) NOT NULL,
        client_master_id     int,
        wh_id   int,
        user        varchar(64),
        remark      text,
        udate       datetime,
        PRIMARY KEY (id)
    )";
    if ( $dbi->query($query)){
        $ret = TRUE;
    }
	return $ret;
}

?>
    
</body>
</html>
