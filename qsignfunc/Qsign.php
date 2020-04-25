<?php

namespace qsignfunc;

/**
 *  Qsign抽象基础类  该类只能继承不能被实例化 ,扩展功能请继承此类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
abstract class Qsign extends \BaseQzone
{
    /**
     * Qsign constructor.
     * @param $uin
     * @param string $skey
     * @param string $pskey
     */
    public function __construct($uin, $skey = '', $pskey = null)
    {
        $this->uin = $uin;
        $this->skey = $skey;
        $this->pskey = $pskey;
        $this->gtk2 = $this->getGTK2($skey);
        $this->parseCookie();

    }

    /**
     * 解析cookie
     * @access private
     */
    private function parseCookie()
    {
        if ($this->pskey == null) {
            $this->gtk = $this->getGTK($this->skey);
            $this->cookie = 'pt2gguin=o0' . $this->uin . '; uin=o0' . $this->uin . '; skey=' . $this->skey;
        } else {
            $this->gtk = $this->getGTK($this->pskey);
            $this->cookie = 'pt2gguin=o0' . $this->uin . '; uin=o0' . $this->uin . '; skey=' . $this->skey . '; p_skey=' . $this->pskey . '; p_uin=o0' . $this->uin;
        }
    }

    /**
     * @param $skey
     * @access  protected
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
     * @param $skey
     * @access  protected
     * @return string
     */
    protected function getGTK2($skey)
    {
        $salt = 5381;
        $md5key = 'tencentQQVIP123443safde&!%^%1282';
        $hash = array();
        $hash[] = ($salt << 5);
        for ($i = 0; $i < strlen($skey); $i++) {
            $ASCIICode = mb_convert_encoding($skey[$i], 'UTF-32BE', 'UTF-8');
            $ASCIICode = hexdec(bin2hex($ASCIICode));
            $hash[] = (($salt << 5) + $ASCIICode);
            $salt = $ASCIICode;
        }
        $md5str = md5(implode($hash) . $md5key);
        return $md5str;
    }

    /**
     * @param $skey
     * @access  protected
     * @return string
     */
    protected function getGTK3($skey)
    {
        $salt = 108;
        $md5key = 'tencent.mobile.qq.csrfauth';
        $hash = array();
        $hash[] = ($salt << 5);
        for ($i = 0; $i < strlen($skey); $i++) {
            $ASCIICode = mb_convert_encoding($skey[$i], 'UTF-32BE', 'UTF-8');
            $ASCIICode = hexdec(bin2hex($ASCIICode));
            $hash[] = (($salt << 5) + $ASCIICode);
            $salt = $ASCIICode;
        }
        $md5str = md5(implode($hash) . $md5key);
        return $md5str;
    }

    /**
     * @param $token
     * @access  protected
     * @return float|int
     */
    protected function getToken($token)
    {
        $len = strlen($token);
        $hash = 0;
        for ($i = 0; $i < $len; $i++) {
            $hash = fmod($hash * 33 + ord($token[$i]), 4294967296);
        }
        return $hash;
    }

    /**
     * @param $token
     * @access  protected
     * @return int
     */
    protected function getToken2($token)
    {
        $len = strlen($token);
        $hash = 0;
        for ($i = 0; $i < $len; $i++) {
            $hash += ($hash << 5 & 2147483647) + ord($token[$i]) & 2147483647;
            $hash &= 2147483647;
        }
        return $hash & 2147483647;
    }
}