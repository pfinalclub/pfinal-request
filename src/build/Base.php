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
use pf\config\Config;
use pf\cookie\Cookie;

class Base
{
    protected $items = [];

    public function __construct()
    {
        $_SERVER['SCRIPT_NAME'] = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            $_SERVER['REQUEST_METHOD'] = '';
        }
        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = '';
        }
        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = '';
        }
        defined('NOW') or define('NOW', $_SERVER['REQUEST_TIME']);
        defined('MICROTIME') or define('MICROTIME', $_SERVER['REQUEST_TIME_FLOAT']);
        defined('__URL__') or define('__URL__', $this->url());
        defined('__HISTORY__') or define("__HISTORY__", $this->history());
        defined('__ROOT__') or define('__ROOT__', $this->domain());
        defined('__WEB__') or define('__WEB__', $this->web());
        define('DS', DIRECTORY_SEPARATOR);
        $this->defineRequestConst();
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

    public function __call($action, $arguments)
    {
        $action = strtoupper($action);
        if (empty($arguments)) {
            return $this->items[$action];
        }
        $data = PFarr::pf_get($this->items[$action], $arguments[0]);
        if (!is_null($data) && !empty($arguments[2])) {
            return $this->batchFunctions($arguments[2], $data);
        }
        return !is_null($data) ? $data : (isset($arguments[1]) ? $arguments[1] : null);
    }


    protected function batchFunctions($functions, $value)
    {
        $functions = is_array($functions) ? $functions : [$functions];
        foreach ($functions as $func) {
            $value = $func($value);
        }
        return $value;
    }

    protected function defineRequestConst()
    {
        $this->items['POST'] = $_POST;
        $this->items['GET'] = $_GET;
        $this->items['REQUEST'] = $_REQUEST;
        $this->items['SERVER'] = $_SERVER;
        $this->items['GLOBALS'] = $GLOBALS;
        //$this->items['SESSION'] = Session::all();
        $this->items['COOKIE'] = Cookie::all();
        if (empty($_POST)) {
            $input = file_get_contents('php://input');
            if ($data = json_decode($input, true)) {
                $this->items['POST'] = $data;
            }
        }
        defined('IS_GET') or define('IS_GET', $this->isMethod('get'));
        defined('IS_POST') or define('IS_POST', $this->isMethod('post'));
        defined('IS_DELETE') or define('IS_DELETE', $this->isMethod('delete'));
        defined('IS_PUT') or define('IS_PUT', $this->isMethod('put'));
        defined('IS_AJAX') or define('IS_AJAX', $this->isAjax());
        defined('IS_WECHAT') or define('IS_WECHAT', $this->isWeChat());
        defined('IS_MOBILE') or define('IS_MOBILE', $this->isMobile());
    }

    public function history()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    public function domain()
    {
        return defined('RUN_MODE') && RUN_MODE != 'HTTP' ? '' : trim('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/\\');
    }

    public function web()
    {
        $root = $this->domain();
        return Config::get('http.rewrite') ? $root : $root . '/index.php';
    }

    public function isMethod($action)
    {
        switch (strtoupper($action)) {
            case 'GET':
                return $_SERVER['REQUEST_METHOD'] == 'GET';
            case 'POST':
                return $_SERVER['REQUEST_METHOD'] == 'POST' || !empty($this->items['POST']);
            case 'DELETE':
                return $_SERVER['REQUEST_METHOD'] == 'DELETE' ?: (isset($_POST['_method']) && $_POST['_method'] == 'DELETE');
            case 'PUT':
                return $_SERVER['REQUEST_METHOD'] == 'PUT' ?: (isset($_POST['_method']) && $_POST['_method'] == 'PUT');
            case 'AJAX':
                return $this->isAjax();
            case 'wechat':
                return $this->isWeChat();
            case 'mobile':
                return $this->isMobile();

        }
    }

    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function isWeChat()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && strrpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

    public function isMobile()
    {
        if ($this->isWeChat()) {
            return true;
        }
        if (!empty($_GET['_mobile'])) {
            return true;
        }
        
    }

}
