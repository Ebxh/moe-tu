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
namespace app;
use app\db\CustomDB;
use app\system\Client\Catbox;
use app\system\Client\Chevereto;
use app\system\Client\Imgvim;
use app\system\Client\Niupic;
use app\system\Client\Ooxxooo;
use app\system\Client\Picsogou;
use app\system\Client\Smms;
use app\system\Client\Tuaixinxi;
use app\system\Client\Uploadcc;
use app\system\Client\Uploadouliu;
use app\system\Client\Weibo;

ignore_user_abort(true); // 忽略客户端断开
set_time_limit(0);    // 设置执行不超时
require_once 'common.php';

/**
 * Class Delivery
 * @package app\system
 * 
 * 分发上传
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'delivery'):

    $pushfile_path = CACHE_PATH . $_POST['pushfile'];
    $pushfile_key = $_POST['key'];
    $pushfile_size = $_POST['size'];

    if (is_string($pushfile_path)):

        $db = new CustomDB();

        $class_arr = array(
            new Picsogou(array('max_upload'    =>  10), [], $pushfile_size),
            new Smms(array('max_upload'    =>  5), [], $pushfile_size),
            new Ooxxooo(array('max_upload'    =>  10), [], $pushfile_size),
            new Niupic(array('max_upload'    =>  10), [], $pushfile_size),
            new Catbox(array('max_upload'    =>  10), [], $pushfile_size),
            new Uploadcc(array('max_upload'    =>  10), [], $pushfile_size),
            new Uploadouliu(array('max_upload'    =>  10), [], $pushfile_size),
            new Imgvim(array('max_upload'    =>  10), [], $pushfile_size)
        );

        try {
            $image_url = (new Weibo(WEIBO_CONFIG, [], $pushfile_size))->upload($pushfile_key, $pushfile_path);
            $db->done_cache_images($pushfile_key, $image_url);
        } catch (\Exception $e) {
            exe_log('delivery.php 70 Weibo', $e->getMessage());
        }

        try {
            $image_url = (new Tuaixinxi(AIXINXI_CONFIG, [], $pushfile_size))->upload($pushfile_key, $pushfile_path);
            $db->done_cache_images($pushfile_key, $image_url);
        } catch (\Exception $e) {
            exe_log('delivery.php 77 Tuaixinxi', $e->getMessage());
        }

        foreach ($class_arr as $class){
            try {
                $image_url = ($class)->upload($pushfile_key, $pushfile_path);
                $db->done_cache_images($pushfile_key, $image_url);
            } catch (\Exception $e) {
                exe_log('delivery.php 85 ' . get_class($class), $e->getMessage());
            }
        }

        foreach (CHEVERETO_CONFIGS as $ch_config){
            try {
                $image_url = (new Chevereto($ch_config, [], $pushfile_size))->upload($pushfile_key, $pushfile_path);
                $db->done_cache_images($pushfile_key, $image_url);
            } catch (\Exception $e) {
                exe_log('delivery.php 94 ' . $ch_config['name'], $e->getMessage());
            }
        }


        /*删除临时文件*/
        @unlink($pushfile_path);

        /*图片分发完成处理*/
        $db->done_upload_images($pushfile_key);
    endif;
endif;