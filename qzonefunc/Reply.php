<?php

namespace qzonefunc;

/**
 * Qzone 说说评论功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Reply extends Qzone
{

    /**
     * 运行功能
     * @param int $do
     * @param array $contents
     * @param array $forbid
     * @param array $only
     * @param int $sleep
     * @param null $image
     * @access public
     */
    public function run($do = 0, $contents = array(), $forbid = array(), $only = array(), $sleep = 0, $image = null)
    {
        if ($shuos = $this->getNew()) {
            $i = 0;
            $e = 0;
            foreach ($shuos as $shuo) {
                $uin = $shuo['userinfo']['user']['uin'];
                if ($this->is_comment($this->uin, $shuo['comment']['comments']) && !in_array($uin, $forbid)) {
                    $appid = $shuo['comm']['appid'];
                    if ($appid != 311) continue;
                    $from = $shuo['userinfo']['user']['from'];
                    $cellid = $shuo['id']['cellid'];
                    if ($only[0] != '' && !in_array($uin, $only)) continue;
                    $content = $contents[array_rand($contents, 1)];
                    if ($do) {
                        $this->pc($content, $uin, $cellid, $from, $image);
                    } else {
                        $param = $this->array_str($shuo['operation']['busi_param']);
                        $this->cp($content, $uin, $cellid, $appid, $param);
                    }
                    if ($this->skeyStatus) ++$e;
                    $this->skeyStatus = false;
                    ++$i;
                    if ($i >= 6) break;
                    if ($sleep) sleep($sleep);
                    else usleep(100000);
                }
            }
            if ($i == 0) $this->setMessage('没有要评论的说说');
        }
    }

    /**
     * 触屏协议 评论
     * @param $content
     * @param $uin
     * @param $cellid
     * @param $type
     * @param $param
     * @access public
     */
    public function cp($content, $uin, $cellid, $type, $param)
    {
        $post = 'res_id=' . $cellid . '&res_uin=' . $uin . '&format=json&res_type=' . $type . '&content=' . urlencode($content) . '&busi_param=' . $param . '&opr_type=addcomment';
        $url = 'https://mobile.qzone.qq.com/operation/publish_addcomment?qzonetoken=' . $this->getToken() . '&g_tk=' . $this->gtk2;
        $json = $this->get_curl($url, $post, 1, $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '评论 ' . $uin . ' 的说说成功';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '评论 ' . $uin . ' 的说说失败！原因:SID已失效，请更新SID';
        } elseif ($arr['code'] == -3001) {
            $msg[] = '评论 ' . $uin . ' 的说说失败！原因:需要验证码';
        } elseif (@array_key_exists('message', $arr)) {
            $msg[] = '评论 ' . $uin . ' 的说说失败！原因:' . $arr['message'];
        } else {
            $msg[] = '获取评论' . $uin . '的说说结果失败！';
        }
        $this->setMessage($msg);
    }

    /**
     * pc 协议秒评
     * @param $content
     * @param $uin
     * @param $cellid
     * @param $from
     * @param null $richval
     * @access public
     */
    public function pc($content, $uin, $cellid, $from, $richval = null)
    {
        $post = 'topicId=' . $uin . '_' . $cellid . '__' . $from . '&feedsType=100&inCharset=utf-8&outCharset=utf-8&plat=qzone&source=ic&hostUin=' . $uin . '&isSignIn=&platformid=52&uin=' . $this->uin . '&format=json&ref=feeds&content=' . urlencode($content);
        if ($richval) {
            $post .= '&richval=' . urlencode($richval) . '&richtype=1';
        } else {
            $post .= '&richval=&richtype=';
        }
        $post .= '&private=0&paramstr=1&qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F' . $this->uin;
        $url = 'https://h5.qzone.qq.com/proxy/domain/taotao.qzone.qq.com/cgi-bin/emotion_cgi_re_feeds?qzonetoken=' . $this->getToken('https://user.qzone.qq.com/' . $this->uin . '/311', 1) . '&g_tk=' . $this->gtk2;
        $ua = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
        $json = $this->get_curl($url, $post, 'https://user.qzone.qq.com/' . $this->uin, $this->cookie, 0, $ua);
        $arr = $this->parseJson($json);
        $arr['data']['feeds'] = '';
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = '评论 ' . $uin . ' 的说说成功';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = '评论 ' . $uin . ' 的说说失败！原因:SID已失效，请更新SID';
        } elseif ($arr['code'] == -3001) {
            $msg[] = '评论 ' . $uin . ' 的说说失败！原因:需要验证码';
        } elseif (@array_key_exists('message', $arr)) {
            $msg[] = '评论 ' . $uin . ' 的说说失败！原因:' . $arr['message'];
        } else {
            $msg[] = '获取评论' . $uin . '的说说结果失败！';
        }
        $this->setMessage($msg);
    }
}