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

namespace app\system;
use app\db\CustomDB;

/**
 * Class Search
 * @package app\system
 *
 * 轮询查询图片
 */
class Search
{

    private $key = null;

    private $db = null;

    public function __construct()
    {
        $this->key = $_POST['key'];
        $this->db = new CustomDB();
    }

    /**
     * @return array
     *
     * 查询数据库中的数据
     */
    public function search() : array
    {
        $result = $this->db->get_key($this->key);
        if (!empty($result)){
            $urls = !empty($result['images']) ? unserialize(htmlspecialchars_decode($result['images'])) : '';
            $data = [
                'code'  =>  0,
                'url'   =>  get_url('moe/' . $this->key),
                'urls'  =>  $urls,
                'done'  =>  $result['done'] == 'false' ? false : true
            ];
        }else{
            $data = [
                'code'  =>  0,
                'msg'  =>  '图片不存在 ~',
                'done'  =>  true
            ];
        }
        return $data;
    }

}