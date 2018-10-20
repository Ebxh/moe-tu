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
 *     © 2018/8/22 Mex.
 *     author : Yprisoner
 *     email : yyprisoner@gmail.com
 *                            ------
 *    「 涙の雨が頬をたたくたびに美しく 」
 */
require_once 'common.php';
/*发送token*/
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'):
    return_json([
       'code'   =>  0,
       'token'  =>  get_token()
    ]);
endif;

if ($_SERVER['REQUEST_METHOD'] == 'POST'):
    if (!check_token($_POST['token'])){
        header('HTTP/1.1 403 Forbidden');
        return_json(['No authority']);
    }

    $action = null;
    if (isset($_POST['action'])){
        $action = trim(strtolower($_POST['action']));
    }

    switch ($action){
        case 'upload':
            $upload = new \app\system\Upload();
            return_json($upload->upload());
            break;
        case 'search':
            $search = new \app\system\Search();
            return_json($search->search());
            break;
        default:
            return_json(['Hello World!']);
            break;
    }
else:
    header('HTTP/1.1 403 Forbidden');
    return_json(['No authority']);
endif;