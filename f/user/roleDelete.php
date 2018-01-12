<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
require_once(__DIR__ . '/phplib/RoleUtil.php');
if (isset($_POST["roleId"])) {
    $roleId = $_POST["roleId"];
    //系统定义的“系统管理员角色”、“用户默认角色”不允许删除
    if ($roleId != FUNCTREE_USER_ROLE_ADMINISTRATOR_ID && $roleId != FUNCTREE_USER_ROLE_DEFAULT_ID && RoleUtil::d_one(['id'=>$roleId])) {
        //删除该角色已有的权限
        RoleUtil::d_RoleToPermission(["roleId"=>$roleId]);
        //删除被分配给用户的该角色关系
        UserUtil::d_UserToRole(["roleId"=>$roleId]);
        ?>
        <div align="center">删除角色成功。</div>
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
        <div align="center">删除角色失败。</div>
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