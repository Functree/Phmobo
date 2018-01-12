<?php 
require_once(__DIR__ . '/phplib/RoleUtil.php');
if (isset($_GET["roleId"])) {
    $roleId = $_GET["roleId"];
    $role = RoleUtil::b_one(["id"=>$roleId], ["projection"=>["name"=>1]]);
    //系统定义的“系统管理员角色”、“用户默认角色”只允许“系统管理员”修改
    if ($role != null 
        && ($roleId != FUNCTREE_USER_ROLE_ADMINISTRATOR_ID && $roleId != FUNCTREE_USER_ROLE_DEFAULT_ID || $_SESSION["authUserEmail"] == FUNCTREE_ADMINISTRATOR_EMAIL)) {
        ?>
        <form name="roleForm" method="post" action="roleChange2">
            <input type="hidden" name="roleId" value="<?php echo $roleId;?>">
            <div class="row">
              <div class="col-md-12">
                <ol class="breadcrumb">
                    <li><a href="role">角色管理</a></li>
                    <li class="active">修改角色</li>
                </ol>
              </div>
            </div>
            <h5><input type="text" name="roleName" value="<?php echo $role["name"];?>">的权限列表</h5>
            <table class="table table-striped">
                <thead>
                    <tr style="background-color:#f2f9fc;font-weight:bold">
                        <td style="width:30px">&nbsp;</td><td>权限</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $allPermissions = FUNCTREE_PERMISSION_LIST;
            $userPermissions = array();
            $permissionArray = RoleUtil::b_RoleToPermission(["roleId"=>$roleId], ["projection"=>["permissionId"=>1]]);
            foreach ($permissionArray as $permission) {
                array_push($userPermissions, $permission["permissionId"]);
            }
            $allPermissions = FUNCTREE_PERMISSION_LIST;
            foreach ($allPermissions as $permissionId=>$permissionName) {
            ?>
                    <tr>
                        <td><input type="checkbox" name="permissionId[]"<?php if (in_array($permissionId, $userPermissions)) {?> checked="checked"<?php }?> value="<?php echo $permissionId;?>"></td>
                        <td><?php echo $permissionName;?></td>
                    </tr>
            <?php }?>
                </tbody>
            </table>
            <div class="row">
            <div class="col-md-2 col-md-offset-5">
                <button type="button" id="submitBtn" onclick="javascript:confirmSubmit()" class="btn btn-primary btn-block">提交</button>
            </div>
            </div>
        </form>
        <script type="text/javascript">
        <!--
        function confirmSubmit() {
            if ($("input[name='permissionId[]']:checked").length == 0) {
                alert("请选择权限。");
            } else {
                if (confirm("确定要修改此角色信息吗？")) {
                    roleForm.submit();
                }
            }
        }
        $(function(){
            $("input[name='permissionId[]']").click(function(){
                var v = $(this).val();
                var pos = v.lastIndexOf("_");
                var suffix = "";
                if (pos > 0) suffix = v.substring(pos);
                //a、c、d权限均需要先有b权限
                if ($(this).prop("checked") && (suffix == "_a" || suffix == "_c" || suffix == "_d")) {
                    $("input[value='" + v.substring(0, pos) + "_b']").prop("checked", "checked");
                }
                if ($(this).prop("checked") == false && (suffix == "_b")) {
                    if ($("input[value='" + v.substring(0, pos) + "_a']").prop("checked") 
                            || $("input[value='" + v.substring(0, pos) + "_c']").prop("checked")
                            || $("input[value='" + v.substring(0, pos) + "_d']").prop("checked")) {
                        $(this).prop("checked", "checked");
                    }
                }
            });
        });
        //-->
        </script>
        <?php
    } else {
        ?>
        <div align="center">要修改角色不存在。</div>
        <?php
    }
} else {
    ?>
    <div align="center">提交角色信息不完整。</div>
    <?php
}
?>
