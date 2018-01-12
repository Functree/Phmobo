<?php 
require_once(__DIR__ . '/phplib/UserUtil.php');
if (isset($_GET["pageNo"])) {
    $pageNo = $_GET["pageNo"];
    if ($pageNo < 1) $pageNo = 1;
} else {
    $pageNo = 1;
}
$pageLimit = 20;
$userCount = UserUtil::b_count([]);
$maxPageNo = ceil($userCount / $pageLimit);
?>
            <div>
            <h4>用户管理</h4>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr style="background-color:#f2f9fc;font-weight:bold">
                        <td width="120px">用户名号</td><td width="240px">Email</td><td>加入时间</td><td>状态</td><td width="120px">操作</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            $userList = UserUtil::b([], ["sort"=>["addTime"=>-1], "skip"=>($pageNo-1)*$pageLimit, "limit"=>$pageLimit]);
            foreach ($userList as $user) {
                $userId = $user["id"];
                $email = $user["email"];
                $userName = $user["name"];
                $status = $user["status"];
            ?>
                    <tr>
                        <td><img id="userPhoto" src="photo?userId=<?php echo $userId;?>" width="16" height="16" style="margin-top:-1px"/>&nbsp;<?php echo $userName;?></td>
                        <td><?php echo $email;?></td>
                        <td><?php echo $user["addTime"];?></td>
                        <td<?php if ($status != 1) {?> style="color:red"<?php }?>><?php echo FUNCTREE_USER_STATUS[$status];?></td>
                        <td>
                        <?php
                        if (in_array("User_c", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"]) && $email != FUNCTREE_ADMINISTRATOR_EMAIL) {//判断是否具备修改用户权限，“系统管理员”不允许被禁用
                            ?>
                            <?php if ($status == -1) {?><a href="javascript:void(0)" onclick="enableUser('<?php echo $userId;?>')">启用</a>
                            <?php } else if ($status == 1) {?><a href="javascript:void(0)" onclick="disableUser('<?php echo $userId;?>')">禁用</a>
                            <?php }
                            }?>
                        <?php
                        if ($email != FUNCTREE_ADMINISTRATOR_EMAIL || $_SESSION["authUserEmail"] == FUNCTREE_ADMINISTRATOR_EMAIL) {?>
                             <a href="userRole?userId=<?php echo $userId;?>">查看用户角色</a>
                        <?php }?>
                        </td>
                    </tr>
            <?php }?>
                </tbody>
            </table>
            <div class="text-center">
                <?php if ($userCount > $pageLimit) {?>
                    <nav>
                        <ul class="pagination" style="margin-top:0px">
                            <li<?php if ($pageNo == 1) {?> class="disabled"<?php }?>><a href="list?pageNo=<?php echo $pageNo-1;?>">上一页</a></li>
                            <li<?php if ($pageNo == $maxPageNo) {?> class="disabled"<?php }?>><a href="list?list?pageNo=<?php echo $pageNo+1;?>">下一页</a></li>
                        </ul>
                    </nav>
                <?php }?>
            </div>
<script type="text/javascript">
function enableUser(userId) {
    $.get("change?cmd=enableUser&userId=" + userId, function(data, status) {
        var result = eval("("+data+")");
        if (result.success) {
            window.location.reload();
        } else {
            alert(result.msg);
        }
    });
}
function disableUser(userId) {
    $.get("change?cmd=disableUser&userId=" + userId, function(data, status) {
        var result = eval("("+data+")");
        if (result.success) {
            window.location.reload();
        } else {
            alert(result.msg);
        }
    });
}
</script>
