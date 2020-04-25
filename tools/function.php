<?php
//辅助函数库
define('TOOLS_PATH', APP_PATH . 'tools' . DS);

function curl_get($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn; R815T Build/JOP40D) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $content = curl_exec($ch);
    curl_close($ch);
    return ($content);
}

/**
 * 获取随机评论内容
 * @return string
 */
function getReplyCentent()
{
    $str = @file_get_contents(TOOLS_PATH . 'sji.db');
    return $str;
}

/**
 * randstr
 * @return mixed
 * @author tgyd_Team
 */
function randstr()
{
    $str = @file_get_contents(TOOLS_PATH . 'ss.txt');
    $str = explode("\n", $str);
    $content = $str[array_rand($str, 1)];
    return $content;
}


/**
 * 获取一张随机图片
 * @return string
 */
function randimg()
{
    $row = @file_get_contents(TOOLS_PATH . 'pic.txt');
    $row = explode("\n", $row);
    shuffle($row);
    $pic = $row[0];
    $pic = trim($pic);
    return $pic;
}

/**
 * 获取说说内容 ，根据mode返回
 * @param $mode
 * @param $content
 * @return string
 */
function getShuoContent($mode, $content)
{
    switch ($mode) {
        case 1:
            $contents = explode('|', $content);
            $content = $contents[array_rand($contents, 1)];
            break;
        case 2:
            $content = hitokoto();
            break;
        case 3:
            $content = hitokoto2('g');
            break;
        case 4:
            $content = hitokoto2('f');
            break;
        case 5:
            $content = hitokoto2('d');
            break;
        case 6:
            $content = hitokoto2('e');
            break;
        case 7:
            $content = hitokoto2('a');
            break;
        case 8:
            $content = hitokoto2('c');
            break;
        case 9:
            $content = hitokoto2('b');
            break;
        case 10:
            $content = "[em]e" . rand(100, 204) . "[/em]";
            break;
        case 11:
            $content = date("Y-m-d H:i:s");
            break;
        case 12:
            $content = comments_163();
            break;
    }
    return $content;
}

function hitokoto()
{
    $url = 'https://v1.hitokoto.cn/?encode=json'; // 不限定内容类型
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    $response = curl_exec($ch);
    if ($error = curl_error($ch)) {
        return randstr(); // 如果6s内，一言API调用失败则输出这个默认句子~
    }
    curl_close($ch);
    $array_data = json_decode($response, true);
    $lxtx_content = $array_data['hitokoto']; // 输出格式：经典语句----《语句出处》
    return $lxtx_content;
}

function comments_163()
{
    $str = curl_get('https://api.uomg.com/api/comments.163?format=text');
    return $str;
}

function hitokoto2($t)
{
    $url = 'https://v1.hitokoto.cn/?encode=json?c=' . $t; // 限定内容类型
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    $response = curl_exec($ch);
    if ($error = curl_error($ch)) {
        return randstr(); // 如果6s内，一言API调用失败则输出这个默认句子~
    }
    curl_close($ch);
    $array_data = json_decode($response, true);
    $lxtx_content = $array_data['hitokoto']; // 输出格式：经典语句----《语句出处》
    return $lxtx_content;
}
