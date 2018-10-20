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
 *     © 2018/8/22 Mex.
 *     author : Yprisoner
 *     email : yyprisoner@gmail.com
 *                            ------
 *    「 涙の雨が頬をたたくたびに美しく 」
 */


const DEBUG = true;
const HTTPS = false;

// QQ 登录设置
const QQ_APP_ID = '';
const QQ_OPEN_ID = '';
const QQ_APP_TOKEN = '';
const QQ_REDIRECT_URL = '';

// 数据库配置
const DB_CONFIG = array(
    // mysql
    'host'  =>  '127.0.0.1',
    'username'  =>  'root',
    'password'  =>  'root',
    'dbname'  =>  'moetu',
    'port'  =>  3306,

    // memcache 缓存数据库
    'memcache_host' =>  '127.0.0.1',
    'memcache_port' =>  11211,
);

// memcache 缓存到期时间 (单位 天)
// 设为秒数时不能大于 2592000（30 天）
const cache_expire_time = 7;

// 允许上传文件大小 (单位 MB)
const upload_max_size = 10;

// 百度鉴黄 API
const BAIDU_AUDIT_CLIENT_IP = false;  // 是否开启IP封禁

const BAIDU_AUDIT_CONFIG = array(
    'is_audit'  =>  true,    // 图片鉴黄开关
    'app_id'    =>  '',       // 百度鉴黄API的配置
    'api_key'   =>  '',
    'secret_key'    =>  ''
);
const BAIDU_AUDIT_COMPLIANCE = 'compliance';        // 合规
const BAIDU_AUDIT_DISAGREEMENT = 'disagreement';    // 不合规
const BAIDU_AUDIT_ERROR         = 'fail';           // 审核失败
const BAIDU_AUDIT_OTHER         = 'other';          // 其他信息

// 微博配置
const WEIBO_CONFIG = array(
    'nickname' => '',       // 微博昵称
    'username' => '',       // 微博用户名
    'password' => ''        // 微博登陆密码
);

// 爱信息床图配置  https://tu.aixinxi.net
const AIXINXI_CONFIG = array(
    'username' => '',  // 用户名
    'password' => '',  // 密码

    'max_upload'    =>  10
);

// Chevereto 配置数组
const CHEVERETO_CONFIGS = array(
    /**
     * 秒速5厘米
     *
     * https://miao.su
     * 配置信息
     */
    array(
        'name'  =>  'Miaosu',
        'root'  =>  'https://miao.su/',
        'upload_api'    =>  'https://miao.su/json',
        'login_api'     =>  'https://miao.su/login',
        'is_login'      =>  true,

        'username' => '',   // 登陆用户名
        'password' => '',   // 登陆密码

        'max_upload'    =>  8   // 允许上传的图片大小 单位 MB
    ),

    /**
     * 路过床图
     *
     * https://imgchr.com
     * 配置信息
     */
    array(
        'name'  =>  'Imgchr',
        'root'  =>  'https://imgchr.com/',
        'upload_api'    =>  'https://imgchr.com/json',
        'login_api'     =>  'https://imgchr.com/login',
        'is_login'      =>  true,

        'username' => '',   // 登陆用户名
        'password' => '',   // 登陆密码

        'max_upload'    =>  10   // 允许上传的图片大小 单位 MB
    ),

    /**
     * Z4A图床
     *
     * https://www.z4a.net
     * 配置信息
     */
    array(
        'name'  =>  'Z4a',
        'root'  =>  'https://www.z4a.net/',
        'upload_api'    =>  'https://www.z4a.net/json',
        'login_api'     =>  'https://www.z4a.net/login',
        'is_login'      =>  true,

        'username' => '',   // 登陆用户名
        'password' => '',   // 登陆密码

        'max_upload'    =>  64   // 允许上传的图片大小 单位 MB
    ),

    /**
     * A.photo
     *
     * https://a.photo
     * 配置信息
     */
    array(
        'name'  =>  'Aphoto',
        'root'  =>  'https://a.photo/',
        'upload_api'    =>  'https://a.photo/json',
        'login_api'     =>  'https://a.photo/login',
        'is_login'      =>  true,

        'username' => '',   // 登陆用户名
        'password' => '',   // 登陆密码

        'max_upload'    =>  10   // 允许上传的图片大小 单位 MB
    ),

    /**
     * MoeTu
     *
     * https://moetu.org
     * 配置信息
     */
    array(
        'name'  =>  'Moetu',
        'root'  =>  'https://moetu.org/',
        'upload_api'    =>  'https://moetu.org/json',
        'login_api'     =>  'https://moetu.org/login',
        'is_login'      =>  true,

        'username' => '',   // 登陆用户名
        'password' => '',   // 登陆密码

        'max_upload'    =>  20   // 允许上传的图片大小 单位 MB
    )
);
