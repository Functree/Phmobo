<?php 
$email = isset($_GET["email"]) ? $_GET["email"] : "";
?>
<script>
function hideAlert() {
    $("#alertDiv").css("display", "none");
}
function showAlert(alertMsg) {
    $("#alertDiv").css("display", "block");
    $("#alertSpan").html(alertMsg);
}
function validEmailFormat() {
    var email = resetForm.email.value;
    if (email == "") {
        showAlert("请输入电子邮箱。");
        resetForm.email.focus();
        return false;
    }
    var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
    if (!pattern.test(email) || email.length > 100) {
        showAlert("请输入正确的电子邮箱。");
        resetForm.email.focus();
        return false;
    }
    return true;
}
function validImageCodeFormat() {
    var imageCode = resetForm.imageCode.value;
    var regex = new RegExp('(?=.*[a-zA-Z0-9]).{4}');
    if (!regex.test(imageCode)) {
        showAlert("请输入验证码。");
        resetForm.imageCode.focus();
        return false;
    }
    return true;
}
function checkEmail() {
    hideAlert();
    var email = resetForm.email.value;
    email && validEmailFormat();
}
function checkForm() {
    if (!validEmailFormat() || !validImageCodeFormat())
        return false;
    else
        return true;
}
function updateCodeImage()
{
    document.getElementById("codeImage").src = '<?php echo FUNCTREE_WEB_ROOT;?>User/imageCode?d='+Math.random();
}
$(function(){
    updateCodeImage();
    $("#email").focus();
});
</script>
    <div class="row text-center">
        <div>
            <img src="<?php echo FUNCTREE_WEB_ROOT.FUNCTREE_FUNC_PATH;?>user/images/logo.png" style="margin-top:30px;margin-bottom:30px">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-1 col-md-4">
        </div>
        <div class="col-xs-10 col-md-4">
            <form class="form-horizontal" name="resetForm" method="post" action="<?php echo FUNCTREE_WEB_ROOT;?>User/forgetPassword2" onsubmit="javascript: return checkForm();">
            <input type="hidden" name="cmd" value="resetPasswordSend">
                <div class="form-group">
                    <div>
                        <input type="email" id="email" name="email" onkeyup="javascript:hideAlert()" onblur="javascript:checkEmail()" class="form-control" placeholder="电子邮箱" value="<?php echo $email;?>">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <table style="width:100%"><tr><td><input type="text" id="imageCode" name="imageCode" onkeyup="javascript:hideAlert()" class="form-control" style="ime-mode: disabled" autocomplete="off" placeholder="验证码"></td><td style="width:82px"><img id="codeImage" border=1 width="82" height="28" src="" onclick="javascript:updateCodeImage()" style="cursor:pointer"></td></tr></table>
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <button type="submit" id="resetBtn" class="btn btn-primary btn-block">获取重置密码邮件</button>
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
