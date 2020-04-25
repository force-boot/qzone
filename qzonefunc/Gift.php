<?php

namespace qzonefunc;

/**
 * qzone 礼物功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Gift extends Qzone
{
    /**
     * 运行功能
     * @param $uin
     * @param $con
     * @param int $do
     * @access public
     */
    public function run($uin, $con, $do = 1)
    {
        if ($do == 1) {
            $this->cp($uin, $con);
        } else {
            $this->pc($uin, $con);
        }
    }

    /**
     * pc 协议
     * @param $uin
     * @param $con
     * @access public
     */
    public function pc($uin, $con)
    {
        $url = "http://drift.qzone.qq.com/cgi-bin/sendgift?g_tk=" . $this->gtk2;
        $post = "qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fgift%2Fsend_list.html%23uin%3D%26type%3D%26itemid%3D%26birthday%3D%26birthdaytab%3D0%26lunarFlag%3D0%26source%3D%26nick%3D%26giveback%3D%26popupsrc%3D301%26open%3D%26is_fest_opt%3D0%26festId%3D%26html%3Dsend_list%26startTime%3D1441885072018%26timeoutId%3D2086&fupdate=1&random=0.06927570731103372&charset=utf-8&uin=" . $this->uin . "&targetuin={$uin}&source=0&giftid=106777&private=0&giveback=&qzoneflag=1&time=&timeflag=0&giftmessage=" . urlencode($con) . "&gifttype=0&gifttitle=%E8%AE%B8%E6%84%BF%E4%BA%91%E6%8A%B1%E6%9E%95+&traceid=&newadmin=1&birthdaytab=0&answerid=&arch=0&clicksrc=&click_src_v2=01%7C01%7C301%7C0556%7C0000%7C01";
        $json = $this->get_curl($url, $post, 0, $this->cookie);
        preg_match('/frameElement\.callback\((.*?)\)\;/is', $json, $jsons);
        $json = $jsons[1];
        $arr = $this->parseJson($json);
        if (array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = $this->uin . " 送礼物成功！";
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = $this->uin . " 未登录";
        } elseif ($arr['code'] == 6) {
            $msg[] = $this->uin . " 收礼人设置了权限";
        } else {
            $msg[] = $this->uin . " 送礼物失败！";
        }
        $this->setMessage($msg);
    }

    /**
     * 触屏协议
     * @param $uin
     * @param $con
     * @access public
     */
    public function cp($uin, $con)
    {
        $url = "http://mobile.qzone.qq.com/gift/giftweb?g_tk=" . $this->gtk2;
        $post = "action=3&itemid=108517&struin={$uin}&content=" . urlencode($con) . "&format=json&isprivate=0";
        $json = $this->get_curl($url, $post, 1, $this->cookie);
        $arr = $this->parseJson($json);
        if (array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = $this->uin . " 送礼物成功！";
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = $this->uin . " 未登录";
        } elseif ($arr['code'] == -10000) {
            $msg[] = $this->uin . " 收礼人设置了权限";
        } else {
            $msg[] = $this->uin . " 送礼物失败！";
        }
        $this->setMessage($msg);
    }
}