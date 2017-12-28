<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$email = $_POST["email"];
$imageCode = $_POST["imageCode"];
if (isset($email) && isset($imageCode)) {
    if (strtolower($imageCode) == $_SESSION["imageCode"]) {
        unset($_SESSION["imageCode"]);
        $document = UserUtil::b_one( [ 'email' => $email ], [ 'projection' => [ 'id' => 1, 'name' => 1 ] ] );
        if ($document != null) {
            $userId = $document["id"];
            $userName = $document["name"];
            $now = time();
            $token = md5($userId.$email.$now);
            $resetPasswordUrl = WEB_ROOT."User/resetPassword?userId=$userId&token=$token";
            UserUtil::c_one( [ 'id' => $userId ], [ 'token' => $token, 'tokenTime' => $now ] );
            $to = $email;
            $subject = "来自" . WEB_NAME . "的重置密码邮件";
            $body = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"><body>尊敬的".$userName."：<br><br>您正在重置您在" . WEB_NAME . "的用户密码，请点击下面链接完成重置密码操作：<br><a href=\"$resetPasswordUrl\">$resetPasswordUrl</a><br><br>请在1小时内完成操作，谢谢！<br><br>温情提示：请勿回复此邮件，此邮件为系统自动发送。</body></html>";
            if (UserUtil::sendMail(WEB_NAME, $to, $subject, $body)) {
            ?>
                <script type="text/javascript">
                <!--
                alert("发送重置密码邮件成功，将返回网站首页。");
                setTimeout("toLogin()", 100);
                function toLogin() {
                    location.href = "<?php echo WEB_ROOT;?>";
                }
                //-->
                </script>
                <?php
            } else {
                ?>
                <div align="center">发送重置密码邮件失败。</div>
                <?php
            }
        } else {
            ?>
            <div align="center">发送重置密码邮件失败，Email不存在。</div>
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
    <div align="center">提交信息不完整。</div>
    <?php
}
?>