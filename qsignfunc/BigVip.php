<?php

namespace qsignfunc;

/**
 * Qsign QQ大会员功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class BigVip extends Qsign
{
    /**
     * 运行功能
     * @access
     */
    public function run()
    {
        //登录我的访客
        $url = 'https://h5.qzone.qq.com/qzone/visitor?_wv=3&_wwv=1024&_proxy=1';
        $this->get_curl($url, 0, 0, $this->cookie);
        $url = 'https://h5.qzone.qq.com/webapp/json/QQBigVipTask/CompleteTask?t=0.' . time() . '906319&g_tk=' . $this->gtk;
        $post = 'outCharset=utf-8&iAppId=0&llTime=' . time() . '&format=json&iActionType=6&strUid=' . $this->uin . '&uin=' . $this->uin . '&inCharset=utf-8';
        $data = $this->get_curl($url, $post, 0, $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0)
            $msg[] = $this->uin . ' 登录我的访客成功！';
        elseif ($arr['ret'] == -3000) {
            $this->setStatus();
            $msg[] = $this->uin . ' 登录我的访客失败！SKEY过期';
        } else
            $msg[] = $this->uin . ' 登录我的访客失败！' . $arr['msg'];
        //登录大会员官网
        $url = 'https://h5.qzone.qq.com/bigVip/home?_wv=16778243&qzUseTransparentNavBar=1&_wwv=1&_ws=32&_proxy=1';
        $this->get_curl($url, 0, 0, $this->cookie);
        $url = 'https://h5.qzone.qq.com/webapp/json/QQBigVipTask/CompleteTask?t=0.' . time() . '906319&g_tk=' . $this->gtk;
        $post = 'outCharset=utf-8&iAppId=0&llTime=' . time() . '&format=json&iActionType=6&strUid=' . $this->uin . '&uin=' . $this->uin . '&inCharset=utf-8';
        $data = $this->get_curl($url, $post, 0, $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0)
            $msg[] = $this->uin . ' 登录大会员官网成功！';
        elseif ($arr['ret'] == -3000) {
            $this->setStatus();
            $msg[] = $this->uin . ' 登录大会员官网失败！SKEY过期';
        } else
            $msg[] = $this->uin . ' 登录大会员官网失败！' . $arr['msg'];
        $this->setMessage($msg);
    }
}