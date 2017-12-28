<script>
function hideAlert() {
    $("#alertDiv").css("display", "none");
}
function showAlert(alertMsg) {
    $("#alertDiv").css("display", "block");
    $("#alertSpan").html(alertMsg);
}
function validEmail() {
    var email = loginForm.email.value;
    if (email == "") {
        showAlert("请输入电子邮箱。");
        loginForm.email.focus();
        return false;
    }
    var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
    if (!pattern.test(email) || email.length > 100) {
        showAlert("请输入正确的电子邮箱。");
        loginForm.email.focus();
        return false;
    }
    return true;
}
function validPassword() {
    var password = loginForm.password.value;
    if (password == "") {
        showAlert("请输入密码。");
        loginForm.password.focus();
        return false;
    }
    var regex = new RegExp('(?=.*[0-9])(?=.*[a-zA-Z]).{8,30}');
    if (!regex.test(password)) {
        showAlert("请输入正确的密码，必须包含字母和数字且不能小于8位。");
        loginForm.password.focus();
        return false;
    }
    return true;
}
function validImageCode() {
    var imageCode = loginForm.imageCode.value;
    var regex = new RegExp('(?=.*[a-zA-Z0-9]).{4}');
    if (!regex.test(imageCode)) {
        showAlert("请输入验证码。");
        loginForm.imageCode.focus();
        return false;
    } else {
        return true;
    }
}
function checkEmail() {
    hideAlert();
    var email = loginForm.email.value;
    email && validEmail();
}
function checkSubmit() {
    if (!validEmail() || !validPassword() || !validImageCode())
        return false;
    else
        return true;
}
function updateCodeImage()
{
    document.getElementById("codeImage").src = '<?php echo WEB_ROOT;?>User/imageCode?d='+Math.random();
}
$(function(){
    updateCodeImage();
    $("#email").focus();
});
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
            <form class="form-horizontal" name="loginForm" method="post" action="<?php echo WEB_ROOT;?>User/login2" onsubmit="javascript: return checkSubmit()">
            <input type="hidden" name="cmd" value="login">
                <div class="form-group">
                    <div>
                        <input type="text" id="email" name="email" onkeyup="javascript:hideAlert()" onblur="javascript:checkEmail()" class="form-control" placeholder="电子邮箱">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <table style="width:100%"><tr><td><input type="password" id="password" name="password" onkeyup="javascript:hideAlert()" class="form-control" placeholder="请输入登录密码"></td><td style="width:82px;text-align:center"><a href="<?php echo WEB_ROOT;?>User/forgetPassword">忘记密码</a></td></tr></table>
                        
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <table style="width:100%"><tr><td><input type="text" id="imageCode" name="imageCode" onkeyup="javascript:hideAlert()" class="form-control" style="ime-mode: disabled" autocomplete="off" placeholder="验证码"></td><td style="width:82px"><img id="codeImage" border=1 width="82" height="28" src="" onclick="javascript:updateCodeImage()" style="cursor:pointer"></td></tr></table>
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <button type="submit" id="loginBtn" class="btn btn-primary btn-block">登录</button>
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
