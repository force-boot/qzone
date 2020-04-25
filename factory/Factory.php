<?php

namespace factory;

/**
 * Qzone工厂类
 * @package app\admin\controller
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Factory
{
    /**
     * @var object 对象池
     */
    private static $_instance = [];

    /**
     * 获取操作实例
     * @param $name
     * @param $param array
     * @return object
     */
    public static function instance($name, $param = [])
    {
        $qq = isset($param['qq']) ? $param['qq'] : false;
        $skey = isset($param['skey']) ? $param['skey'] : false;
        $pskey = isset($param['pskey']) ? $param['pskey'] : false;
        if (!$qq || !$skey || !$pskey) {
            return false;
        }
        unset($param['qq'], $param['skey'], $param['pskey']);
        $uid = md5(serialize($name . $qq . $skey . $pskey));
        if (!isset(self::$_instance[$uid])) {
            self::$_instance[$uid] = new $name($qq, $skey, $pskey);
        }
        return self::$_instance[$uid];
    }
}