<?php
namespace qsignfunc;

/**
 * Qsign 黄钻功能类
 * @package qsignfunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Hz extends Qsign
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $url = 'https://vip.qzone.qq.com/fcg-bin/v2/fcg_mobile_vip_site_checkin?t=0.89457' . time() . '&g_tk=' . $this->gtk . '&qzonetoken=423659183';
        $post = 'uin=' . $this->uin . '&format=json';
        $referer = 'https://h5.qzone.qq.com/vipinfo/index?plg_nld=1&source=qqmail&plg_auth=1&plg_uin=1&_wv=3&plg_dev=1&plg_nld=1&aid=jh&_bid=368&plg_usr=1&plg_vkey=1&pt_qzone_sig=1';
        $data = $this->get_curl($url, $post, $referer, $this->cookie);
        $arr = json_decode($data, true);
        if (array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '黄钻签到成功！';
        } elseif (array_key_exists('code', $arr) && $arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '黄钻签到失败！SKEY已失效';
        } elseif (array_key_exists('code', $arr)) {
            $msg[] = '黄钻签到失败！' . $arr['message'];
        } else {
            $msg[] = '黄钻签到失败！' . $data;
        }

        $url = 'https://activity.qzone.qq.com/fcg-bin/fcg_huangzuan_daily_signing?t=0.' . time() . '906035&g_tk=' . $this->gtk . '&qzonetoken=-1';
        $post = 'option=sign&uin=' . $this->uin . '&format=json';
        $data = $this->get_curl($url, $post, $url, $this->cookie);
        $data = mb_convert_encoding($data, "UTF-8", "GB2312");
        $arr = json_decode($data, true);
        if (array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '黄钻公众号签到成功！';
        } elseif (array_key_exists('code', $arr) && $arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '黄钻公众号签到失败！SKEY已失效';
        } elseif ($arr['code'] == -90002) {
            $msg[] = '黄钻公众号签到失败！非黄钻用户无法签到';
        } elseif (array_key_exists('code', $arr)) {
            $msg[] = '黄钻公众号签到失败！' . $arr['message'];
        } else {
            $msg[] = '黄钻公众号签到失败！' . $data;
        }
        $this->setMessage($msg);
    }
}