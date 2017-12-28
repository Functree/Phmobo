<?php
Class FunctreeUtil {
    /**
     * 保存缓存数据
     * @param mixed $data 需要缓存的数据
     * @param string $name 缓存数据标识名称，目前为funcList、permissionList
     * @param int $timeout 缓存过期时间，单位：秒
     * @return number|boolean 保存失败返回false，否则返回数据size
     */
    function save_cache($data, $name, $timeout) {
        // delete cache
        @$id=shmop_open($this->get_cache_id($name), "a", 0, 0);
        if ($id) {
            shmop_delete($id);
            shmop_close($id);
        }
        
        // get id for name of cache
        $id=shmop_open($this->get_cache_id($name), "c", 0644, strlen(serialize($data)));
        
        // return int for data size or boolean false for fail
        if ($id) {
            $this->set_timeout($name, $timeout);
            return shmop_write($id, serialize($data), 0);
        }
        else return false;
    }
    /**
     * 获取缓存数据
     * @param string $name 缓存数据标识名称，目前为funcList、permissionList
     * @return boolean|mixed 缓存数据不存在时返回false，否则返回数据内容
     */
    function get_cache($name) {
        if (!$this->check_timeout($name)) {
            @$id=shmop_open($this->get_cache_id($name), "a", 0, 0);
            
            if ($id) $data=unserialize(shmop_read($id, 0, shmop_size($id)));
            else return false;          // failed to load data
            
            if ($data) {                // array retrieved
                shmop_close($id);
                return $data;
            }
            else return false;          // failed to load data
        }
        else return false;              // data was expired
    }
    /**
     * 删除缓存数据
     * @param string $name 缓存数据标识名称，目前为funcList、permissionList
     * @return boolean|mixed 缓存数据不存在时返回false，否则返回数据内容
     */
    function delete_cache() {
        @$id=shmop_open($this->get_cache_id('funcList'), "a", 0, 0);
        if ($id) {
            shmop_delete($id);
            shmop_close($id);
        }
        @$id=shmop_open($this->get_cache_id('permissionList'), "a", 0, 0);
        if ($id) {
            shmop_delete($id);
            shmop_close($id);
        }
        @$id=shmop_open(100, "a", 0, 0);
        if ($id) {
            shmop_delete($id);
            shmop_close($id);
        }
    }
    //目前为funcList、permissionList
    private function get_cache_id($name) {
        // maintain list of caches here
        $id=array('funcList' => 1, 'permissionList' => 2);
        
        return $id[$name];
    }
    private function set_timeout($name, $int) {
        $timeout=new DateTime(date('Y-m-d H:i:s'));
        date_add($timeout, date_interval_create_from_date_string("$int seconds"));
        $timeout=date_format($timeout, 'YmdHis');
        
        @$id=shmop_open(100, "a", 0, 0);
        if ($id) $tl=unserialize(shmop_read($id, 0, shmop_size($id)));
        else $tl=array();
        if ($id) {
            shmop_delete($id);
            shmop_close($id);
        }
        
        $tl[$name]=$timeout;
        $id=shmop_open(100, "c", 0644, strlen(serialize($tl)));
        shmop_write($id, serialize($tl), 0);
    }
    private function check_timeout($name) {
        $now=new DateTime(date('Y-m-d H:i:s'));
        $now=date_format($now, 'YmdHis');
        
        @$id=shmop_open(100, "a", 0, 0);
        if ($id) $tl=unserialize(shmop_read($id, 0, shmop_size($id)));
        else return true;
        if ($id) {
            shmop_close($id);
        }
        if (isset($tl[$name])) {
            $timeout=$tl[$name];
        } else {
            $timeout = false;
        }
        return $timeout ? (intval($now)>intval($timeout)) : false;
    }
    /////////////////////////////////////////
    /**
     * 解析URI，获取Http访问的功件名称和功件方法名称
     * @param string $uri_string uri信息
     * @param string $visitFuncName 访问功件名称
     * @param string $visitMethodName 访问功件的方法名称
     * @return boolean 解析成功返回true
     */
    function parse_uri( $uri_string, &$visitFuncName, &$visitMethodName )
    {
        $visitFuncName = "";
        $visitMethodName = "";
        if ( $uri_string != "" AND $uri_string != "/" )
        {
            $uri_string = $this->valid_uri_str( $uri_string );
            $uri_string = str_replace( '/index.php', '', $uri_string );
            $uri_string = $this->remove_left_slash( $uri_string );
            $uri_string = $this->remove_right_slash( $uri_string );
            
            $array_tmp = explode( "/", $uri_string );
            if ( is_array($array_tmp) )
            {
                $count = count( $array_tmp );
                if ( $count == 1 ){
                    $visitFuncName = $array_tmp[0];
                }elseif ( $count == 2 ){
                    $visitFuncName = $array_tmp[0];
                    $visitMethodName = $array_tmp[1];
                }
            }
        }
        // Clean $_POST Data
        if (is_array($_POST) AND count($_POST) > 0)
        {
            foreach($_POST as $key => $val)
            {
                $_POST[$this->valid_input_key($key)] = $this->valid_input_data($val);
            }
        }
        
        return TRUE;
    }
    /**
     * 获取Http访问的URI信息
     * @param string $ignoreStr 需要忽略的字符串
     * @return string 返回Http访问的URI字符串
     */
    function get_uri_string( $ignoreStr )
    {
        $path = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : @getenv('REQUEST_URI');
        if ( $path != '' AND !strrpos($path, "/index.php") )
        {
            $path = str_replace( $ignoreStr, '', $path );
            if (strpos($path, '?')) {
                $path = substr( $path, 0, strpos($path, '?') );
            }
            return $path;
        }else{
            return '';
        }
    }
    /**
     * 显示提示信息
     * @param string $errmessage 需要显示的信息
     */
    function show_message( $errmessage )
    {
        echo "<div style='width:100%;text-align:center'>$errmessage</div>";
        exit;
    }
    /**
     * 清除字符串左侧的/
     * @param string $str 需要处理的字符串
     * @return string 返回处理完成后的字符串
     */
    function remove_left_slash( $str )
    {
        if ( substr($str,0,1) == "/" ){
            $str = substr( $str, 1 );
            $str = $this->remove_left_slash( $str );
        }
        
        return $str;
    }
    /**
     * 清除字符串右侧的/
     * @param string $str 需要处理的字符串
     * @return string 返回处理完成后的字符串
     */
    function remove_right_slash( $str )
    {
        if ( substr($str,-1) == "/" ){
            $str = substr( $str, 0, strlen($str)-1 );
            $str = $this->remove_right_slash( $str );
        }
        
        return $str;
    }
    private function valid_uri_str( $str )
    {
        $str = str_replace( " ", "", $str );
        if ( ! preg_match("/^[A-Za-z0-9_\/-]*$/", $str) )
        {
            $this->show_message( '包含非法字符，只允许“A-Za-z0-9_/-”等字符。' );
        }
        
        return $str;
    }
    private function valid_input_data( $str )
    {
        if (is_array( $str) )
        {
            $new_array = array();
            foreach ( $str as $key => $val )
            {
                $new_array[$this->valid_input_key($key)] = $this->valid_input_data($val);
            }
            return $new_array;
        }
        
        if ( !get_magic_quotes_gpc() )
        {
            $str = addslashes( $str );
        }
        
        return $str;
    }
    private function valid_input_key( $str )
    {
        if ( ! preg_match("/^[a-z0-9:_\/-]+$/i", $str) )
        {
            $this->show_message( '包含非法字符，只允许“a-z0-9:_/-”字符。' );
        }
        
        return $str;
    }
}

?>