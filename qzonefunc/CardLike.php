<?php

namespace qzonefunc;

/**
 * Qzone 名片赞点赞功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class CardLike extends Qzone
{
    /**
     * 运行功能
     * @param $uin
     * @access public
     * @return array
     */
    public function run($uin)
    {
        $url = 'https://club.vip.qq.com/visitor/like?g_tk=' . $this->gtk . '&nav=0&uin=' . $uin . '&t=' . $this->times();
        $json = $this->get_curl($url, 0, 'https://club.vip.qq.com/visitor/index?_wv=4099&_nav_bgclr=ffffff&_nav_titleclr=ffffff&_nav_txtclr=ffffff&_nav_alpha=0', $this->cookie);
        $arr = $this->parseJson($json);
        return $arr;
    }

    /**
     * @access public
     * @return float
     */
    public function times()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}