<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$cmd = $_POST["cmd"];
$email = $_POST["email"];
$password = $_POST["password"];
$imageCode = $_POST["imageCode"];
if (isset($email) && isset($password) && isset($imageCode)) {
    if (strtolower($imageCode) == $_SESSION["imageCode"]) {
        unset($_SESSION["imageCode"]);
        $document = UserUtil::b_one( [ 'email' => $email ], [ 'limit' => 1, 'projection' => [ 'id' => 1, 'name' => 1, 'password' => 1 ] ] );
        if ($document != null) {
            $exist = true;//登录Email存在
            $id = $document["id"];
            $dbPassword = $document["password"];
            if (UserUtil::md5Password($password) == $dbPassword) {
                //登录成功，重新刷新该用户的菜单列表
                $_SESSION["loginUserId"] = $id;
                $_SESSION["loginUserName"] = $document["name"];
                unset($_SESSION["menuLeftList"]);
                unset($_SESSION["menuRightList"]);
            ?>
                <div align="center">登录成功，将返回网站首页。</div>
                <script type="text/javascript">
                <!--
                setTimeout("toLogin()", 3000);
                function toLogin() {
                    location.href = "<?php echo WEB_ROOT;?>";
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