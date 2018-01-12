<?php
//引入功件配置文件，包括配置参数、公共类库
require_once(__DIR__ . '/config.php');

Class RoleUtil {
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
     * 增加新角色
     * @param string $id 角色ID
     * @param string $name 用户名号
     * @param string $addTime 可选参数，添加时间，时间格式："Y-m-d H:i:s"
     * @return bool 增加成功返回true，否则返回false
     */
    static public function a($id, $name, $addTime = null) {
        $mongoDb = self::getMongoDb();
        try {
            $document = array();
            if (isset($id)) {
                $document["id"] = $id;
            } else {
                exit;
            }
            if (isset($name)) {
                $document["name"] = $name;
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
            $tableName = FUNCTREE_USER_ROLE_TABLENAME;
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
     * 增加新角色与权限关系
     * @param string $roleId 角色ID
     * @param string $permissionId 权限ID
     * @param string $addTime 可选参数，添加时间，时间格式："Y-m-d H:i:s"
     * @return bool 增加成功返回true，否则返回false
     */
    static public function a_RoleToPermission($roleId, $permissionId, $addTime = null) {
        $mongoDb = self::getMongoDb();
        try {
            $document = array();
            if (isset($roleId)) {
                $document["roleId"] = $roleId;
            } else {
                exit;
            }
            if (isset($permissionId)) {
                $document["permissionId"] = $permissionId;
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
            $tableName = FUNCTREE_USER_ROLE_TO_PERMISSION_TABLENAME;
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
     * 查询用户角色信息
     * @param array $where 查询where条件，如：[ ]
     * @param array $options 可选参数，查询其他条件，包括排序、字段筛选等，如：，如：[ 'projection' => [ 'name' => 1 ] ]
     * @return array 返回用户角色信息Array
     */
    static public function b($where, $options=null) {
        $ret = array();
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_ROLE_TABLENAME;
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
     * 查询用户角色与权限关系信息
     * @param array $where 查询where条件，如：[ 'roleId' => $roleId ]
     * @param array $options 可选参数，查询其他条件，包括排序、字段筛选等，如：，如：[ 'projection' => [ 'permissionId' => 1 ] ]
     * @return array 返回用户角色与权限关系信息Array
     */
    static public function b_RoleToPermission($where, $options=null) {
        $ret = array();
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_ROLE_TO_PERMISSION_TABLENAME;
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
     * 查询一条用户角色信息
     * @param array $where 查询where条件，如：[ 'roleId' => $roleId ]
     * @param array $options 可选参数，查询其他条件，包括排序、字段筛选等，如：，如：[ 'projection' => [ 'name' => 1 ] ]
     * @return array 返回一条用户角色信息document，如果没有记录返回，则返回null
     */
    static public function b_one($where, $options=null) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_ROLE_TABLENAME;
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
     * 查询角色数量
     * @param array $where 查询where条件，如：['id' => $id]
     * @return int 返回角色数量，如果查询失败则返回-1
     */
    static public function b_count($where) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_ROLE_TABLENAME;
            $collection = $mongoDb->$tableName;
            $count = $collection->count($where);
        } catch (Exception $e) {
            $count = -1;
        }
        return $count;
    }
    /**
     * 更改一条角色信息
     * @param array $where 查询where条件，如：[ "id" => $roleId ]
     * @param array $field 需要更改的信息，如：，如：[ 'name' => $name ]
     * @return bool 更改成功返回true，否则返回false
     */
    static public function c_one($where, $field) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_ROLE_TABLENAME;
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
     * 删除一条角色信息
     * @param array $where 查询where条件，如：[ 'id' => $roleId ]
     * @return bool 删除成功返回true，否则返回false
     */
    static public function d_one($where) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_ROLE_TABLENAME;
            $collection = $mongoDb->$tableName;
            $result = $collection->deleteOne( $where );
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
     * 删除角色与权限关系信息
     * @param array $where 查询where条件，如：[ 'roleId' => $roleId ]
     * @return bool 删除成功返回true，否则返回false
     */
    static public function d_RoleToPermission($where) {
        $mongoDb = self::getMongoDb();
        try {
            $tableName = FUNCTREE_USER_ROLE_TO_PERMISSION_TABLENAME;
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
}
?>