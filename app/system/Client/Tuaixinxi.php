<?php
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
 *     © 2018/8/24 Mex.
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
 * Class Tuaixinxi
 * @package app\system\Client
 *
 * https://tu.aixinxi.net
 */
class Tuaixinxi extends Client implements ClientInterface, ClientExpand
{

    private $name = 'Tuaixinxi';

    private $root = 'https://tu.aixinxi.net/';

    private $image_api = 'https://t1.aixinxi.net/';

    private $login_api = 'https://tu.aixinxi.net/includes/userAction.php';

    private $upload_token_api = 'https://tu.aixinxi.net/includes/token.php';

    private $upload_save_api = 'https://tu.aixinxi.net/includes/save.php';

    private $upload_api = 'https://tu-t1.oss-cn-hangzhou.aliyuncs.com';

    private $cookies = null;

    private $cookie_path = null;

    private $upload_key = null;

    public function __construct(array $client_config = [], array $curl_config = [], $size)
    {

        $this->cookie_path = COOKIE_PATH . md5($this->name);

        parent::__construct($client_config, array(
            CURLOPT_COOKIEJAR => $this->cookie_path,
            CURLOPT_COOKIEFILE => $this->cookie_path,
        ), $size);

        // --------------------------------------------------
        if (intval($size) > intval($client_config['max_upload']) * 1024 * 1024 ){
            // 文件过大
            return '';
        }
        // --------------------------------------------------

        if (!file_exists($this->cookie_path)){
            file_put_contents($this->cookie_path, '');
        }
        $this->cookies = file_get_contents($this->cookie_path);
        if (empty($this->cookies)){
            /*登录获取cookie*/
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

        $this->upload_key = 'o_' . $key . '.' . pathinfo( $file )['extension'];

        $oos_info = $this->get_oos_info();

        if (!empty($oos_info['uptoken'])){
            /*重新登录获取cookie*/
            $this->login();
            $oos_info = $this->get_oos_info();
        }

        if (!empty($oos_info['AccessKeyId'])){

            $save_data = array(
                'ming'  =>  $this->upload_key
            );
            $this->http->curl_post($this->upload_save_api, $save_data);

            if ($this->http->curl_status_code == 200){

                $request_header = array(
                    'Origin'        =>  'https://tu.aixinxi.net',
                    'Referer'       =>  'https://tu.aixinxi.net/index.php',
                    'User-Agent'    =>  'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
                );

                $options = array(
                    'name'              =>  $this->upload_key,
                    'policy'            =>  $oos_info['policy'],
                    'signature'         =>  $oos_info['signature'],
                    'OSSAccessKeyId'    =>  $oos_info['AccessKeyId'],
                    'key'               =>  $this->upload_key,
                    'success_action_status' =>  200,
                    'file'              =>  new \CURLFile( $file )
                );

                $this->http->setCurlOptions(array(CURLOPT_HTTPHEADER    =>  $request_header));
                $this->http->curl_post( $this->upload_api, $options );
                if ($this->http->curl_status_code == 200){
                    $return_url = $this->image_api . $this->upload_key . '-w.jpg';
                    $this->db->update_images($key, $return_url);
                }
            }
        }
        return $return_url;
    }

    /**
     * 初始化上传
     *
     * 第一步 获取 OOS 信息
     */
    private function get_oos_info(){
        $result = $this->http->curl_get( $this->upload_token_api );

        if ($this->http->curl_status_code == 200){
            return json_decode($result, true);
        }
        return null;
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
        $request_header = array(
            ':authority'    =>  'tu.aixinxi.net',
            ':method'       =>  'POST',
            ':path'         =>  '/includes/userAction.php',
            ':scheme'       =>  'https',
            'accept'        =>  '*/*',
            'accept-encoding'   =>  'gzip, deflate',
            'accept-language'   =>  'zh-CN,zh;q=0.9,en;q=0.8,ja;q=0.7',
            'origin'            =>  'https://tu.aixinxi.net',
            'referer'           =>  'https://tu.aixinxi.net/views/login.php',
            'x-requested-with'  =>  'XMLHttpRequest',
            'user-agent'        =>  'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
        );

        $request_data = array(
            'action'    =>  'login',
            'username'  =>  $this->config['username'],
            'password'  =>  $this->config['password']
        );

        $this->http->setCurlOptions(array(CURLOPT_HTTPHEADER => $request_header));
        $result = $this->http->curl_post($this->login_api, $request_data);
        if ($this->http->curl_status_code == 200){
            $result_data = json_decode($result, true);
            if (strtolower(trim($result_data['code'])) == 'ok'){
                return true;
            }
        }
        return false;
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