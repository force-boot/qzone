<?php

namespace qzonefunc;

/**
 * Qzone 说说转发功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Forward extends Qzone
{
    /**
     * 运行任务
     * @param int $do
     * @param array $uins
     * @param null $con
     * @access public
     */
    public function run($do = 0, $uins = array(), $con = null)
    {
        $myshuoshuo = array();
        if ($shuos = $this->getMyNew()) {
            foreach ($shuos as $shuo) {
                if (array_key_exists('original', $shuo)) {
                    $myshuoshuo[] = $shuo['original']['cell_id']['cellid'];
                }
            }
        }
        if (count($uins) == 1 && $uins[0] != '') {
            $uin = $uins[0];
            if ($shuos = $this->getMyNew($uin)) {
                $i = 0;
                foreach ($shuos as $shuo) {
                    $cellid = $shuo['id']['cellid'];
                    if (in_array($cellid, $myshuoshuo)) break;
                    if ($do) {
                        $this->pc($con, $uin, $cellid);
                        if ($this->skeyStatus) break;
                    } else {
                        $this->cp($con, $uin, $cellid);
                        if ($this->skeyStatus) break;
                    }
                    ++$i;
                    if ($i >= 3) break;
                    usleep(100000);
                }
            }
        } elseif (count($uins) > 1) {
            if ($shuos = $this->getNew()) {
                foreach ($shuos as $shuo) {
                    $uin = $shuo['userinfo']['user']['uin'];
                    if (in_array($uin, $uins)) {
                        $cellid = $shuo['id']['cellid'];
                        if (in_array($cellid, $myshuoshuo)) break;
                        if ($do) {
                            $this->pc($con, $uin, $cellid);
                            if ($this->skeyStatus) break;
                        } else {
                            $this->cp($con, $uin, $cellid);
                            if ($this->skeyStatus) break;
                        }
                    }
                }
            }
        } else {
            if ($shuos = $this->getnew()) {
                foreach ($shuos as $shuo) {
                    $uin = $shuo['userinfo']['user']['uin'];
                    $cellid = $shuo['id']['cellid'];
                    if (in_array($cellid, $myshuoshuo)) break;
                    if ($do) {
                        $this->pc($con, $uin, $cellid);
                        if ($this->skeyStatus) break;
                    } else {
                        $this->cp($con, $uin, $cellid);
                        if ($this->skeyStatus) break;
                    }
                }
            }
        }
    }

    /**
     * pc协议 转发说说
     * @param $con
     * @param $touin
     * @param $tid
     * @access public
     */
    public function pc($con, $touin, $tid)
    {
        $url = 'https://user.qzone.qq.com/proxy/domain/taotao.qzone.qq.com/cgi-bin/emotion_cgi_forward_v6?qzonetoken=' . $this->getToken('https://user.qzone.qq.com/' . $this->uin . '/311', 1) . '&g_tk=' . $this->gtk2;
        $post = 'tid=' . $tid . '&t1_source=1&t1_uin=' . $touin . '&signin=0&con=' . urlencode($con) . '&with_cmt=0&fwdToWeibo=0&forward_source=2&code_version=1&format=json&out_charset=UTF-8&hostuin=' . $this->uin . '&qzreferrer=https%3A%2F%2Fuser.qzone.qq.com%2F' . $this->uin . '%2Finfocenter';
        $json = $this->get_curl($url, $post, 'https://user.qzone.qq.com/' . $this->uin . '/infocenter', $this->cookie);
        if ($json) {
            $arr = $this->parseJson($json);
            if (array_key_exists('code', $arr) && $arr['code'] == 0) {
                $this->shuotid = $arr['tid'];
                $msg[] = $this->uin . '转发 ' . $touin . ' 说说成功';
            } elseif ($arr['code'] == -3000) {
                $this->setStatus();
                $msg[] = $this->uin . '转发 ' . $touin . ' 说说失败！原因:' . $arr['message'];
            } elseif (array_key_exists('code', $arr)) {
                $msg[] = $this->uin . '转发 ' . $touin . ' 说说失败！原因:' . $arr['message'];
            } else {
                $msg[] = $this->uin . '转发 ' . $touin . ' 说说失败！原因' . $json;
            }
        } else {
            $msg[] = $this->uin . '获取转发 ' . $touin . ' 说说结果失败';
        }
        $this->setMessage($msg);
    }

    /**
     * 触屏协议 转发说说
     * @param $con
     * @param $touin
     * @param $tid
     * @access public
     */
    public function cp($con, $touin, $tid)
    {
        $url = 'https://mobile.qzone.qq.com/operation/operation_add?g_tk=' . $this->gtk2 . '&qzonetoken=' . $this->getToken();
        $post = 'res_id=' . $tid . '&res_uin=' . $touin . '&format=json&reason=' . urlencode($con) . '&res_type=311&opr_type=forward&operate=1';
        $json = $this->get_curl($url, $post, 1, $this->cookie);
        if ($json) {
            $arr = $this->parseJson($json);
            if (array_key_exists('code', $arr) && $arr['code'] == 0) {
                $msg[] = $this->uin . '转发 ' . $touin . ' 说说成功';
            } elseif ($arr['code'] == -3000) {
                $this->setStatus();
                $msg[] = $this->uin . '转发 ' . $touin . ' 说说失败！原因:' . $arr['message'];
            } else {
                $msg[] = $this->uin . '转发 ' . $touin . ' 说说失败！原因:' . $arr['message'];
            }
        } else {
            $msg[] = $this->uin . '获取转发 ' . $touin . ' 说说结果失败';
        }
        $this->setMessage($msg);
    }
}