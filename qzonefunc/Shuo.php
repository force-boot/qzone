<?php

namespace qzonefunc;

/**
 * Qzone 说说功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Shuo extends Qzone
{
    /**
     * 发表说说
     * @param int $do
     * @param $content
     * @param null $image
     * @param string $sname
     * @param bool $delete
     * @access public
     */
    public function send($do = 0, $content, $image = null, $sname = '', $delete = false)
    {
        if ($delete && $shuos = $this->getMyNew(null, 1)) {
            $cellid = $shuos[0]['id']['cellid'];
            $this->pcDelete($cellid);
        }
        if (!empty($image) && $pic = $this->openu($image)) {
            $image_size = getimagesize($image);
            $richval = $this->uploadimg($pic, $image_size);
        } else {
            $richval = null;
        }
        if ($do) {
            $this->pcSend($content, $richval);
        } else {
            $this->cpSend($content, $richval, $sname);
        }
    }

    /**
     * 删除说说
     * @access public
     */
    public function delete()
    {
        if ($shuos = $this->getMyNew()) {
            $i = 0;
            foreach ($shuos as $shuo) {
                $cellid = $shuo['id']['cellid'];
                $this->pcDelete($cellid);
                if ($this->skeyStatus) break;
                ++$i;
                if ($i >= 10) break;
            }
        }
    }

    /**
     * 触屏协议发表
     * @param $content
     * @param string $richval
     * @param string $sname
     * @param string $lon
     * @param string $lat
     * @access public
     */
    public function cpSend($content, $richval = '', $sname = '', $lon = '', $lat = '')
    {
        $url = 'https://mobile.qzone.qq.com/mood/publish_mood?qzonetoken=' . $this->getToken() . '&g_tk=' . $this->gtk2;
        $post = 'opr_type=publish_shuoshuo&res_uin=' . $this->uin . '&content=' . urlencode($content) . '&richval=' . $richval . '&lat=' . $lat . '&lon=' . $lon . '&lbsid=&issyncweibo=0&is_winphone=2&format=json&source_name=' . $sname;
        $result = $this->get_curl($url, $post, 1, $this->cookie);
        $arr = $this->parseJson($result);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = $this->uin . ' 发布说说成功';
        } elseif ($arr['code'] == -3000) {
           $this->setStatus();
            $msg[] = $this->uin . ' 发布说说失败！原因:SID已失效，请更新SID';
        } elseif ($arr['code'] == -3001) {
            $msg[] = $this->uin . ' 发布说说失败！原因:需要验证码';
        } elseif (@array_key_exists('code', $arr)) {
            $msg[] = $this->uin . ' 发布说说失败！原因:' . $arr['message'];
        } else {
            $msg[] = $this->uin . ' 发布说说失败！原因:' . $result;
        }
        $this->setMessage($msg);
    }

    /**
     * PC协议发表
     * @param $content
     * @param int $richval
     * @access public
     */
    public function pcSend($content, $richval = 0)
    {
        $url = 'https://user.qzone.qq.com/proxy/domain/taotao.qzone.qq.com/cgi-bin/emotion_cgi_publish_v6?g_tk=' . $this->gtk2 . '&qzonetoken=' . $this->getToken('https://user.qzone.qq.com/' . $this->uin . '/311', 1);
        $post = 'syn_tweet_verson=1&paramstr=1&pic_template=';
        if ($richval) {
            $post .= "&richtype=1&richval=" . $this->uin . ",{$richval}&special_url=&subrichtype=1&pic_bo=uAE6AQAAAAABAKU!%09uAE6AQAAAAABAKU!";
        } else {
            $post .= "&richtype=&richval=&special_url=";
        }
        $post .= "&subrichtype=&con=" . urlencode($content) . "&feedversion=1&ver=1&ugc_right=1&to_tweet=0&to_sign=0&hostuin=" . $this->uin . "&code_version=1&format=json&qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F" . $this->uin . "%2F311";
        $json = $this->get_curl($url, $post, 'https://user.qzone.qq.com/' . $this->uin . '/311', $this->cookie);
        $arr = $this->parseJson($json);
        $arr['feedinfo'] = '';
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $msg[] = $this->uin . ' 发布说说成功';
        } elseif ($arr['code'] == -3000) {
            $this->setStatus();
            $msg[] = $this->uin . ' 发布说说失败！原因:SID已失效，请更新SID';
        } elseif ($arr['code'] == -3001) {
            $msg[] = $this->uin . ' 发布说说失败！原因:需要验证码';
        } elseif ($arr['code'] == -10045) {
            $msg[] = $this->uin . ' 发布说说失败！原因:' . $arr['message'];
        } elseif (@array_key_exists('code', $json)) {
            $msg[] = $this->uin . ' 发布说说失败！原因:' . $arr['message'];
        } else {
            $msg[] = $this->uin . ' 获取发布说说结果失败';
        }
        $this->setMessage($msg);
    }

    /**
     * pc协议删除说说
     * @param $cellid
     * @access public
     */
    public function pcDelete($cellid)
    {
        if (strlen($cellid) == 10) {
            $url = 'https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzsharedelete?g_tk=' . $this->gtk2;
            $post = 'notice=1&fupdate=1&platform=qzone&format=json&token=' . $this->gtk2 . '&owneruin=' . $this->uin . '&itemid=' . $cellid . '&entryuin=' . $this->uin . '&ugcPlatform=300&qzreferrer=https%3A%2F%2Fsns.qzone.qq.com%2Fcgi-bin%2Fqzshare%2Fcgi_qzsharegetmylistbytype%3Fuin%3D' . $this->uin;
        } else {
            $url = 'https://user.qzone.qq.com/proxy/domain/taotao.qzone.qq.com/cgi-bin/emotion_cgi_delete_v6?g_tk=' . $this->gtk2;
            $post = 'hostuin=' . $this->uin . '&tid=' . $cellid . '&t1_source=1&code_version=1&format=json&qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F' . $this->uin . '%2F311';
        }
        $json = $this->get_curl($url, $post, 'https://user.qzone.qq.com/' . $this->uin . '/311', $this->cookie);
        if ($json) {
            $arr = $this->parseJson($json);
            if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
                $msg[] = '删除说说' . $cellid . '成功';
            } elseif ($arr['code'] == -3000) {
                $this->setStatus();
                $msg[] = '删除说说' . $cellid . '失败！原因:SKEY已失效';
            } elseif (@array_key_exists('code', $json)) {
                $msg[] = '删除说说' . $cellid . '失败！原因:' . $arr['message'];
            } else {
                $msg[] = '删除说说' . $cellid . '失败！原因:' . $json;
            }
        } else {
            $msg[] = $this->uin . '获取删除结果失败';
        }
        $this->setMessage($msg);
    }

    /**
     * 上传图片
     * @param $image
     * @param array $image_size
     * @access public
     * @return string|void
     */
    public function uploadimg($image, $image_size = array())
    {
        $url = 'https://mobile.qzone.qq.com/up/cgi-bin/upload/cgi_upload_pic_v2?qzonetoken=' . $this->getToken() . '&g_tk=' . $this->gtk2;
        $post = 'picture=' . urlencode(base64_encode($image)) . '&base64=1&hd_height=' . $image_size[1] . '&hd_width=' . $image_size[0] . '&hd_quality=90&output_type=json&preupload=1&charset=utf-8&output_charset=utf-8&logintype=sid&Exif_CameraMaker=&Exif_CameraModel=&Exif_Time=&uin=' . $this->uin;
        $data = preg_replace("/\s/", "", $this->get_curl($url, $post, 1, $this->cookie, 0, 1));
        preg_match('/_Callback\((.*)\);/', $data, $arr);
        $data = $this->parseJson($arr[1]);
        if ($data && array_key_exists('filemd5', $data)) {
            $this->setMessage('图片上传成功！');
            $post = 'output_type=json&preupload=2&md5=' . $data['filemd5'] . '&filelen=' . $data['filelen'] . '&batchid=' . time() . rand(100000, 999999) . '&currnum=0&uploadNum=1&uploadtime=' . time() . '&uploadtype=1&upload_hd=0&albumtype=7&big_style=1&op_src=15003&charset=utf-8&output_charset=utf-8&uin=' . $this->uin . '&logintype=sid&refer=shuoshuo';
            $img = preg_replace("/\s/", "", $this->get_curl($url, $post, 1, $this->cookie, 0, 1));
            preg_match('/_Callback\(\[(.*)\]\);/', $img, $arr);
            $data = json_decode($arr[1], true);
            if ($data && array_key_exists('picinfo', $data)) {
                if ($data['picinfo']['albumid'] != "") {
                    $this->setMessage('图片信息获取成功！');
                    return '' . $data['picinfo']['albumid'] . ',' . $data['picinfo']['lloc'] . ',' . $data['picinfo']['sloc'] . ',' . $data['picinfo']['type'] . ',' . $data['picinfo']['height'] . ',' . $data['picinfo']['width'] . ',,,';
                } else {
                    $this->setMessage('图片信息获取失败！');
                    return;
                }
            } else {
                $this->setMessage('图片信息获取失败！');
                return;
            }
        } else {
            $this->setMessage('图片上传失败！原因：' . $data['msg']);
            return;
        }
    }
}