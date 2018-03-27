<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/27
 * Time: 9:27
 */

namespace app\back\behavior;


use think\Session;

class CheckAuth
{
    public function run(&$params)
    {
        $request= request();
        $ext = [
            'admin' => ['login','aaa']
        ];
        //判断用户当前访问的是哪个action
        $controller = $request->controller();
        if (isset($ext[strtolower($controller)]))
        {
            $action = $request->action();
            if (in_array($action,$ext[strtolower($controller)]))
            {
                return;
            }
        }

        //检测用户是否登录
        if(!Session::has('admin'))
        {
            redirect('back/admin/login')->send();
            die;
        }else{
            //定义路由，判断session是否储存了用户之前访问的网址
            //Session::pull('route') get&&del
            $route = 'site/index';
            redirect($route);//Session::has('route')?Session::pull('route'):
        }
    }
}