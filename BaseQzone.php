<?php

/**
 * Class BaseQzone
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
abstract class BaseQzone
{
    /**
     * @var int $uin qq
     */
    protected $uin;

    /**
     * @var string $pskey pskey
     */
    protected $pskey;

    /**
     * @var string $skey skey
     */
    protected $skey;

    /**
     * @var int $gtk gtk
     */
    protected $gtk;

    /**
     * @var int $gtk2 gtk2
     */
    protected $gtk2;

    /**
     * @var string $cookie cookie
     */
    protected $cookie;

    /**
     * @var string $cookie2 cookie2
     */
    protected $cookie2;

    /**
     * @var string $qzoneToken
     */
    protected $qzoneToken;

    /**
     * @var string $qzoneTokenPc
     */
    protected $qzoneTokenPc;

    /**
     * @var int $skeyStatus skey状态
     */
    protected $skeyStatus = 0;

    /**
     * @var array 返回信息
     */
    protected $message = [];

    /**
     * 解析json数据
     * @param $json string
     * @param $fetch bool true 返回数组，false返回code
     * @access protected
     * @return array|int
     */
    protected function parseJson($json, $fetch = true)
    {
        $arr = json_decode($json, true);
        if (!$fetch && array_key_exists('code', $arr)) {
            return $arr['code'];
        } else {
            return $arr;
        }
    }

    /**
     * 修改返回信息
     * @param $message string
     * @access public
     * @return array 返回修改后的信息
     */
    public function setMessage($message = '')
    {
        if (is_array($message)) {
            $this->message = array_merge($this->message, $message);
        } else {
            $this->message[] = $message;
        }
        return $this->message;
    }

    /**
     * 获取返回信息
     * @return array
     */
    public function getMessage()
    {
        $message = $this->message;
        $this->message = [];
        return $message;
    }

    /**
     * 设置当前skey状态
     * @param $status int 1失效，0正常
     * @access public
     * @return int
     */
    public function setStatus($status = 1)
    {
        notice($this->uin);
        return $this->skeyStatus = $status;
    }

    /**
     * 获取当前skey状态
     * @access public
     * @return int 1失效，0正常
     */
    public function getStatus()
    {
        return $this->skeyStatus;
    }


    /**
     * @param $url
     * @param int $post
     * @param int $referer
     * @param int $cookie
     * @param int $header
     * @param int $ua
     * @param int $nobaody
     * @param int $addheader
     * @access
     * @return bool|string
     */
    public function get_curl($url, $post = 0, $referer = 1, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0, $addheader = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: application/json";
        $httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: close";
        if ($addheader) {
            $httpheader = array_merge($httpheader, $addheader);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
        }
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if ($referer) {
            if ($referer == 1) {
                curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
            } else {
                curl_setopt($ch, CURLOPT_REFERER, $referer);
            }
        }
        if ($ua) {
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.5 Mobile Safari/533.1');
        }
        if ($nobaody) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        //$ret=mb_convert_encoding($ret, "UTF-8", "UTF-8");
        return $ret;
    }


}