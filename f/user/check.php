<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
if (isset($_GET["cmd"])) {
    $cmd = $_GET["cmd"];
    if ($cmd == "checkEmail" && isset($_GET["email"])) {//检查email是否存在
        $email = $_GET["email"];
        $where = ['email' => $email];
        if (UserUtil::b_count($where) == 0) {
            echo "{success:true}";
        } else {
            echo "{success:false}";
        }
    } else if ($cmd == "checkName" && isset($_GET["name"])) {//检查名号是否存在
        $name = $_GET["name"];
        $where = ['name' => $name];
        if (UserUtil::b_count($where) == 0) {
            echo "{success:true}";
        } else {
            echo "{success:false}";
        }
    } else {
        echo "{success:false}";
    }
}
?>