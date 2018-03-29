<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/27
 * Time: 9:27
 */

namespace app\back\behavior;


use priv\Privilege;
use think\Session;

class CheckAuth
{
    public function run(&$params)
    {
        $request= request();
//        dump($request->controller());
//        die;
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
            Session::set('route',$request->path());
            redirect('back/admin/login')->send();
            die;
        }

        //判断用户是否有权限访问每一个动作
        if (!Privilege::checkPrivAction($request->path()))
        {
            echo $request->path();
            redirect('back/admin/login',[],302,[
                'message' => '你没有权限'
            ])->send();die;
        }
    }
}