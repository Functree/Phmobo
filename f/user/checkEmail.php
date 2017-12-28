<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$email = $_GET["email"];
$where = ['email' => $email];
if (UserUtil::b_count($where) == 0) {
    echo "{success:true}";
} else {
    echo "{success:false}";
}
?>