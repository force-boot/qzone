<?php

use \factory\Factory;

/**
 * Class QsignTask
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class QsignTask
{
    /**
     * @var object 存储实例
     */
    private static $task;

    /**
     * @var array 当前请求参数
     */
    private static $param = [];

    /**
     * QzoneTask constructor.
     */
    private function __construct()
    {

    }

    /**
     * 初始化
     * @param array $param
     * @static
     * @access public
     * @return QsignTask
     */
    public static function init($param = [])
    {
        self::$param = $param; //保存当前请求参数
        if (!self::$task instanceof self) {
            self::$task = new self();
        }
        return self::$task;
    }

    /**
     * 工厂方法
     * @param $name
     * @access private
     * @return object
     */
    private function factory($name)
    {
        return Factory::instance("\\qsignfunc\\" . ucwords($name), self::$param);
    }

    /**
     * 获取请求参数，留空获取全部
     * @param $name string $name 参数名
     * @param $default string 不存在参数，默认值
     * getParam('method',1); //如果method参数不存在就变成默认值 1
     * @access public
     * @return array|mixed
     */
    private function getParam($name = '', $default = '')
    {
        if (!empty($name)) {
            return isset(self::$param[$name]) ? self::$param[$name] : $default;
        } else {
            return self::$param;
        }
    }

    /**
     * 运行签到任务
     * @param $func
     * @access public
     * @return mixed
     */
    public function run($func)
    {
        $instance = $this->factory($func);
        $superkey = $this->getParam('superkey', true);
        if ($superkey) {
            $instance->run();
        } else {
            $instance->run($superkey);
        }
        return $instance->getMessage();
    }
}