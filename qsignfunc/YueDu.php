<?php

namespace qsignfunc;

/**
 * Qsign 阅读功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class YueDu extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $url = "http://ubook.3g.qq.com/8/user/myMission?k1={$this->skey}&u1=o0{$this->uin}";
        $data = $this->get_curl($url, 0, 'http://ubook.qq.com/8/mymission.html');
        $arr = json_decode($data, true);
        if ($arr['isLogin'] == 'true' && $arr['signMap']['code'] == 0) {
            $msg[] = '图书签到成功！';
        } elseif ($arr['signMap']['code'] == -2) {
            $msg[] = '图书今日已经签到！';
        } elseif ($arr['isLogin'] == false) {
            $msg[] = '图书签到失败！SKEY过期！';
        } else {
            $msg[] = '图书签到失败！数据异常';
        }
        $guid = md5($this->uin . time());
        $url = "https://novelsns.html5.qq.com/ajax?m=task&type=sign&aid=20&t=" . time() . "586";
        $data = $this->get_curl($url, 0, 'https://bookshelf.html5.qq.com/discovery.html', 'Q-H5-ACCOUNT=' . $this->uin . '; Q-H5-SKEY=' . $this->skey . '; luin=' . $this->uin . '; Q-H5-USERTYPE=1; Q-H5-GUID=' . $guid . ';');
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = '小说书架签到成功！已连续签到' . $arr['continuousDays'] . '天,获得书豆' . $arr['beans'];
        } elseif ($arr['ret'] == -2) {
            $msg[] = '小说书架今天已签到！已连续签到' . $arr['continuousDays'] . '天,获得书豆' . $arr['beans'];
        } else {
            $msg[] = '小说书架签到失败！' . $arr['msg'];
        }
        $url = "https://novelsns.html5.qq.com/ajax?m=shareSignPageObtainBeans&aid=20&t=" . time() . "586";
        $data = $this->get_curl($url, 0, 'https://bookshelf.html5.qq.com/discovery.html', 'Q-H5-ACCOUNT=' . $this->uin . '; Q-H5-SKEY=' . $this->skey . '; luin=' . $this->uin . '; Q-H5-USERTYPE=1; Q-H5-GUID=' . $guid . ';');
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = '小说书架分享成功！获得书豆' . $arr['beans'];
        } else {
            $msg[] = '小说书架分享失败！' . $arr['msg'];
        }
        $url = 'http://reader.sh.vip.qq.com/cgi-bin/reader_page_csrf_cgi?merge=2&ditch=100020&cfrom=account&current=sign_index&tf=2&sid=' . $this->uin . '&client=1&version=qqreader_1.0.669.0001_android_qqplugin&channel=00000&_bid=2036&ChannelID=100020&plat=1&qqVersion=0&_from=sign_index&_=' . time() . '017&g_tk=' . $this->gtk . '&p_tk=&sequence=' . time() . '755';
        $post = 'param=%7B%220%22%3A%7B%22param%22%3A%7B%22tt%22%3A0%7D%2C%22module%22%3A%22reader_sign_manage_svr%22%2C%22method%22%3A%22UserTodaySign%22%7D%2C%221%22%3A%7B%22param%22%3A%7B%22tt%22%3A0%7D%2C%22module%22%3A%22reader_sign_manage_svr%22%2C%22method%22%3A%22GetSignGifts%22%7D%7D';
        $data = $this->get_curl($url, $post, $url, $this->cookie);
        $arr = json_decode($data, true);
        if ($arr = $arr['data']['0']['retBody']) {
            if (array_key_exists('result', $arr) && $arr['result'] == 0) {
                $msg[] = '手机QQ阅读签到成功！获得书券' . $arr['data']['awards'][0]['awardNum'] . ',已连续签到' . $arr['data']['lastDays'] . '天';
            } else {
                $msg[] = '手机QQ阅读签到失败！' . $arr['message'];
            }
        } else {
            $msg[] = '手机QQ阅读签到失败！' . $data;
        }
        $url = 'http://reader.sh.vip.qq.com/cgi-bin/reader_page_csrf_cgi?merge=1&ditch=100020&cfrom=account&current=sign_index&tf=2&sid=' . $this->uin . '&client=1&version=qqreader_1.0.669.0001_android_qqplugin&channel=00000&_bid=2036&ChannelID=100020&plat=1&qqVersion=0&_from=sign_index&_=' . time() . '017&g_tk=' . $this->gtk . '&p_tk=&sequence=' . time() . '755';
        $post = 'param=%7B%220%22%3A%7B%22param%22%3A%7B%22tt%22%3A0%7D%2C%22module%22%3A%22reader_sign_manage_svr%22%2C%22method%22%3A%22GrantBigGift%22%7D%7D';
        $data = $this->get_curl($url, $post, $url, $this->cookie);
        $arr = json_decode($data, true);
        if ($arr = $arr['data']['0']['retBody']) {
            if (array_key_exists('result', $arr) && $arr['result'] == 0) {
                $msg[] = '手机QQ阅读抽奖成功！获得奖品' . $arr['data']['newlyGift']['giftName'];
            } elseif ($arr['result'] == 1004) {
                $msg[] = '手机QQ阅读抽奖：需要连续签到5天才可以抽奖';
            } else {
                $msg[] = '手机QQ阅读抽奖失败！' . $arr['message'];
            }
        } else {
            $msg[] = '手机QQ阅读抽奖失败！' . $data;
        }
        $this->setMessage($msg);
    }
}