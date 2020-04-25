<?php

namespace database;

use Exception;

use PDO;

class Db
{
    /**
     * @var object Db实例
     */
    private static $_instance = null;

    /**
     * @var PDO pdo对象
     */
    private static $pdo = null;

    /**
     * @var array 数据库配置信息
     */
    private static $config = [
        'hostname' => '127.0.0.1',
        'hostport' => '3306',
        'database' => 'php7',
        'username' => 'root',
        'password' => 'root',
    ];

    /**
     * Db constructor.
     */
    private function __construct()
    {
        $config = __DIR__ . DS . 'config.php';
        if (file_exists($config)) {
            self::$config = include $config;
        }
    }

    /**
     * 获取Db实例
     * @return Db
     */
    public static function instance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 连接方法，创建PDO实例
     * @static
     * @access private
     */
    private function connect()
    {
        $dsn = "mysql:host=" . self::$config['hostname'] . ";dbname=" . self::$config['database'] . ";port=" . self::$config['hostport'];
        try {
            self::$pdo = new PDO($dsn, self::$config['username'], self::$config['password']);
        } catch (Exception $e) {
            exit('PDO Connection Error:' . $e->getMessage());
        }
        self::$pdo->exec("set names utf8");
    }

    /**
     * PDO预处理查询和绑定
     * @param $sql string SQL语句
     * @param $bind array 绑定参数
     * @access public
     * @return \PDOStatement 结果集对象
     */
    public function sql($sql, $bind = [])
    {
        if (is_null(self::$pdo)) {//判断是否存在PDO对象
            $this->connect();
        }
        //无参数时，使用普通查询
        if (empty($bind)) {
            return self::$pdo->query($sql);
        }
        //提交预处理
        $pdoStmt = self::$pdo->prepare($sql);
        //绑定预处理参数
        $pdoStmt->execute($bind);
        return $pdoStmt;
    }

    /**
     * 获取当前数据库配置
     * @access public
     * @return array|mixed
     */
    public function getConfig()
    {
        return self::$config;
    }

    /**
     * 析构方法，关闭PDO连接
     */
    public function __destruct()
    {
        self::$pdo = null;
    }
}