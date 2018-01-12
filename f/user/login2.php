<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
require_once(__DIR__ . '/phplib/RoleUtil.php');
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["imageCode"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $imageCode = $_POST["imageCode"];
    if (strtolower($imageCode) == $_SESSION["imageCode"]) {
        unset($_SESSION["imageCode"]);
        $user = UserUtil::b_one( [ 'email' => $email ], [ 'limit' => 1, 'projection' => [ 'id' => 1, 'email' => 1, 'name' => 1, 'password' => 1, 'status' => 1 ] ] );
        if ($user != null) {
            $status = $user["status"];
            //“有效”状态用户才允许登录
            if ($status == 1) {
                $dbPassword = $user["password"];
                if (UserUtil::md5Password($password) == $dbPassword) {
                    //登录成功，缓存用户信息
                    $id = $user["id"];
                    $_SESSION["authUserId"] = $id;
                    $_SESSION["authUserEmail"] = $user["email"];
                    $_SESSION["authUserName"] = $user["name"];
                    //获取用户角色所具备权限，并缓存权限ID
                    $authPermissions = array("UserLogin");//已登录用户均具备“已登录”权限：UserLogin
                    $roleList = UserUtil::b_UserToRole([ "userId" => $id ]);
                    foreach ($roleList as $role) {
                        $permissionList = RoleUtil::b_RoleToPermission([ "roleId" => $role["roleId"]]);
                        foreach ($permissionList as $permission) {
                            array_push($authPermissions, $permission["permissionId"]);
                        }
                    }
                    //去除重复的permissionId
                    $authPermissions = array_flip(array_flip($authPermissions));
                    $_SESSION["FUNCTREE_AUTH_PERMISSIONS"] = $authPermissions;
                    //登录成功后，重置导航菜单
                    unset($_SESSION["FUNCTREE_MENU_LEFT_LIST"]);
                    unset($_SESSION["FUNCTREE_MENU_RIGHT_LIST"]);
                    ?>
                    <div align="center">登录成功。</div>
                    <script type="text/javascript">
                    <!--
                    setTimeout("toLogin()", 1000);
                    function toLogin() {
                        location.href = "<?php echo FUNCTREE_WEB_ROOT;?>User/baseInfo";
                    }
                    //-->
                    </script>
                    <?php
                } else {
                    ?>
                    <div align="center">登录失败，密码错误。</div>
                    <?php
                }
            } else {
                ?>
                <div align="center">登录失败，用户状态异常。</div>
                <?php
            }
        } else {
            ?>
            <div align="center">登录失败，Email不存在。</div>
            <?php
        }
    } else {
        ?>
        <script type="text/javascript">
        <!--
        alert("图片验证码不正确，请重新输入。");
        history.back();
        //-->
        </script>
        <?php
    }
} else {
    ?>
    <div align="center">提交登录信息不完整。</div>
    <?php
}
?>