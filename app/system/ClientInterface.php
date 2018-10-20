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


interface ClientInterface
{

    /**
     * @param string $key
     * 唯一 key
     * @param string $file
     * 临时文件路径
     * @return string
     * 上传成功 返回 图片链接; 上传失败 返回 空字符串
     */
    public function upload(string $key, string $file) : string;

}