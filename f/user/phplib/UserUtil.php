<?php
use PHPMailer\PHPMailer\PHPMailer;
//引入功件配置文件，包括配置参数、公共类库
require_once(__DIR__ . '/config.php');

Class UserUtil {
    /**
     * 获取MongoDB数据库MongoDB\Database对象实例
     * @return MongoDB\Database 返回MongoDB的数据库实例
     */
    static public function getMongoDb() {
        $client = new MongoDB\Client(FUNCTREE_MONGODB_CONNECTION);
        $db = FUNCTREE_MONGODB_DBNAME;
        return $client->$db;
    }
    /**
     * 增加新用户
     * @param string $email 用户Email
     * @param string $password 登录密码
     * @param string $name 用户名号
     * @param int $userGroup 可选参数，用户分组
     * @param string $addTime 可选参数，添加时间，时间格式："Y-m-d H:i:s"
     * @return NULL|string 返回用户ID字符串；如果增加用户失败，则返回null
     */
    static public function a($email, $password, $name, $userGroup = null, $addTime = null) {
        $mongoDb = self::getMongoDb();
        try {
            $document = array();
            if (isset($email)) {
                $document["email"] = $email;
            } else {
                exit;
            }
            if (isset($password)) {
                $document["password"] = $password;
            } else {
                exit;
            }
            if (isset($name)) {
                $document["name"] = $name;
            } else {
                exit;
            }
            if (!isset($userGroup)) {
                $userGroup = 1;
            }
            $document["userGroup"] = $userGroup;
            $document["status"] = 1;//用户默认状态为1；1=有效，-1=禁用
            $userId = self::createGuid($userGroup);
            $document["id"] = $userId;
            if (isset($addTime)) {
                $document["addTime"] = $addTime;
            } else {
                date_default_timezone_set('PRC');
                $addTime = date("Y-m-d H:i:s", time());
                $document["addTime"] = $addTime;
            }
            $tableName = FUNCTREE_USER_TABLENAME;
            $collection = $mongoDb->$tableName;
            $result = $collection->insertOne( $document );
            if ($result->isAcknowledged() && $result->getInsertedCount() > 0) {
                $ret = $userId;
            } else {
                $ret = null;
            }
        } catch (Exception $e) {
            $ret = null;
        }
        return $ret;
    }
    /**
     * 增加新用户图片
     * @param string $userId 用户ID
     * @param string $type 图片类型，如："photo"代表用户头像
     * @param string $suffix 图片文件后缀
     * @param string $image 图片数据的base64字符串，
     $image = file_get_contents($_FILES['file0']['tmp_name']);
     $image = base64_encode($image);
     
     * @return bool 增加成功返回true，否则返回false
     */
    static public function a_UserImage($userId, $type, $suffix, $image) {
        $mongoDb = self::getMongoDb();
        try {
            $document = array();
            if (isset($userId)) {
                $document["userId"] = $userId;
            } else {
                exit;
            }
            if (isset($type)) {
                $document["type"] = $type;
            } else {
                exit;
            }
            if (isset($suffix)) {
                $document["suffix"] = $suffix;
            } else {
                exit;
            }
            if (isset($image)) {
                $document["image"] = $image;
            } else {
                exit;
            }
            $tableName = FUNCTREE_USER_IMAGE_TABLENAME;
            $collection = $mongoDb->$tableName;
            $result = $collection->insertOne( $document );
            if ($result->isAcknowledged() && $result->getInsertedCount() > 0) {
                $ret = true;
            } else {
                $ret = false;
            }
        } catch (Exception $e) {
            $ret = false;
        }
        return $ret;
    }
    /**
     * 增加用户与角色关系
     * @param string $userId 用户ID
     * @param string $roleId 角色ID
     * @return bool 增加成功返回true，否则返回false
     */
    static public function a_UserToRole($userId, $roleId, $addTime = null) {
        $mongoDb = self::getMongoDb();
        try {
            $document = array();
            if (isset($userId)) {
                $document["userId"] = $userId;
            } else {
                exit;
            }
            if (isset($roleId)) {
                $document["roleId"] = $roleId;
            } else {
                exit;
            }
            if (isset($addTime)) {
                $document["addTime"] = $addTime;
            } else {
                date_default_timezone_set('PRC');
                $addTime = date("Y-m-d H:i:s", time());
                $document["addTime"] = $addTime;
            }
            $tableName = FUNCTREE_USER_TO_ROLE_TABLENAME;
            $collection = $mongoDb->$tableName;
            $result = $collection->insertOne( $document );
            if ($result->isAcknowledged() && $result->getInsertedCount() > 0) {
                $ret = true;
            } else {
                $ret = false;
            }
        } catch (Exception $e) {
            $ret = false;
        }
        return $ret;
    }
    /**
     * 查询用户信息
     * @param array $where 查询where条件，如：[ "id" => $userId ]
     * @param array $options 可选参数，查询其他条件，包括排序、字段筛选等，如：[ 'sort' => [ 'addTime' => -1 ], 'limit' => 10, 'projection' => [ 'name' => 1 ] ]
     * @return array 返回结果集数组
     */
    static public function b($where, $options=null) {
        $ret = array();
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_TABLENAME;
            $collection = $mongoDb->$tableName;
            if (isset($options)) {
                $result = $collection->find( $where, $options );
            } else {
                $result = $collection->find( $where );
            }
            foreach ($result as $entry) {
                array_push($ret, $entry);
            }
        } catch (Exception $e) {
        }
        return $ret;
    }
    /**
     * 查询用户与角色关系信息
     * @param array $where 查询where条件，如：[ "userId" => $userId ]
     * @param array $options 可选参数，查询其他条件，包括排序、字段筛选等，如：[ 'sort' => [ 'addTime' => -1 ], 'limit' => 10, 'projection' => [ 'roleId' => 1 ] ]
     * @return array 返回结果集数组
     */
    static public function b_UserToRole($where, $options=null) {
        $ret = array();
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_TO_ROLE_TABLENAME;
            $collection = $mongoDb->$tableName;
            if (isset($options)) {
                $result = $collection->find( $where, $options );
            } else {
                $result = $collection->find( $where );
            }
            foreach ($result as $entry) {
                array_push($ret, $entry);
            }
        } catch (Exception $e) {
        }
        return $ret;
    }
    /**
     * 查询一条用户信息
     * @param array $where 查询where条件，如：[ "id" => $userId ]
     * @param array $options 可选参数，查询其他条件，包括排序、字段筛选等，如：，如：[ 'projection' => [ 'name' => 1 ] ]
     * @return array 返回一条用户信息document，如果没有记录返回，则返回null
     */
    static public function b_one($where, $options=null) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_TABLENAME;
            $collection = $mongoDb->$tableName;
            if (isset($options)) {
                $ret = $collection->findOne( $where, $options );
            } else {
                $ret = $collection->findOne( $where );
            }
        } catch (Exception $e) {
            $ret = null;
        }
        return $ret;
    }
    /**
     * 查询一条用户图片信息
     * @param array $where 查询where条件，如：[ 'userId' => $userId, 'type' => 'photo' ]
     * @param array $options 可选参数，查询其他条件，包括排序、字段筛选等，如：，如：[ 'projection' => [ 'suffix' => 1, 'image' => 1 ] ]
     * @return array 返回一条用户图片信息document，如果没有记录返回，则返回null
     */
    static public function b_one_UserImage($where, $options=null) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_IMAGE_TABLENAME;
            $collection = $mongoDb->$tableName;
            if (isset($options)) {
                $ret = $collection->findOne( $where, $options );
            } else {
                $ret = $collection->findOne( $where );
            }
        } catch (Exception $e) {
            $ret = null;
        }
        return $ret;
    }
    /**
     * 查询用户数量
     * @param array $where 查询where条件，如：['email' => $email]
     * @return int 返回用户数量，如果查询失败则返回-1
     */
    static public function b_count($where) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_TABLENAME;
            $collection = $mongoDb->$tableName;
            $count = $collection->count($where);
        } catch (Exception $e) {
            $count = -1;
        }
        return $count;
    }
    /**
     * 查询用户图片数量
     * @param array $where 查询where条件，如：[ 'userId' => $userId, 'type' => 'photo' ]
     * @return int 返回用户数量，如果查询失败则返回-1
     */
    static public function b_count_UserImage($where) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_IMAGE_TABLENAME;
            $collection = $mongoDb->$tableName;
            $count = $collection->count($where);
        } catch (Exception $e) {
            $count = -1;
        }
        return $count;
    }
    /**
     * 更改一条用户信息
     * @param array $where 查询where条件，如：[ "id" => $userId ]
     * @param array $field 需要更改的信息，如：，如：[ 'realName' => $realName, 'address' => $address, 'contactPhone' => $contactPhone ]
     * @return bool 更改成功返回true，否则返回false
     */
    static public function c_one($where, $field) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_TABLENAME;
            $collection = $mongoDb->$tableName;
            $result = $collection->updateOne( $where, [ '$set' => $field ] );
            if ($result->isAcknowledged() && $result->getModifiedCount() > 0) {
                $ret = true;
            } else {
                $ret = false;
            }
        } catch (Exception $e) {
            $ret = false;
        }
        return $ret;
    }
    /**
     * 更改一条用户图片信息
     * @param array $where 查询where条件，如：[ 'userId' => $userId, 'type' => 'photo' ]
     * @param array $field 需要更改的信息，如：，如：[ 'suffix' => $suffix, 'image' => $image ]
     * @return bool 更改成功返回true，否则返回false
     */
    static public function c_one_UserImage($where, $field) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_IMAGE_TABLENAME;
            $collection = $mongoDb->$tableName;
            $result = $collection->updateOne( $where, [ '$set' => $field ] );
            if ($result->isAcknowledged() && $result->getModifiedCount() > 0) {
                $ret = true;
            } else {
                $ret = false;
            }
        } catch (Exception $e) {
            $ret = false;
        }
        return $ret;
    }
    /**
     * 删除用户与角色关系信息
     * @param array $where 查询where条件，如：[ 'userId' => $userId ]
     * @return bool 删除成功返回true，否则返回false
     */
    static public function d_UserToRole($where) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_TO_ROLE_TABLENAME;
            $collection = $mongoDb->$tableName;
            $result = $collection->deleteMany( $where );
            if ($result->isAcknowledged() && $result->getDeletedCount() > 0) {
                $ret = true;
            } else {
                $ret = false;
            }
        } catch (Exception $e) {
            $ret = false;
        }
        return $ret;
    }
    /**
     * 将密码加密保存
     * @param string $password 密码明文
     * @return string 返回加密后的密码
     */
    static public function md5Password($password) {
        return md5($password."xabcdtFunctree3");
    }
    /**
     * 生成全局UserId
     * @param string $userGroup 用户分组
     * @return string 返回带有时间排序的Guid
     */
    static public function createGuid($userGroup = '') {
        static $guid = '';
        $uid = uniqid($userGroup, true);
        $data = $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : "";
        $data .= isset($_SERVER['LOCAL_PORT']) ? $_SERVER['LOCAL_PORT'] : "";
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) .
        '-' .
        substr($hash, 8, 4) .
        '-' .
        substr($hash, 12, 4) .
        '-' .
        substr($hash, 16, 4) .
        '-' .
        substr($hash, 20, 12);
        return $guid;
    }
    /**发送邮件方法
     * @param $from：发送者邮箱
     * @param $fromName：发送者名称
     * @param $to：接收者邮箱
     * @param $subject：标题
     * @param $body：邮件内容
     * @return bool true:发送成功 false:发送失败
     */
    static public function sendMail($fromName, $to ,$subject, $body){
        
        //引入PHPMailer的核心文件 使用require_once包含避免出现PHPMailer类重复定义的警告
        require_once(__DIR__ . "/phpmailer/6.0.2/Exception.php");
        require_once(__DIR__ . "/phpmailer/6.0.2/PHPMailer.php");
        require_once(__DIR__ . "/phpmailer/6.0.2/SMTP.php");
        //实例化PHPMailer核心类
        $mail = new PHPMailer();
        
        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        //$mail->SMTPDebug = 1;
        
        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();
        if (FUNCTREE_SMTP_USERNAME != "") {
            //smtp需要鉴权 这个必须是true
            $mail->SMTPAuth = true;
            //smtp登录的账号
            $mail->Username = FUNCTREE_SMTP_USERNAME;
            //smtp登录的密码
            $mail->Password = FUNCTREE_SMTP_PASSWORD;
            //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
            $mail->From = FUNCTREE_SMTP_USERNAME;
        }
        
        //邮箱的SMTP服务器地址
        $mail->Host = FUNCTREE_SMTP_HOST;
        
        //设置使用安全加密方式登录鉴权
        if (FUNCTREE_SMTP_SECURE != "") {
            $mail->SMTPSecure = FUNCTREE_SMTP_SECURE;
        }
        
        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，可选465或587
        $mail->Port = FUNCTREE_SMTP_PORT;
        
        //设置发送的邮件的编码
        $mail->CharSet = 'UTF-8';
        
        //设置发件人姓名（昵称）任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = $fromName;
        
        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);
        
        //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        $mail->addAddress($to);
        
        //添加多个收件人 则多次调用方法即可
        // $mail->addAddress('xxx@163.com','lsgo在线通知');
        
        //添加该邮件的主题
        $mail->Subject = $subject;
        
        //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
        $mail->Body = $body;
        
        //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
        // $mail->addAttachment('./d.jpg','mm.jpg');
        //同样该方法可以多次调用 上传多个附件
        // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');
        
        $status = $mail->send();
        //简单的判断与提示信息
        if($status) {
            return true;
        }else{
            return false;
        }
    }
}
?>