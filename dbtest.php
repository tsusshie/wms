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
$ret = resetitemtable();
if ($ret==1) {
    echo "<h2>テーブル item_master を初期化しました。</h2><br />";
} else if ($ret==2) {
    echo "<h2>テーブル item_master のCREATEに失敗しました。設定を確認してください。</h2><br />";
} else {
    echo "<h2>テーブル item_master のDROPに失敗しました。</h2><br />"; 
}

$dbi->close();

function resetitemtable()
{
    global $dbi;
    $query = "DROP TABLE IF EXISTS item_master";
    if (!$dbi->query($query)){
        $ret = 0;
    }
    $query = "CREATE TABLE testtable (
        id		varchar(12) NOT NULL,
        name		varchar(128) NOT NULL,
        status              int,
        udate		datetime,
        PRIMARY KEY (id)
    )";
    if ( $dbi->query($query)){
        $ret = 1;
    } else{
        $ret = 2;
    }
    echo "CREATE ERROR :".$dbi->errno."<br />";
    return $ret;
}

    
?>
</body>
</html>