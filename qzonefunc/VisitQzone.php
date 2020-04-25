<?php

namespace qzonefunc;

/**
 * qzone 访问空间功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class VisitQzone extends Qzone
{
    /**
     * 运行功能
     * @param $uin
     * @access public
     */
    public function run($uin)
    {
        $url = 'http://h5.qzone.qq.com/webapp/json/friendSetting/getMainPage?g_tk=' . $this->gtk2 . '&uin=' . $uin . '&visituin=' . $this->uin;
        $json = $this->get_curl($url, 0, 'http://user.qzone.qq.com/' . $uin, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('ret', $arr) && $arr['ret'] == 0) {
            $msg[] = $this->uin . ' 访问 ' . $uin . ' 的空间成功';
        } elseif (array_key_exists('code', $arr)) {
            $msg[] = $this->uin . ' 访问 ' . $uin . ' 的空间失败！原因:' . $arr['message'];
        } else {
            $msg[] = $this->uin . ' 访问 ' . $uin . ' 的空间失败！请10秒后再试';
        }
        $this->setMessage($msg);
    }
}