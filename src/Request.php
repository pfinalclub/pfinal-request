<?php
/**
 * Created by PhpStorm.
 * User: 南丞
 * Date: 2019/2/28
 * Time: 11:00
 *
 *
 *                      _ooOoo_
 *                     o8888888o
 *                     88" . "88
 *                     (| ^_^ |)
 *                     O\  =  /O
 *                  ____/`---'\____
 *                .'  \\|     |//  `.
 *               /  \\|||  :  |||//  \
 *              /  _||||| -:- |||||-  \
 *              |   | \\\  -  /// |   |
 *              | \_|  ''\---/''  |   |
 *              \  .-\__  `-`  ___/-. /
 *            ___`. .'  /--.--\  `. . ___
 *          ."" '<  `.___\_<|>_/___.'  >'"".
 *        | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *        \  \ `-.   \_ __\ /__ _/   .-` /  /
 *  ========`-.____`-.___\_____/___.-`____.-'========
 *                       `=---='
 *  ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *           佛祖保佑       永无BUG     永不修改
 *
 */

namespace pf\request;

use pf\request\build\Base;

class Request
{
    protected static $link;
    public static function single()
    {
        if ( ! self::$link) {
            self::$link = new Base();
        }
        return self::$link;
    }
    public function __call($method, $params)
    {
        return call_user_func_array([self::single(), $method], $params);
    }
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([self::single(), $name], $arguments);
    }
}