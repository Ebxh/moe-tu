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

namespace app\system\Http;


class Http
{

    /**
     * @var null|resource
     *
     */
    private $curl_http = null;

    /**
     * @var string
     *
     */
    private $ua;

    /**
     * @var null
     *
     * 状态码
     */
    public $curl_status_code = null;

    /**
     * @var null
     *
     * curl 信息
     */
    public $curl_http_info = null;

    /**
     * Http constructor.
     * @param array $options
     *
     */
    public function __construct(array $options = [])
    {
        $this->ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:45.0) Gecko/20100101 Firefox/45.0';
        $this->curl_http = curl_init();
        curl_setopt($this->curl_http, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($this->curl_http, CURLOPT_SSL_VERIFYHOST, 2);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($this->curl_http, CURLOPT_TIMEOUT, 120); // 设置超时限制防止死循环
        if (!empty($options)){
            foreach ($options as $k => $v){
                curl_setopt($this->curl_http, $k, $v);
            }
        }
    }

    /**
     * @param array $options
     *
     * 设置参数
     */
    public function setCurlOptions(array $options = []){
        if (!empty($options)){
            foreach ($options as $k => $v){
                curl_setopt($this->curl_http, $k, $v);
            }
        }
    }

    /**
     * @param string $url
     * @param callable $func
     * @return mixed
     *
     * GET
     */
    public function curl_get($url, array $data = []){
        if (!empty($data)){
            if (substr($url, -1) == '/'){
                $url = substr($url,0, -1);
            }
            $url .= '?';
            /*拼接数据*/
            foreach ($data as $k => $v){
                $url .= $k . '=' . $v . '&';
            }
            if (substr($url, -1) == '&'){
                $url = substr($url,0, -1);
            }
        }
        curl_setopt($this->curl_http,CURLOPT_URL, $url);
        curl_setopt($this->curl_http, CURLOPT_HEADER, 0);
        curl_setopt($this->curl_http, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($this->curl_http);
        $this->curl_status_code = curl_getinfo($this->curl_http, CURLINFO_HTTP_CODE);
        return $result;
    }

    /**
     * @param string $url
     * @param array $data
     * @param callable $func
     * @return mixed
     * @throws \Exception

     *
     * POST
     */
    public function curl_post( $url,  array $data = [], bool $location = false){
        curl_setopt($this->curl_http,CURLOPT_URL, $url);
        curl_setopt($this->curl_http, CURLOPT_USERAGENT, $this->ua);
        curl_setopt($this->curl_http, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($this->curl_http, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($this->curl_http, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($this->curl_http, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($this->curl_http, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $result = curl_exec($this->curl_http);
        if (curl_errno($this->curl_http)) {
            exe_log('Http.php', '[ url : ' . $url . ' ] 详细 ' . curl_error($this->curl_http));
        }
        if ($location){
            $this->curl_http_info = curl_getinfo($this->curl_http);
        }
        $this->curl_status_code = curl_getinfo($this->curl_http, CURLINFO_HTTP_CODE);
        return $result;
    }

    /**
     * close
     */
    public function __destruct()
    {
        // if (!empty($this->curl_http)) curl_close($this->curl_http);
    }

}