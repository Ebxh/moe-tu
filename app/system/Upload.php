<?php declare(strict_types=1);

namespace app\system;
use app\db\CustomDB as DB;
use app\lib\ImageAudit;

/**
 * Class Client
 * @package app\system
 *
 * 上传文件、触发异步上传
 */
class Upload
{

    private $picture = null;

    private $picture_md5 = null;

    public $picture_name = null;

    public $picture_key = null;

    private $picture_type = null;

    private $picture_size = null;

    private $check_images = array("jpg", "jpeg", "gif", "png", "bmp");

    public function __construct()
    {
        if ($_FILES['file']['error'] > 0){
            return [
                'code'  => 1,
                'msg'   => $_FILES["file"]["error"]
            ];
        }
        $this->picture = $_FILES['file'];
        $this->picture_name = $_FILES['file']['name'];
        $this->picture_type = $_FILES['file']['type'];
        $this->picture_size = $_FILES['file']['size'];
        $this->picture_key = get_key();
    }

    public function upload() : array
    {
        $name_arr = explode('.', $this->picture['name']);
        $extension = end($name_arr);
        if ( in_array($extension, $this->check_images) && stristr($this->picture_type, 'image') !== false){

            // --------------------------------------------------
            if (intval($this->picture_size) > intval(upload_max_size) * 1024 * 1024 ){
                // 文件过大
                return [
                    'code'    =>  1,
                    'msg'     =>  '上传失败~ 文件大于 ' . upload_max_size . 'MB'
                ];
            }
            // --------------------------------------------------

            $db = new DB();

            move_uploaded_file($_FILES["file"]["tmp_name"], CACHE_PATH . $this->picture_name);

            /*判断文件是否上传过*/
            $this->picture_md5 = md5_file(CACHE_PATH . $this->picture_name);
            $check_result = $db->check_md5($this->picture_md5);

            if ($check_result['status']){
                if (is_file(CACHE_PATH . $this->picture_name)){
                    @unlink(CACHE_PATH . $this->picture_name);
                }
                /*已经上传过 直接返回*/
                return [
                    'code'    =>  0,
                    'key'     =>    $check_result['key'],
                    'msg'     =>  '上传成功!'
                ];
            }

            /** 非登陆用户进行色情鉴别 */
            if (empty($_SESSION['root']) && BAIDU_AUDIT_CONFIG['is_audit'] && $extension != 'gif'){
                $image_audit = new ImageAudit();
                $audit_result = $image_audit->check_image(file_get_contents(CACHE_PATH . $this->picture_name));

                // 审核不通过
                if ($audit_result['status'] == BAIDU_AUDIT_DISAGREEMENT){
                    if (BAIDU_AUDIT_CLIENT_IP){
                        /*封禁IP*/
                        prohibition_client_ip();
                    }else{
                        @unlink(CACHE_PATH . $this->picture_name);
                    }
                    try {
                        exe_log('图片检测', ' IP : ' . get_client_ip() . ' 图片名称 : ' . $this->picture_name, 0, IMAGE_AUDIT_PATH);
                    } catch (\Exception $e) {
                    }
                    return [
                        'code'  =>  1,
                        'msg'   =>  $audit_result['msg']
                    ];
                }

                // 审核失败
                if ($audit_result['status'] == BAIDU_AUDIT_ERROR){
                    @unlink(CACHE_PATH . $this->picture_name);
                    return [
                        'code'  =>  1,
                        'msg'   =>  $audit_result['msg']
                    ];
                }

                // 其他情况
                if ($audit_result['status'] == BAIDU_AUDIT_OTHER){
                    @unlink(CACHE_PATH . $this->picture_name);
                    return [
                        'code'  =>  1,
                        'msg'   =>  '服务器错误 ~'
                    ];
                }


            }

            try{
                /*插入数据库*/
                $data = array(
                    'key'   =>  $this->picture_key,
                    'md5'   =>  $this->picture_md5
                );

                if (!$db->add($data)){
                    @unlink(CACHE_PATH . $this->picture_name);
                    return [
                        'code'  =>  1,
                        'msg'   =>  '上传失败~ 请稍后再试 ...'
                    ];
                }

                try{
                    /*异步分发上传*/
                    $asyncResult = asyncExecute(get_url('app/delivery.php'), [
                        'action'    =>  'delivery',
                        'key'       =>  $this->picture_key,
                        'pushfile'  =>  $this->picture_name,
                        'size'      =>  $this->picture_size
                    ]);

                    if ($asyncResult){
                        $result = [
                            'code'    =>  0,
                            'key'     =>    $this->picture_key,
                            'msg'     =>  '上传成功! 后台分发中 ...'
                        ];
                    }else{
                        $result = [
                            'code'    =>  1,
                            'msg'     =>  '分发失败 ~'
                        ];
                    }

                }catch (\ErrorException $e){
                    $result = [
                        'code'    =>  1,
                        'msg'     =>  $e->getMessage()
                    ];
                }

            }catch(\ErrorException $e){
                $result = [
                    'code'    =>  1,
                    'msg'     =>  $e->getMessage()
                ];
            }

            return $result;
        }else{
            return [
                'code'  =>  1,
                'msg'   =>  '文件类型错误 ~ '
            ];
        }
    }

}
