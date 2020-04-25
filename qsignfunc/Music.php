<?php

namespace qsignfunc;

/**
 * Qsign qq音乐功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Music extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $addheader = array("Content-Type: application/json");
        $url = 'https://u.y.qq.com/cgi-bin/musicu.fcg';
        $post = '{"req_0":{"module":"UserGrow.UserGrowScore","method":"receive_score","param":{"musicid":"' . $this->uin . '","type":15}},"comm":{"g_tk":' . $this->gtk . ',"uin":' . $this->uin . ',"format":"json","ct":23,"cv":0}}';
        $data = $this->get_curl($url, $post, $url, $this->cookie, 0, 0, 0, $addheader);
        $arr = json_decode($data, true);
        $arr = $arr['req_0']['data'];
        if (array_key_exists('retCode', $arr) && $arr['retCode'] == 0) {
            $msg[] = $this->uin . ' QQ音乐签到成功！获得积分:' . $arr['todayScore'] . ',签到天数:' . $arr['totalDays'] . ',总积分:' . $arr['totalScore'];
        } elseif ($arr['retCode'] == 40001) {
            $msg[] = $this->uin . ' QQ音乐今日已签到！';
        } elseif ($arr['ret'] == -13004) {
            $this->setStatus();
            $msg[] = $this->uin . ' QQ音乐签到失败！SKEY已失效';
        } else {
            $msg[] = $this->uin . ' QQ音乐签到失败！' . $arr['errMsg'];
        }

        $post = '{"req_0":{"module":"UserGrow.UserGrowScore","method":"receive_score","param":{"musicid":"' . $this->uin . '","type":1}},"comm":{"g_tk":' . $this->gtk . ',"uin":' . $this->uin . ',"format":"json","ct":23,"cv":0}}';
        $data = $this->get_curl($url, $post, $url, $this->cookie, 0, 0, 0, $addheader);
        $arr = json_decode($data, true);
        $arr = $arr['req_0']['data'];
        if (array_key_exists('retCode', $arr) && $arr['retCode'] == 0) {
            $msg[] = $this->uin . ' QQ音乐分享成功！获得积分:' . $arr['todayScore'] . ',签到天数:' . $arr['totalDays'] . ',总积分:' . $arr['totalScore'];
        } elseif ($arr['retCode'] == 40001) {
            $msg[] = $this->uin . ' QQ音乐今日已分享！';
        } elseif ($arr['retCode'] == 40002) {
            $msg[] = $this->uin . ' QQ音乐今日分享未完成！';
        } elseif ($arr['ret'] == -13004) {
            $this->setStatus();
            $msg[] = $this->uin . ' QQ音乐分享失败！SKEY已失效';
        } else {
            $msg[] = $this->uin . ' QQ音乐分享失败！' . $arr['errMsg'];
        }

        $post = '{"req_0":{"module":"Radio.RadioLucky","method":"clockIn","param":{"platform":2}},"comm":{"g_tk":' . $this->gtk . ',"uin":' . $this->uin . ',"format":"json"}}';
        $data = $this->get_curl($url, $post, $url, $this->cookie, 0, 0, 0, $addheader);
        $arr = json_decode($data, true);
        $arr = $arr['req_0']['data'];
        if (array_key_exists('retCode', $arr) && $arr['retCode'] == 0) {
            $msg[] = $this->uin . ' QQ音乐电台锦鲤打卡成功！积分+' . $arr['score'];
        } elseif ($arr['retCode'] == 40001) {
            $msg[] = $this->uin . ' QQ音乐电台锦鲤已打卡！';
        } elseif ($arr['ret'] == -13004) {
            $this->setStatus();
            $msg[] = $this->uin . ' QQ音乐电台锦鲤打卡失败！SKEY已失效';
        } else {
            $msg[] = $this->uin . ' QQ音乐电台锦鲤打卡失败！' . $arr['errMsg'];
        }
        $this->setMessage($msg);
    }
}