<?php

namespace qsignfunc;

/**
 * Qsign 手游功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Game extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $url = 'http://reader.sh.vip.qq.com/cgi-bin/common_async_cgi?g_tk=' . $this->gtk . '&plat=1&version=6.6.6&param=%7B%22key0%22%3A%7B%22param%22%3A%7B%22bid%22%3A13792605%7D%2C%22module%22%3A%22reader_comment_read_svr%22%2C%22method%22%3A%22GetReadAllEndPageMsg%22%7D%7D';
        $data = $this->get_curl($url, 0, $url, $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ecode', $arr) && $arr['ecode'] == 0) {
            $msg[] = 'QQ手游加速0.2天成功！';
        } else {
            $msg[] = 'QQ手游加速失败！' . $data;
        }
        $this->setMessage($msg);
    }
}