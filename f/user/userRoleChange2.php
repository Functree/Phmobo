<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
if (isset($_POST["userId"])) {
    $userId = $_POST["userId"];
    $user = UserUtil::b_one([ "id" => $userId ], [ 'projection' => [ 'email' => 1 ] ]);
    //分配给“系统管理员”的角色只允许“系统管理员”修改
    if ($user != null && ($user["email"] != FUNCTREE_ADMINISTRATOR_EMAIL || $_SESSION["authUserEmail"] == FUNCTREE_ADMINISTRATOR_EMAIL)) {
        //先删除该用户已有的角色
        UserUtil::d_UserToRole(["userId"=>$userId]);
        $roleIds = $_POST["roleId"];
        //再增加新角色
        foreach ($roleIds as $roleId) {
            UserUtil::a_UserToRole($userId, $roleId);
        }
        ?>
        <div align="center">修改用户角色成功。</div>
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
    <div align="center">没有修改系统管理员角色的权限。</div>
    <?php
    }
} else {
    ?>
    <div align="center">提交用户信息不完整。</div>
    <?php
}
?>