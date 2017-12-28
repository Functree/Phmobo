<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
//用户必须已经登录才能保存基本信息
if (isset($_SESSION["loginUserId"])) {
    $userId = $_SESSION["loginUserId"];
    $realName = isset($_POST['realName']) ? $_POST['realName'] : "";
    $address = isset($_POST['address']) ? $_POST['address'] : "";
    $contactPhone = isset($_POST['contactPhone']) ? $_POST['contactPhone'] : "";
    $where = [ 'id' => $userId ];
    $field = [ 'realName' => $realName, 'address' => $address, 'contactPhone' => $contactPhone ];
    if (UserUtil::c_one($where, $field)) {
        //header('Location: '.WEB_ROOT."User/baseInfo");
        ?>
        <script type="text/javascript">
        alert("保存基本信息成功。");
        location.href = "<?php echo WEB_ROOT;?>User/baseInfo";
        </script>
        <?php
    } else {
        ?>
        <script type="text/javascript">
        alert("保存基本信息失败。");
        location.href = "<?php echo WEB_ROOT;?>User/baseInfo";
        </script>
        <?php
    }
} else {
    ?>
        <script type="text/javascript">
        alert("登录已过期，请重新登录。");
        location.href = "<?php echo WEB_ROOT;?>User/login";
        </script>
    <?php
}
?>