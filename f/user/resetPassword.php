<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$allowRestPassword = false;
if (isset($_GET["userId"]) && isset($_GET["token"])) {
    $userId = $_GET["userId"];
    $token = $_GET["token"];
    $document = UserUtil::b_one( [ 'id' => $userId, 'token' => $token ], [ 'projection' => [ 'email' => 1, 'tokenTime' => 1 ] ] );
    if ($document != null) {
        $email = $document["email"];
        $tokenTime = $document["tokenTime"];
        $now = time();
        //token过期时间为1小时：3600秒
        if (($tokenTime+3600) > $now) {
            $allowRestPassword = true;
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

//如果不允许重置密码，则停止处理
if (!$allowRestPassword) {
    exit;
}
?>
<script>
function hideAlert() {
    $("#alertDiv").css("display", "none");
}
function showAlert(alertMsg) {
    $("#alertDiv").css("display", "block");
    $("#alertSpan").html(alertMsg);
}
function validPasswordFormat() {
    var password = resetForm.password.value;
    if (password == "") {
        showAlert("请输入密码。");
        resetForm.password.focus();
        return false;
    }
    var regex = new RegExp('(?=.*[0-9])(?=.*[a-zA-Z]){8,30}');
    if (!regex.test(password)) {
        showAlert("请输入正确的密码，必须包含字母和数字且不能小于8位。");
        resetForm.password.focus();
        return false;
    }
    return true;
}
function checkSubmit() {
    if (!validPasswordFormat())
        return false;
    else
        return true;
}
</script>
    <div class="row text-center">
        <div>
            <img src="<?php echo WEB_ROOT.FUNC_PATH;?>user/images/logo.png" style="margin-top:30px;margin-bottom:30px">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-1 col-md-4">
        </div>
        <div class="col-xs-10 col-md-4">
            <form class="form-horizontal" name="resetForm" method="post" action="<?php echo WEB_ROOT;?>User/resetPassword2" onsubmit="javascript: return checkSubmit()">
            <input type="hidden" name="cmd" value="resetPasswordComplete">
            <input type="hidden" name="userId" value="<?php echo $userId;?>">
            <input type="hidden" name="token" value="<?php echo $token;?>">
                <div class="form-group">
                    <div>
                        <label class="control-label" for="password">重置 <?php echo $email;?> 账号的密码：</label>
                        <input type="password" id="password" name="password" onkeyup="javascript:hideAlert()" class="form-control" placeholder="请输入登录密码">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <button type="submit" id="resetBtn" class="btn btn-primary btn-block">重置密码</button>
                    </div>
                </div>
            </form>
            <div id="alertDiv" style="display:none" class="alert alert-danger" role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span class="sr-only">错误：</span>
                <span id="alertSpan"></span>
            </div>
        </div>
        <div class="col-xs-1 col-md-4">
        </div>
    </div>
