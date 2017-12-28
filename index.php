<?php
//启动session，保存用户登录状态和菜单项等信息
session_start();

//首先引入网站phplib库中的配置参数和工具类
require_once('phplib/config.php');
require_once('phplib/FunctreeUtil.php');
$functreeUtil = new FunctreeUtil();
//////////////////////////////增加或减少功件时，使用下列2行语句清除功件缓存、权限缓存和菜单缓存。当功件配置稳定后必须注释下列2行语句///////////////////////////////////////////////////////////
//$functreeUtil->delete_cache();
//$_SESSION = array();
/////////////////////////////////////////////////////////////////////////////////////////

//本网站的所有功件均位于网站根目录的f目录下，一般不要修改，遍历此目录可获取所有功件列表
define( 'FUNC_PATH', "f/" );


//其次根据uri获取用户浏览器正在访问的功件名称和方法名称，如User/login解析为：$requestFuncName=User，$requestMethodName=login
if (!$functreeUtil->parse_uri($functreeUtil->get_uri_string("/".ROOT_PATH), $requestFuncName, $requestMethodName))
{
    $functreeUtil->show_message('解析URI失败。');
}

//然后从内存缓存中读取功件列表。功件列表缓存于内存中，保证所有用户都可以访问
$funcList = $functreeUtil->get_cache("funcList");
if (!$funcList) {
    $funcList = array();
    //实现功件发现机制，仅执行一次：遍历功件所在目录：php目录，获取功件列表并缓存
    foreach(scandir(FUNC_PATH) as $funcFileName)
    {
        if($funcFileName=='.'||$funcFileName=='..') continue;
        //功件主文件名称后缀必须为".php"
        $suffix = substr($funcFileName, strrpos($funcFileName, '.'));
        if(!is_dir(FUNC_PATH.$funcFileName) && $suffix == '.php')
        {
            //功件主文件名称，即为功件类名称
            $funcName = substr($funcFileName, 0, strrpos($funcFileName, '.php'));
            //保存功件名称到功件列表
            array_push($funcList, $funcName);
        }
    }
    if (!empty($funcList)) {
        $functreeUtil->save_cache($funcList, "funcList", 24*3600);
    }
}
//引入功件主文件内容，并完成功件的类实例化，方便后续使用
foreach ($funcList as $funcName) {
    require(FUNC_PATH.$funcName.'.php');
    //完成功件的类实例化，方便后续使用
    $$funcName = new $funcName();
}
//从内存缓存中读取权限列表。权限项列表缓存于内存中，保证所有用户都可以访问
$permissionList = $functreeUtil->get_cache("permissionList");
if (!$permissionList) {
    $permissionList = array();
    //实现权限发现机制，仅执行一次：遍历功件x方法获取权限项并缓存
    foreach ($funcList as $funcName) {
        //根据功件主类的x方法返回值中的permission声明，生成权限项列表
        $func = $$funcName->x();
        $permissionArray = $func["permission"];
        //保存权限项到权限列表
        foreach ($permissionArray as $permission) {
            array_push($permissionList, $permission);
        }
    }
    if (!empty($permissionList)) {
        $functreeUtil->save_cache($permissionList, "permissionList", 24*3600);
    }
}

//从Session缓存中读取菜单列表。因为根据用户登录状态的不同，其菜单显示不同，所以菜单项列表缓存于用户Session中
if (isset($_SESSION["menuLeftList"])) {
    $menuLeftList = $_SESSION["menuLeftList"];//除用户相关的菜单项位于导航栏左侧
    $menuRightList = $_SESSION["menuRightList"];//用户相关菜单项位于导航栏右侧
} else {
    $menuLeftList = array();
    $menuRightList = array();
    //实现菜单发现机制：遍历功件x方法获取菜单项并缓存；如果用户登录成功，则需要刷新菜单列表，因为菜单项可能发生变化
    foreach ($funcList as $funcName) {
        //根据功件主类的x方法返回值中的menu声明，生成导航菜单项列表
        $func = $$funcName->x();
        $menuArray = $func["menu"];
        //保存菜单项到菜单列表
        if ($funcName == "User") {
            //用户相关菜单项位于右侧
            foreach ($menuArray as $menu) {
                array_push($menuRightList, $menu);
            }
        } else {
            //非用户相关菜单项位于左侧
            foreach ($menuArray as $menu) {
                array_push($menuLeftList, $menu);
            }
        }
    }
    if (!empty($menuLeftList)) {
        $_SESSION["menuLeftList"] = $menuLeftList;
    }
    if (!empty($menuRightList)) {
        $_SESSION["menuRightList"] = $menuRightList;
    }
}

