    <div class="alert alert-info">请完善以下信息，以保障您的账号更加安全</div>
<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$userId = $_SESSION["authUserId"];
$document = UserUtil::b_one( [ 'id' => $userId ], [ 'projection' => [ 'email' => 1 ] ] );
if ($document != null) {
    $email = $document["email"];
}
?>
    <form name="basicInfo" class="form-horizontal">
    <div class="console-title">
    <h5>安全信息</h5>
    </div>
    <p></p>
      <div class="form-group">
        <label class="col-sm-2 control-label"><span>登录密码：</span></label>
        <div class="col-sm-8">
          <p class="form-control-static">安全性高的密码可以使帐号更安全。建议您定期更换密码，设置一个包含字母和数字且长度超过8位的密码。</p>
        </div>
        <div class="col-sm-2">
          <p class="form-control-static"><span class="text-success">已设置</span>，<a href="<?php echo FUNCTREE_WEB_ROOT;?>User/forgetPassword?email=<?php echo $email;?>">修改</a></p>
        </div>
      </div>
      <div class="form-group">
        <label for="realName" class="col-sm-2 control-label"><span>邮箱绑定：</span></label>
        <div class="col-sm-8">
          <p class="form-control-static"><?php echo $email;?></p>
        </div>
        <div class="col-sm-2">
          <p class="form-control-static"><span class="text-success">已设置</span><!-- ，<a href="User?">修改</a></p> -->
        </div>
      </div>
    </form>
