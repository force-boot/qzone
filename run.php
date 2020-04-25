<?php
define('DS', DIRECTORY_SEPARATOR);//动态目录分隔符

define('APP_PATH', getcwd() . DS); //应用根目录

define('RUN_KEY', 123456);//运行密钥

//引入辅助函数
include APP_PATH . 'tools' . DS . 'function.php';

use database\Db;

/**
 * 失效通知，qq失效会回调该函数
 * @param $uin int qq号码
 */
function notice($uin)
{
    if (!empty($uin)) {
        $sql = "SELECT * FROM mz_qqs AS q JOIN mz_users AS u ON q.uid=u.uid WHERE q.qq = ?";
        $info = Db::instance()->sql($sql, [$uin])->fetch();
        if ($info) {
            Db::instance()->sql("UPDATE `mz_qqs` SET `cookiezt` =1 WHERE `qid`= ? ", [$info['qid']])->rowCount();
            Db::instance()->sql("UPDATE `mz_qqjob` SET `cookiezt`=1 WHERE `qid`= ? ", [$info['qid']])->rowCount();
            //邮件通知等
        }
    }
}

include APP_PATH . 'HttpServer.php';

//运行http服务器
HttpServer::run();
