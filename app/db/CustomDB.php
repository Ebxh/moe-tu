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

/**
 * Class CustomDB
 * @package app\db
 *
 * 数据库快捷操作
 */
class CustomDB
{

    private $db;

    public function __construct()
    {
        $this->db = new Mysql(DB_CONFIG);
    }

    /**
     * @param $key
     * @return array|null
     *
     * 根据 key 查询 图片链接
     */
    public function get_images_key($key)
    {
        $sql = 'SELECT * FROM picture WHERE `key` = "' . $key . '" ORDER BY put_date DESC LIMIT 0,1;';
        $result = $this->db->query($sql);
        if ($result){
            $data = mysqli_fetch_array($result);
            if (!empty($data['images'])){
                return unserialize(htmlspecialchars_decode($data['images']));
            }
        }
        return null;
    }

    /**
     * @param $key
     * @return array|null
     *
     * 根据 key 查询
     */
    public function get_key($key)
    {
        $sql = 'SELECT * FROM picture WHERE `key` = "' . $key . '";';
        $data = $this->db->query($sql);
        if ($data){
            $result = mysqli_fetch_array($data);
            return $result;
        }
        return null;
    }


    /**
     * @param $md5
     * @return bool|\mysqli_result
     *
     * 检测图片是否上传过
     */
    public function check_md5(string $md5) : array
    {
        $sql = 'SELECT * FROM picture WHERE md5="' . $md5 . '";';
        $data = $this->db->query($sql);
        if ($data){
            $result = mysqli_fetch_array($data);

            if ($result[0] > 0){
                return array(
                    'status'    =>  true,
                    'key'       =>  $result['key']
                );
            }else{
                return array(
                    'status'    =>  false
                );
            }
        }
        return array(
            'status'    =>  false
        );
    }

    /**
     * @param array $data
     * @return bool
     *
     * 添加
     */
    public function add(array $data)
    {
        $sql = 'INSERT INTO picture (`key`, md5, put_date) VALUES ("' . $data['key'] . '", "' . $data['md5'] . '", "' . date("Y-m-d H:i:s") . '");';
        return $this->db->query($sql);
    }

    /**
     * @param $key
     * @param $value
     * @return bool|\mysqli_result|null
     * 更新图片链接
     */
    public function update_images($key, $value)
    {
        $sql = 'SELECT * FROM picture WHERE `key` ="' . $key . '" ;';
        $result = $this->db->query($sql);
        if ($result){
            $data = mysqli_fetch_array($result);
            if (!empty($data['images'])){
                $images = unserialize(htmlspecialchars_decode($data['images']));
                if (!in_array($value, $images)){
                    $images[] = $value;
                }
            }else{
                $images[] = $value;
            }
            /*update*/
            $sql = 'UPDATE picture SET images = "' . htmlspecialchars(serialize($images)) . '"  WHERE `key` ="' . $key . '";';
            $this->db->query($sql);
        }
    }

    /**
     * @param $key
     *
     * 图片分发上传完成
     */
    public function done_upload_images($key)
    {
        $sql = 'SELECT * FROM picture WHERE `key` ="' . $key . '" ;';
        $result = $this->db->query($sql);
        if ($result){
            $data = mysqli_fetch_array($result);

            $sql = 'DELETE FROM picture WHERE `key` ="' . $key . '";';
            if (!empty($data['images'])){
                $images = unserialize(htmlspecialchars_decode($data['images']));
                if (count($images) > 0){
                    /*更新done*/
                    $sql = 'UPDATE picture SET done = "true"  WHERE `key` ="' . $key . '";';
                }
            }
            $this->db->query($sql);
        }
    }

    /**
     * @param $key
     * @param $value
     *
     * 缓存分发第一个完成的图片链接
     */
    public function done_cache_images($key, $value)
    {
        if (!empty($value)){
            /*添加图片链接到Memcache缓存*/
            $memcache = new Memcach();
            if (empty($memcache->get($key))){
                $memcache->add($key, $value);
            }
        }
    }

    public function delete()
    {

    }

}