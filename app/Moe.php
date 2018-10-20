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
 *     © 2018/8/26 Mex.
 *     author : Yprisoner
 *     email : yyprisoner@gmail.com
 *                            ------
 *    「 涙の雨が頬をたたくたびに美しく 」
 */

namespace app;


use app\db\CustomDB;
use app\db\Memcach;
use app\system\Http\Http;

class Moe
{
    private $key = null;

    private $db;

    private $memcache;

    private $http;

    public function __construct($moe)
    {
        $this->key =  $moe;
        $this->memcache = new Memcach();
        $this->db = new CustomDB();
        $this->http = new Http();
    }

    /**
     * 查询缓存 进行跳转
     */
    public function redirect()
    {
        if (!empty($image_url = $this->memcache->get($this->key))){
            redirect($image_url);
        }
        $data = $this->db->get_images_key($this->key);
        if (!empty($data)){
            foreach ($data as $image_url){
                $this->http->curl_get($image_url);
                if ($this->http->curl_status_code == 200){
                    $this->memcache->add($this->key, $image_url);
                    redirect($image_url);
                }
            }
        }else{
            redirect(get_url('assets/images/404.jpg'));
        }

        if (!empty($image_url = $this->memcache->get($this->key))){
            redirect($image_url);
        }else{
            redirect(get_url('assets/images/404.jpg'));
        }
    }


    /**
     * @return array
     *
     * 查询图片
     */
    public function select() : array
    {
        $data = $this->db->get_images_key($this->key);
        if (!empty($data)){
            $result = array(
                'code'  =>  0,
                'urls'  =>  $data,
                'msg'   =>  ''
            );
        }else{
            $result = array(
                'code'  =>  404,
                'msg'   =>  '图片不存在'
            );
        }
        return $result;
    }

}