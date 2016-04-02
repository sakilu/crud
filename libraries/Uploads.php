<?php

class UploadConfig
{
    public static $databaseHost = 'localhost';
    public static $databaseName = 'uploadclass';
    public static $databaseUsername = 'root';
    public static $databasePassword = '';
    public static $relativefolderPath = '../../uploads/files';    //relative path, related to upload.php

    /*************** DO NOT CHANGE ANYTHING BELOW ***************/
    public static $baseDir;
    public static $baseUrl;

    public static function findPath()
    {
        self::$baseDir = dirname(__FILE__);
        self::$baseUrl = self::selfURL();
    }

    public static function selfURL()
    {
        $host = $_SERVER['HTTP_HOST'];
        $self = $_SERVER['PHP_SELF'];
        $url = "http://$host" . dirname($self);
        return $url;
    }

}

class UploadDatabase
{

    private $db_host = null;
    private $db_user = null;
    private $db_pass = null;
    private $db_name = null;

    private $con = false;               // Checks to see if the connection is active
    private $result = array();          // Results that are returned from the query

    public function __construct()
    {
        $this->db_host = UploadConfig::$databaseHost;
        $this->db_user = UploadConfig::$databaseUsername;
        $this->db_pass = UploadConfig::$databasePassword;
        $this->db_name = UploadConfig::$databaseName;
    }

