<?php

namespace qzonefunc;

/**
 * Qzone 说说点赞功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Like extends Qzone
{
    /**
     * 运行功能
     * @param int $do
     * @param array $forbid
     * @param int $sleep
     * @access public
     * @return $this
     */
    public function run($do = 0, $forbid = array(), $sleep = 0)
    {
        if ($shuos = $this->getnew()) {
            $i = 0;
            $e = 0;
            foreach ($shuos as $shuo) {
                $like = $shuo['like']['isliked'];
                if ($like == 0 && !in_array($shuo['userinfo']['user']['uin'], $forbid)) {
                    $appid = $shuo['comm']['appid'];
                    $typeid = $shuo['comm']['feedstype'];
                    $curkey = urlencode($shuo['comm']['curlikekey']);
                    $uinkey = urlencode($shuo['comm']['orglikekey']);
                    $uin = $shuo['userinfo']['user']['uin'];
                    $nickname = $shuo['userinfo']['user']['nickname'];
                    $from = $shuo['userinfo']['user']['from'];
                    $abstime = $shuo['comm']['time'];
                    $cellid = $shuo['id']['cellid'];
                    $shuo_con = $shuo['summary']['summary'];
                    if ($do) {
                        $this->pc($uin, $curkey, $uinkey, $from, $appid, $typeid, $abstime, $cellid, $nickname, $shuo_con);
                    } else {
                        $this->cp($uin, $appid, $uinkey, $curkey);
                    }
                    if ($this->skeyStatus) ++$e;
                    $this->skeyStatus = false;
                    ++$i;
                    if ($i >= 8) break;
                    if ($sleep) sleep($sleep);
                    else usleep(100000);
                }
            }
            if ($i == 0) $this->setMessage('暂时没有动态需要点赞');
        }
        return $this;
    }

    /**
     * pc协议 点赞
     * @param $uin
     * @param $curkey
     * @param $unikey
     * @param $from
     * @param $appid
     * @param $typeid
     * @param $abstime
     * @param $fid
     * @param $nickname
     * @param $con
     * @access public
     */
    public function pc($uin, $curkey, $unikey, $from, $appid, $typeid, $abstime, $fid, $nickname, $con)
    {
        $post = 'qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F' . $this->uin . '%2finfocenter%3fvia%3dtoolbar&opuin=' . $this->uin . '&unikey=' . $unikey . '&curkey=' . $curkey . '&from=' . $from . '&appid=' . $appid . '&typeid=' . $typeid . '&abstime=' . $abstime . '&fid=' . $fid . '&active=0&fupdate=1';
        $url = 'https://user.qzone.qq.com/proxy/domain/w.qzone.qq.com/cgi-bin/likes/internal_dolike_app?qzonetoken=' . $this->getToken() . '&g_tk=' . $this->gtk2;
        $ua = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
        $get = $this->get_curl($url, $post, 'https://user.qzone.qq.com/' . $this->uin, $this->cookie, 0, $ua);
        preg_match('/callback\((.*?)\)\;/is', $get, $json);
        if ($json = $json[1]) {
            $arr = $this->parseJson($json);
            if ($arr['message'] == 'succ' || $arr['msg'] == 'succ') {
                $msg[] = '点赞 ' . $nickname . '(' . $uin . ') 的说说 ' . $con . ' 成功';
            } elseif ($arr['code'] == -3000) {
                $this->setStatus();
                $msg[] = '点赞 ' . $nickname . '(' . $uin . ') 的说说失败！原因:SKEY已失效';
            } elseif (@array_key_exists('message', $arr)) {
                $msg[] = '点赞 ' . $nickname . '(' . $uin . ') 的说说失败！原因:' . $arr['message'];
            } else {
                $msg[] = '点赞 ' . $nickname . '(' . $uin . ') 的说说失败！原因:' . $json;
            }
        } else {
            $msg[] = '智能检测跳过，' . $nickname . ' (' . $uin . ')的说说可能存在异常，请手动核实';
        }
        $this->setMessage($msg);
    }


    /**
     * 触屏协议 点赞
     * @param $uin
     * @param $appid
     * @param $unikey
     * @param $curkey
     * @access public
     */
    public function cp($uin, $appid, $unikey, $curkey)
    {
        $post = 'opuin=' . $uin . '&unikey=' . $unikey . '&curkey=' . $curkey . '&appid=' . $appid . '&opr_type=like&format=purejson';
        $url = 'https://h5.qzone.qq.com/proxy/domain/w.qzone.qq.com/cgi-bin/likes/internal_dolike_app?qzonetoken=' . $this->getToken() . '&g_tk=' . $this->gtk2;
        $json = $this->get_curl($url, $post, 1, $this->cookie);
        if ($json) {
            $arr = $this->parseJson($json);
            if (@array_key_exists('ret', $arr) && $arr['ret'] == 0) {
                $msg[] = '赞 ' . $uin . ' 的说说成功';
            } elseif ($arr['ret'] == -3000) {
                $this->setStatus();
                $msg[] = '赞' . $uin . '的说说失败！原因:SKEY已失效';
            } elseif (@array_key_exists('msg', $arr)) {
                $msg[] = '赞 ' . $uin . ' 的说说失败！原因:' . $arr['msg'];
            } else {
                $msg[] = '赞 ' . $uin . ' 的说说失败！原因:' . $json;
            }
        } else {
            $msg[] = '获取赞' . $uin . '的说说结果失败！';
        }
        $this->setMessage($msg);
    }
}