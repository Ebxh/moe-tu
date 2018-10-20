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
 * Class Smms
 * @package app\system\Client
 *
 * https://sm.ms/doc/
 */
class Smms extends Client implements ClientInterface
{

    private $name = 'Smms';

    private $api = 'https://sm.ms/api/upload';

    public function __construct(array $client_config = [], array $curl_config = [], $size)
    {
        parent::__construct($client_config, $curl_config, $size);

        // --------------------------------------------------
        if (intval($size) > intval($client_config['max_upload']) * 1024 * 1024 ){
            // 文件过大
            return '';
        }
        // --------------------------------------------------

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
            'smfile'    =>  new \CURLFile($file),
            'ssl'   =>  true,
            'format'    =>  'json'
        );

        try {
            $result = $this->http->curl_post($this->api, $options);
            $data = json_decode($result, true);
            if ($data['code'] == 'success'){
                $url = $data['data']['url'];
                $this->db->update_images($key, $url);
                $return_url = $url;
            }
        } catch (\Exception $e) {
            exe_log($this->name, $e->getMessage(), $e->getCode());
        }
        return $return_url;
    }
}