<?php

use \factory\Factory;

/**
 * Class QzoneTask
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class QzoneTask
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
     * @return QzoneTask
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
        return Factory::instance("\\qzonefunc\\" . ucwords($name), self::$param);
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
     * 执行秒赞
     * @access public
     */
    public function zan()
    {
        //获取秒赞功能实例
        $instance = $this->factory('like');
        //获取所需参数
        $method = $this->getParam('method', 1);
        $forbid = explode('.', $this->getParam('forbid', null));
        $sleep = $this->getParam('selep', 0);
        //执行任务
        $instance->run($method, $forbid, $sleep);
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 执行秒评
     * @access public
     */
    public function reply()
    {
        //获取秒评功能实例
        $instance = $this->factory('reply');
        //获取所需参数
        $method = $this->getParam('method', 1);
        $sleep = $this->getParam('selep', 0);
        $forbid = explode('.', $this->getParam('forbid', null));
        $content = $this->getParam('content', getReplyCentent());
        $content = explode('|', $content);
        $img = $this->parseImg();
        //执行任务
        $instance->run($method, $content, $forbid, '', $sleep, $img);
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 执行发说说
     * @access public
     */
    public function shuo()
    {
        //获取说说功能实例
        $instance = $this->factory('shuo');
        $mode = $this->getParam('mode', null);
        $content = getShuoContent($mode, $this->getParam('content', null));
        $img = $this->parseImg();
        $method = $this->getParam('method', 1);
        $delete = $this->getParam('delete', null);
        //执行任务
        $instance->send($method, $content, $img, '', $delete);
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 签到发说说
     * @access public
     */
    public function qdShuo()
    {
        //获取说说功能实例
        $instance = $this->factory('shuo');
        $content = $this->getParam('content', null);
        $img = $this->parseImg();
        $method = $this->getParam('method', 1);
        $delete = $this->getParam('delete', null);
        //执行任务
        $instance->send($method, $content, $img, '', $delete);
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 执行删除说说
     * @access public
     */
    public function del()
    {
        //获取说说功能实例
        $instance = $this->factory('shuo');
        $method = $this->getParam('method', 1);
        //执行任务
        $instance->delete($method);
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 执行删除留言
     * @access public
     * @return mixed
     */
    public function delly()
    {
        //获取留言功能实例
        $instance = $this->factory('StayMsg');
        $method = $this->getParam('method', 1);
        //执行任务
        $instance->delete($method);
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 执行转发说说
     * @access public
     */
    public function zf()
    {
        //获取说说转发功能实例
        $instance = $this->factory('Forward');
        $method = $this->getParam('method', 1);
        $uins = $this->getParam('uin',null);
        $content = $this->getParam('content', null);
        //执行任务
        $instance->run($method, $uins, $content);
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 执行空间签到
     * @access public
     * @return mixed
     */
    public function qd()
    {
        //获取空间签到功能实例
        $instance = $this->factory('Sign');
        $method = $this->getParam('method', 2);
        $content = $this->getParam('content', null);
        //执行任务
        $instance->run($method, $content);
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 花藤功能
     * @access public
     */
    public function ht()
    {
        //获取花藤功能实例
        $instance = $this->factory('Flower');
        //执行任务
        $instance->run();
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 情侣空间签到
     * @access public
     * @return mixed
     */
    public function ql()
    {
        //获取情侣功能实例
        $instance = $this->factory('sweet');
        //执行任务
        $instance->run();
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 执行点名片赞
     * @access public
     */
    public function mpz()
    {
        //获取名片赞功能实例
        $instance = $this->factory('CardLike');
        $uin = $this->getParam('uin', null);
        //执行任务
        $instance->run($uin);
        //返回执行信息
        return $instance->getMessage();
    }

    /**
     * 获取qzonetoken
     * @access public
     */
    public function getToken()
    {
        //获取实例
        $instance = $this->factory('Token');
        $url = $this->getParam('url', 'https://h5.qzone.qq.com/mqzone/index');
        $pc = $this->getParam('pc', false);
        return $instance->run($url, $pc);
    }

    /**
     * 解析图片
     * @access private
     * @return string
     */
    private function parseImg()
    {
        $imgId = $this->getParam('tuid', null);
        if ($imgId == 1) {
            $img = $this->getParam('img');
        } elseif ($imgId == 2) {
            $img = randimg();
        } else {
            $img = null;
        }
        return $img;
    }


    /**
     * 禁止clone
     * @access private
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 析构方法
     */
    public function __destruct()
    {
        //清理请求参数
        self::$param = null;
    }
}

