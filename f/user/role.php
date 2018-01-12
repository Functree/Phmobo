<?php 
require_once(__DIR__ . '/phplib/RoleUtil.php');
?>
            <div class="row">
            <div class="col-md-10"><h4>角色管理</h4></div>
            <div class="col-md_2 text-right">
            <?php if (in_array("UserRole_a", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {?>
                <a class="btn btn-primary" style="margin-right:15px" href="roleAdd" role="button">增加新角色</a>
            <?php }?>
            </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr style="background-color:#f2f9fc;font-weight:bold">
                        <td width="120px">角色ID</td><td width="120px">角色名称</td><td>权限</td><td width="120px">操作</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $roleArray = RoleUtil::b([], ['sort'=>['name'=>1]]);
            foreach ($roleArray as $role) {
                $roleId = $role["id"];
                $roleName = $role["name"];
                $permissionArray = RoleUtil::b_RoleToPermission([ 'roleId' => $roleId ], [ 'projection' => [ 'permissionId' => 1 ] ]);
            ?>
                    <tr>
                        <td><?php echo $roleId; ?></td>
                        <td><?php echo $roleName; ?></td><td><div style="float:left; display:inline;">
                        <?php
                        $permissionStr = "";
                        $allPermission = FUNCTREE_PERMISSION_LIST;
                        foreach ($permissionArray as $permission) {
                            $permissionId = $permission["permissionId"];
                            $permissionStr .= ",".(isset($allPermission[$permissionId]) ? $allPermission[$permissionId] : $permissionId);
                        }
                        echo $permissionStr == "" ? "" : substr($permissionStr, 1);
                        ?>
                        </div></td><td>
                        <?php if (in_array("UserRole_d", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"]) && $roleId != FUNCTREE_USER_ROLE_ADMINISTRATOR_ID && $roleId != FUNCTREE_USER_ROLE_DEFAULT_ID) {//“系统管理员角色”和“用户默认角色”不允许被删除?>
                        <a href="javascript:d_role('<?php echo $roleId;?>', '<?php echo $roleName;?>')" target="_blank">删除</a> 
                        <?php }?>
                        <?php if (in_array("UserRole_c", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"]) && ($roleId != FUNCTREE_USER_ROLE_ADMINISTRATOR_ID && $roleId != FUNCTREE_USER_ROLE_DEFAULT_ID || $_SESSION["authUserEmail"] == FUNCTREE_ADMINISTRATOR_EMAIL)) {?>
                        <a href="roleChange?roleId=<?php echo $roleId;?>">更改</a>
                        <?php }?>
                        </td>
                    </tr>
            <?php }?>
                </tbody>
            </table>
<form name="userForm" method="post" action="roleDelete">
<input type="hidden" name="roleId" value="">
</form>
<script type="text/javascript">
<!--
function d_role(roleId, roleName) {
    if (confirm("确定要删除“"+roleName+"”这个角色吗？")) {
        userForm.roleId.value = roleId;
        userForm.submit();
    }
}
//-->
</script>
