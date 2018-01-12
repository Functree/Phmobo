<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$userId = $_SESSION["authUserId"];
$document = UserUtil::b_one( [ 'id' => $userId ] );
if ($document == null) {
    ?>
    <div align="center">用户信息不存在。</div>
    <?php
} else {
    ?>
    <div class="alert alert-info">请完善以下信息，方便我们更好的为您服务</div>
    <form name="basicInfo" class="form-horizontal" method="post" action="<?php echo FUNCTREE_WEB_ROOT;?>User/baseInfo2">
    <input type="hidden" name="cmd" value="update">
    <div class="console-title">
    <h5>基本信息</h5>
    </div>
    <p></p>
      <div class="form-group">
        <label class="col-sm-2 control-label"><span>会员头像：</span></label>
        <div class="col-sm-4">
          <p class="form-control-static"><img id="userPhoto" src="" width="100" height="100"/>
          <input type="button" onclick="selectFile()" value="上传照片" style="vertical-align:bottom" />
          </p>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label"><span>会员名号：</span></label>
        <div class="col-sm-4">
          <p class="form-control-static"><?php echo $document["name"];?></p>
        </div>
      </div>
      <div class="form-group">
        <label for="realName" class="col-sm-2 control-label"> <span>真实姓名：</span></label>
        <div class="col-sm-4">
          <input type="text" id="realName" name="realName" class="form-control" value="<?php echo isset($document["realName"]) ? $document["realName"] : "";?>">
        </div>
      </div>
    <p></p>
    <div class="console-title">
    <h5>联系信息</h5>
    </div>
    <p></p>
      <div class="form-group">
        <label for="address" class="col-sm-2 control-label">注册邮箱：</label>
        <div class="col-sm-4">
            <p class="form-control-static"><?php echo $document["email"];?></p>
        </div>
      </div>
      <div class="form-group">
        <label for="address" class="col-sm-2 control-label">详细地址：</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($document["address"]) ? $document["address"] : "";?>">
        </div>
      </div>
      <div class="form-group">
        <label for="contactPhone" class="col-sm-2 control-label">联系电话：</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="contactPhone" name="contactPhone" value="<?php echo isset($document["contactPhone"]) ? $document["contactPhone"] : "";?>" placeholder="例如：8601088888888">
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-4">
          <button type="submit" class="btn btn-primary">保存</button>
        </div>
      </div>
    </form>
    <script>
    function selectFile() {
        var fileObj = $('<input type=file id="file0" name="file0" onchange="uploadImage(this)">');
        fileObj.trigger('click');
    }
    function uploadImage(file) {
        if (file.value) {
            submitForm(file);
        }
    }
    function submitForm(file) {
        var form = $('<form method="post" enctype="multipart/form-data" action="<?php echo FUNCTREE_WEB_ROOT;?>User/photo" target="hiddenFrame"></form>');
        form.append(file);
        form.appendTo("body");
        form.css('display','none');
        form.submit();
    }
    function changeUserPhoto() {
        $("#userPhoto").attr("src", "<?php echo FUNCTREE_WEB_ROOT;?>User/photo?userId=<?php echo $userId;?>&ct="+new Date());
    }
    $(document).ready(function() {
        changeUserPhoto();
    });
    </script>
    <iframe name="hiddenFrame" id="hiddenFrame" style="display:none"></iframe>
    <?php
}
?>
