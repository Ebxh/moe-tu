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
 *     © 2018/8/23 Mex.
 *     author : Yprisoner
 *     email : yyprisoner@gmail.com
 *                            ------
 *    「 涙の雨が頬をたたくたびに美しく 」
 */

namespace app\system\Client;


use app\system\Client;
use app\system\ClientExpand;
use app\system\ClientInterface;

/**
 * Class Weibo
 * @package app\system\Client
 *
 * Weibo
 */
class Weibo extends Client implements ClientInterface, ClientExpand
{

    private $name = 'Weibo';

    private $image_size = 'large';

    private $prelogin_url = 'https://login.sina.com.cn/sso/prelogin.php';

    private $login_url = 'https://login.sina.com.cn/sso/login.php';

    private $upload_url = 'http://picupload.service.weibo.com/interface/pic_upload.php';

    private $cookies = null;

    private $cookie_path = null;

    private $encode_username = null;

    private $encode_password = null;

    private $login_status = true;

    public function __construct(array $client_config = [], array $curl_config = [], $size)
    {
        $this->cookie_path = COOKIE_PATH . md5($this->name);

        $request_header = array(
            'Accept'            =>  '*/*',
            'Accept-Encoding'   =>  'gzip, deflate',
            'Accept-Language'   =>  'zh-CN,zh;q=0.9,en;q=0.8,ja;q=0.7',
            'Connection'        =>  'keep-alive',
            'Host'              =>  'login.sina.com.cn',
            'Referer'           =>  'http://login.sina.com.cn/',
            'User-Agent'        =>  'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'

        );

        parent::__construct($client_config, array(
            CURLOPT_COOKIEJAR   => $this->cookie_path,
            CURLOPT_COOKIEFILE  => $this->cookie_path,
            CURLOPT_HTTPHEADER  =>  $request_header
        ), $size);

        if (!file_exists($this->cookie_path)){
            file_put_contents($this->cookie_path, '');
        }
        $this->cookies = file_get_contents($this->cookie_path);
        if (empty($this->cookies)){
            /*登录获取cookies*/
            if ($this->login() !== true){
                return '';
            }
            $this->cookies = file_get_contents($this->cookie_path);
        }
    }

    /**
     * @param string $key
     * 唯一 key
     * @param string $file
     * 临时文件路径
     * @return string
     * 上传成功 返回 图片链接; 上传失败 返回 空字符串
     * @throws \ErrorException
     * @throws \Exception
     */
    public function upload(string $key, string $file): string
    {
        $return_url = '';

        $this->upload_url = $this->upload_url . '?mime=image%2Fjpeg&data=base64&url=0&markpos=1&logo=&nick=0&marks=1&app=miniblog';

        $options = array(
            'b64_data' => base64_encode(file_get_contents($file))
        );

        $result = $this->http->curl_post( $this->upload_url, $options);

        if ($this->http->curl_status_code == 200){
            $result_arr = explode('</script>', $result);
            $json = trim($result_arr[1]);
            $data = json_decode($json, true)['data'];
            if ($data['count'] > 0){
                $image_pid = $data['pics']['pic_1']['pid'];
                $return_url = self::getImageUrl($image_pid, $this->image_size);
                $this->db->update_images($key, $return_url);
            }else{
                if ($data['count'] == -1 && $this->login_status){
                    /*重新登录 上传*/
                    $this->login();
                    $this->upload( $key,  $file );
                    $this->login_status = false;
                }
            }
        }

        return  $return_url;
    }

