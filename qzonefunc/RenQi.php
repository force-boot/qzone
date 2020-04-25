<?php

namespace qzonefunc;

/**
 * Qzone刷人气功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class RenQi extends Qzone
{
    /**
     * 运行功能
     * @param $uin
     * @access public
     * @return bool|int
     */
    public function run($uin)
    {
        $url = 'http://h5.qzone.qq.com/proxy/domain/r.qzone.qq.com/cgi-bin/qzone_dynamic_v7.cgi?uin=' . $uin . '&param=848';
        $body = $this->get_curl($url);
        $body = str_replace(array('_Callback(', ')'), array('', ''), $body);
        if ($ret = json_decode($body, true)) {
            if (@array_key_exists('code', $ret) && $ret['code'] == 0) {
                $count = $ret['data']['app_848']['data']['modvisitcount'][0]['todaycount'];
                return $count;
            }
        }
        return false;
    }
}