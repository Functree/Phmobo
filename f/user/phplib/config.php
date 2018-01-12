<?php 
//使用网站共享库phplib中的MongoDB
require_once(__DIR__ . '/../../../phplib/mongodb/1.2.0/vendor/autoload.php');
//SMTP发送Email相关参数，需要自定义
defined('FUNCTREE_SMTP_HOST') or define( 'FUNCTREE_SMTP_HOST', "smtp.aa.com" );//邮箱的SMTP服务器地址
defined('FUNCTREE_SMTP_PORT') or define( 'FUNCTREE_SMTP_PORT', 25 );//邮箱的SMTP服务器端口号
defined('FUNCTREE_SMTP_USERNAME') or define( 'FUNCTREE_SMTP_USERNAME', "noreply@aa.cn" );//SMTP邮箱的用户名称，如果为""则不需要认证
defined('FUNCTREE_SMTP_PASSWORD') or define( 'FUNCTREE_SMTP_PASSWORD', "pwd" );//SMTP邮箱的登录密码
defined('FUNCTREE_SMTP_SECURE') or define( 'FUNCTREE_SMTP_SECURE', "" );//SMTP邮箱是否使用安全加密方式登录：tls ssl，如果为""则不使用加密方式

//自动注册的“系统管理员”账号Email，需要自定义;“系统管理员”账号默认具有“系统管理员角色”
defined('FUNCTREE_ADMINISTRATOR_EMAIL') or define( 'FUNCTREE_ADMINISTRATOR_EMAIL', "email@aa.net" );
//自动注册的“系统管理员”账号密码，需要自定义
defined('FUNCTREE_ADMINISTRATOR_PASSWORD') or define( 'FUNCTREE_ADMINISTRATOR_PASSWORD', "pwd" );
//自动注册的“系统管理员”账号名称
defined('FUNCTREE_ADMINISTRATOR_NAME') or define( 'FUNCTREE_ADMINISTRATOR_NAME', "系统管理员" );

//MongoDB数据库连接字符串，需要自定义
defined('FUNCTREE_MONGODB_CONNECTION') or define( 'FUNCTREE_MONGODB_CONNECTION', "mongodb://user:pwd@localhost:27017/Phmobo" );
//数据库名称
defined('FUNCTREE_MONGODB_DBNAME') or define( 'FUNCTREE_MONGODB_DBNAME', "Phmobo" );

//用户数据表
defined('FUNCTREE_USER_TABLENAME') or define( 'FUNCTREE_USER_TABLENAME', "User" );
//用户图片数据表
defined('FUNCTREE_USER_IMAGE_TABLENAME') or define( 'FUNCTREE_USER_IMAGE_TABLENAME', "UserImage" );

//用户角色数据表
defined('FUNCTREE_USER_ROLE_TABLENAME') or define( 'FUNCTREE_USER_ROLE_TABLENAME', "UserRole" );
//用户与角色关系数据表
defined('FUNCTREE_USER_TO_ROLE_TABLENAME') or define( 'FUNCTREE_USER_TO_ROLE_TABLENAME', "UserToRole" );
//角色与权限关系数据表
defined('FUNCTREE_USER_ROLE_TO_PERMISSION_TABLENAME') or define( 'FUNCTREE_USER_ROLE_TO_PERMISSION_TABLENAME', "UserRoleToPermission" );

//自动生成的“系统管理员角色”的角色ID，默认只有自动注册的“系统管理员”账号具备此角色；“系统管理员角色”具备所有系统权限
defined('FUNCTREE_USER_ROLE_ADMINISTRATOR_ID') or define( 'FUNCTREE_USER_ROLE_ADMINISTRATOR_ID', "Administrator" );
//自动生成的“系统管理员角色”的角色名称
defined('FUNCTREE_USER_ROLE_ADMINISTRATOR_NAME') or define( 'FUNCTREE_USER_ROLE_ADMINISTRATOR_NAME', "系统管理员角色" );
//自动生成的“用户默认角色”的角色ID，所有注册用户均默认具备此角色；“用户默认角色”默认不具备系统权限，需要系统管理员分配
defined('FUNCTREE_USER_ROLE_DEFAULT_ID') or define( 'FUNCTREE_USER_ROLE_DEFAULT_ID', "DefaultUserRole" );
//自动生成的“用户默认角色”的角色名称
defined('FUNCTREE_USER_ROLE_DEFAULT_NAME') or define( 'FUNCTREE_USER_ROLE_DEFAULT_NAME', "用户默认角色" );

//用户状态，“有效”状态用户才允许登录
defined('FUNCTREE_USER_STATUS') or define( 'FUNCTREE_USER_STATUS', [1=>"有效", -1=>"禁用"] );
?>