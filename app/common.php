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
date_default_timezone_set('PRC');
session_start();
######################################################################
// 系统常量
const ROOT_PATH = __DIR__ . DIRECTORY_SEPARATOR . '../';
const APP_PATH = ROOT_PATH . 'app/';
const LIB_PATH = APP_PATH . 'lib/';
const CACHE_PATH = APP_PATH . 'cache/';
const LOG_PATH = CACHE_PATH . 'log/';
const STATIC_PATH = ROOT_PATH . 'assets/';
const COOKIE_PATH = CACHE_PATH . 'cookies/';
const IMAGE_AUDIT_PATH = CACHE_PATH . 'audit/';
const PROHIBITION_IP_FILE = CACHE_PATH . 'prohibition_client_ip.dat';
######################################################################
// load..
require ROOT_PATH . 'config.php';
require 'functions.php';
spl_autoload_register('load');
