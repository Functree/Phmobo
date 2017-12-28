<?php 
//使用网站共享库phplib中的MongoDB
require_once(__DIR__ . '/../../../phplib/mongodb/1.2.0/vendor/autoload.php');
//SMTP发送Email相关参数，根据实际情况设置
defined('SMTP_HOST') or define( 'SMTP_HOST', "smtp.aa.com" );//邮箱的SMTP服务器地址
defined('SMTP_PORT') or define( 'SMTP_PORT', 25 );//邮箱的SMTP服务器端口号
defined('SMTP_USERNAME') or define( 'SMTP_USERNAME', "noreply@aa.cn" );//SMTP邮箱的用户名称，如果为""则不需要认证
defined('SMTP_PASSWORD') or define( 'SMTP_PASSWORD', "pwd" );//SMTP邮箱的登录密码
defined('SMTP_SECURE') or define( 'SMTP_SECURE', "" );//SMTP邮箱是否使用安全加密方式登录：tls ssl，如果为""则不使用加密方式

//MongoDB数据库连接字符串
defined('MONGODB_CONNECTION') or define( 'MONGODB_CONNECTION', "mongodb://user:pwd@localhost:27017/Phmobo" );
//数据库名称
defined('MONGODB_DBNAME') or define( 'MONGODB_DBNAME', "Phmobo" );
//用户数据表
defined('MONGODB_USER_TABLENAME') or define( 'MONGODB_USER_TABLENAME', "User" );
//用户图片数据表
defined('MONGODB_USER_IMAGE_TABLENAME') or define( 'MONGODB_USER_IMAGE_TABLENAME', "UserImage" );
?>