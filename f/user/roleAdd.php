    <div class="row">
      <div class="col-md-12">
        <ol class="breadcrumb">
            <li><a href="role">角色管理</a></li>
            <li class="active">增加角色</li>
        </ol>
      </div>
    </div>
    <div class="row text-center">
        <h3 class="page_title">增加角色</h3>
    </div>
    <p style="margin:10px"></p>
    <div class="row">
        <div class="col-xs-1 col-md-4">
        </div>
        <div class="col-xs-10 col-md-4">
            <form class="form-horizontal" name="roleForm" method="post" action="roleAdd2" onsubmit="javascript: return checkSubmit()">
            <input type="hidden" name="cmd" value="saveRole">
                <div class="form-group">
                    <label for="name">角色ID<span class="text-danger">*</span></label>
                    <input type="text" id="roleId" name="roleId" style="ime-mode:disable" onkeyup="javascript:hideAlert()" onblur="javascript:checkId()" class="form-control">
                </div>
                <div class="form-group">
                    <label for="name">角色名称<span class="text-danger">*</span></label>
                    <input type="text" id="roleName" name="roleName" onkeyup="javascript:hideAlert()" onblur="javascript:checkName()" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" id="submitBtn" class="btn btn-primary btn-block">提交</button>
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
<script>
function hideAlert() {
    $("#alertDiv").css("display", "none");
}
function showAlert(alertMsg) {
    $("#alertDiv").css("display", "block");
    $("#alertSpan").html(alertMsg);
}
function validId() {
    var roleId = $("#roleId").val();
    if (roleId == "") {
        showAlert("请输入角色ID。");
        $("#roleId").focus();
        return false;
    }
    if (!roleId.match(/^[A-Za-z0-9_\-]*$/)) {
        showAlert("请输入正确的角色ID，仅允许字母、数字、下划线和减号。");
        $("#roleId").focus();
        return false;
    }
    if (roleId && roleId.length > 20) {
        showAlert("角色ID的长度不能超过20位。");
        $("#roleId").focus();
        return false;
    }
    return true;
}
function validName() {
    var name = $("#roleName").val();
    if (name == "") {
        showAlert("请输入角色名称。");
        $("#roleName").focus();
        return false;
    }
    if (name && name.length > 30) {
        showAlert("角色名称的长度不能超过30位。");
        $("#roleName").focus();
        return false;
    }
    return true;
}
function checkId() {
    hideAlert();
    var roleId = $("#roleId").val();
    if (roleId && validId()) {
        $.get("roleCheck?cmd=checkId&roleId=" + roleId, function(data, status) {
            var result = eval("("+data+")");
            if (result.success) {
                $("#roleId").removeClass("has-error");
                $("#submitBtn").removeAttr("disabled");
            } else {
                $("#roleId").addClass("has-error");
                $("#submitBtn").attr("disabled", "disabled");
                $("#roleId").focus();
                showAlert("角色ID已存在。");
            }
        });
    }
}
function checkName() {
    hideAlert();
    var roleName = $("#roleName").val();
    if (roleName && validName()) {
        $.get("roleCheck?cmd=checkName&roleName=" + roleName, function(data, status) {
            var result = eval("("+data+")");
            if (result.success) {
                $("#roleName").removeClass("has-error");
                $("#submitBtn").removeAttr("disabled");
            } else {
                $("#roleName").addClass("has-error");
                $("#submitBtn").attr("disabled", "disabled");
                $("#roleName").focus();
                showAlert("角色名称已存在。");
            }
        });
    }
}
function checkSubmit() {
    if (!validId() || !validName())
        return false;
    else
        return true;
}
</script>
