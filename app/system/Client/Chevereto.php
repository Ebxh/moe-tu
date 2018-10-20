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
 *     © 2018/8/25 Mex.
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
 * Class Chevereto
 * @package app\system\Client
 *
 * 使用 Chevereto 床图程序的站点 公用类
 *
 * https://chevereto.com/
 */
class Chevereto extends Client implements ClientInterface, ClientExpand
{

    private $name;

    private $root = null;

    private $upload_api = null;

    private $login_api = null;

    private $is_login = false;

    private $cookies = null;

    private $cookie_path = null;

    private $auth_token = null;

    private $upload_status = true;

    /**
     * Chevereto constructor.
     * @param array $client_config
     * @param array $curl_config
     *
     * 传入不同站点的配置文件
     * @throws \Exception
     */
    public function __construct(array $client_config = [], array $curl_config = [], $size)
    {
        /*加载配置信息*/
        $this->name = $client_config['name'];
        $this->root = $client_config['root'];
        $this->upload_api = $client_config['upload_api'];
        $this->login_api = $client_config['login_api'];
        $this->is_login = $client_config['is_login'];

        //-------------------------------------------------------

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

        /*获取auth_token*/
        $this->get_auth_token();

        if (!file_exists($this->cookie_path)){
            file_put_contents($this->cookie_path, '');
        }
        $this->cookies = file_get_contents($this->cookie_path);
        if (empty($this->cookies)){
            /*获取cookies*/
            if ($this->is_login){
                /*登录获取*/
                if ($this->login() !== true){
                    return '';
                }
            }else{
                $this->http->curl_get($this->root);
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
     * @throws \Exception
     */
    public function upload(string $key, string $file): string
    {
        $return_url = '';
        if (!empty($this->auth_token)){
            $options = array(
                'source'    =>  new \CURLFile($file),
                'type'  =>  'file',
                'action'    =>  'upload',
                'privacy'   =>  'public',
                'timestamp' =>  get_timestamp(),
                'category_id'   =>  null,
                'nsfw'  =>  0,
                'auth_token'    =>  $this->auth_token
            );

            try {
                $result = $this->http->curl_post($this->upload_api, $options);
                $data = json_decode($result, true);
                if ($this->http->curl_status_code == 200){
                    if ($data['status_code'] == 200 && strtolower($data['status_txt']) == 'ok') {
                        $url = str_replace('.md', '', $data['image']['display_url']);
                        if (!empty($url)) {
                            $this->db->update_images($key, $url);
                            $return_url = $url;
                        }
                    }
                }else{
                    if ($data['status_code'] == 400
                        && $data['error']['code'] == 403
                        && stristr($data['error']['message'], '登录') !== false
                        && $this->upload_status){
                        $this->upload_status = false;
                        $this->get_auth_token();
                        $this->login();
                        $this->upload($key, $file);
                    }
                }
            } catch (\Exception $e) {
                exe_log($this->name, $e->getMessage(), $e->getCode());
            }
        }
        return $return_url;
    }

    /**
     * @return mixed
     *
     * 模拟登录 获取cookies
     * @throws \Exception
     */
    public function login()
    {
        $options = array(
            'auth_token'    =>  $this->auth_token,
            'login-subject' =>  $this->config['username'],
            'password'      =>  $this->config['password'],
            'keep-login'    =>  1
        );
        try {
            $this->http->curl_post($this->login_api, $options, true);
            $result_url = strtolower($this->http->curl_http_info['url']);
            if (stristr($result_url, 'login') !== false){
                return false;
            }
            return true;
        } catch (\Exception $e) {
            exe_log($this->name, $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return mixed
     *
     * 获取 token
     * @throws \Exception
     */
    public function get_auth_token()
    {
        $auth_token = null;
        $page_html = $this->http->curl_get($this->root);
        if ($this->http->curl_status_code == 200){
            $html = new \simple_html_dom();
            $html->load($page_html);
            if (!empty(($auth_token_html = $html->find('input[name=auth_token]', 0)))){
                $auth_token_dom = new \simple_html_dom();
                $auth_token_dom->load($auth_token_html);
                $auth_token = $auth_token_dom->find('input[name=auth_token]', 0)->value;
            }else{
                try{
                    $index = 0;
                    switch ($this->name){
                        case 'Moetu':
                        case 'Aphoto':
                        case 'Z4a':
                            $index = 1;
                            break;
                    }
                    $user_panel_html = $html->find('div[class=pop-box-inner pop-box-menu]', $index);
                    $user_panel_dom = new \simple_html_dom();
                    $user_panel_dom->load($user_panel_html);
                    $auth_token_url = $user_panel_dom->find('a', -1)->href;
                    $url_arr = parse_url($auth_token_url);
                    if (!empty($user_url = $url_arr['query'])){
                        $auth_token = str_replace('auth_token=','', urldecode($user_url));
                    }else{
                        exe_log($this->name, '未匹配到 auth_token : ' . $auth_token_url);
                    }
                }catch (\Exception $e){
                    exe_log($this->name, $e->getMessage(), $e->getCode());
                }
            }
        }
        $this->auth_token = $auth_token;
    }
}