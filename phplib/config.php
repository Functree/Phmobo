<?php 
//网站网址相关参数，根据实际情况设置
define( 'WEB_DOMAIN', 'localhost' );//网站域名，根据实际情况设置
define( 'ROOT_PATH', 'Phmobo' );//网站程序驻留根路径，根据实际情况设置
define( 'WEB_ROOT', "http://localhost/Phmobo/" );//网站程序RootUrl

//本网站的名称
define( 'WEB_NAME', '我的网站' );//网站名称，用于发送注册Email等

//SMTP发送Email相关参数，根据实际情况设置
define( 'SMTP_HOST', "smtp.aa.com" );//邮箱的SMTP服务器地址
define( 'SMTP_PORT', 25 );//邮箱的SMTP服务器端口号
define( 'SMTP_USERNAME', "noreply@aa.cn" );//SMTP邮箱的用户名称，如果为""则不需要认证
define( 'SMTP_PASSWORD', "pwd" );//SMTP邮箱的登录密码
define( 'SMTP_SECURE', "" );//SMTP邮箱是否使用安全加密方式登录：tls ssl，如果为""则不使用加密方式

//MongoDB数据库连接字符串
define( 'MONGODB_CONNECTION', "mongodb://user:pwd@localhost:27017/Phmobo" );
define( 'MONGODB_DBNAME', "Phmobo" );
?>