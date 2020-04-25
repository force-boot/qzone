<?php

namespace qsignfunc;

/**
 * Qsign 部落功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Buluo extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $this->love();//领爱心
        $url = 'https://buluo.qq.com/cgi-bin/bar/card/bar_list_by_page?uin=' . $this->uin . '&neednum=30&startnum=0&r=0.98389' . time();
        $url2 = 'https://buluo.qq.com/cgi-bin/bar/user/sign';
        $data = $this->get_curl($url, 0, 'https://buluo.qq.com/mobile/personal.html', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
            $msg[] = $this->uin . '获取兴趣部落列表成功！';
            $arr = $arr['result']['followbars'];
            foreach ($arr as $row) {
                $post = 'bid=' . $row['bid'] . '&bkn=' . $this->gtk . '&r=0.84746' . time();
                $data = $this->get_curl($url2, $post, 'https://buluo.qq.com/mobile/personal.html', $this->cookie);
                $arrs = json_decode($data, true);
                if (array_key_exists('retcode', $arrs) && $arrs['retcode'] == 0) {
                    if ($arrs['result']['sign'] == 1)
                        $msg[] = $row['name'] . ' 部落已签到！';
                    else
                        $msg[] = $row['name'] . ' 部落签到成功！';
                } elseif ($arrs['retcode'] == 100000) {
                    $this->setStatus();
                    $msg[] = $row['name'] . ' 部落签到失败！SKEY已失效。';
                } else {
                    $msg[] = $row['name'] . ' 部落签到失败！' . $data;
                }
            }
        } elseif ($arr['retcode'] == 100000) {
            $this->setStatus();
            $msg[] = $this->uin . '兴趣部落签到失败！SKEY已失效。';
        } else {
            $msg[] = $this->uin . '兴趣部落签到失败！' . $data;
        }
        $this->setMessage($msg);
    }

    /**
     * 领取爱心
     * @access public
     */
    public function love()
    {
        $url = 'https://buluo.qq.com/cgi-bin/bar/login_present_heart';
        $post = 'bkn=' . $this->gtk;
        $data = $this->get_curl($url, $post, 'https://buluo.qq.com/mobile/my_heart.html', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
            if ($arr['result']['add_hearts'] == 0)
                $msg[] = '今日已领取爱心';
            else
                $msg[] = '成功领取爱心 +' . $arr['result']['add_hearts'];
        } elseif ($arr['retcode'] == 100000) {
            $this->setStatus();
            $msg[] = '领取爱心失败！SKEY已失效。';
        } else {
            $msg[] = '领取爱心失败！' . $data;
        }
        $this->setMessage($msg);
    }
}