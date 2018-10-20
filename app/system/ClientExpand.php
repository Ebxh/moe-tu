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


interface ClientExpand
{

    /**
     * @return mixed
     *
     * 模拟登录 获取cookies
     */
    public function login();

    /**
     * @return mixed
     *
     * 获取 token
     */
    public function get_auth_token();

}