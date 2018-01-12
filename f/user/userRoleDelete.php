<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
if (isset($_POST["userId"]) && isset($_POST["roleId"])) {
    $userId = $_POST["userId"];
    $user = UserUtil::b_one([ "id" => $userId ], [ 'projection' => [ 'email' => 1 ] ]);
    //分配给“系统管理员”的角色只允许“系统管理员”删除
    if ($user["email"] != FUNCTREE_ADMINISTRATOR_EMAIL || $_SESSION["authUserEmail"] == FUNCTREE_ADMINISTRATOR_EMAIL) {
        $roleId = $_POST["roleId"];
        //删除该用户的角色
        UserUtil::d_UserToRole(["userId"=>$userId, "roleId"=>$roleId]);
        ?>
        <div align="center">删除用户角色成功。</div>
        <script type="text/javascript">
        <!--
        setTimeout("toRedirect()", 1000);
        function toRedirect() {
            location.href = "<?php echo FUNCTREE_WEB_ROOT;?>User/userRole?userId=<?php echo $userId;?>";
        }
        //-->
        </script>
        <?php
    } else {
        ?>
        <div align="center">没有权限删除系统管理员的角色。</div>
        <?php
    }
} else {
    ?>
    <div align="center">提交用户角色信息不完整。</div>
    <?php
}
?>