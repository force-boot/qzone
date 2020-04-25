<?php

namespace qzonefunc;

/**
 * Qzone 花藤功能操作类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Flower extends Qzone
{
    /**
     * 运行功能
     * @access
     */
    public function run()
    {
        $this->coll(); //执行合集，浇水，修剪，施肥等
        $this->picking(); // 摘果
        $this->gift(); //领取礼包
        $this->fert(); //神奇肥料
    }

    /**
     * 功能合集 浇水，修剪，施肥等
     * @access
     */
    public function coll()
    {
        $url = 'https://h5.qzone.qq.com/proxy/domain/flower.qzone.qq.com/fcg-bin/cgi_plant?g_tk=' . $this->gtk2;
        $post = 'fl=1&fupdate=1&act=rain&uin=' . $this->uin . '&newflower=1&outCharset=utf%2D8&g%5Ftk=' . $this->gtk2 . '&format=json';
        $json = $this->get_curl($url, $post, $url, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '浇水成功！';
        } elseif ($arr['code'] == -6002) {
            $msg[] = '今天浇过水啦！';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '浇水失败！原因:SKEY已过期！';
        } else {
            $msg[] = '浇水失败！' . $arr['message'];
        }

        $post = 'fl=1&fupdate=1&act=love&uin=' . $this->uin . '&newflower=1&outCharset=utf%2D8&g%5Ftk=' . $this->gtk2 . '&format=json';
        $json = $this->get_curl($url, $post, $url, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '修剪成功！';
        } elseif ($arr['code'] == -6002) {
            $msg[] = '今天修剪过啦！';
        } elseif ($arr['code'] == -6007) {
            $msg[] = '您的爱心值今天已达到上限！';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '修剪失败！原因:SKEY已过期！';
        } else {
            $msg[] = '修剪失败！' . $arr['message'];
        }

        $post = 'fl=1&fupdate=1&act=sun&uin=' . $this->uin . '&newflower=1&outCharset=utf%2D8&g%5Ftk=' . $this->gtk2 . '&format=json';
        $json = $this->get_curl($url, $post, $url, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '光照成功！';
        } elseif ($arr['code'] == -6002) {
            $msg[] = '今天日照过啦！';
        } elseif ($arr['code'] == -6007) {
            $msg[] = '您的阳光值今天已达到上限！';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '光照失败！原因:SKEY已过期！';
        } else {
            $msg[] = '光照失败！' . $arr['message'];
        }

        $post = 'fl=1&fupdate=1&act=nutri&uin=' . $this->uin . '&newflower=1&outCharset=utf%2D8&g%5Ftk=' . $this->gtk2 . '&format=json';
        $json = $this->get_curl($url, $post, $url, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '施肥成功！';
        } elseif ($arr['code'] == -6005) {
            $msg[] = '暂不能施肥！';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '施肥失败！原因:SKEY已过期！';
        } else {
            $msg[] = '施肥失败！' . $arr['message'];
        }
        $this->setMessage($msg);
    }

    /**
     * 摘果
     * @access public
     */
    public function picking()
    {
        $url = 'https://h5.qzone.qq.com/proxy/domain/flower.qzone.qq.com/cgi-bin/fg_pickup_fruit?g_tk=' . $this->gtk2;
        $post = 'format=json&outCharset=utf-8&random=23552.762577310205';
        $json = $this->get_curl($url, $post, $url, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '摘果成功！';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '摘果失败！原因:SKEY已过期！';
        } else {
            $msg[] = '摘果失败！' . $arr['message'];
        }
        $this->setMessage($msg);
    }

    /**
     * 领取礼包
     * @access public
     */
    public function gift()
    {
        $url = 'https://h5.qzone.qq.com/proxy/domain/flower.qzone.qq.com/cgi-bin/fg_get_giftpkg?g_tk=' . $this->gtk2;
        $post = 'outCharset=utf-8&format=json';
        $json = $this->get_curl($url, $post, 'https://ctc.qzs.qq.com/qzone/client/photo/swf/RareFlower/FlowerVineLite.swf', $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            if ($arr['data']['vDailyGiftpkg'][0]['caption']) {
                $msg[] = $arr['data']['vDailyGiftpkg'][0]['caption'] . ':' . $arr['data']['vDailyGiftpkg'][0]['content'];
                $giftpkgid = $arr['data']['vDailyGiftpkg'][0]['id'];
                $granttime = $arr['data']['vDailyGiftpkg'][0]['granttime'];
                $url = 'https://h5.qzone.qq.com/proxy/domain/flower.qzone.qq.com/cgi-bin/fg_use_giftpkg?g_tk=' . $this->gtk2;
                $post = 'giftpkgid=' . $giftpkgid . '&outCharset=utf%2D8&granttime=' . $granttime . '&format=json';
                $this->get_curl($url, $post, 'https://ctc.qzs.qq.com/qzone/client/photo/swf/RareFlower/FlowerVineLite.swf', $this->cookie);
            } else
                $msg[] = '领取每日登录礼包成功！';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '领取每日登录礼包失败！原因:SKEY已过期！';
        } else {
            $msg[] = '领取每日登录礼包失败！' . $arr['message'];
        }
        $this->setMessage($msg);
    }

    /**
     * 神奇肥料
     * @access public
     */
    public function fert()
    {
        $url = 'https://h5.qzone.qq.com/proxy/domain/flower.qzone.qq.com/cgi-bin/cgi_pickup_oldfruit?g_tk=' . $this->gtk2;
        $post = 'mode=1&g%5Ftk=' . $this->gtk2 . '&outCharset=utf%2D8&fupdate=1&format=json';
        $json = $this->get_curl($url, $post, $url, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '兑换神奇肥料成功！';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '兑换神奇肥料失败！原因:SKEY已过期！';
        } else {
            $msg[] = '兑换神奇肥料失败！' . $arr['message'];
        }
        $this->setMessage($msg);
    }
}