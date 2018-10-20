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
 * Class Ooxxooo
 * @package app\system\Client
 *
 * https://ooxx.ooo
 */
class Ooxxooo extends Client implements ClientInterface
{

    private $name = 'Ooxxooo';

    private $root = 'https://ooxx.ooo/';

    private $api = 'https://ooxx.ooo/upload';

    private $image_url = 'https://i.ooxx.ooo/';

    private $cookies = null;

    private $cookie_path = null;

    public function __construct(array $client_config = [], array $curl_config = [], $size)
    {
        $this->cookie_path = COOKIE_PATH . md5($this->name);

        parent::__construct($client_config, array(
            CURLOPT_COOKIEJAR => $this->cookie_path,
            CURLOPT_COOKIEFILE => $this->cookie_path
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
            /*获取cookies*/
            $this->http->curl_get($this->api);
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
            'files[]' => new \CURLFile($file)
        );
        try {
            $result = $this->http->curl_post($this->api, $options);
            if ($this->http->curl_status_code == 200){
                $data = json_decode($result, true);
                $url = $this->image_url . $data[0];
                $this->db->update_images($key, $url);
                $return_url = $url;
            }
        } catch (\Exception $e) {
            exe_log($this->name, $e->getMessage(), $e->getCode());
        }
        return $return_url;
    }
}