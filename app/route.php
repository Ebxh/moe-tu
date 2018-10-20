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

use app\Moe;
#################################################################################

if (!empty($_GET['action']) && !empty($_GET['args'])){
    $action = htmlspecialchars(strip_tags(trim($_GET['action'])));
    $args = htmlspecialchars(strip_tags(trim($_GET['args'])));
    switch ($action){
        case 'moe':
            (new Moe($args))->redirect();
            break;
        case 'select':
            $result = (new Moe($args))->select();
            return_json($result, false);
            break;
        default:
            break;
    }
    exit();
}