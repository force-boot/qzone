<?php

namespace qsignfunc;

/**
 * Qsign qq打卡功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Checkin extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $url = 'https://ti.qq.com/hybrid-h5/api/json/daily_attendance/SignIn';
        $post = json_encode(array('uin' => $this->uin, 'type' => 1, 'sld' => ''));
        $addheader = array("Content-Type: application/json; charset=utf-8");
        $data = $this->get_curl($url, $post, 'https://ti.qq.com/signin/public/indexv2.html?_wv=1090532257&_wwv=13', $this->cookie, 0, 0, 0, $addheader);
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            if ($arr['data'] && $arr['data']['retCode'] == 0) {
                $msg[] = 'QQ每日打卡成功！已打卡' . $arr['data']['totalDays'] . '天';
            } elseif ($arr['data']['retCode'] == 1) {
                $msg[] = 'QQ每日打卡今日已完成！';
            } else {
                $msg[] = 'QQ每日打卡状态未知';
            }
        } elseif ($arr['ret'] == -200) {
            $msg[] = 'QQ打卡失败，您可能未获得测试资格';
        } elseif ($arr['ret'] == -3000) {
            $this->setStatus();
            $msg[] = 'QQ打卡失败，SKEY已失效';
        } else {
            $msg[] = 'QQ打卡失败！' . $arr['msg'];
        }
        $msg[] = '----------';
        $this->setMessage($msg);
    }
}