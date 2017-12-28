<?php
Class User {
    var $name = "User";//功件名称
    var $localName = "用户";//功件中文名称
    //功件的x方法返回result内容，显示功件的描述内容
    static $xDescripton = <<<xDescripton
<p>这是一个基于PHP+MongoDB+Bootstrap开发的“用户”功件。</p>

<p>本功件使用了PHP Library for MongoDB 1.2，位于网站根目录下的phplib类库共享目录，对应MongoDB extension版本为1.3.4，MongoDB数据库版本为～3.2。</p>

<p>本功件实现了x方法，为功件的默认方法，为主页index.php提供了menu声明、permission声明和homePage声明，分别给出了菜单项、权限项和首页显示内容，也为其他功件提供关于用户信息的访问功能。</p>
xDescripton;

    /**
     * User功件的默认一级方法，可获取menu声明、permission声明和homePage声明等信息
     * @param array $param_arr 输入参数为索引数组
     * @return array
     */
    static public function x($param_arr = null) {
        //$param_arr参数为null时，返回内容外部可见，返回User功件的描述、菜单、权限、主页相关内容
        if ($param_arr == null) {
            //菜单项声明，用户登录前和登录后的有不同的菜单项；用户登录成功后，存在$_SESSION["loginUserId"]
            if (isset($_SESSION["loginUserId"])) {
                $menu[0] = array('id'=>"UserCenter", 'name'=>$_SESSION["loginUserName"], 'children'=>[
                    array('id'=>"UserBaseInfo", 'name'=>"基本信息", 'path'=>"User/baseInfo"),
                    array('id'=>"UserSecureInfo", 'name'=>"安全信息", 'path'=>"User/secureInfo"),
                    array('id'=>"UserLogout", 'name'=>"退出", 'path'=>"User/logout")
                ]);
            } else {
                $menu[0] = array('id'=>"UserLogin", 'name'=>"登录", 'path'=>"User/login");
                $menu[1] = array('id'=>"UserRegister", 'name'=>"注册", 'path'=>"User/register");
            }
            //权限项声明
            $permission[0] = array('id'=>"UserA", 'name'=>"增加用户");
            $permission[1] = array('id'=>"UserB", 'name'=>"查询用户");
            $permission[2] = array('id'=>"UserC", 'name'=>"更改用户");
            $permission[3] = array('id'=>"UserD", 'name'=>"删除用户");
            //定制首页内容声明
            $newUserList = self::b("{\"sort\":{\"addTime\":-1},\"limit\":10}");
            $children = array();
            foreach ($newUserList as $user) {
                array_push($children, array('name'=>$user["name"], 'path'=>"User/info?userId=".$user["id"]));
            }
            $homePage[0] = array('name'=>"最新注册用户", 'position'=>"right", 'children'=>$children);
            return array('result'=>self::$xDescripton, 'menu'=>$menu, 'permission'=>$permission, 'homePage'=>$homePage);
        } else {//$param_arr参数不为null时，外部不可访问，仅用于程序内部功件之间的调用
            
        }
    }
    /**
     * 增加新用户
     * @param string $queryStr json格式的查询字符串，如："{\"email\": \"123@abc.com\", \"password\": \"123\", \"name\": \"abc\"}"
     * @return array 返回查询结果集数组
     */
    static private function a($queryStr) {
        $json = json_decode($queryStr, true);//强制返回关联数组
        if (!array_key_exists("email", $json) || !array_key_exists("password", $json) || !array_key_exists("name", $json)) {
            exit;
        }
        $email = $json["email"];
        $password = $json["password"];
        $name = $json["name"];
        if (array_key_exists("userGroup", $json)) {
            $userGroup = $json["userGroup"];
        } else {
            $userGroup = 1;
        }
        if (array_key_exists("addTime", $json)) {
            $addTime = $json["addTime"];
        } else {
            date_default_timezone_set('PRC');
            $addTime = date("Y-m-d H:i:s", time());
        }
        include('user/a.php');
        $ret = UserUtil::a($email, $password, $name, $userGroup, $addTime);
        return $ret;
    }
    /**
     * 查询用户信息
     * @param string $queryStr json格式的查询字符串，如："{\"where\":{\"name\": \"abc\"}, \"sort\":{\"addTime\":-1}, \"limit\":10}"
     * @return array 返回查询结果集数组
     */
    static private function b($queryStr) {
        $json = json_decode($queryStr, true);//强制返回关联数组
        if (array_key_exists("where", $json)) {
            $where = $json["where"];
        } else {
            $where = array();
        }
        $options = array();
        if (array_key_exists("sort", $json)) {
            $options['sort'] = $json["sort"];
        }
        if (array_key_exists("limit", $json)) {
            $options['limit'] = $json["limit"];
        }
        if (array_key_exists("field", $json)) {
            $options['projection'] = $json["field"];
        }
        include('user/b.php');
        $ret = UserUtil::b($where, $options);
        return $ret;
    }
    //检查注册用户的Email是否已存在
    static public function checkEmail($param_arr = null) {
        include('user/checkEmail.php');
        //由checkName.php负责检查Email是否存在并返回信息给浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //检查注册用户的名号是否已存在
    static public function checkName($param_arr = null) {
        include('user/checkName.php');
        //由checkName.php负责检查名称是否存在并返回信息给浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //注册用户获取Email验证码
    static public function emailCode($param_arr = null) {
        include('user/emailCode.php');
        //由emailCode.php负责发送Email并返回信息给浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //用户注册页面
    static public function register($param_arr = null) {
        //如果用户未登录
        if (!isset($_SESSION["loginUserId"])) {
            //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
            ob_start();
            include('user/register.php');
            $ret = ob_get_contents();
            ob_end_clean();
            //激活菜单项声明
            $activeMenu = array("UserRegister");
            return array('result'=>$ret, 'activeMenu'=>$activeMenu);
        } else {
            return array('result'=>"<div align='center'>您已注册并登录成功。</div><script>setTimeout(\"location.href='".WEB_ROOT."'\", 3000);</script>");
        }
    }
    //用户提交注册后的处理页面
    static public function register2($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/register2.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明
        $activeMenu = array("UserRegister");
        return array('result'=>$ret, 'activeMenu'=>$activeMenu);
    }
    //用户登录页面
    static public function login($param_arr = null) {
        //如果用户未登录
        if (!isset($_SESSION["loginUserId"])) {
            //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
            ob_start();
            include('user/login.php');
            $ret = ob_get_contents();
            ob_end_clean();
            //激活菜单项声明
            $activeMenu = array("UserLogin");
            return array('result'=>$ret, 'activeMenu'=>$activeMenu);
        } else {
            return array('result'=>"<div align='center'>您已登录成功。</div><script>setTimeout(\"location.href='".WEB_ROOT."'\", 3000);</script>");
        }
    }
    //用户提交登录后的处理页面
    static public function login2($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/login2.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明
        $activeMenu = array("UserLogin");
        return array('result'=>$ret, 'activeMenu'=>$activeMenu);
    }
    //用户登录、忘记密码时获取的图片验证码
    static public function imageCode($param_arr = null) {
        include('user/imageCode.php');
        //由emailCode.php负责图片输出到浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //用户登出操作
    static public function logout($param_arr = null) {
        $_SESSION = array();
        session_destroy();
        return array('result'=>"<div align='center'>您已退出登录。</div><script>setTimeout(\"location.href='".WEB_ROOT."'\", 3000);</script>");
    }
    //用户信息显示页面，用于显示所有用户的个性化信息，不需要用户登录
    static public function info($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/info.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明
        $activeMenu = array();
        return array('result'=>$ret, 'activeMenu'=>$activeMenu);
    }
    //用户基本信息
    static public function baseInfo($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["loginUserId"])) {
            //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
            ob_start();
            include('user/baseInfo.php');
            $ret = ob_get_contents();
            ob_end_clean();
            //激活菜单项声明
            $activeMenu = array("UserCenter", "UserBaseInfo");
            return array('result'=>$ret, 'activeMenu'=>$activeMenu);
        } else {
            return array('result'=>"<div align='center'>登录已过期，请重新登录。</div><script>setTimeout(\"location.href='".WEB_ROOT."User/login'\", 3000);</script>");
        }
    }
    //保存用户基本信息
    static public function baseInfo2($param_arr = null) {
        include('user/baseInfo2.php');
        //由baseInfo2.php负责内容输出到浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //用户安全信息
    static public function secureInfo($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["loginUserId"])) {
            //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
            ob_start();
            include('user/secureInfo.php');
            $ret = ob_get_contents();
            ob_end_clean();
            //激活菜单项声明
            $activeMenu = array("UserCenter", "UserSecureInfo");
            return array('result'=>$ret, 'activeMenu'=>$activeMenu);
        } else {
            return array('result'=>"<div align='center'>登录已过期，请重新登录。</div><script>setTimeout(\"location.href='".WEB_ROOT."User/login'\", 3000);</script>");
        }
    }
    //忘记密码
    static public function forgetPassword($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/forgetPassword.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明，登录-》忘记密码和安全信息-》修改密码功能都会用到forgetPassword
        $activeMenu = array("UserLogin", "UserCenter", "UserSecureInfo");
        return array('result'=>$ret, 'activeMenu'=>$activeMenu);
    }
    //忘记密码，发送重置密码邮件
    static public function forgetPassword2($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/forgetPassword2.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明，登录-》忘记密码和安全信息-》修改密码功能都会用到forgetPassword2
        $activeMenu = array("UserLogin", "UserCenter", "UserSecureInfo");
        return array('result'=>$ret, 'activeMenu'=>$activeMenu);
    }
    //重置密码
    static public function resetPassword($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/resetPassword.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明，登录-》重置密码和安全信息-》重置密码功能都会用到resetPassword
        $activeMenu = array("UserLogin", "UserCenter", "UserSecureInfo");
        return array('result'=>$ret, 'activeMenu'=>$activeMenu);
    }
    //重置密码，修改数据库中的用户密码
    static public function resetPassword2($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/resetPassword2.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明，登录-》重置密码和安全信息-》重置密码功能都会用到resetPassword2
        $activeMenu = array("UserLogin", "UserCenter", "UserSecureInfo");
        return array('result'=>$ret, 'activeMenu'=>$activeMenu);
    }
    //用户头像的获取或保存
    static public function photo($param_arr = null) {
        include('user/photo.php');
        //由photo.php负责图片输出到浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
}
?>