<script>
function hideAlert() {
    $("#alertDiv").css("display", "none");
}
function showAlert(alertMsg) {
    $("#alertDiv").css("display", "block");
    $("#alertSpan").html(alertMsg);
}
function validEmail() {
    var email = $("#email").val();
    if (email == "") {
        showAlert("请输入电子邮箱。");
        $("#email").focus();
        return false;
    }
    var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
    if (!pattern.test(email) || email.length > 100) {
        showAlert("请输入正确的电子邮箱。");
        $("#email").focus();
        return false;
    }
    return true;
}
function validPassword() {
    var password = $("#password").val();
    if (password == "") {
        showAlert("请输入密码。");
        $("#password").focus();
        return false;
    }
    var regex = new RegExp('(?=.*[0-9])(?=.*[a-zA-Z]).{8,30}');
    if (!regex.test(password)) {
        showAlert("请输入正确的密码，必须包含字母和数字且不能小于8位。");
        $("#password").focus();
        return false;
    }
    return true;
}
function validName() {
    var name = $("#name").val();
    if (name == "") {
        showAlert("请输入名号。");
        $("#name").focus();
        return false;
    }
    if (name.length > 30) {
        showAlert("您的名号太长了。");
        $("#name").focus();
        return false;
    }
    if (name.length < 2) {
        showAlert("您的名号太短了。");
        $("#name").focus();
        return false;
    }
    return true;
}
function validEmailCode() {
    var emailCode = $("#emailCode").val();
    if (emailCode == "") {
        showAlert("请输入Email验证码。");
        $("#emailCode").focus();
        return false;
    }
    var regex = /^\d{6}$/;
    if (!regex.test(emailCode)) {
        showAlert("Email验证码为6位数字。");
        $("#emailCode").focus();
        return false;
    }
    return true;
}
function checkEmail() {
    hideAlert();
    var email = $("#email").val();
    if (email && validEmail()) {
        $.get("<?php echo FUNCTREE_WEB_ROOT;?>User/check?cmd=checkEmail&email=" + email, function(data, status) {
            var result = eval("("+data+")");
            if (result.success) {
                emailExist = false;
                $("#userEmail").removeClass("has-error");
                $("#getEmailCode").removeAttr("disabled");
                $("#registerBtn").removeAttr("disabled");
            } else {
                emailExist = true;
                $("#userEmail").addClass("has-error");
                $("#getEmailCode").attr("disabled", "disabled");
                $("#registerBtn").attr("disabled", "disabled");
                showAlert("这个电子邮箱已经被注册。");
                $("#email").focus();
            }
        });
    }
}
function checkName() {
    hideAlert();
    var name = $("#name").val();
    if (validName()) {
        $.get("<?php echo FUNCTREE_WEB_ROOT;?>User/check?cmd=checkName&name=" + name, function(data, status) {
            var result = eval("("+data+")");
            if (result.success) {
                nameExist = false;
                $("#userName").removeClass("has-error");
                $("#getEmailCode").removeAttr("disabled");
                $("#registerBtn").removeAttr("disabled");
            } else {
                nameExist = true;
                $("#userName").addClass("has-error");
                $("#getEmailCode").attr("disabled", "disabled");
                $("#registerBtn").attr("disabled", "disabled");
                showAlert("这个名号已经被其他人占用。");
                $("#name").focus();
            }
        });
    }
}
var emailExist = false, nameExist = false;
function checkPassword() {
    if (!emailExist && !nameExist) {
        hideAlert();
        validPassword();
    }
}
function submitForm() {
    if (validEmail() && validPassword() && validName() && validEmailCode()) {
        registerForm.submit();
    }
}
$(function(){
    $("#email").focus();
    $("#getEmailCode").click(function(){
        var email = $("#email").val();
        if(!validEmail(email)){
            return false;
        }
        $('#popModal').modal({backdrop: 'static', keyboard: false});
        $.get("<?php echo FUNCTREE_WEB_ROOT;?>User/emailCode?email=" + email, function(data, status) {
            $('#popModal').modal('hide');
            var result = eval("("+data+")");
            if (result.success) {
                $("#getEmailCode").attr("disabled", "disabled");
                var count = 60;
                var countdown = setInterval(CountDown, 1000);
                function CountDown() {
                    $("#getEmailCode").val(count+"秒后重新获取");
                    if (count == 0) {
                        $("#getEmailCode").val("获取Email验证码").removeAttr("disabled");
                        clearInterval(countdown);
                    }
                    count--;
                }
                $("#registerBtn").removeAttr("disabled");
            } else {
                $("#userEmail").addClass("has-error");
                $("#registerBtn").attr("disabled", "disabled");
                showAlert(result.msg);
                $("#email").focus();
            }
        });
    });
});
</script>
    <div class="row text-center">
        <div>
            <img src="<?php echo FUNCTREE_WEB_ROOT.FUNCTREE_FUNC_PATH;?>user/images/logo.png" width="48" height="48" style="margin-top:30px">
        </div>
        <div style="margin-bottom:30px;margin-top:30px">
            <h4>用户注册</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-1 col-md-4">
        </div>
        <div class="col-xs-10 col-md-4">
            <form class="form-horizontal" name="registerForm" method="post" action="<?php echo FUNCTREE_WEB_ROOT;?>User/register2">
            <input type="hidden" name="cmd" value="register">
                <div id="userEmail" class="form-group">
                    <div>
                        <input type="email" class="form-control" id="email" name="email" placeholder="请输入电子邮箱" onkeyup="javascript:hideAlert()" onblur="javascript:checkEmail()">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <input type="password" class="form-control" id="password" name="password" onkeyup="javascript:hideAlert()" onblur="javascript:checkPassword()" placeholder="请设定登录密码">
                    </div>
                </div>
                <div id="userName" class="form-group">
                    <div>
                        <input type="text" class="form-control" id="name" name="name" placeholder="请给自己设一个名号，注意：将不可更改" onkeyup="javascript:hideAlert()" onblur="javascript:checkName()">
                    </div>
                </div>
                <div class="form-group">
                    <table><tr><td width="100%">
                        <input type="text" class="form-control" id="emailCode" name="emailCode" placeholder="请输入Email验证码" onkeyup="javascript:hideAlert()">
                        </td><td style="padding-left:3px;padding-right:3px">
                        <input type="button" class="btn btn-default" id="getEmailCode" value="获取Email验证码">
                    </td></tr></table>
                </div>
                <div class="form-group">
                    <div>
                        <button type="button" onclick="submitForm()" id="registerBtn" class="btn btn-primary btn-block">创建账号</button>
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

<div id="popModal" class="modal bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content text-center" style="padding:10px">
      <img alt="" src="<?php echo FUNCTREE_WEB_ROOT.FUNCTREE_FUNC_PATH;?>user/images/loading.gif"> 请稍等......
    </div>
  </div>
</div>
