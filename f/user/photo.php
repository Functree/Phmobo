<?php
require_once(__DIR__ . '/phplib/UserUtil.php');
$type = 'photo';
if (isset($_GET["userId"])) {//如果存在$userId，则是获取用户的头像信息
    $userId = $_GET["userId"];
    ob_clean();
    $document = UserUtil::b_one_userImage( [ 'userId' => $userId, 'type' => $type ], [ 'projection' => [ 'suffix' => 1, 'image' => 1 ] ] );
    if ($document != null) {
        $suffix = strtolower($document["suffix"]);
        $image = $document["image"];
        $image = base64_decode($image);
        if ($suffix == "png") {
            header("content-type:image/png");
        } else if ($suffix == "gif") {
            header("content-type:image/gif");
        } else if ($suffix == "jpg" || $suffix == "jpeg" || $suffix == "jpe") {
            header("content-type:image/jpeg");
        }
        echo($image);
    } else {
        //             $filename = __DIR__ . "/images/userDefaultPhoto.png";
        //             $img = file_get_contents($filename, true);
        //             //使用图片头输出浏览器
        //             header("Content-Type: image/png");
        //             echo $img;
        //             exit;
        //header('Location: '.WEB_ROOT.FUNC_PATH."user/images/userDefaultPhoto.png");
        $filename = __DIR__ . "/images/userDefaultPhoto.png";
        $size = getimagesize($filename); //获取mime信息
        $fp = fopen($filename, "rb"); //二进制方式打开文件
        if ($size && $fp) {
            header("Content-type: {$size['mime']}");
            fpassthru($fp); //输出至浏览器
            exit;
        }
    }
} else {//否则是保存用户头像
    //用户必须已经登录才能保存头像
    if (isset($_SESSION["loginUserId"])) {
        $userId = $_SESSION["loginUserId"];
        $imgName = $_FILES['file0']['name'];
        $suffix = substr($imgName, strpos($imgName, ".") + 1);
        $image = file_get_contents($_FILES['file0']['tmp_name']);
        $image = base64_encode($image);
        $count = UserUtil::b_count_userImage(['userId' => $userId, 'type' => $type ]);
        $success = false;
        if ($count > 0) {
            $success = UserUtil::c_one_userImage( [ 'userId' => $userId, 'type' => $type ], [ 'suffix' => $suffix, 'image' => $image ] );
        } else {
            $success = UserUtil::a_userImage($userId, $type, $suffix, $image);
        }
        if ($success) {
        ?>
            <script type="text/javascript">
            parent.changeUserPhoto();
            </script>
            <?php
        } else {
            ?>
            <script type="text/javascript">
            alert("保存用户头像信息失败。");
            </script>
            <?php
        }
    }
}
?>