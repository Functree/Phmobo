<?php 
//网站网址相关参数，需要自定义
define( 'FUNCTREE_WEB_DOMAIN', 'localhost' );//网站域名
define( 'FUNCTREE_ROOT_PATH', 'Phmobo' );//网站程序驻留根路径，如果没有则给""
define( 'FUNCTREE_WEB_ROOT', "http://localhost/Phmobo/" );//网站程序RootUrl，格式："http://".FUNCTREE_WEB_DOMAIN."/".FUNCTREE_ROOT_PATH."/"

//本网站的名称，需要自定义
define( 'FUNCTREE_WEB_NAME', '我的网站' );//网站名称，用于发送注册Email等

//SMTP发送Email相关参数，需要自定义
define( 'FUNCTREE_SMTP_HOST', "smtp.mxhichina.com" );//邮箱的SMTP服务器地址
define( 'FUNCTREE_SMTP_PORT', 80 );//邮箱的SMTP服务器端口号
define( 'FUNCTREE_SMTP_USERNAME', "noreply@functree.cn" );//SMTP邮箱的用户名称，如果为""则不需要认证
define( 'FUNCTREE_SMTP_PASSWORD', "mwne2*nh" );//SMTP邮箱的登录密码
define( 'FUNCTREE_SMTP_SECURE', "" );//SMTP邮箱是否使用安全加密方式登录：tls ssl，如果为""则不使用加密方式

//“系统管理员”账号Email，需要自定义；“系统管理员”账号默认具有“系统管理员角色”
define( 'FUNCTREE_ADMINISTRATOR_EMAIL', "weilai_2006@yeah.net" );
//“系统管理员”账号密码，需要自定义
define( 'FUNCTREE_ADMINISTRATOR_PASSWORD', "admin123" );

//MongoDB数据库连接字符串，需要自定义
define( 'FUNCTREE_MONGODB_CONNECTION', "mongodb://phmoboUser:phmoboPwd@localhost:27017/Phmobo" );
define( 'FUNCTREE_MONGODB_DBNAME', "Phmobo" );

//全局常量：FUNCTREE_FUNC_PATH，功件所处目录所在路径，本网站的所有功件均位于网站根目录的f目录下，一般不要修改，遍历此目录可获取所有功件列表
define( 'FUNCTREE_FUNC_PATH', "f/" );
?>