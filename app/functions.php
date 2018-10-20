<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * ------------Oooo---
 * -----------(----)---
 * ------------)--/----
 * ------------(_/-
 * ----oooO----
 * ----(---)----
 * -----\--(--
 * ------\_)-
 * ----
 *     © 2018/8/22 Mex.
 *     author : Yprisoner
 *     email : yyprisoner@gmail.com
 *                            ------
 *    「 涙の雨が頬をたたくたびに美しく 」
 */

/**
 * 检测安装
 */
function check_install(){
    if (!file_exists(CACHE_PATH . 'install.lock')){
        redirect(get_url('install/index.php?go=1'));
    }
}

/**
 * 检测目录
 */
function check_dir(){
    if (!is_dir(CACHE_PATH)){
        mkdir(CACHE_PATH, 0777);
    }
    if (!is_dir(COOKIE_PATH)){
        mkdir(COOKIE_PATH, 0777);
    }
    if (!is_dir(LOG_PATH)){
        mkdir(LOG_PATH, 0777);
    }
    if (!is_dir(IMAGE_AUDIT_PATH)){
        mkdir(IMAGE_AUDIT_PATH, 0777);
    }
    if (!file_exists(PROHIBITION_IP_FILE)){
        file_put_contents(PROHIBITION_IP_FILE, 'BEGIN:' . PHP_EOL);
    }
}

/**
 * @param string $name
 * @param string $message
 * @param int $code
 *
 * Log
 * @throws Exception
 */
function exe_log(string $name = "", string $message = "", int $code = 0, $save_path = null){
    $str = '[code : ' . $code . '] [ 文件 : ' . $name . ' ] [ 信息 : 【 ' . $message . ' 】 ]  ' . date('Y-m-d H:i:s') . PHP_EOL;
    if (!empty($save_path)){
        file_put_contents($save_path . date('Y-m-d') . '.log', $str);
    }else{
        file_put_contents(LOG_PATH . date('Y-m-d') . '.log', $str);
    }
}

/**
 * @param $url
 * @return string
 *
 * 返回 url
 */
function get_url($url){
    $port = $_SERVER['SERVER_PORT'] == 80 ? '' : ':' . $_SERVER['SERVER_PORT'];
    $http = HTTPS ? 'https://' : 'http://';
    return $http . $_SERVER['HTTP_HOST'] . $port . '/' . $url;
}

/**
 * @param $url
 *
 * 跳转
 */
function redirect($url = '/'){
    header('Location:' . $url);
    exit();
}

/**
 * @return array
 *
 * 生成唯一标识符
 */
function get_key() : string
{
    return md5(uniqid(md5('md5' . microtime(true)), true));
}

/**
 * @param array $data
 * 返回 json
 */
function return_json(array $data = [], bool $token = true)
{
    header('Content-Type:application/json');
    if ($token){
        $data['token']  =   get_token();
    }
    echo json_encode($data);
    exit();
}

/**
 * @param $filename
 *
 * 静态文件
 */
function asset(string $filename)
{
    echo get_url('assets/' . $filename);
}

/***
 * @return mixed
 *
 * make Token
 */
function token()
{
    echo get_token();
}

/**
 * @return mixed
 *
 */
function get_token()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($i = 0; $i < 10; $i++) {
        $str .= $characters[rand(0, strlen($characters) - 1)];
    }
    $str .= date('Y-m-d HH:ii:ss', time()) . microtime();
    $token = str_replace('=', '', base64_encode(strval(md5($str)) . md5($str)));
    $_SESSION['token'] = $token;
    return $token;
}

/**
 * @param $value
 * @return bool
 *
 * 检测 Token
 */
function check_token($value): bool
{
    if ($_SESSION['token'] == $value)
        return true;
    return false;
}

/**
 * @return float
 *
 * 返回时间戳
 */
function get_timestamp() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}

/**
 * @param $class
 * 自动加载类
 * @return bool
 */
function load($class)
{
    $class = str_replace('\\', '/', $class) . '.php';
    if (is_file(ROOT_PATH . $class)) {
        require ROOT_PATH . $class;
    } else {
        return false;
    }
}

/**
 * [asyncExecute PHP异步执行任务]
 * @param  string $url 执行任务的url地址
 * @param  array $post_data 需要post提交的数据POST
 * @param  array $cookie cookie数据用于登录等的设置
 * @return bool
 */
