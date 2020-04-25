<?php

namespace qsignfunc;


/**
 * Qsign VIP功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Vip extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $this->vipPanel();//vip面板签到
        $this->vipPc();//vip电脑签到
        $this->vipMb();//vip手机签到
        $this->vipJf();//vip积分签到
        $this->vipJd();//vip金豆
        $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?g_tk=' . $this->gtk2 . '&actid=27754&_=' . time(), 0, 'https://vip.qq.com/', $this->cookie);//超级会员每月成长值
        $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?g_tk=' . $this->gtk2 . '&actid=27755&_c=page&_=' . time(), 0, 'https://vip.qq.com/', $this->cookie);//超级会员每月积分
        $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?g_tk=' . $this->gtk2 . '&actid=22894&_c=page&_=' . time(), 0, 'https://vip.qq.com/', $this->cookie);//每月分享积分
        $this->get_curl('https://iyouxi4.vip.qq.com/ams3.0.php?g_tk=' . $this->gtk2 . '&actid=239371&_c=page&format=json&_=' . time(), 0, 'https://vip.qq.com/', $this->cookie);//每周薪水积分
        $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?g_tk=' . $this->gtk2 . '&actid=22887&_c=page&format=json&_=' . time(), 0, 'https://vip.qq.com/', $this->cookie);//每周邀请好友积分
        $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?g_tk=' . $this->gtk2 . '&actid=202041&_c=page&format=json&_=' . time(), 0, 'https://vip.qq.com/', $this->cookie);//手Q每日签到
        $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?g_tk=' . $this->gtk2 . '&actid=202049&_c=page&format=json&_=' . time(), 0, 'https://vip.qq.com/', $this->cookie);//手Q每日SVIP签到
    }

    /**
     * 会员面板签到
     * @access public
     */
    public function vipPanel()
    {
        $data = $this->get_curl("https://iyouxi3.vip.qq.com/ams3.0.php?_c=page&actid=79968&format=json&g_tk=" . $this->gtk2 . "&cachetime=" . time(), 0, 'https://vip.qq.com/', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = $this->uin . ' 会员面板签到成功！';
        } elseif ($arr['ret'] == 10601) {
            $msg[] = $this->uin . ' 会员面板今天已经签到！';
        } elseif ($arr['ret'] == 10002) {
            $msg[] = $this->uin . ' 会员面板签到失败！SKEY过期';
        } elseif ($arr['ret'] == 20101) {
            $msg[] = $this->uin . ' 会员面板签到失败！不是QQ会员！';
        } else {
            $msg[] = $this->uin . ' 会员面板签到失败！' . $arr['msg'];
        }
        $this->setMessage($msg);
    }

    /**
     * VIP电脑端签到
     * @access
     */
    public function vipPc()
    {
        $data = $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?_c=page&actid=403490&rand=0.27489888' . time() . '&g_tk=' . $this->gtk2 . '&format=json', 0, 'https://vip.qq.com/', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = $this->uin . ' 会员电脑端签到成功！';
        } elseif ($arr['ret'] == 10601) {
            $msg[] = $this->uin . ' 会员电脑端今天已经签到！';
        } elseif ($arr['ret'] == 10002) {
            $this->setStatus();
            $msg[] = $this->uin . ' 会员电脑端签到失败！SKEY过期';
        } else {
            $msg[] = $this->uin . ' 会员电脑端签到失败！' . $arr['msg'];
        }
        $this->setMessage($msg);
    }

    /**
     * 会员手机端签到
     * @access public
     */
    public function vipMb()
    {
        $data = $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?actid=52002&rand=0.27489888' . time() . '&g_tk=' . $this->gtk2 . '&format=json', 0, 'https://vip.qq.com/', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = $this->uin . ' 会员手机端签到成功！';
        } elseif ($arr['ret'] == 10601) {
            $msg[] = $this->uin . ' 会员手机端今天已经签到！';
        } elseif ($arr['ret'] == 10002) {
            $this->setStatus();
            $msg[] = $this->uin . ' 会员手机端签到失败！SKEY过期';
        } else {
            $msg[] = $this->uin . ' 会员手机端签到失败！' . $arr['msg'];
        }
        $this->setMessage($msg);
    }

    /**
     * 会员积分签到
     * @access public
     */
    public function vipJf()
    {
        $data = $this->get_curl('https://iyouxi4.vip.qq.com/ams3.0.php?_c=page&actid=239151&isLoadUserInfo=1&format=json&g_tk=' . $this->gtk2, 0, 'https://vip.qq.com/', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0)
            $msg[] = $this->uin . ' 会员积分签到成功！';
        elseif ($arr['ret'] == 10601)
            $msg[] = $this->uin . ' 会员积分今天已经签到！';
        elseif ($arr['ret'] == 10002) {
            $this->setStatus();
            $msg[] = $this->uin . ' 会员积分签到失败！SKEY过期';
        } else
            $msg[] = $this->uin . ' 会员积分签到失败！' . $arr['msg'];
        $data = $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?_c=page&actid=54963&isLoadUserInfo=1&format=json&g_tk=' . $this->gtk2, 0, 'https://vip.qq.com/', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0)
            $msg[] = $this->uin . ' 会员积分签到成功！';
        elseif ($arr['ret'] == 10601)
            $msg[] = $this->uin . ' 会员积分今天已经签到！';
        elseif ($arr['ret'] == 10002) {
            $this->setStatus();
            $msg[] = $this->uin . ' 会员积分签到失败！SKEY过期';
        } else
            $msg[] = $this->uin . ' 会员积分签到失败！' . $arr['msg'];
        $data = $this->get_curl('https://iyouxi3.vip.qq.com/ams3.0.php?_c=page&actid=23074&format=json&g_tk=' . $this->gtk2, 0, 'https://vip.qq.com/', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ret', $arr) && $arr['ret'] == 0)
            $msg[] = $this->uin . ' 会员积分手机端签到成功！';
        elseif ($arr['ret'] == 10601)
            $msg[] = $this->uin . ' 会员积分手机端今天已经签到！';
        elseif ($arr['ret'] == 10002) {
            $this->setStatus();
            $msg[] = $this->uin . ' 会员积分手机端签到失败！SKEY过期';
        } else
            $msg[] = $this->uin . ' 会员积分手机端签到失败！' . $arr['msg'];
        $this->setMessage($msg);
    }

    /**
     * vip金豆
     * @access public
     */
    public function vipJd()
    {
        $data = $this->get_curl('https://pay.qun.qq.com/cgi-bin/group_pay/good_feeds/gain_give_stock?gain=1&bkn=' . $this->gtk, 0, 'https://m.vip.qq.com/act/qun/jindou.html', $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('ec', $arr) && $arr['ec'] == 0)
            $msg[] = $this->uin . ' 免费领金豆成功！';
        elseif ($arr['ec'] == 1010)
            $msg[] = $this->uin . ' 今天已经领取过金豆了！';
        else
            $msg[] = $this->uin . ' 领金豆失败！' . $arr['em'];
        $this->setMessage($msg);
    }
}