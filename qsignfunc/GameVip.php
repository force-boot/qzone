<?php

namespace qsignfunc;

/**
 * Qsign 蓝钻功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class GameVip extends Qsign
{

    /**
     * 运行功能
     * @param $superkey
     * @access public
     */
    public function run($superkey)
    {
        $supertoken = (string)$this->getToken($superkey);
        $url = "https://ssl.ptlogin2.qq.com/pt4_auth?daid=176&appid=21000110&auth_token=" . $this->getToken($supertoken);
        $data = $this->get_curl($url, 0, 'https://ui.ptlogin2.qq.com/cgi-bin/login', 'superuin=o0' . $this->uin . '; superkey=' . $superkey . '; supertoken=' . $supertoken . ';');
        if (preg_match('/ptsigx=(.*?)&/', $data, $match)) {
            $url = 'https://ptlogin2.gamevip.qq.com/check_sig?uin=' . $this->uin . '&ptsigx=' . $match[1] . '&daid=176&pt_login_type=4&service=pt4_auth&pttype=2&regmaster=&aid=21000110&s_url=http%3A%2F%2Fgamevip.qq.com%2F';
            $data = $this->get_curl($url, 0, 'https://ui.ptlogin2.qq.com/cgi-bin/login', 0, 1);
            preg_match("/skey=(.*?);/", $data, $match);
            $skey = $match[1];
            preg_match("/p_skey=(.*?);/", $data, $match);
            $pskey = $match[1];
            $cookie = 'pt2gguin=o0' . $this->uin . '; uin=o0' . $this->uin . '; skey=' . $skey . '; p_uin=o0' . $this->uin . '; p_skey=' . $pskey . '; DomainID=176;';
            $url = 'https://app.gamevip.qq.com/cgi-bin/gamevip_sign/GameVip_SignIn?format=json&g_tk=' . $this->gtk . '&_=' . time() . '0334';
            $data = $this->get_curl($url, 0, 'https://gamevip.qq.com/sign_pop/sign_pop_v2.html', $cookie);
            $arr = json_decode($data, true);
            if (array_key_exists('result', $arr) && $arr['result'] == 0) {
                $msg[] = '蓝钻签到成功！当前签到积分' . $arr['SignScore'] . '点';
            } elseif ($arr['result'] == 1000005) {
                $msg[] = '蓝钻签到失败！P_skey已失效';
            } else {
                $msg[] = '蓝钻签到失败！' . $arr['resultstr'];
            }
            $url = 'https://app.gamevip.qq.com/cgi-bin/gamevip_sign/GameVip_Lottery?format=json&g_tk=' . $this->gtk . '&_=' . time() . '0334';
            $data = $this->get_curl($url, 0, $url, $cookie);
            $data = mb_convert_encoding($data, "UTF-8", "GB2312");
            $arr = json_decode($data, true);
            if (array_key_exists('result', $arr) && $arr['result'] == 0) {
                $msg[] = '蓝钻抽奖成功！';
            } elseif ($arr['result'] == 1000005) {
                $msg[] = '蓝钻抽奖失败！P_skey已失效';
            } elseif ($arr['result'] == 102) {
                $msg[] = '蓝钻抽奖次数已用完';
            } else {
                $msg[] = '蓝钻抽奖失败！' . $arr['resultstr'];
            }

            $url = 'https://app.gamevip.qq.com/cgi-bin/gamevip_m_sign/GameVip_m_SignIn';
            $data = $this->get_curl($url, 0, $url, $cookie);
            $data = mb_convert_encoding($data, "UTF-8", "GB2312");
            $arr = json_decode($data, true);
            if (array_key_exists('result', $arr) && $arr['result'] == 0) {
                $msg[] = '蓝钻手机签到成功！奖励魔法卡片' . $arr['MagicCard'] . '张，星星' . $arr['McStar'] . '颗';
            } elseif ($arr['result'] == 1000005) {
                $msg[] = '蓝钻手机签到失败！P_skey已失效';
            } else {
                $msg[] = '蓝钻手机签到失败！' . $arr['resultstr'];
            }
        } else {
            $msg[] = '蓝钻签到失败！superkey已失效';
        }

        $url = "https://apps.game.qq.com/ams/ame/ame.php?ameVersion=0.3&sServiceType=qqgame&iActivityId=54614&sServiceDepartment=newterminals&set_info=newterminals";
        $post = "iActivityId=54614&iFlowId=279055&g_tk=" . $this->gtk . "&e_code=0&g_code=0&eas_url=http%253A%252F%252Flz.qq.com%252Fact%252Fa20160712sign%252F&eas_refer=&sServiceDepartment=group_h&sServiceType=qqgame";
        $data = $this->get_curl($url, $post, 0, $this->cookie);
        $arr = json_decode($data, true);
        $arr = $arr['modRet'];
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = '蓝钻微信公众号签到成功！';
        } elseif ($arr['ret'] == 600) {
            $msg[] = '蓝钻微信公众号今天已签到！';
        } else {
            $msg[] = '蓝钻微信公众号签到失败！' . $arr['msg'];
        }
        $this->setMessage($msg);
    }
}