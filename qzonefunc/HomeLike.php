<?php

namespace qzonefunc;

/**
 * Qzone 主页赞功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class HomeLike extends Qzone
{
    /**
     * 运行功能
     * @param $uin
     * @access
     */
    public function run($uin)
    {
        $randuin = rand(111111111, 999999999);
        $url = 'http://w.qzone.qq.com/cgi-bin/likes/internal_dolike_app?qzonetoken=' . $this->getToken('https://user.qzone.qq.com/' . $this->uin . '/311', 1) . '&g_tk=' . $this->gtk2;
        $post = 'qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F' . $randuin . '&appid=7030&face=0&fupdate=1&from=1&query_count=200&format=json&opuin=' . $this->uin . '&unikey=http%3A%2F%2Fuser.qzone.qq.com%2F' . $randuin . '&curkey=http%3A%2F%2Fuser.qzone.qq.com%2F' . $uin . '&zb_url=http%3A%2F%2Fi.gtimg.cn%2Fqzone%2Fspace_item%2Fpre%2F10%2F' . rand(10000, 99999) . '_1.gif';
        $json = $this->get_curl($url, $post, 'http://user.qzone.qq.com/' . $uin, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = $this->uin . ' 赞 ' . $uin . ' 主页成功';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = $this->uin . ' 赞 ' . $uin . ' 主页失败！原因:' . $arr['message'];
        } elseif (array_key_exists('code', $arr)) {
            $msg[] = $this->uin . ' 赞 ' . $uin . ' 主页失败！原因:' . $arr['message'];
        } else {
            $msg[] = $this->uin . ' 赞 ' . $uin . ' 主页失败！请10秒后再试';
        }
        $this->setMessage($msg);
    }

}