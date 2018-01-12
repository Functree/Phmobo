<?php
require_once(__DIR__ . '/phplib/RoleUtil.php');
if (isset($_POST["roleId"]) && isset($_POST["roleName"])) {
    $roleId = $_POST["roleId"];
    $roleName = $_POST["roleName"];
    $role = RoleUtil::b_one(["id"=>$roleId], ["projection"=>["name"=>1]]);
    //如果提交角色名称与已有名称相等，或者提交角色名称已修改但提交名称不存在，同时，系统定义的“系统管理员角色”、“用户默认角色”只允许“系统管理员”修改
    if (($roleId != FUNCTREE_USER_ROLE_ADMINISTRATOR_ID && $roleId != FUNCTREE_USER_ROLE_DEFAULT_ID || $_SESSION["authUserEmail"] == FUNCTREE_ADMINISTRATOR_EMAIL) 
        && ($role["name"] == $roleName || RoleUtil::b_count(['name'=>$roleName]) == 0)) {
        if ($role["name"] != $roleName) {
            RoleUtil::c_one(["id"=>$roleId], ["name"=>$roleName]);
        }
        //先删除该角色已有的权限
        RoleUtil::d_RoleToPermission(["roleId"=>$roleId]);
        $permissionIds = $_POST['permissionId'];
        //再增加新权限
        foreach ($permissionIds as $permissionId) {
            RoleUtil::a_RoleToPermission($roleId, $permissionId);
        }
        ?>
        <div align="center">修改角色成功。</div>
        <script type="text/javascript">
        <!--
        setTimeout("toRedirect()", 1000);
        function toRedirect() {
            location.href = "<?php echo FUNCTREE_WEB_ROOT;?>User/role";
        }
        //-->
        </script>
        <?php
    } else {
        ?>
        <div align="center">提交角色名称已存在。</div>
        <script type="text/javascript">
        <!--
        setTimeout("toRedirect()", 1000);
        function toRedirect() {
            history.go(-1);
        }
        //-->
        </script>
        <?php
    }
} else {
    ?>
    <div align="center">提交角色信息不完整。</div>
    <?php
}
?>