function asyncExecute($url, $post_data = array(), $cookie = array()): bool
{
    $method = "GET";
    $url_array = parse_url($url);
    $port = isset($url_array['port']) ? $url_array['port'] : 80;
    $fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30);
    if (!$fp) {
        return false;
    }
    $getPath = isset($url_array['path']) ? $url_array['path'] : '/';
    if (isset($url_array['query'])) {
        $getPath .= "?" . $url_array['query'];
    }
    if (!empty($post_data)) {
        $method = "POST";
    }
    $header = $method . " /" . $getPath;
    $header .= " HTTP/1.1\r\n";
    $header .= "Host: " . $url_array['host'] . "\r\n";

    $header .= "Connection: Close\r\n";
    if (!empty($cookie)) {
        $_cookie = strval(NULL);
        foreach ($cookie as $k => $v) {
            $_cookie .= $k . "=" . $v . "; ";
        }
        $cookie_str = "Cookie: " . base64_encode($_cookie) . " \r\n";
        $header .= $cookie_str;
    }
    if (!empty($post_data)) {
        $_post = strval(NULL);
        $atComma = '';
        foreach ($post_data as $k => $v) {
            $_post .= $atComma . $k . "=" . $v;
            $atComma = '&';
        }
        $post_str = "Content-Type: application/x-www-form-urlencoded\r\n";
        $post_str .= "Content-Length: " . strlen($_post) . "\r\n";
        $post_str .= "\r\n" . $_post . "\r\n";
        $header .= $post_str;
    }
    $header .= "\r\n";
    stream_set_blocking($fp, true);
    stream_set_timeout($fp, 1);
    fwrite($fp, $header);

    /*获取执行结果*/
//    $resp_str = '';
//    while(!feof($fp)){
//        $resp_str .= fgets($fp,512);
//    }
//    file_put_contents(CACHE_PATH . 'asyncExecute', $resp_str);

    usleep(1000);
    fclose($fp);
    return true;
}

/**
 * @return array|false|string
 *
 * 获取用户IP
 */
function get_client_ip(){
    static $realip;
    if(isset($_SERVER)){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $realip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
            $realip=$_SERVER['HTTP_CLIENT_IP'];
        }else{
            $realip=$_SERVER['REMOTE_ADDR'];
        }
    }else{
        if(getenv('HTTP_X_FORWARDED_FOR')){
            $realip=getenv('HTTP_X_FORWARDED_FOR');
        }else if(getenv('HTTP_CLIENT_IP')){
            $realip=getenv('HTTP_CLIENT_IP');
        }else{
            $realip=getenv('REMOTE_ADDR');
        }
    }
    return $realip;
}

/**
 * 检测用户IP
 */
function check_client_ip(){
    $client_ip = get_client_ip();
    $ban = file_get_contents(PROHIBITION_IP_FILE);
    if(stripos($ban, $client_ip))
    {
        $GLOBALS['check_client_ip'] = false;
    }
}

/**
 * 封禁该用户IP
 */
function prohibition_client_ip(){
    $client_ip = get_client_ip();
    $content = file_get_contents(PROHIBITION_IP_FILE);
    file_put_contents(PROHIBITION_IP_FILE, $content . PHP_EOL . $client_ip);
}

/**
 * @param string $message
 * @param string $exponent
 * @param string $pubkey
 * @return string
 *
 * 微博登录密码加密
 */
function rsa_encrypt(string $message, string $exponent, string $pubkey): string
{
    openssl_public_encrypt($message, $result, rsa_pkey(hex2bin($exponent), hex2bin($pubkey)), OPENSSL_PKCS1_PADDING);
    return $result;
}
function asn1_length(int $length): string
{
    if ($length <= 0x7f) {
        return chr($length);
    }

    $tmp = ltrim(pack('N', $length), chr(0));
    return pack('Ca*', 0x80 | strlen($tmp), $tmp);
}
function rsa_pkey(string $exponent, string $modulus): string
{
    $pkey = pack('Ca*a*', 0x02, asn1_length(strlen($modulus)), $modulus)
        . pack('Ca*a*', 0x02, asn1_length(strlen($exponent)), $exponent);

    $pkey = pack('Ca*a*', 0x30, asn1_length(strlen($pkey)), $pkey);
    $pkey = pack('Ca*', 0x00, $pkey);
    $pkey = pack('Ca*a*', 0x03, asn1_length(strlen($pkey)), $pkey);
    $pkey = pack('H*', '300d06092a864886f70d0101010500') . $pkey;
    $pkey = pack('Ca*a*', 0x30, asn1_length(strlen($pkey)), $pkey);

    return "-----BEGIN PUBLIC KEY-----\r\n" . chunk_split(base64_encode($pkey)) . '-----END PUBLIC KEY-----';
}