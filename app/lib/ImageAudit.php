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

namespace app\lib;

require_once 'baiduAudit/AipImageCensor.php';

/**
 * Class ImageAudit
 * @package app\imageAudit
 *
 * 检测上传图像
 */
class ImageAudit
{

    private $imageaudit = null;

    public function __construct()
    {
        $this->imageaudit = new \AipImageCensor(BAIDU_AUDIT_CONFIG['app_id'], BAIDU_AUDIT_CONFIG['api_key'], BAIDU_AUDIT_CONFIG['secret_key']);
    }

    /**
     * @param $resources
     * @return array
     *
     * 鉴别图片
     */
    public function check_image($resources) : array
    {
        $return = array(
            'status'    =>  BAIDU_AUDIT_OTHER
        );
        $result = $this->imageaudit->imageCensorUserDefined($resources);

        file_put_contents(IMAGE_AUDIT_PATH . 'audit.php',var_export($result, true));

        if (!empty($result['data'])){
            /*存在详细数据*/
            $audit_data = $result['data'][0];
            if (!empty($audit_data['error_msg'])){
                // 审核失败
                $return = array(
                    'status'    =>  BAIDU_AUDIT_ERROR,
                    'msg'       =>  $result['conclusion'] . ' info : ' . $audit_data['error_msg']
                );
            }else{
                // 内容审核不通过
                if ($result['conclusion'] == '不合规'){
                    $return = array(
                        'status'    =>  BAIDU_AUDIT_DISAGREEMENT,
                        'msg'       =>  $audit_data['msg']
                    );
                }
            }
        }else{
            /*不存在详细数据*/
            if (!empty($result['conclusion'])){
                /*正常返回*/
                if ($result['conclusion'] == '合规'){
                    $return = array(
                        'status'    =>  BAIDU_AUDIT_COMPLIANCE
                    );
                }
            }else{
                /*服务器错误*/
                if (!empty($result['error_msg'])){
                    $return = array(
                        'status'    =>  BAIDU_AUDIT_ERROR,
                        'msg'       =>  '审核服务器 : ' . $result['error_msg']
                    );
                }
            }
        }

        return $return;
    }

}