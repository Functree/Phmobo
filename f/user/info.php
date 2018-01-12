<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
if (isset($_GET["userId"])) {
    $userId = $_GET["userId"];
    $user = UserUtil::b_one( [ 'id' => $userId ] );
    if ($user == null) {
    ?>
    <div align="center">用户信息不存在。</div>
    <?php
    } else {
        ?>
        <form name="basicInfo" class="form-horizontal">
        <input type="hidden" name="cmd" value="update">
        <div class="console-title">
        <h5>基本信息</h5>
        </div>
        <p></p>
          <div class="form-group">
            <label class="col-sm-2 control-label"><span>会员头像：</span></label>
            <div class="col-sm-4">
              <p class="form-control-static"><img id="userPhoto" src="" width="100" height="100"/>
              </p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><span>会员名号：</span></label>
            <div class="col-sm-4">
              <p class="form-control-static"><?php echo $user["name"];?></p>
            </div>
          </div>
        </form>
        <script>
        function changeUserPhoto() {
            $("#userPhoto").attr("src", "<?php echo FUNCTREE_WEB_ROOT;?>User/photo?userId=<?php echo $userId;?>&ct="+new Date());
        }
        $(document).ready(function() {
            changeUserPhoto();
        });
        </script>
        <?php
    }
} else {
    ?>
    <div align="center">查询用户信息时出现异常。</div>
    <?php
}
?>
