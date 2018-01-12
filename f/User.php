<?php
Class User {
    static $name = "User";//功件名称
    static $localName = "用户";//功件中文名称
    //功件的x方法返回result内容，显示功件的描述内容
    static $xDescripton = <<<xDescripton
<p>这是一个基于PHP+MongoDB+Bootstrap开发的“用户”功件，关键词：authPermissions、needPermissions、id、name、url、position、children、menus、permissions、homePages、result、activeMenus、exit。</p>

<p>本功件使用了PHP Library for MongoDB 1.2，位于网站根目录下的phplib类库共享目录，对应MongoDB extension版本为1.3.4，MongoDB数据库版本为～3.2。</p>

<p>本功件实现了x方法，为功件的默认方法，为主页index.php提供了menus声明、permissions声明和homePages声明，分别给出了菜单项列表、权限项列表和首页显示内容列表，也为其他功件提供关于用户信息的访问方法。</p>
xDescripton;
    //已登录提示
    static $loggedAlert = "<div align='center'>您已登录成功。</div><script>setTimeout(\"location.href='".FUNCTREE_WEB_ROOT."'\", 1000);</script>";
    //登录过期提示
    static $loginExpiredAlert = "<div align='center'>登录已过期，请重新登录。</div><script>setTimeout(\"location.href='".FUNCTREE_WEB_ROOT."User/login'\", 3000);</script>";
    //退出登录提示
    static $logoutAlert = "<div align='center'>您已退出登录。</div><script>setTimeout(\"location.href='".FUNCTREE_WEB_ROOT."'\", 2000);</script>";
    
    /**
     * User功件的默认一级方法，可获取menus声明、permissions声明和homePages声明等信息，以及用户相关方法调用
     * @param array $param_arr 输入参数为索引数组
     * @return array
     */
    static public function x($param_arr = null) {
        //$param_arr参数为null时，返回内容外部可见，返回User功件的描述、菜单、权限、首页相关内容
        if ($param_arr == null) {
            //用户相关菜单项声明：索引数组[关联数组]，根据菜单项配置的权限要求以及用户的授权权限，显示不同的菜单项；用户登录前、登录后使用不同的权限标识
            $menuArray = array();
            if (isset($_SESSION["authUserId"])) {//如果用户已经登录
                //登录成功用户默认具有UserLogin权限
                $menu = array('id'=>"UserCenter", 'name'=>$_SESSION["authUserName"], 'needPermissions'=>["UserLogin"], 'children'=>[
                    array('id'=>"UserBaseInfo", 'name'=>"基本信息", 'url'=>FUNCTREE_WEB_ROOT."User/baseInfo", 'needPermissions'=>["UserLogin"]),//登录成功用户可见菜单
                    array('id'=>"UserSecureInfo", 'name'=>"安全信息", 'url'=>FUNCTREE_WEB_ROOT."User/secureInfo", 'needPermissions'=>["UserLogin"]),//登录成功用户可见菜单
                    array('id'=>"UserManage", 'name'=>"用户管理", 'url'=>FUNCTREE_WEB_ROOT."User/list", 'needPermissions'=>["User_b"]),//至少具备查询用户权限，才可进行用户管理操作
                    array('id'=>"UserRoleManage", 'name'=>"角色管理", 'url'=>FUNCTREE_WEB_ROOT."User/role", 'needPermissions'=>["UserRole_b"]),//至少具备查询角色权限，才可进行角色管理操作
                    array('id'=>"UserLogout", 'name'=>"退出", 'url'=>FUNCTREE_WEB_ROOT."User/logout", 'needPermissions'=>["UserLogin"])//登录成功用户可见菜单
                ]);
                array_push($menuArray, $menu);
            } else {
                //未登录用户默认具有UserLogout权限
                array_push($menuArray, array('id'=>"UserLogin", 'name'=>"登录", 'url'=>FUNCTREE_WEB_ROOT."User/login", 'needPermissions'=>["UserLogout"]));//未登录用户可见菜单
                array_push($menuArray, array('id'=>"UserRegister", 'name'=>"注册", 'url'=>FUNCTREE_WEB_ROOT."User/register", 'needPermissions'=>["UserLogout"]));//未登录用户可见菜单
            }
            //用户相关权限项声明：关联数组[权限ID=>权限名称]
            $permissionArray = array(
                "User_a" => "增加用户",
                "User_b" => "查询用户",
                "User_c" => "更改用户",
                "User_d" => "删除用户",
                "UserRole_a" => "增加角色",
                "UserRole_b" => "查询角色",
                "UserRole_c" => "更改角色",
                "UserRole_d" => "删除角色"
            );
            //用户功件定制的首页内容声明：索引数组[关联数组]
            $newUserList = self::b("{\"sort\":{\"addTime\":-1},\"limit\":10}");
            $children = array();
            foreach ($newUserList as $user) {
                array_push($children, array('name'=>$user["name"], 'url'=>FUNCTREE_WEB_ROOT."User/info?userId=".$user["id"]));
            }
            $homePageArray[0] = array('name'=>"最新注册用户", 'position'=>"right", 'children'=>$children);
            return array('result'=>self::$xDescripton, 'menus'=>$menuArray, 'permissions'=>$permissionArray, 'homePages'=>$homePageArray);
        } else {//$param_arr参数不为null时，外部不可访问，仅用于程序内部功件之间的调用
            
        }
    }
    /**
     * 用于系统自动注册“系统管理员”用户账号和默认系统角色，同时分配其角色和权限，仅运行一次
     * @return bool 成功返回true，否则返回false
     */
    static public function a_administrator() {
        $ret = false;
        include('user/a_administrator.php');
        //查看系统管理员是否已注册
        $count = UserUtil::b_count([ "email" => FUNCTREE_ADMINISTRATOR_EMAIL ]);
        //如果“系统管理员”已注册
        if ($count > 0) {
            $ret = true;
        } else {
            //增加“系统管理员”用户账号，仅运行一次
            $userId = UserUtil::a(FUNCTREE_ADMINISTRATOR_EMAIL, UserUtil::md5Password(FUNCTREE_ADMINISTRATOR_PASSWORD), FUNCTREE_ADMINISTRATOR_NAME);
            if ($userId != null) {
                //增加“系统管理员角色”
                RoleUtil::a(FUNCTREE_USER_ROLE_ADMINISTRATOR_ID, FUNCTREE_USER_ROLE_ADMINISTRATOR_NAME);
                //增加“用户默认角色”
                RoleUtil::a(FUNCTREE_USER_ROLE_DEFAULT_ID, FUNCTREE_USER_ROLE_DEFAULT_NAME);
                //给“系统管理员”分配“系统管理员角色”
                UserUtil::a_UserToRole($userId, FUNCTREE_USER_ROLE_ADMINISTRATOR_ID);
                //给“系统管理员”分配“用户默认角色”
                UserUtil::a_UserToRole($userId, FUNCTREE_USER_ROLE_DEFAULT_ID);
                //给“系统管理员角色”分配所有系统权限
                foreach (FUNCTREE_PERMISSION_LIST as $permissionId => $permissionName) {
                    RoleUtil::a_RoleToPermission(FUNCTREE_USER_ROLE_ADMINISTRATOR_ID, $permissionId);
                }
                $ret = true;
            }
        }
        return $ret;
    }
    /**
     * 查询用户信息，用于功件内部调用
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
    /**
     * 给其他功件调用，获取当前授权用户ID
     * @return string 授权用户ID，用户未登录并得到授权返回null
     */
    static public function b_authUserId() {
        //登录用户ID即为授权用户ID
        if (isset($_SESSION["authUserId"])) {
            return $_SESSION["authUserId"];
        } else {
            return null;
        }
    }
    /**
     * 给其他功件调用，获取授权用户名称
     * @param string $authUserId 授权用户ID
     * @return string 授权用户的名号，用户未登录并得到授权返回null
     */
    static public function b_authUserName($authUserId = null) {
        //登录用户名称即为授权用户名称
        if (isset($_SESSION["authUserName"])) {
            return $_SESSION["authUserName"];
        } else {
            return null;
        }
    }
    /**
     * 给其他功件调用，获取授权用户权限列表
     * @param string $authUserId 授权用户ID
     * @return array 授权用户具有的权限ID数组
     */
    static public function b_authPermissions($authUserId = null) {
        //登录用户具备的权限即为授权用户具有的权限
        if (!isset($_SESSION["FUNCTREE_AUTH_PERMISSIONS"]) || empty($_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
            //未登录用户均具备“未登录”权限：UserLogout，可用于“登录”、“注册”菜单项显示
            $_SESSION["FUNCTREE_AUTH_PERMISSIONS"] = array("UserLogout");
        }
        return $_SESSION["FUNCTREE_AUTH_PERMISSIONS"];
    }
    //用户基本信息页面
    static public function baseInfo($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
            ob_start();
            include('user/baseInfo.php');
            $ret = ob_get_contents();
            ob_end_clean();
            //激活菜单项声明
            $activeMenus = array("UserCenter", "UserBaseInfo");
            return array('result'=>$ret, 'activeMenus'=>$activeMenus);
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //保存用户基本信息操作
    static public function baseInfo2($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            include('user/baseInfo2.php');
            //由baseInfo2.php负责内容输出到浏览器，index.php主页面不再处理
            return array('exit'=>true);
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //修改用户状态等信息操作
    static public function change($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“修改用户”权限
            if (in_array("User_c", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                include('user/change.php');
                //由change.php负责修改用户状态并返回信息给浏览器，index.php主页面不再处理
                return array('exit'=>true);
            } else {
                return array('result'=>"<div align='center'>没有修改用户权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //检查注册用户的Email或名号是否已存在
    static public function check($param_arr = null) {
        include('user/check.php');
        //由check.php负责检查Email或名号是否存在并返回信息给浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //注册用户时获取Email验证码
    static public function emailCode($param_arr = null) {
        include('user/emailCode.php');
        //由emailCode.php负责发送Email并返回信息给浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //忘记密码页面
    static public function forgetPassword($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/forgetPassword.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明，登录-》忘记密码和安全信息-》修改密码功能都会用到forgetPassword
        $activeMenus = array("UserLogin", "UserCenter", "UserSecureInfo");
        return array('result'=>$ret, 'activeMenus'=>$activeMenus);
    }
    //忘记密码，发送重置密码邮件操作
    static public function forgetPassword2($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/forgetPassword2.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明，登录-》忘记密码和安全信息-》修改密码功能都会用到forgetPassword2
        $activeMenus = array("UserLogin", "UserCenter", "UserSecureInfo");
        return array('result'=>$ret, 'activeMenus'=>$activeMenus);
    }
    //用户登录、忘记密码时获取的图片验证码
    static public function imageCode($param_arr = null) {
        include('user/imageCode.php');
        //由emailCode.php负责图片输出到浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //公开的用户信息显示页面，用于显示所有用户的个性化信息，不需要用户登录
    static public function info($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/info.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明
        $activeMenus = array();
        return array('result'=>$ret, 'activeMenus'=>$activeMenus);
    }
    //用户管理页面
    static public function list($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“查询用户”权限
            if (in_array("User_b", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/list.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有查询用户权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //用户登录页面
    static public function login($param_arr = null) {
        //如果用户未登录
        if (!isset($_SESSION["authUserId"])) {
            //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
            ob_start();
            include('user/login.php');
            $ret = ob_get_contents();
            ob_end_clean();
            //激活菜单项声明
            $activeMenus = array("UserLogin");
            return array('result'=>$ret, 'activeMenus'=>$activeMenus);
        } else {
            return array('result'=>self::$loggedAlert);
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
        $activeMenus = array("UserLogin");
        return array('result'=>$ret, 'activeMenus'=>$activeMenus);
    }
    //用户登出操作
    static public function logout($param_arr = null) {
        $_SESSION = array();
        session_destroy();
        //登出用户均具备“未登录”权限：UserLogout，可用于菜单项显示
        $_SESSION["FUNCTREE_AUTH_PERMISSIONS"] = array("UserLogout");
        return array('result'=>self::$logoutAlert);
    }
    //用户头像的获取或保存操作
    static public function photo($param_arr = null) {
        include('user/photo.php');
        //由photo.php负责图片输出到浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //用户注册页面
    static public function register($param_arr = null) {
        //如果用户未登录
        if (!isset($_SESSION["authUserId"])) {
            //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
            ob_start();
            include('user/register.php');
            $ret = ob_get_contents();
            ob_end_clean();
            //激活菜单项声明
            $activeMenus = array("UserRegister");
            return array('result'=>$ret, 'activeMenus'=>$activeMenus);
        } else {
            return array('result'=>self::$loggedAlert);
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
        $activeMenus = array("UserRegister");
        return array('result'=>$ret, 'activeMenus'=>$activeMenus);
    }
    //重置密码页面
    static public function resetPassword($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/resetPassword.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明，登录-》重置密码和安全信息-》重置密码功能都会用到resetPassword
        $activeMenus = array("UserLogin", "UserCenter", "UserSecureInfo");
        return array('result'=>$ret, 'activeMenus'=>$activeMenus);
    }
    //重置密码操作，修改数据库中的用户密码
    static public function resetPassword2($param_arr = null) {
        //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
        ob_start();
        include('user/resetPassword2.php');
        $ret = ob_get_contents();
        ob_end_clean();
        //激活菜单项声明，登录-》重置密码和安全信息-》重置密码功能都会用到resetPassword2
        $activeMenus = array("UserLogin", "UserCenter", "UserSecureInfo");
        return array('result'=>$ret, 'activeMenus'=>$activeMenus);
    }
    //角色管理页面
    static public function role($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“查询角色”权限
            if (in_array("UserRole_b", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/role.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserRoleManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有角色管理权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //增加角色页面
    static public function roleAdd($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“增加角色”权限
            if (in_array("UserRole_a", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/roleAdd.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserRoleManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有增加角色权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //增加角色操作
    static public function roleAdd2($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“增加角色”权限
            if (in_array("UserRole_a", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/roleAdd2.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserRoleManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有增加角色权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //修改角色页面
    static public function roleChange($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“修改角色”权限
            if (in_array("UserRole_c", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/roleChange.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserRoleManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有修改角色权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //修改角色操作
    static public function roleChange2($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“修改角色”权限
            if (in_array("UserRole_c", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/roleChange2.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserRoleManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有修改角色权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //删除角色操作
    static public function roleDelete($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“删除角色”权限
            if (in_array("UserRole_d", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/roleDelete.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserRoleManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有删除角色权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //检查角色是否已存在
    static public function roleCheck($param_arr = null) {
        include('user/roleCheck.php');
        //由roleCheck.php负责检查角色是否存在并返回信息给浏览器，index.php主页面不再处理
        return array('exit'=>true);
    }
    //用户安全信息页面
    static public function secureInfo($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
            ob_start();
            include('user/secureInfo.php');
            $ret = ob_get_contents();
            ob_end_clean();
            //激活菜单项声明
            $activeMenus = array("UserCenter", "UserSecureInfo");
            return array('result'=>$ret, 'activeMenus'=>$activeMenus);
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //用户的角色管理页面
    static public function userRole($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“查询用户”权限
            if (in_array("User_b", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/userRole.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有查询用户权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //修改用户的角色页面
    static public function userRoleChange($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“修改用户”权限
            if (in_array("User_c", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/userRoleChange.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有修改用户角色权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //修改用户角色操作
    static public function userRoleChange2($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“修改用户”权限
            if (in_array("User_c", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/userRoleChange2.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有修改用户角色权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
    //删除用户角色操作
    static public function userRoleDelete($param_arr = null) {
        //如果用户已登录
        if (isset($_SESSION["authUserId"])) {
            //判断用户是否具备“删除用户”权限
            if (in_array("User_d", $_SESSION["FUNCTREE_AUTH_PERMISSIONS"])) {
                //使用ob_start获取页面输出缓存内容，返回给index.php做后续处理
                ob_start();
                include('user/userRoleDelete.php');
                $ret = ob_get_contents();
                ob_end_clean();
                //激活菜单项声明
                $activeMenus = array("UserCenter", "UserManage");
                return array('result'=>$ret, 'activeMenus'=>$activeMenus);
            } else {
                return array('result'=>"<div align='center'>没有删除用户角色权限。</div>");
            }
        } else {
            return array('result'=>self::$loginExpiredAlert);
        }
    }
}
?>