<?php

namespace qsignfunc;


/**
 * Qsign 腾讯视频签到类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Video extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $url = 'https://vip.video.qq.com/fcgi-bin/comm_cgi?name=hierarchical_task_system&cmd=2&_=' . time() . '8906';
        $data = $this->get_curl($url, 0, $url, $this->cookie);
        preg_match('/QZOutputJson=\((.*?)\)/is', $data, $json);
        $arr = json_decode($json[1], true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = $this->uin . ' 腾讯视频VIP会员签到成功！获得' . $arr['checkin_score'] . '成长值';
        } elseif ($arr['ret'] == -10006) {
            $this->setStatus();
            $msg[] = $this->uin . ' 腾讯视频VIP会员签到失败！SKEY已失效';
        } elseif ($arr['ret'] == -10019) {
            $msg[] = $this->uin . ' 你不是腾讯视频VIP会员，无法签到';
        } else {
            $msg[] = $this->uin . ' 腾讯视频VIP会员签到失败！' . $arr['msg'];
        }

        $url = 'https://vip.video.qq.com/fcgi-bin/comm_cgi?name=spp_novel_checkin&cmd=2&_=' . time() . '676';
        $data = $this->get_curl($url, 0, $url, $this->cookie);
        preg_match('/QZOutputJson=\((.*?)\)/is', $data, $json);
        $arr = json_decode($json[1], true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = $this->uin . ' 腾讯视频小说频道签到成功！获得' . $arr['goodsFacevalueInToday'] . '书卷';
        } elseif ($arr['ret'] == -1006) {
            $msg[] = $this->uin . ' 腾讯视频小说频道当天已经签过到';
        } elseif ($arr['ret'] == -11) {
            $this->setStatus();
            $msg[] = $this->uin . ' 腾讯视频小说频道签到失败！SKEY已失效';
        } else {
            $msg[] = $this->uin . ' 腾讯视频小说频道签到失败！' . $arr['msg'];
        }

        $url = 'https://growth.video.qq.com/fcgi-bin/sync_task?callback=&otype=json&taskid=22&platform=1&_=' . time() . '8906';
        $data = $this->get_curl($url, 0, $url, $this->cookie);
        preg_match('/QZOutputJson=(.*?)\;/is', $data, $json);
        $arr = json_decode($json[1], true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = $this->uin . ' 腾讯视频PC端签到成功！';
        } elseif ($arr['ret'] == -13004) {
            $this->setStatus();
            $msg[] = $this->uin . ' 腾讯视频PC端签到失败！SKEY已失效';
        } else {
            $msg[] = $this->uin . ' 腾讯视频PC端签到失败！' . $arr['errmsg'];
        }

        $this->setMessage($msg);
    }
}