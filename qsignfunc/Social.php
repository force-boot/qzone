<?php

namespace qsignfunc;

/**
 * Qsign qq附近签到
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Social extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $url = 'https://play.mobile.qq.com/pansocial/cgi/checkin/checkInAction?actionType=0&token=' . $this->getGTK3($this->skey) . '&packageId=0&_=' . time() . '2588&callback=';
        $data = $this->get_curl($url, 0, 'https://play.mobile.qq.com/play/mqqplay/keepsign/index.html', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('errCode', $arr) && $arr['errCode'] == 0) {
            $msg[] = 'QQ附近签到成功！魅力值+' . $arr['uint32_added_charm'];
        } elseif ($arr['errCode'] == 1) {
            $msg[] = 'QQ附近签到今日已签';
        } elseif ($arr['errCode'] == 100000) {
            $this->setStatus();
            $msg[] = 'QQ附近签到失败！SKEY过期';
        } else {
            $msg[] = 'QQ附近签到失败！' . $data;
        }
        $this->setMessage($msg);
    }
}