<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$cmd = $_POST["cmd"];
$email = $_POST["email"];
$password = $_POST["password"];
$name = $_POST["name"];
$emailCode = $_POST["emailCode"];
if (isset($email) && isset($password) && isset($name) && isset($emailCode)) {
    if (strtolower($emailCode) == $_SESSION["emailCode"]) {
        unset($_SESSION["emailCode"]);
        $userGroup = "1";//用户分组
        date_default_timezone_set('PRC');
        $addTime = date("Y-m-d H:i:s", time());
        $userId = UserUtil::a($email, UserUtil::md5Password($password), $name, $userGroup, $addTime);
        if ($userId != null) {
            ?>
            <div align="center">注册成功，请登录。</div>
            <script type="text/javascript">
            <!--
            setTimeout("toLogin()", 3000);
            function toLogin() {
                location.href = "<?php echo WEB_ROOT;?>User/login";
            }
            //-->
            </script>
            <?php
        } else {
            ?>
            <div align="center">保存注册信息时出现异常。</div>
            <?php
        }
    } else {
        ?>
        <div align="center">Email验证码不正确，请重新输入。</div>
        <script type="text/javascript">
        <!--
        history.back();
        //-->
        </script>
        <?php
    }
} else {
    ?>
    <div align="center">提交注册信息不完整。</div>
    <?php
}
?>