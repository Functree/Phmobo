<?php
require_once(__DIR__ . '/phplib/RoleUtil.php');
if (isset($_POST["roleId"]) && isset($_POST["roleName"])) {
    $roleId = $_POST["roleId"];
    $roleName = $_POST["roleName"];
    //增加新角色
    if (RoleUtil::a($roleId, $roleName)) {
        ?>
        <div align="center">增加角色成功。</div>
        <script type="text/javascript">
        <!--
        setTimeout("toRedirect()", 300);
        function toRedirect() {
            location.href = "<?php echo FUNCTREE_WEB_ROOT;?>User/role";
        }
        //-->
        </script>
        <?php
    } else {
        ?>
        <div align="center">保存角色信息时出现异常。</div>
        <?php
    }
} else {
    ?>
    <div align="center">提交角色信息不完整。</div>
    <?php
}
?>