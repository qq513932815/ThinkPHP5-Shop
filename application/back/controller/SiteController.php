<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/26
 * Time: 20:10
 */

namespace app\back\controller;


use think\Controller;

class SiteController extends Controller
{
    public function indexAction()
    {
        $user = session('admin')->toArray();
        $username = $user['username'];
        $this->assign('username',$username);
        return $this->fetch();
    }
}