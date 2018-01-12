<?php 
require_once(__DIR__ . '/phplib/UserUtil.php');
require_once(__DIR__ . '/phplib/RoleUtil.php');
if (isset($_POST["userId"])) {
    $userId = $_GET["userId"];
    $user = UserUtil::b_one(["id"=>$userId]);
    //分配给“系统管理员角色”的角色只允许“系统管理员”修改
    if ($user != null && ($user["email"] != FUNCTREE_ADMINISTRATOR_EMAIL || $_SESSION["authUserEmail"] == FUNCTREE_ADMINISTRATOR_EMAIL)) {
        ?>
        <div class="row">
          <div class="col-md-12">
            <ol class="breadcrumb">
                <li><a href="list">用户管理</a></li>
                <li class="active">修改用户角色</li>
            </ol>
          </div>
        </div>
        <h4>修改“<?php echo $user["name"];?>”的角色</h4>
        <form name="userForm" method="post" action="userRoleChange2">
        <input type="hidden" name="userId" value="<?php echo $userId;?>">
            <table class="table table-striped">
                <thead>
                    <tr style="background-color:#f2f9fc;font-weight:bold">
                        <td style="width:30px">&nbsp;</td><td style="width:120px">角色</td><td>权限</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $userRoleArray = array();
            $userRoleList = UserUtil::b_UserToRole(["userId"=>$userId]);
            foreach ($userRoleList as $userRole) {
                array_push($userRoleArray, $userRole["roleId"]);
            }
            $roleList = RoleUtil::b([]);//所有角色列表
            foreach ($roleList as $role) {
                $roleId = $role["id"];
            ?>
                    <tr>
                        <td><input type="checkbox" name="roleId[]"<?php if (in_array($roleId, $userRoleArray)) {?> checked="checked"<?php }?> value="<?php echo $roleId;?>"></td>
                        <td><?php echo $role["name"];?></td><td>
                        <?php
                        $permissionStr = "";
                        $allPermission = FUNCTREE_PERMISSION_LIST;
                        $permissionList = RoleUtil::b_RoleToPermission(["roleId"=>$roleId]);
                        foreach ($permissionList as $permission) {
                            $permissionId = $permission["permissionId"];
                            $permissionStr .= ",".(isset($allPermission[$permissionId]) ? $allPermission[$permissionId] : $permissionId);
                        }
                        echo $permissionStr == "" ? "" : substr($permissionStr, 1);
                        ?>
                        </td>
                    </tr>
            <?php }?>
                </tbody>
            </table>
            <div class="row">
            <div class="col-md-4">
            </div>
            <div class="col-md-2">
                <button type="button" id="cancel" onclick="history.go(-1)" class="btn btn-primary btn-block">返回</button>
            </div>
            <div class="col-md-2">
                <button type="button" id="submitBtn" onclick="javascript:confirmSubmit()" class="btn btn-primary btn-block">提交</button>
            </div>
            <div class="col-md-4">
            </div>
            </div>
        </form>
        <script type="text/javascript">
        <!--
        function confirmSubmit() {
            if ($("input[name='roleId[]']:checked").length == 0) {
                alert("请选择角色。");
            } else {
                if (confirm("确定修改此用户的角色吗？")) {
                    userForm.submit();
                }
            }
        }
        //-->
        </script>
        <?php
    } else {
        ?>
        <div align="center">要修改用户不存在。</div>
        <?php
    }
} else {
    ?>
    <div align="center">提交用户信息不完整。</div>
    <?php
}
?>