//以下为生成页面内容
//页面Title
$pageTitle = "";
//激活菜单项列表，如果为空则激活“首页”菜单项
$activeMenuArray = array();
//页面上、左、右、下区域的Html内容
$index_top = $index_left = $index_right = $index_bottom = "";
//$requestFuncName不等于""，则调用功件及其方法返回网页内容，否则生成网站首页显示内容
if ($requestFuncName != "") {
    //当前请求功件，如果为null，则为首页请求
    if (isset($$requestFuncName)) {
        $requestFunc = $$requestFuncName;
        //$requestFunc存在，则调用功件方法，生成网页内容
        //将功件名称设为页面title
        $pageTitle = isset($requestFunc->localName) ? $requestFunc->localName : (isset($requestFunc->name) ? $requestFunc->name : get_class($requestFunc));
        //如果没有访问方法，则默认调用x方法
        if ($requestMethodName == "") {
            $requestMethodName = "x";
        }
        if (method_exists($requestFunc, $requestMethodName)) {
            //$param_arr设为空，返回内容外部可见
            $param_arr = null;
            //调用功件主类的方法，并获取返回页面内容
            $result = call_user_func_array(array($requestFuncName, $requestMethodName), array($param_arr));
            //如果$result['exit']=true，则说明功件方法已完成网页内容输出，应停止后续任务；此类网页的所有显示内容（包括前端库引用、导航条显示等）由功件方法本身完成echo
            if (isset($result['exit']) && $result['exit'] === true) {
                //由于功件方法已返回页面内容，所以停止程序处理
                exit;
            } else {
                //是否有激活菜单项
                if (isset($result['activeMenu'])) {
                    $activeMenuArray = $result['activeMenu'];
                }
                //由功件返回的局部页面内容默认显示在index_top区域
                $index_top .= $result['result'];
            }
        } else {//404错误：访问功件方法不存在
            $pageTitle = "404错误";
            $index_top .= "<div align='center'>您访问功件方法不存在。</div>";
        }
    } else {
        if ($requestFuncName == "x") {
            $pageTitle = $requestFuncName;
            //通过http://WEB_DOMAIN/ROOT_PATH/x方法访问时显示的本网站的描述内容
            $xDescripton = <<<xDescripton
<p>这是一个基于PHP7+MongoDB3+Bootstrap3开发的Web程序：
<ul>
<li>项目根目录下的index.php作为网站主页和URLRewrite入口文件，项目根目录下的.htaccess文件为用于Apache服务器的URLRewrite配置，注意开启http.conf中的“LoadModule rewrite_module modules/mod_rewrite.so”模块，并修改AllowOverride None 为 AllowOverride All；其他种类服务器请使用相应的URLRewrite配置；
<br>其中Apache服务器.htaccess文件示例：
<br>RewriteEngine on
<br>RewriteRule !\.(txt|js|html|ico|gif|jpg|png|css|xml|map|woff|woff2|ttf)$ index.php
<br><br>
Nginx服务器配置文件nginx.conf内容示例：
<br>        location / {
<br>            root   html;
<br>            index  index.html index.htm index.php;
<br>            if (!-e \$request_filename){
<br>                 rewrite ^/Phmobo/(.*)$ /Phmobo/index.php?$1 last;
<br>                 break;
<br>            }
<br>            #try_files \$uri /index.php\$uri;
<br>        }
</li>
<li>项目根目录下的phplib为功件共享类库文件目录，功件主目录下的phplib为功件专有类库文件目录，类库文件目录下的config.php为全局或功件专有配置文件；</li>
<li>项目根目录下的jslib为功件共享前端库文件目录，同样的，功件主目录下的jslib为功件专有前端库文件目录；功件共享图片位置：http://WEB_DOMAIN/ROOT_PATH/images/功件名称，如images/index/logo.png。</li>
</ul>
</p>

<p>网站主页包含网页顶部、底部以及可以定制内容的中部，网页顶部为网站导航菜单，中部为首页内容和其他功件的显示内容。</p>

<p>如果网站访问URL中未明确声明调用某功件时，默认显示首页内容，由网站主页通过调用功件的x方法获取homePage的声明内容组合而成；网站主页通过功件x方法中的menu声明获取菜单项，动态生成网站导航菜单，通过功件x方法中的homePage声明获取首页中部显示内容，写入网站主页的内容显示区域；功件本身的的HTML显示内容，则通过功件方法的返回值，写入网站主页的内容显示区域。

<p>php开发的功件位于php目录下，功件主文件名称规范：功件名称.php，如User.php，功件主文件有且仅有一个，其内部有一个公共类，类名称即为功件名称，如User；功件主目录名称为功件名称的小写，如user/，功件主目录包含该功件相关的所有附属文件。</p>

<p>功件类实现规范：每个功件必须实现一个由功件名称命名的主类，其必须实现功件的一级方法：x方法；其中x方法为默认方法，即如果URL中未明确声明调用某方法时，默认调用功件的x方法返回该功件的相关内容；其中功件名称和功件方法名称均为英文字母、数字、下划线等组成。</p>

<p>功件方法实现规范：功件的x方法，有且仅有一个参数param_arr，为索引数组，返回值为return_arr，为关联数组；功件内部方法调用或URL访问方法名称、参数可自定义，返回值均为return_arr，return_arr的返回值格式类似如下：["result"=>"HTML字符串", "menu"=>menuArray, "permission"=>permissionArray, "homePage"=>homePageArray, "activeMenu"=>activeMenuArray]；</p>

<p>menu格式：array('id'=>"UserInfo", 'name'=>"用户信息", 'path'=>"User/info", 'children'=>[])；其中id为菜单项唯一标识，用于菜单激活显示；name为菜单项名称和显示内容；path为菜单项链接相对路径；children为菜单子项
<br>permission格式：array('id'=>"UserA", 'name'=>"增加用户")，id为权限唯一标识，name为权限名称和显示内容
<br>homePage格式：array('name'=>"最新用户", 'path'=>"User/b", 'position'=>"right", 'children'=>[
            array('name'=>"用户1",'path'=>"User/b?id=1"),
            array('name'=>"用户2",'path'=>"User/b?id=2")
        ])；其中name为显示内容标题；path为链接相对路径；position为内容显示区域（top：显示在顶部；left：显示在左侧，宽度为9，用于显示长内容；right：显示在右侧，宽度为3，用于显示短内容；bottom：显示在底部）；children为内容详细
<br>activeMenu格式：array(menuId)，其中menuId为菜单项ID，对应上述menu中的id
。</p>

<p>功件访问URL规范：http://WEB_DOMAIN/ROOT_PATH/功件名称/功件方法?param1=value1&amp;param2=value2。</p>
xDescripton;
            $index_top .= $xDescripton;
        } else {//404错误：访问功件不存在
            $pageTitle = "404错误";
            $index_top .= "<div align='center'>您访问功件页面不存在。</div>";
        }
    }
} else {
    //生成首页内容
    $pageTitle = "首页";
    //遍历功件主类的x方法返回值中的homePage声明，动态生成网站首页内容
    foreach ($funcList as $funcName) {
        $func = $$funcName->x();
        $homePageArray = $func["homePage"];
        foreach ($homePageArray as $page) {
            $title = $page["name"];//内容的标题
            $pageContent = "
                <div class=\"panel panel-default\">
                <div class=\"panel-heading\">";
            if (isset($page["path"])) {
                $path = $page["path"];//标题链接地址
                $pageContent .= "<a href='".WEB_ROOT."$path'>$title</a>";
            } else {
                $pageContent .= $title;
            }
            $pageContent .= "
                </div>
                <div class=\"panel-body\">
            ";
            if (isset($page["children"])) {
                $children = $page["children"];//内容详细
                foreach ($children as $child) {
                    if (isset($child["path"]) && $child["path"] != "") {
                        $pageContent .= "<div><a href='".WEB_ROOT.$child["path"]."'>".$child["name"]."</a></div>";
                    } else {
                        $pageContent .= "<div>".$child["name"]."</div>";
                    }
                }
            }
            $pageContent .= "
                </div>
                </div>
            ";
            //根据position确定显示区域
            if (isset($page["position"])) {
                $position = $page["position"];//内容显示位置：top、left、right、bottom
                if ($position == "left") {
                    $index_left .= $pageContent;
                } else if ($position == "right") {
                    $index_right .= $pageContent;
                } else if ($position == "bottom") {
                    $index_bottom .= $pageContent;
                } else {
                    $index_top .= $pageContent;
                }
            } else {//默认显示在index_top区域
                $index_top .= $pageContent;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title><?php echo $pageTitle;?></title>

    <!-- Bootstrap -->
    <link href="<?php echo WEB_ROOT;?>jslib/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="<?php echo WEB_ROOT;?>jslib/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="<?php echo WEB_ROOT;?>jslib/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo WEB_ROOT;?>jslib/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo WEB_ROOT;?>jslib/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style type="text/css">
/* global css */
Body {
    padding-top: 60px;
    padding-bottom: 50px;
    font-family: 'Helvetica Neue',Helvetica,'PingFang SC','Hiragino Sans GB','Microsoft YaHei',Arial,sans-serif;
    font-size: 12px;
}
.Nav-Style {
    font-size: 14px;
}
.btn,.input-group-addon,.dropdown-menu {
    font-size: 12px;
}
.form-control {
    font-size: 12px;
    height: 31px;
}
.navbar {
    min-height: 38px;
}
.navbar-default {
    background-color: #fff;
}
.Nav-Style .navbar-nav>li>a:focus,.Nav-Style .navbar-nav>li>a:hover{
    color: #eb7350;
}
.navbar-nav>li>a {
    padding-top: 9px;
    padding-bottom: 9px;
}
.navbar-toggle {
    padding: 5px 8px;
    margin: 6px 15px
}
.navbar-toggle collapsed {
    padding-top: 9px;
    padding-bottom: 9px;
    margin-top: 2px
}
.navbar-brand {
    height: 38px;
    padding: 3px 15px;
}
.breadcrumb {
    margin-bottom: 0px;
    background-color: #fff;
    padding: 5px 0px;
}
.badge-custom {
    font-weight: normal;
    padding: 1px 3px;
    margin-top: -3px;
}
    </style>
  </head>
  <body>
   <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top Nav-Style">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">导航转换</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo WEB_ROOT;?>"><img src="<?php echo WEB_ROOT;?>images/index/logo.png"></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="<?php if (empty($activeMenuArray)) echo "active";?>"><a href="<?php echo WEB_ROOT;?>">首页</a></li>
                <?php
                foreach ($menuLeftList as $menu) {
                    //如果有菜单子项
                    if (array_key_exists("children", $menu) && !empty($menu["children"])) {
                        $children = $menu["children"];
                    ?>
                    <li class="dropdown <?php if (in_array($menu["id"], $activeMenuArray)) echo "active";?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $menu["name"];?><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                        <?php foreach ($children as $child) {?>
                            <li class="<?php if (in_array($child["id"], $activeMenuArray)) echo "active";?>"><a href="<?php echo WEB_ROOT.$child["path"]?>"><?php echo $child["name"]?></a></li>
                        <?php }?>
                        </ul>
                    </li>
                <?php
                    } else {
                ?>
                    <li class="<?php if (in_array($menu["id"], $activeMenuArray)) echo "active";?>"><a href="<?php echo WEB_ROOT.$menu["path"];?>"><?php echo $menu["name"];?></a></li>
                <?php
                    }
                }
                ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                <?php
                foreach ($menuRightList as $menu) {
                    //如果有菜单子项
                    if (array_key_exists("children", $menu) && !empty($menu["children"])) {
                        $children = $menu["children"];
                    ?>
                    <li class="dropdown <?php if (in_array($menu["id"], $activeMenuArray)) echo "active";?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $menu["name"];?><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                        <?php foreach ($children as $child) {?>
                            <li class="<?php if (in_array($child["id"], $activeMenuArray)) echo "active";?>"><a href="<?php echo WEB_ROOT.$child["path"]?>"><?php echo $child["name"]?></a></li>
                        <?php }?>
                        </ul>
                    </li>
                <?php
                    } else {
                ?>
                    <li class="<?php if (in_array($menu["id"], $activeMenuArray)) echo "active";?>"><a href="<?php echo WEB_ROOT.$menu["path"];?>"><?php echo $menu["name"];?></a></li>
                <?php
                    }
                }
                ?>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>
    
    <div class="container">
        <div class="row">
          <div class="col-md-12" id="index_top"><?php echo $index_top;?></div>
        </div>
        <div class="row">
          <div class="col-md-9" id="index_left"><?php echo $index_left;?></div>
          <div class="col-md-3" id="index_right"><?php echo $index_right;?></div>
        </div>
        <div class="row">
          <div class="col-md-12" id="index_bottom"><?php echo $index_bottom;?></div>
        </div>
    </div>
    <style type="text/css">
      .page_footer_bottom {
          width: 100%;
          background-color: white;
          color: #999;
          border-top: 1px solid #eee;
          position: fixed;
          z-index: 1030;
          bottom: 0px;
          padding: 5px;
          font-size: 12px;
          margin-top: 20px
      }
      .page_footer_bottom a{
          color: #999
      }
    </style>
    <div class="page_footer_bottom">
    <div class="text-center"><footer>
        <div class="col-md-6">&copy; 2018 <?php echo WEB_DOMAIN;?> 
        
        </div>
        <div class="col-md-6"><a href="SitePolicy" target="_blank">免责声明</a> <a href="ContactMe">联系我们</a></div></footer></div>
    </div>
  </body>
</html>