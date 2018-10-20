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
 *     © 2018/8/27 Mex.
 *     author : Yprisoner
 *     email : yyprisoner@gmail.com
 *                            ------
 *    「 涙の雨が頬をたたくたびに美しく 」
 */

require 'app/common.php';

/*是否登录*/
if (!empty($_SESSION['root'])){
    redirect('/');
}

/*判断书否包含验证 Code */
if (!isset($_GET['code'])) {
    $state = md5(uniqid(strval(rand()), TRUE));
    $url = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=' . QQ_APP_ID . '&redirect_uri=' . urlencode(QQ_REDIRECT_URL) . '&scope=' . $state . '';
    redirect($url);
} else {
    $code = $_GET['code'];

    $state = md5(uniqid(strval(rand()), TRUE));
    $getAccessToken = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=' . QQ_APP_ID. '&client_secret=' . QQ_APP_TOKEN . '&code=' . $code . '&state=' . $state . '&redirect_uri=' . QQ_REDIRECT_URL . '';
    $accessToken = file_get_contents($getAccessToken);
    $u = "https://graph.qq.com/oauth2.0/me?" . $accessToken . "";
    $str = file_get_contents($u);
    if (strpos($str, "callback") !== false) {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str = substr($str, $lpos + 1, $rpos - $lpos - 1);
    }
    $user_data = json_decode($str, true);
    /*用户openId , 唯一标识*/
    $user_openid = $user_data['openid'];
    if($user_openid === QQ_OPEN_ID){
        /**完成登录*/
        $_SESSION['root'] = $user_openid;
        redirect('/');
    }else{
        echo 'Hello World';
    }
}