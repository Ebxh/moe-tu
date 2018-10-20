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

namespace app\system;
use app\db\CustomDB;
use app\system\Http\Http;

require LIB_PATH . 'simple_html_dom.php';

/**
 * Class Client
 * @package app\system
 *
 * 所有扩展 公共资源
 */
class Client
{

    /**
     * @var CustomDB
     *
     * 数据库对象
     */
    protected $db;

    /**
     * @var Http
     *
     * Http 对象
     */
    protected $http;

    /**
     * @var array
     *
     * 自定义配置
     */
    protected $config = [];


    public function __construct(array $client_config = [], array $curl_config = [], $size)
    {
        $this->config = $client_config;
        $this->http = new Http($curl_config);
        $this->db = new CustomDB();
    }

    public function upload(string $key, string $file) : string{ return ''; }

}