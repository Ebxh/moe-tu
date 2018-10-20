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

namespace app\db;

/***
 * Class DB
 * @package app\db
 *
 * 数据库
 */
class Mysql
{

    private static $conn;

    public function __construct(array $db_config)
    {
        self::$conn = mysqli_connect($db_config['host'],$db_config['username'],$db_config['password'],$db_config['dbname'],$db_config['port']);
    }

    public function connect_errno(){
        return mysqli_connect_errno();
    }

    public function connect_error(){
        return mysqli_connect_error();
    }

    public function fetch($sql){
        return mysqli_fetch_assoc($sql);
    }

    public function get_row($sql){
        $result = mysqli_query(self::$conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function count($sql){
        $result = mysqli_query(self::$conn, $sql);
        $count = mysqli_fetch_array($result);
        return $count[0];
    }

    public function query($sql){
        return mysqli_query(self::$conn, $sql);
    }

    public function escape($str){
        return mysqli_real_escape_string(self::$conn,$str);
    }

    public function affected(){
        return mysqli_affected_rows(self::$conn);
    }

    public function errno(){
        return mysqli_errno(self::$conn);
    }

    public function error(){
        return mysqli_error(self::$conn);
    }

}