    /**
     * @return mixed
     *
     * 模拟登录 获取cookies
     * @throws \ErrorException
     * @throws \Exception
     */
    public function login()
    {
        $pre_return_data = $this->pre_login();
        if (!empty($pre_return_data)){

            $pwd_msg = "{$pre_return_data['servertime']}\t{$pre_return_data['nonce']}\n{$this->config['password']}";

            $this->encode_password = bin2hex( rsa_encrypt($pwd_msg, '010001', $pre_return_data['pubkey']) );

            $this->login_url = $this->login_url . '?client=ssologin.js(v1.4.15)&_=' . get_timestamp();

            $request_data = array(
                'entry'         =>  'account',
                'gateway'       =>  1,
                'from'          =>  '',
                'savestate'     =>  30,
                'useticket'     =>  0,
                'pagerefer'     =>  'https://www.google.com/',
                'vsnf'          =>  1,
                'su'            =>  $this->encode_username,
                'service'       =>  'service',
                'servertime'    =>  $pre_return_data['servertime'],
                'nonce'         =>  $pre_return_data['nonce'],
                'pwencode'      =>  'rsa2',
                'rsakv'         =>  $pre_return_data['rsakv'],
                'sp'            =>  $this->encode_password,
                'sr'            =>  '1920*1080',
                'encoding'      =>  'UTF-8',
                'cdult'         =>  '3',
                'domain'        =>  'sina.com.cn',
                'prelt'         =>  42,
                'returntype'    =>  'TEXT'
            );

            $result = $this->http->curl_post( $this->login_url, $request_data );
            if ($this->http->curl_status_code == 200){
                $data = json_decode($result, true);
                $crossDomainUrlList = $data['crossDomainUrlList'];
                // 访问连接 获取cookie
                foreach ($crossDomainUrlList as $k => $v){
                    $request_url =  $v . '&callback=sinaSSOController.doCrossDomainCallBack&scriptId=ssoscript' .
                                    $k . '&client=ssologin.js(v1.4.15)&_=' . get_timestamp();
                    $this->http->curl_get($request_url);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     *
     * 预登录
     *
     * 第一步: 获取pubkey/nonce/rsak等用于加密用户信息
     */
    private function pre_login()
    {
        $this->encode_username = base64_encode( urlencode( $this->config['username'] ) );

        $request_data = array(
            'entry'     =>  'account',
            'callback'  =>  'sinaSSOController.preloginCallBack',
            'su'        =>  $this->encode_username,
            'rsakt'     =>  'mod',
            'client'    =>  'ssologin.js(v1.4.15)',
            '_'         =>  get_timestamp()
        );

        $result_callback_val = $this->http->curl_get($this->prelogin_url, $request_data);
        if ($this->http->curl_status_code == 200){
            $result_callback_val = str_replace('sinaSSOController.preloginCallBack(','', trim($result_callback_val));
            $result_callback_val = str_replace(')','', trim($result_callback_val));
            $data = json_decode($result_callback_val, true);
            return $data;
        }
        return null;
    }

    /**
     * @param string $pid
     * @param string $size
     * @param bool $https
     * @return null|string|string[]
     * @throws \ErrorException
     *
     * 获取图片链接
     */
    private static function getImageUrl(string $pid, string $size = 'large', bool $https = true)
    {
        $imgUrl = '';
        $pid = trim($pid);

        // 传递 pid
        if (preg_match('/^[a-zA-Z0-9]{32}$/', $pid) === 1) {
            return ($https ? 'https' : 'http') . '://' . ($https ? 'ws' : 'ww')
                . ((crc32($pid) & 3) + 1) . ".sinaimg.cn/" . $size
                . "/$pid." . ($pid[21] === 'g' ? 'gif' : 'jpg');
        }

        // 传递 url
        $url = $pid;
        $imgUrl = preg_replace_callback('/^(https?:\/\/[a-z]{2}\d\.sinaimg\.cn\/)'
            . '(large|bmiddle|mw1024|mw690|small|square|thumb180|thumbnail)'
            . '(\/[a-z0-9]{32}\.(jpg|gif))$/i', function ($match) use ($size) {
            return $match[1] . $size . $match[3];
        }, $url, -1, $count);

        return $imgUrl;
    }

    /**
     * @return mixed
     *
     * 获取 token
     */
    public function get_auth_token()
    {
        // TODO: Implement get_auth_token() method.
    }
}