    /*
     * Connects to the database, only one connection
     * allowed
     */
    public function connect()
    {
        if (!$this->con) {
            $myconn = @mysql_connect($this->db_host, $this->db_user, $this->db_pass);
            if ($myconn) {
                $seldb = @mysql_select_db($this->db_name, $myconn);
                if ($seldb) {
                    $this->con = true;
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /*
     * Changes the new database, sets all current results
     * to null
     */
    public function setDatabase($name)
    {
        if ($this->con) {
            if (@mysql_close()) {
                $this->con = false;
                $this->results = null;
                $this->db_name = $name;
                $this->connect();
            }
        }

    }

    /*
     * Checks to see if the table exists when performing
     * queries
     */
    private function tableExists($table)
    {
        $tablesInDb = @mysql_query('SHOW TABLES FROM ' . $this->db_name . ' LIKE "' . $table . '"');
        if ($tablesInDb) {
            if (mysql_num_rows($tablesInDb) == 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    /*
     * Selects information from the database.
     * Required: table (the name of the table)
     * Optional: rows (the columns requested, separated by commas)
     *           where (column = value as a string)
     *           order (column DIRECTION as a string)
     */
    public function select($table, $rows = '*', $where = null, $order = null)
    {
        $q = 'SELECT ' . $rows . ' FROM ' . $table;
        if ($where != null) $q .= ' WHERE ' . $where;
        if ($order != null) $q .= ' ORDER BY ' . $order;

        $query = @mysql_query($q);
        if ($query) {
            $this->numResults = mysql_num_rows($query);
            for ($i = 0; $i < $this->numResults; $i++) {
                $r = mysql_fetch_array($query);
                $key = array_keys($r);
                for ($x = 0; $x < count($key); $x++) {
                    // Sanitizes keys so only alphavalues are allowed
                    if (!is_int($key[$x])) {
                        if (mysql_num_rows($query) > 1) $this->result[$i][$key[$x]] =
                            $r[$key[$x]]; else if (mysql_num_rows($query) < 1) $this->result = null; else
                            $this->result[0][$key[$x]] = $r[$key[$x]];
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /*
     * Insert values into the table
     * Required: table (the name of the table)
     *           values (the values to be inserted)
     * Optional: rows (if values don't match the number of rows)
     */
    public function insert($table, $values, $rows = null)
    {
        if ($this->tableExists($table)) {
            $insert = 'INSERT INTO ' . $table;
            if ($rows != null) {
                $insert .= ' (' . $rows . ')';
            }

            for ($i = 0; $i < count($values); $i++) {
                if (is_string($values[$i])) $values[$i] = '"' . $values[$i] . '"';
            }
            $values = implode(',', $values);
            $insert .= ' VALUES (' . $values . ')';

            $ins = @mysql_query($insert);

            if ($ins) {
                return true;
            } else {
                return false;
            }
        }
    }

    /*
     * Deletes table or records where condition is true
     * Required: table (the name of the table)
     * Optional: where (condition [column =  value])
     */
    public function delete($table, $where = null)
    {
        if ($this->tableExists($table)) {
            if ($where == null) {
                $delete = 'DELETE ' . $table;
            } else {
                $delete = 'DELETE FROM ' . $table . ' WHERE ' . $where;
            }
            $del = @mysql_query($delete);

            if ($del) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * Updates the database with the values sent
     * Required: table (the name of the table to be updated
     *           rows (the rows/values in a key/value array
     *           where (the row/condition in an array (row,condition) )
     */
    public function update($table, $rows, $where)
    {
        if ($this->tableExists($table)) {
            // Parse the where values
            // even values (including 0) contain the where rows
            // odd values contain the clauses for the row
            for ($i = 0; $i < count($where); $i++) {
                if ($i % 2 != 0) {
                    if (is_string($where[$i])) {
                        if (($i + 1) != null) $where[$i] = '"' . $where[$i] . '" AND '; else
                            $where[$i] = '"' . $where[$i] . '"';
                    }
                }
            }
            $where = implode('', $where);


            $update = 'UPDATE ' . $table . ' SET ';
            $keys = array_keys($rows);
            for ($i = 0; $i < count($rows); $i++) {
                if (is_string($rows[$keys[$i]])) {
                    $update .= $keys[$i] . '="' . $rows[$keys[$i]] . '"';
                } else {
                    $update .= $keys[$i] . '=' . $rows[$keys[$i]];
                }

                // Parse to add commas
                if ($i != count($rows) - 1) {
                    $update .= ',';
                }
            }
            $update .= ' WHERE ' . $where;
            $query = @mysql_query($update);

            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * Returns the result set
     */
    public function getResult()
    {
        return $this->result;
    }
}

class Uploads
{

    private $_maxSize;
    public $_allowExt;
    public $_msg;
    private $_path;

    private $_database;

    /**
     * Constructor
     */
    public function __construct()
    {
        //set variables
        $this->_maxSize = -1;        //in kb
        $this->_allowExt = array();
        $this->_msg = array();
        @mkdir(UploadConfig::$baseDir . '/' . UploadConfig::$relativefolderPath, 0777, true);
        $this->_path = realpath(UploadConfig::$baseDir . '/' . UploadConfig::$relativefolderPath) . '/';

        $ci = &get_instance();
        UploadConfig::$databaseHost = $ci->db->hostname;
        UploadConfig::$databaseName = $ci->db->database;
        UploadConfig::$databaseUsername = $ci->db->username;
        UploadConfig::$databasePassword = $ci->db->password;

    }

    /********************* PRIVATE FUNCTIONS ********************/
    /*
     * insert data into database
     * @param array of data
     */
    public function _insert($array)
    {
        $this->_database = new UploadDatabase();
        $this->_database->connect();
        $values = array_values($array);
        //format fields string
        $fields = implode(',', array_keys($array));
        return $this->_database->insert('files', $values, $fields);
    }

    /*
     * update data
     * @param array of data
     */
    public function _update($array, $id)
    {
        $this->_database = new UploadDatabase();
        $this->_database->connect();
        return $this->_database->update('files', $array, array(
            'id=',
            intval($id)
        ));
    }

    /*
     * read a row from database
     */
    private function _read($id)
    {
        $this->_database = new UploadDatabase();
        $this->_database->connect();
        if ($this->_database->select('files', '*', 'id=' . $id)) {
            return $this->_database->getResult();
        }
        return null;
    }

    /*
     * read all from database
     */
    private function _readAll()
    {
        $this->_database = new UploadDatabase();
        $this->_database->connect();
        if ($this->_database->select('files', '*', null, ' id DESC')) {
            return $this->_database->getResult();
        }
        return array();
    }

    /*
     * delete a row from database
     */
    private function _delete($id)
    {
        $this->_database = new UploadDatabase();
        $this->_database->connect();
        return ($this->_database->delete('files', 'id=' . $id));
    }

    /*
     * delete all from database
     */
    private function _deleteAll()
    {
        $this->_database = new UploadDatabase();
        $this->_database->connect();
        return ($this->_database->delete('files', 'id>0'));
    }

    /*
     * create dir if it does not exsit
     */
    private function _createDir($path)
    {
        if (!is_dir($path)) {
            return mkdir($path, 0777);
        } else {
            chmod($path, 0777);
            return true;
        }
    }

    /*
     * set msg
     * @param string or array of string
     * @return void
     */
    private function _setMsg($msg)
    {
        if (!is_array($msg)) {
            $msg = array($msg);
        }
        $this->_msg = array_merge($this->_msg, $msg);
    }

    /*
     * remove folder
     */
    private function _removeFolder($path)
    {
        foreach (glob($path . '/*') as $file) {
            //echo $file;
            if (!unlink($file)) {
                $this->_setMsg('unable to unlink file ' . $file);
                return false;
            };
        }
        if (!rmdir(($path))) {
            $this->_setMsg('unable to remove folder' . dirname($path));
            return false;
        }
        return true;
    }
    /********************* PUBLIC FUNCTIONS ********************/

    /*
     * call this functions to verify if it is working properly
     */
    public function debug()
    {
        $msg = array();
        //check database connection
        $this->_database = new UploadDatabase();
        if (!$this->_database->connect()) {
            $msg[] = 'Error: Unable to connect to database';
        }

        //check safe mode
        if (ini_get('safe_mode')) {
            $msg[] =
                'Error: PHP is running on safe_mode, you need to at least disable safe_mode for dir ' . $this->_path;
        }
        //check permission of the destination folder
        if (!is_writable($this->_path)) {
            $msg[] = 'Error:  ' . $this->_path . ' is not writable by PHP, please set permission properly';
        }
        //check post_max_size larger than upload_max_filesize
        if (intval(ini_get('post_max_size')) < intval(ini_get('upload_max_filesize'))) {
            $msg[] = 'Error:  ' . 'post_max_size should be larger than upload_max_filesize, please reset it.';
        }
        //check open_basedir
        $msg[] = 'Info: open_basedir is ' . ini_get('open_basedir');
        //output post_max_size
        $msg[] = 'Info: post_max_size is ' . ini_get('post_max_size');
        //output upload_max_filesize
        $msg[] = 'Info: upload_max_filesize is ' . ini_get('upload_max_filesize');
        //output max_execution_time
        $msg[] = 'Info: max_execution_time is ' . ini_get('max_execution_time') . ' seconds';
        foreach ($msg as $ms) {
            echo '<pre>';
            echo $ms . '<br/>';
            echo '</pre>';
        }
    }

    /*
     * set allowed file types
     * @param array of file types
     * @return void
     */
    public function setAllowExt($type, $append = true)
    {
        if (!is_array($type)) {
            $type = array($type);
        }
        //append types
        if (true === $append) {
            $this->_allowExt = array_merge($this->_allowExt, $type);
            //eliminate duplicate values
            $this->_allowExt = array_map("strtolower", array_values(array_unique($this->_allowExt)));
            //reset types
        } else {
            $this->_allowExt = strtolower($type);
        }
    }

    /*
     * set maximium file size
     * @param int file size in (mb)
     * @return void
     */
    public function setMaxSize($size)
    {
        $this->_maxSize = $size;
    }

    /*
     * upload a file.
     * if it returns false, call getMsg() to get the error messages
     * @param _FILE_
     * @return file id
     */
    public function upload($file)
    {
        //check file exist
        $fileSize = ((isset($file['size'])) ? $file['size'] : 0);
        if (0 === $fileSize) {
            $this->_setMsg('no file is detected');
            return false;
        }
        //check file size
        if ((-1 !== $this->_maxSize) && ($fileSize > ($this->_maxSize * 1024))) {
            $this->_setMsg('file size has exceed the limist of ' . $this->_maxSize);
            return false;
        }
        //check allow ext
        $fileName = $file['name'];
        $fileExt = (pathinfo($fileName, PATHINFO_EXTENSION));
        $fileName = uniqid() . '.' . $fileExt;
        if (sizeof($this->_allowExt) > 0 && (!in_array(strtolower($fileExt), $this->_allowExt))) {
            $this->_setMsg('副檔名錯誤, 僅支援 ' . implode(',', $this->_allowExt));
            return false;
        }
        //check destination folder permission
        if (!is_writable(dirname($this->_path))) {
            $this->_setMsg($this->_path . ' is not writable');
            return false;
        }

        //insert into database
        if ($this->_insert(array(
            'name' => $fileName,
            'size' => $fileSize,
            'ext' => $fileExt
        ))
        ) {
            //current insert value
            $current = $this->_readAll();
            $current = $current[0];
            //dir for storing the file
            $dir = $this->_path . '/' . $current['id'];
            $path = '/uploads/files/' . $current['id'] . '/' . $fileName;
            //create dir
            if ($this->_createDir($dir)) {
                //upload to dir
                if (move_uploaded_file($file['tmp_name'], $dir . '/' . $fileName)) {
                    //update database
                    if ($this->_update(array('path' => $path), ($current['id']))) {
                        return $current['id'];
                    } else {
                        $this->_setMsg('unable to update database');
                        return false;
                    }
                }
            } else {
                $this->_setMsg('unable to create folder for storing file');
                return false;
            }
        } else {
            $this->_setMsg('insert into database failed');
            return false;
        }

    }

    /*
     * read a file
     * @param file id
     * @return array with file content
     */
    public function read($id)
    {
        return $this->_read($id);
    }

    /*
     * read all files
     * @return array with file content
     */
    public function readAll()
    {
        return $this->_readAll();
    }

    /*
     * delete a file
     * if it returns false, call getMsg() to get the error messages
     * @param file id
     * @return bool
     */
    public function delete($id)
    {
        $file = $this->_read($id);
        $file = isset($file[0]) ? $file[0] : null;
        //check file db record exsit
        if (null == $file || '' == $file['path']) {
            $this->_setMsg('file does not exsit');
            return false;
        }
        //check file exsit
        if (!file_exists(FCPATH . '/' . $file['path'])) {
            $this->_setMsg('file not found');
            return false;
        };
        //check file writable
        if (!is_writable(FCPATH . '/' . $file['path'])) {
            $this->_setMsg($file['name'] . ' is not writable');
            return false;
        };
        //check folder writable
        if (!is_writable(dirname(FCPATH . '/' . $file['path']))) {
            $this->_setMsg($file['name'] . ' container folder is not writable');
            return false;
        };
        //remove folder
        if ($this->_removeFolder(dirname(FCPATH . '/' . $file['path']))) {
            if ($this->_delete($id)) {
                return true;
            } else {
                $this->_setMsg('unable to delete in database');
                return false;
            }
        }
    }

    /*
     * delete all files
     * @return bool
     */
    public function deleteAll()
    {
        //check folder writable
        if (!is_writable((UploadConfig::$baseDir . '/' . UploadConfig::$relativefolderPath))) {
            $this->_setMsg(UploadConfig::$baseDir . '/' . UploadConfig::$relativefolderPath .
                ' folder is not writable');
            return false;
        };

        //remove all files
        $allFiles = $this->readAll();
        $result = true;
        foreach ($allFiles as $file) {
            $result = ($this->delete($file['id'])) && $result;
        }
        return $result;
    }

    /*
     * get error messages
     * $return array of error message
     */
    public function getMsg()
    {
        return implode('<br >', $this->_msg);
    }
}

//set path
UploadConfig::findPath();