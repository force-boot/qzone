<?php

namespace qzonefunc;

/**
 * Class Token
 * @package qzonefunc
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Token extends Qzone
{
    /**
     * 获取qzonetoken
     * @param string $url
     * @param bool $pc
     * @access public
     * @return bool
     */
    public function run($url = 'https://h5.qzone.qq.com/mqzone/index', $pc = false)
    {
        return $this->getToken($url, $pc);
    }
}