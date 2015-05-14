<?php
//セッション開始
session_start();

include "./functions/common_inc.php";

//データベース準備
$dbi = new mysqli($DBHOST, $DBUSER, $DBPASS);
if ($dbi->connect_errno){
	die('Database Connect error : errno = '.$dbi->connect_errno);
} else {
    $dbi->select_db($DBNAME);
}
// Fixed login ID
$admin_id="admin";
$admin_pass="adminpass";

$loginname = "";
$role = -1;

//ログイン
$ids = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING );
$passwd = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING );
if( $ids ){
    $ids = htmlspecialchars(stripslashes($ids));
    $passwd = htmlspecialchars(stripslashes($passwd));
}

$ret = chkuser($ids, $passwd);

if (!$ret){
     if( ($ids == $admin_id && $passwd == $admin_pass) ){
         $ret = TRUE;
         $loginname = $admin_id;
         $role = 0;
     }
}

if ( $ret ){
    $_SESSION['login']=$loginname;
    $_SESSION['role']=$role;
//echo "NAME: ".$_SESSION['login']."<br />";
//echo "ROLE: ".$_SESSION['role']."<br />";
    header("location:home.php");
} else {
    unset($_SESSION['login']);
    header("location:index.html");
}

function chkuser($ids, $passwd){
    global $dbi, $loginname, $role;
    $ret = FALSE;
    $que = "SELECT code, name, role FROM user_master WHERE code='".$ids."' AND password='".$passwd."'";
    $result = $dbi->query($que);
    if ($result->num_rows > 0){
        $row = $result->fetch_array(MYSQLI_BOTH);
        $loginname = $row['name'];
        $role = $row['role'];
        $ret = TRUE;
    }
    return $ret;
}
?>
