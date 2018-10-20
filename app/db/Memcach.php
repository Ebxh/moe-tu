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
 *     © 2018/8/23 Mex.
 *     author : Yprisoner
 *     email : yyprisoner@gmail.com
 *                            ------
 *    「 涙の雨が頬をたたくたびに美しく 」
 */

namespace app\db;


class Memcach
{

    private $memcache = null;

    public function __construct()
    {
        $this->memcache = new \Memcache();
        $this->memcache->connect(DB_CONFIG['memcache_host'], DB_CONFIG['memcache_port']) or die('缓存服务器连接失败 ~');
    }

    /**
     * @param $key
     * @return array|string
     *
     * 查询
     */
    public function get($key)
    {
        return $this->memcache->get($key);
    }

    /**
     * @param $key
     * @param $value
     *
     * 添加
     */
    public function add($key, $value){
        $this->memcache->set($key, $value, 0, (cache_expire_time * 3600 * 24));
    }

    /**
     * @param $key
     * @param $value
     *
     *更新 
     */
    public function update($key, $value)
    {
        $this->memcache->replace($key, $value, 0, (cache_expire_time * 3600 * 24));
    }

    /**
     * @param $key
     *
     * 删除
     */
    public function delete($key)
    {
        $this->memcache->delete($key);
    }

}