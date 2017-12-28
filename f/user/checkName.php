<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$name = $_GET["name"];
$where = ['name' => $name];
if (UserUtil::b_count($where) == 0) {
    echo "{success:true}";
} else {
    echo "{success:false}";
}
?>