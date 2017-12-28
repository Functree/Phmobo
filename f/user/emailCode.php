<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$to = $_GET["email"];
$subject = "来自" . WEB_NAME . "的注册激活邮件";
$emailCode = str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_BOTH);
$body = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"><body>您好：<br><br>欢迎加入" . WEB_NAME . "，您的验证码是：$emailCode<br>有效期为20分钟，请立即验证。<br><br>温情提示：请勿回复此邮件，此邮件为系统自动发送。</body></html>";
if (UserUtil::sendMail(WEB_NAME, $to, $subject, $body)) {
    //保存Email验证码到session，以便注册时使用
    $_SESSION["emailCode"] = strtolower($emailCode);
    echo "{success:true}";
} else {
    echo "{success:false, msg:'发送Email失败。'}";
}
?>