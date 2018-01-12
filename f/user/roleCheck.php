<?php
require_once(__DIR__ . '/phplib/RoleUtil.php');
$cmd = $_GET["cmd"];
$success = false;
if ($cmd == "checkId") {//检查角色ID是否存在
    $roleId = $_GET["roleId"];
    if (RoleUtil::b_count(['id'=>$roleId]) == 0) {
        $success = true;
    }
} else if ($cmd == "checkName") {//检查角色名称是否存在
    $roleName = $_GET["roleName"];
    if (RoleUtil::b_count(['name'=>$roleName]) == 0) {
        $success = true;
    }
}
if ($success) {
    echo "{success:true}";
} else {
    echo "{success:false}";
}
?>