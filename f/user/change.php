<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
if (isset($_GET["cmd"]) && isset($_GET["userId"])) {
    $cmd = $_GET["cmd"];
    $userId = $_GET["userId"];
    $user = UserUtil::b_one( [ 'id' => $userId ], [ 'projection' => [ 'email' => 1 ] ]  );
    if ($user != null && $user["email"] != FUNCTREE_ADMINISTRATOR_EMAIL) {
        $where = ['id' => $userId];
        if ($cmd == "enableUser") {//启用
            if (UserUtil::c_one($where, ["status"=>1])) {
                echo "{success:true}";
            } else {
                echo "{success:false}";
            }
        } else if ($cmd == "disableUser") {//禁用
            if (UserUtil::c_one($where, ["status"=>-1])) {
                echo "{success:true}";
            } else {
                echo "{success:false}";
            }
        } else {
            echo "{success:false}";
        }
    } else {
        echo "{success:false}";
    }
}
?>