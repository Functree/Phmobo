<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$userId = $_SESSION["authUserId"];
$realName = isset($_POST['realName']) ? $_POST['realName'] : "";
$address = isset($_POST['address']) ? $_POST['address'] : "";
$contactPhone = isset($_POST['contactPhone']) ? $_POST['contactPhone'] : "";
$where = [ 'id' => $userId ];
$field = [ 'realName' => $realName, 'address' => $address, 'contactPhone' => $contactPhone ];
if (UserUtil::c_one($where, $field)) {
    //header('Location: '.FUNCTREE_WEB_ROOT."User/baseInfo");
    ?>
    <script type="text/javascript">
    alert("保存基本信息成功。");
    location.href = "<?php echo FUNCTREE_WEB_ROOT;?>User/baseInfo";
    </script>
    <?php
} else {
    ?>
    <script type="text/javascript">
    alert("保存基本信息失败。");
    location.href = "<?php echo FUNCTREE_WEB_ROOT;?>User/baseInfo";
    </script>
    <?php
}
?>