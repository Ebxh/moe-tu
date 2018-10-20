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
use app\system\ClientInterface;

/**
 * Class Picsogou
 * @package app\system\Client
 *
 * http://pic.sogou.com
 */
class Picsogou extends Client implements ClientInterface
{

    private $name = 'Picsogou';

    private $root = 'http://pic.sogou.com/';

    private $upload_api = 'http://pic.sogou.com/ris_upload';

    private $cookies = null;

    private $cookie_path = null;

    public function __construct(array $client_config = [], array $curl_config = [], $size)
    {
        $this->cookie_path = COOKIE_PATH . md5($this->name);
        $header = array(
            'Origin'    =>  'http://pic.sogou.com',
            'Referer'   =>  'http://pic.sogou.com/'
        );
        parent::__construct($client_config, array(
            CURLOPT_COOKIEJAR => $this->cookie_path,
            CURLOPT_COOKIEFILE => $this->cookie_path,
            CURLOPT_HTTPHEADER =>  $header
        ), $size);

        if (!file_exists($this->cookie_path)){
            file_put_contents($this->cookie_path, '');
        }
        $this->cookies = file_get_contents($this->cookie_path);
        if (empty($this->cookies)){
            /*获取cookies*/
            $this->http->curl_get($this->root);
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
        $options = array(
            'pic_path'    =>  new \CURLFile($file),
            'flag'  =>  1
        );

        try {
            $this->http->curl_post($this->upload_api, $options, true);

            if ($this->http->curl_status_code == 200){
                $url = $this->http->curl_http_info;
                $url = explode('&oname', $url['url']);
                $url = $url[0];
                $url = str_replace('query=','', urldecode(parse_url($url)['query']));
                if (stristr($url, 'http') !== false){
                    if (stristr($url, 'https') == false){
                        /*存在http不存在https*/
                        $url = str_replace('http', 'https', $url);
                    }
                }else {
                    $url = 'https://' . $url;
                }
                $this->db->update_images($key, $url);
                $return_url = $url;
            }
        } catch (\Exception $e) {
            exe_log($this->name, $e->getMessage(), $e->getCode());
        }
        return $return_url;
    }
}