<?php

namespace qsignfunc;

/**
 * Qsign 微视功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Weishi extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $url = 'https://h5.qzone.qq.com/weishi/jifen/main?_proxy=1&_wv=3&navstyle=2&titleh=55.0&statush=20.0';
        $data = $this->get_curl($url, 0, 0, $this->cookie, 1);
        if (strpos($data, '未找到有效登陆信息')) {
            $this->setStatus();
            $this->setMessage($this->uin . ' QQ微视签到失败！SKEY过期');
            return;
        }
        $cookie = $this->cookie . '; ';
        preg_match_all('/Set-Cookie: (.*?);/i', $data, $matchs);
        foreach ($matchs[1] as $val) {
            if (substr($val, -1) == '=') continue;
            $cookie .= $val . '; ';
        }
        $cookie = substr($cookie, 0, -2);
        $url = 'https://h5.qzone.qq.com/proxy/domain/activity.qzone.qq.com/fcg-bin/fcg_weishi_task_report_login?t=0.' . time() . '030444&g_tk=' . $this->gtk;
        $post = 'task_appid=weishi&task_id=SignIn&qua=_placeholder&format=json&uin=' . $this->uin . '&inCharset=utf-8&outCharset=utf-8';
        $data = $this->get_curl($url, $post, 0, $cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('code', $arr) && $arr['code'] == 0)
            $msg[] = $this->uin . ' QQ微视签到成功！';
        else
            $msg[] = $this->uin . ' QQ微视签到失败！' . $arr['message'];
        $this->setMessage($msg);
    }
}