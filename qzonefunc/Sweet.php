<?php

namespace qzonefunc;

/**
 * 情侣空间功能类
 * @package qzonefunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Sweet extends Qzone
{
    /**
     * 运行功能
     * @access public
     */
    public function run()
    {
        $url = 'https://h5.qzone.qq.com/mood/lover?_wv=3';
        $data = $this->get_curl($url, 0, $url, $this->cookie);
        if ($data) $this->setMessage('情侣空间登录成功！');
        $url = 'https://h5.qzone.qq.com/proxy/domain/sweet.snsapp.qq.com/v2/cgi-bin/sweet_share_write?version=v2&t=0.0531703351366811&g_tk=' . $this->gtk2;
        $post = 'type=501&uin=' . $this->uin . '&plat=1&opuin=' . $this->uin . '&content=%E5%AE%9D%E8%B4%9D%E4%BD%A0%E5%B0%B1%E6%98%AF%E6%88%91%E5%94%AF%E4%B8%80%EF%BC%81&sync=0&lbs=&appicnum=0&format=html&inCharset=utf-8&outCharset=utf-8';
        $data = $this->get_curl($url, $post, $url, $this->cookie);
        preg_match('/callback\((.*?)\n\);/', $data, $json);
        $arr = json_decode($json[1], true);
        if (array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '情侣空间登录成功！';
            $msg[] = '情侣空间发表状态成功！';
        } elseif ($arr['code'] == -1001) {
            $this->setStatus();
            $msg[] = '情侣空间发表状态失败！SKEY已过期！';
        } else {
            $msg[] = '情侣空间发表状态失败！' . $arr['message'];
        }
        $this->setMessage($msg);
    }
}