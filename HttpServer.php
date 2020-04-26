<?php

/**
 * Class HttpServer
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class HttpServer
{
    /**
     * @var object swoole http实例
     */
    private static $http;

    /**
     * @var int 监听端口
     */
    private static $port = 7100;

    /**
     * @var string 0.0.0.0允许所有人
     */
    private static $host = "0.0.0.0";

    /**
     * 启动服务
     * @access
     */
    public static function run()
    {
        //实例化swoole http服务器
        self::$http = new Swoole\Http\Server(self::$host, self::$port);
        //监听请求
        self::onRequest();
        //启动
        self::$http->start();
    }

    /**
     * 监听请求
     * @static
     * @access private
     */
    private static function onRequest()
    {
        self::$http->on('request', function ($request, $response) {
            spl_autoload_register(function ($className) {
                $filename = APP_PATH . '/' . str_replace('\\', DS, $className) . '.php';
                if (file_exists($filename)) {
                    include $filename;
                }
            });
            $uri = explode('/', trim($request->server['request_uri'], '/'));
            $type = isset($uri[0]) ? $uri[0] : false;
            $func = isset($uri[1]) ? $uri[1] : false;
            $runkey = $request->get['runkey'];
            if ($runkey != RUN_KEY) {
                $response->status(404);
                $response->end('404 Not Found');
            }
            if (false != $type && in_array($type, ['qzone', 'qsign'])) {
                $instance = self::$type($request->get);
                if ($type == 'qzone' && method_exists($instance, $func)) {
                    $run_message = $instance->$func();
                } else {
                    $run_message = $instance->run($func);
                }
                $response->header("Content-Type", "application/json; charset=utf-8");
                $response->end(json_encode($run_message));
            } else {
                $response->status(404);
                $response->end('404 Not Found');
            }
        });
    }

    /**
     * 执行qzone任务
     * @static
     * @access private
     * @param $param array
     * @return QzoneTask
     */
    private static function qzone($param = [])
    {
        return QzoneTask::init($param);
    }

    /**
     * 执行qsign任务
     * @static
     * @access private
     * @param $param array
     * @return QsignTask
     */
    private static function qsign($param = [])
    {
        return QsignTask::init($param);
    }
}
