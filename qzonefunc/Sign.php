<?php

namespace qzonefunc;

/**
 * Qzone 签到功能类
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Sign extends Qzone
{
    /**
     * 运行功能
     * @param int $do
     * @param string $content
     * @param int $sealid
     * @access public
     */
    public function run($do = 0, $content = '签到', $sealid = 50001)
    {
        $html = '<!DOCTYPE html><html style="background:transparent;"><head><meta charset="UTF-8"><link rel="stylesheet" type="text/css" href="http://qzonestyle.gtimg.cn/touch/proj-qzone-app/checkin-pc/index.css"></head><body style="background:transparent;"><div class="checkIn-days" style="background: transparent;"><div class="template-area screenshot style-thirteen"><div class="pic-area j-pic-area"><div class="upload-pic j-upload-pic"><img src="https://qzonestyle.gtimg.cn/qzone/qzactStatics/imgs/20180808144259_db402a.jpg"></div> <div class="operate-area j-operate-area" style="background-image: url(&quot;data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7&quot;);"></div> <div class="camera-wrap"><i class="icon icon-camera"></i></div> <div class="date-area"><div class="week week-1"><img src="https://qzonestyle.gtimg.cn/touch/proj-qzone-app/checkin-2017/img/number/style-thirteen/week-1.png" class="week1-img"></div> <div class="date-inner"><div class="day">' . date('d') . '</div> <div class="month">' . date("F") . '</div></div></div></div> <div class="word-area j-word-area edit-state"><div class="word-main"><p class="word j-word">' . $content . '</p> <textarea class="wordTextarea" style="overflow: hidden;">' . $content . '</textarea><textarea class="wordTextarea" style="overflow: hidden; visibility: hidden; position: absolute;"></textarea></div> <div class="word-side j-edit-btn"><button class="btn-refresh"><i class="icon-refresh"></i></button></div></div></div></div></body></html>';
        $post = json_encode(array('html' => $html, 'viewport' => array('width' => 820, 'height' => 820), 'type' => 'png', 'cache' => true));
        $url = 'https://h5.qzone.qq.com/services/picGenerator?cmd=stringToUrl&g_tk=' . $this->gtk2;
        $json = $this->get_curl($url, $post, 'https://h5.qzone.qq.com/checkinv2/editor?type=daily', $this->cookie);
        $arr = $this->parseJson($json);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $url = 'https://h5.qzone.qq.com/webapp/json/publishDiyMood/publishmood?g_tk=' . $this->gtk2 . '&qzonetoken=' . $this->getToken('https://h5.qzone.qq.com/checkinv2/editor?type=daily');
            $richval = 'aurl=' . urlencode($arr['picUrl']['sOriUrl']) . '&s_width=400&s_height=300&murl=' . urlencode($arr['picUrl']['sOriUrl']) . '&m_width=600&m_length=450&burl=' . urlencode($arr['picUrl']['sOriUrl']) . '&b_width=800&b_length=600&pic_type=10&templateId=1&who=2';
            $post = 'uin=' . $this->uin . '&content=&issynctoweibo=0&isWinPhone=2&richtype=1&richval=' . urlencode($richval) . '&sourceName=&frames=10&source.subtype=33&extend_info.checkinfall=%7B%22uuid%22%3A%22' . time() . '123%22%7D&right_info.ugc_right=1&stored_extend_info.is_diy=1&stored_extend_info.event_tags=&stored_extend_info.pic_jump_type=0&stored_extend_info.signin_group_id=1&stored_extend_info.signin_seal_id=285&stored_extend_info.signin_op=1&format=json&inCharset=utf-8&outCharset=utf-8';
            $json = $this->get_curl($url, $post, 'https://h5.qzone.qq.com/checkinv2/editor?type=daily', $this->cookie);
            $arr = $this->parseJson($json);
            if (@array_key_exists('ret', $arr) && $arr['ret'] == 0) {
                $msg[] = $this->uin . ' 签到成功';
            } elseif ($arr['ret'] == -3000) {
                $this->setStatus();
                $msg[] = $this->uin . ' 签到失败！原因:SID已失效，请更新SID';
            } elseif ($arr['code'] == -3001) {
                $msg[] = $this->uin . ' 签到失败！原因:需要验证码';
            } elseif (@array_key_exists('ret', $arr)) {
                $msg[] = $this->uin . ' 签到失败！原因:' . $arr['msg'];
            } else {
                $msg[] = $this->uin . ' 签到失败！原因:' . $json;
            }
        } else {
            $msg[] = $this->uin . ' 生成签到图片失败！' . $arr['msg'];
        }
        $this->setMessage($msg);
    }
}