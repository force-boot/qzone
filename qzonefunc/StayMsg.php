<?php

namespace qzonefunc;

/**
 * qzone 留言操作类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class StayMsg extends Qzone
{
    /**
     * @var int 删除锁，值为1 说明没有可删除的留言
     */
    private $delLock;

    /**
     * 删除留言
     * @param int $do
     * @access public
     */
    public function delete($do = 0)
    {
        if ($liuyans = $this->getStayMessage()) {
            $idList = '';
            $uinList = '';
            foreach ($liuyans as $row) {
                $idList .= $row['id'] . ',';
                $uinList .= $row['uin'] . ',';
                $exists = true;
            }
            if ($exists) {
                $idList = trim($idList, ',');
                $uinList = trim($uinList, ',');
                $this->pcDelete($idList, $uinList);
            } else {
                $this->delLock = 1;
            }
        }
    }

    /**
     * 发送留言（刷留言）
     * @param $uin int 被发送留言的QQ
     * @param $con string 内容
     * @param int $do 默认1，触屏协议
     * @access public
     */
    public function send($uin, $con, $do = 1)
    {
        if ($do == 1) {
            $this->cpSend($uin, $con);
        } else {
            $this->pcSend($uin, $con);
        }
    }

    /**
     * pc协议 删除留言
     * @param $idList
     * @param $uinList
     * @access public
     */
    public function pcDelete($idList, $uinList)
    {
        $url = "https://h5.qzone.qq.com/proxy/domain/m.qzone.qq.com/cgi-bin/new/del_msgb?g_tk=" . $this->gtk2;
        $post = "qzreferrer=https%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fmsgboard%2Fmsgbcanvas.html%23page%3D1&hostUin=" . $this->uin . "&idList=" . urlencode($idList) . "&uinList=" . urlencode($uinList) . "&format=json&iNotice=1&inCharset=utf-8&outCharset=utf-8&ref=qzone&json=1&g_tk=" . $this->gtk2;
        $data = $this->get_curl($url, $post, 'https://ctc.qzs.qq.com/qzone/msgboard/msgbcanvas.html', $this->cookie);
        $arr = $this->parseJson($data);
        if ($arr) {
            if (array_key_exists('code', $arr) && $arr['code'] == 0) {
                $msg[] = '删除 留言成功！' . $arr['message'];
            } elseif ($arr['code'] == -3000) {
                $this->setStatus();
                $msg[] = '删除  留言失败！原因:' . $arr['message'];
            } elseif (array_key_exists('code', $arr)) {
                $msg[] = '删除  留言失败！' . $arr['message'];
            }
        } else {
            $msg[] = "未知错误，删除失败！";
        }
    }

    /**
     * pc协议发送留言
     * @param $uin
     * @param $con
     * @access
     */
    public function pcSend($uin, $con)
    {
        $url = 'http://h5.qzone.qq.com/proxy/domain/m.qzone.qq.com/cgi-bin/new/add_msgb?qzonetoken=' . $this->getToken('https://user.qzone.qq.com/' . $this->uin . '/311', 1) . '&g_tk=' . $this->gtk2;
        $post = 'qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fmsgboard%2Fmsgbcanvas.html%23page%3D1&content=' . urlencode($con) . '&hostUin=' . $uin . '&uin=' . $this->uin . '&format=json&inCharset=utf-8&outCharset=utf-8&iNotice=1&ref=qzone&json=1&g_tk=' . $this->gtk;
        $json = $this->get_curl($url, $post, 'http://cnc.qzs.qq.com/qzone/msgboard/msgbcanvas.html', $this->cookie);
        $arr = $this->parseJson($json);
        if (array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = $this->uin . ' 为 ' . $uin . ' 刷留言成功';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = $this->uin . ' 为 ' . $uin . ' 刷留言失败！原因:' . $arr['message'];
        } else {
            $msg[] = $this->uin . ' 为 ' . $uin . ' 刷留言失败！原因:' . $arr['message'];
        }
        $this->setMessage($msg);
    }

    /**
     * 触屏协议发送留言
     * @param $uin
     * @param $con
     * @access public
     */
    public function cpSend($uin, $con)
    {
        $url = "http://mobile.qzone.qq.com/msgb/fcg_add_msg?g_tk=" . $this->gtk2;
        $post = "res_uin={$uin}&format=json&content=" . urlencode($con) . "&opr_type=add_comment";
        $json = $this->get_curl($url, $post, 1, $this->cookie);
        $arr = $this->parseJson($json);
        if (array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = $this->uin . ' 为 ' . $uin . ' 刷留言成功';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = $this->uin . ' 为 ' . $uin . ' 刷留言失败！原因:' . $arr['message'];
        } elseif ($arr['code'] == -4017) {
            $msg[] = $this->uin . ' 为 ' . $uin . ' 刷留言成功，留言内容将在你审核后展示';
        } else {
            $msg[] = $this->uin . ' 为 ' . $uin . ' 刷留言失败！原因:' . $arr['message'];
        }
        $this->setMessage($msg);
    }

    /**
     * 获取留言列表
     * @access
     * @return mixed
     */
    public function getStayMessage()
    {
        $ua = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0';
        $url = 'https://user.qzone.qq.com/proxy/domain/m.qzone.qq.com/cgi-bin/new/get_msgb?uin=' . $this->uin . '&hostUin=' . $this->uin . '&start=0&s=0.860240' . time() . '&format=json&num=10&inCharset=utf-8&outCharset=utf-8&g_tk=' . $this->gtk2;
        $json = $this->get_curl($url, 0, 'http://user.qzone.qq.com/', $this->cookie, 0, $ua);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $this->setMessage('获取留言列表成功！');
            return $arr['data']['commentList'];
        } else {
            $this->setMessage('获取留言列表失败！');
        }
    }
}