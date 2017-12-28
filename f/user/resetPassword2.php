<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
if (isset($_POST["userId"]) && isset($_POST["token"]) && isset($_POST["password"])) {
    $userId = $_POST["userId"];
    $token = $_POST["token"];
    $password = $_POST["password"];
    $document = UserUtil::b_one( [ 'id' => $userId, 'token' => $token ], [ 'projection' => [ 'tokenTime' => 1 ] ] );
    if ($document != null) {
        $tokenTime = $document["tokenTime"];
        $now = time();
        //token过期时间为1小时：3600秒
        if (($tokenTime+3600) > $now) {
            //重置密码，并删除token
            UserUtil::c_one( [ 'id' => $userId ], [ 'password' => UserUtil::md5Password($password), 'token' => '', 'tokenTime' => 0 ] );
        ?>
            <script type="text/javascript">
            <!--
            alert("重置密码成功，将返回网站首页。");
            setTimeout("toHome()", 100);
            function toHome() {
                location.href = "<?php echo WEB_ROOT;?>";
            }
            //-->
            </script>
            <?php
        } else {
            ?>
            <div align="center">重置密码失败，token已过期。</div>
            <?php
        }
    } else {
        ?>
        <div align="center">重置密码失败，token不存在。</div>
        <?php
    }
} else {
    ?>
    <div align="center">提交信息不完整。</div>
    <?php
}
?>