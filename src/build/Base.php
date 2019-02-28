<?php
/**
 * Created by PhpStorm.
 * User: 南丞
 * Date: 2019/2/28
 * Time: 11:01
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

namespace pf\request\build;

use pf\arr\PFarr;

class Base
{
    protected $items = [];

    public function __construct()
    {

    }

    public function url()
    {
        return trim('http://' . $_SERVER['HTTP_HOST'] . '/' . trim($_SERVER['REQUEST_URI'], '/\\'), '/');
    }

    public function set($name, $value)
    {
        $info = explode('.', $name);
        $action = strtoupper(array_shift($info));
        if (isset($this->items[$action])) {
            $this->items[$action] = PFarr::pf_set($this->items[$action], implode('.', $info), $value);
            return true;
        }
    }
}
