<?php

namespace qsignfunc;

/**
 * Qsign qq运动功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Yundong extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $steps = rand(11111, 99999);
        $timestamp = time();
        $params = '{"reqtype":11,"mbtodayStep":' . $steps . ',"todayStep":' . $steps . ',"timestamp":' . $timestamp . '}';
        $url = 'https://yundong.qq.com/cgi/common_daka_tcp?g_tk=' . $this->gtk;
        $post = 'params=' . urlencode($params) . '&l5apiKey=daka.server&dcapiKey=daka_tcp';
        $refer = 'https://yundong.qq.com/daka/index?_wv=2098179&rank=1&steps=' . $steps . '&asyncMode=1&type=&mid=105&timestamp=' . $timestamp;
        $data = $this->get_curl($url, $post, $refer, $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = 'QQ运动打卡成功！QQ成长值+0.2天';
        } elseif ($arr['code'] == -10001) {
            $msg[] = '今天步数未达到打卡门槛，再接再厉！';
        } elseif ($arr['code'] == -10003) {
            $msg[] = '今天已经打过卡了，明天再来吧~';
        } elseif ($arr['code'] == -1001) {
            $this->setStatus();
            $msg[] = 'QQ运动打卡失败！原因：SKEY已失效';
        } else {
            $msg[] = 'QQ运动打卡失败！原因：' . ($arr['emsg'] ? $arr['emsg'] : $arr['msg']);
        }
        $this->setMessage($msg);
    }
}