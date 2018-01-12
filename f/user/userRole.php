<?php 
require_once(__DIR__ . '/phplib/UserUtil.php');
require_once(__DIR__ . '/phplib/RoleUtil.php');
if (isset($_GET["userId"])) {
    $userId = $_GET["userId"];
    $user = UserUtil::b_one(["id"=>$userId]);
    if ($user != null) {
?>
    <div class="row">
      <div class="col-md-12">
        <ol class="breadcrumb">
            <li><a href="list">用户管理</a></li>
            <li class="active">查看用户角色</li>
        </ol>
      </div>
    </div>
    <div class="row">
    <div class="col-md-10"><h4>“<?php echo $user["name"]?>”的角色列表</h4></div>
    <div class="col-md_2 text-right">
    <?php
    if (in_array("User_c", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"]) && ($user["email"] != FUNCTREE_ADMINISTRATOR_EMAIL || $_SESSION["authUserEmail"] == FUNCTREE_ADMINISTRATOR_EMAIL)) {//具备修改用户权限
    ?>
        <a class="btn btn-primary" style="margin-right:15px" href="userRoleChange?userId=<?php echo $userId;?>" role="button">更改角色</a>
    <?php
    }
    ?>
    </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr style="background-color:#f2f9fc;font-weight:bold">
                <td width="120px">角色名称</td><td>权限</td><td width="60px">操作</td>
            </tr>
        </thead>
        <tbody>
    <?php
    $roleList = UserUtil::b_UserToRole(["userId"=>$userId]);
    foreach ($roleList as $userRole) {
        $roleId = $userRole["roleId"];
        $role = RoleUtil::b_one(["id"=>$roleId]);
        $permissionList = RoleUtil::b_RoleToPermission(["roleId"=>$roleId]);
    ?>
            <tr>
                <td><?php echo $role["name"];?></td><td>
                <?php
                $permissionStr = "";
                $allPermissions = FUNCTREE_PERMISSION_LIST;
                foreach ($permissionList as $permission) {
                    $permissionId = $permission["permissionId"];
                    $permissionStr .= ",".(isset($allPermissions[$permissionId]) ? $allPermissions[$permissionId] : $permissionId);
                }
                echo $permissionStr == "" ? "" : substr($permissionStr, 1);
                ?>
                </td><td>
                <?php if ($user["email"] != FUNCTREE_ADMINISTRATOR_EMAIL || $_SESSION["authUserEmail"] == FUNCTREE_ADMINISTRATOR_EMAIL) {?>
                <a href="javascript:d_userRole('<?php echo $roleId;?>')" target="_blank">删除</a>
                <?php }?>
                </td>
            </tr>
    <?php }?>
        </tbody>
    </table>
    <form name="userForm" method="post" action="userRoleDelete">
    <input type="hidden" name="userId" value="<?php echo $userId;?>">
    <input type="hidden" name="roleId" value="">
    </form>
    <script type="text/javascript">
    <!--
    function d_userRole(roleId) {
        if (confirm("确定要删除用户的这个角色吗？")) {
            userForm.roleId.value = roleId;
            userForm.submit();
        }
    }
    //-->
    </script>
    <?php
    } else {?>
    <div align="center">用户不存在。</div>
<?php
    }
} else {
?>
    <div align="center">提交用户信息不完整。</div>
<?php
}?>
