<?php

namespace qzonefunc;

/**
 *  Qzone抽象基础类  该类只能继承不能被实例化 ,扩展功能请继承此类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
abstract class Qzone extends \BaseQzone
{
    /**
     * 构造方法初始化
     * @param int $uin
     * @param string $skey
     * @param string $pskey
     */
    public function __construct($uin, $skey = '', $pskey = null)
    {
        $this->uin = $uin;
        $this->pskey = $pskey;
        $this->skey = $skey;
        $this->gtk = $this->getGTK($skey);
        $this->gtk2 = $this->getGTK($pskey);
        //解析cookie
        $this->parseCookie();
    }

    /**
     * 解析cookie
     * @access public
     */
    private function parseCookie()
    {
        if ($this->pskey == null)
            $this->cookie = 'pt2gguin=o0' . $this->uin . '; uin=o0' . $this->uin . '; skey=' . $this->skey . ';';
        else {
            $this->cookie = 'pt2gguin=o0' . $this->uin . '; uin=o0' . $this->uin . '; skey=' . $this->skey . '; p_skey=' . $this->pskey . '; p_uin=o0' . $this->uin . ';';
            $this->cookie2 = 'pt2gguin=o0' . $this->uin . '; uin=o0' . $this->uin . '; skey=' . $this->skey . ';';
        }
    }
    /**
     * 验证当前qq状态
     * @access public
     * @return bool
     */
    protected function check_status()
    {
        $url = 'http://r.qzone.qq.com/cgi-bin/user/qzone_cgi_msg_getcnt2?uin=' . $this->uin . '&bm=0800950000008001&v=1&g_tk=' . $this->gtk2 . '&g=0.291287' . time();
        $data = $this->get_curl($url, 0, 'http://cnc.qzs.qq.com/qzone/v6/setting/profile/profile.html', $this->cookie);
        preg_match('/\_Callback\((.*?)\);/is', $data, $json);
        $arr = $this->parseJson($json[1]);
        if ($arr['error'] == 4004) {
            $this->setStatus();
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取最新动态
     * @param string $do
     * @param int $time
     * @access protected
     * @return bool|array
     */
    protected function getNew($do = '', $time = 0)
    {
        $url = 'https://h5.qzone.qq.com/webapp/json/mqzone_feeds/getActiveFeeds?qzonetoken=' . $this->getToken() . '&g_tk=' . $this->gtk2;
        $post = 'res_type=0&res_attach=&refresh_type=2&format=json&attach_info=';
        $json = $this->get_curl($url, $post, 1, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $this->setMessage('获取最新动态列表成功！');
            if (isset($arr['data']['vFeeds'])) {
                return $arr['data']['vFeeds'];
            } else {
                return $arr['data']['feeds']['vFeeds'];
            }
        } elseif (strpos($arr['message'], '登录') || $arr['code'] == -3000) {
            $this->setMessage('获取最新动态列表失败！原因:SKEY已失效');
            $this->setStatus();
            return false;
        } elseif (strpos($arr['message'], '统繁忙')) {
            if ($time == 0) {
                return $this->getnew($do, 1);
            } else {
                $this->setMessage('获取最新动态列表失败！原因:' . $arr['message']);
                return false;
            }
        } else {
            $this->setMessage('获取最新动态列表失败！原因:' . $arr['message']);
            return false;
        }
    }

    /**
     * 获取我的最新动态
     * @param null $uin
     * @param int $num
     * @access protected
     * @return bool|array
     */
    protected function getMyNew($uin = null, $num = 20)
    {
        !empty($uin) ?: $uin = $this->uin;
        $url = 'https://mobile.qzone.qq.com/list?qzonetoken=' . $this->getToken() . '&g_tk=' . $this->gtk2 . '&res_attach=&format=json&list_type=shuoshuo&action=0&res_uin=' . $this->uin . '&count=' . $num;
        $json = $this->get_curl($url, 0, 1, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $this->setMessage('获取说说列表成功！');
            return $arr['data']['vFeeds'];
        } else {
            $this->setMessage('获取最新说说失败！原因:' . $arr['message']);
            return false;
        }
    }

    /**
     * 获取token
     * @param string $url
     * @param bool $pc
     * @access protected
     * @return bool
     */
    protected function getToken($url = 'https://h5.qzone.qq.com/mqzone/index', $pc = false)
    {
        if ($this->qzoneToken && $this->qzoneTokenPc == $pc) return $this->qzoneToken;
        $filename = APP_PATH. 'runtime/qzonetoken/temp_' . md5($this->uin . $this->pskey . $url);
        if (file_exists($filename)) {
            $result = file_get_contents($filename);
            $this->qzoneToken = $result;
            $this->qzoneTokenPc = $pc;
            return $this->qzoneToken;
        }
        $ua = $this->getUa($pc);
        $json = $this->get_curl($url, 0, 0, $this->cookie, 0, $ua);
        preg_match('/\(function\(\){ try{*.return (.*?);} catch\(e\)/i', $json, $match);
        if ($data = $match[1]) {
            $word = array('([]+[][(![]+[])[!+[]+!![]+!![]]+([]+{})[+!![]]+(!![]+[])[+!![]]+(!![]+[])[+[]]][([]+{})[!+[]+!![]+!![]+!![]+!![]]+([]+{})[+!![]]+([][[]]+[])[+!![]]+(![]+[])[!+[]+!![]+!![]]+(!![]+[])[+[]]+(!![]+[])[+!![]]+([][[]]+[])[+[]]+([]+{})[!+[]+!![]+!![]+!![]+!![]]+(!![]+[])[+[]]+([]+{})[+!![]]+(!![]+[])[+!![]]]((!![]+[])[+!![]]+([][[]]+[])[!+[]+!![]+!![]]+(!![]+[])[+[]]+([][[]]+[])[+[]]+(!![]+[])[+!![]]+([][[]]+[])[+!![]]+([]+{})[!+[]+!![]+!![]+!![]+!![]+!![]+!![]]+(![]+[])[!+[]+!![]]+([]+{})[+!![]]+([]+{})[!+[]+!![]+!![]+!![]+!![]]+(+{}+[])[+!![]]+(!![]+[])[+[]]+([][[]]+[])[!+[]+!![]+!![]+!![]+!![]]+([]+{})[+!![]]+([][[]]+[])[+!![]])())' => 'https', '([][[]]+[])' => 'undefined', '([]+{})' => '[object Object]', '(+{}+[])' => 'NaN', '(![]+[])' => 'false', '(!![]+[])' => 'true');
            $words = array();
            $i = 0;
            foreach ($word as $k => $v) {
                $words[$i] = $v;
                $data = str_replace($k, '$words[' . $i . ']', $data);
                $i++;
            }
            $data = str_replace(array('!+[]', '+!![]', '+[]'), array('+1', '+1', '+0'), $data);
            $data = str_replace(array('+(', '+$'), array('.(', '.$'), $data);
            eval('$result=' . $data . ';');
            if (!$result) {
                $this->setMessage('计算qzonetoken失败!');
                return false;
            }
        file_put_contents($filename, $result);
        $this->qzoneToken = $result;
        $this->qzoneTokenPc = $pc;
        return $this->qzoneToken;
        } elseif ($this->check_status() == false) {
            $this->setMessage('获取qzonetoken失败！原因:SKEY已失效');
            return false;
        } else {
            $this->setMessage('获取qzonetoken失败！');
            return false;
        }
    }

    /**
     * 获取ua
     * @param bool $pc
     * @access private
     * @return string
     */
    private function getUa($pc = false)
    {
        if ($pc) {
            $ua = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
        } else {
            $ua = 'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.5 Mobile Safari/533.1';
        }
        return $ua;
    }

    /**
     * @param $url
     * @access protected
     * @return bool|string
     */
    protected function openu($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: */*";
        $httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    /**
     * @param $skey
     * @access protected
     * @return int
     */
    protected function getGTK($skey)
    {
        $len = strlen($skey);
        $hash = 5381;
        for ($i = 0; $i < $len; $i++) {
            $hash += ($hash << 5 & 2147483647) + ord($skey[$i]) & 2147483647;
            $hash &= 2147483647;
        }
        return $hash & 2147483647;
    }

    /**
     * @param $uin
     * @param $arrs
     * @access protected
     * @return bool
     */
    protected function is_comment($uin, $arrs)
    {
        if ($arrs) {
            foreach ($arrs as $arr) {
                if ($arr['user']['uin'] == $uin) {
                    return false;
                    break;
                }
            }
            return true;
        } else {
            return true;
        }
    }

    /**
     * @param $array
     * @access
     * @return mixed|string
     */
    protected function array_str($array)
    {
        $str = '';
        if ($array[-100]) {
            $array100 = explode(' ', trim($array[-100]));
            $new100 = implode('+', $array100);
            $array[-100] = $new100;
        }
        foreach ($array as $k => $v) {
            if ($k != '-100') {
                $str = $str . $k . '=' . $v . '&';
            }
        }
        $str = urlencode($str . '-100=') . $array[-100] . '+';
        $str = str_replace(':', '%3A', $str);
        return $str;
    }
}