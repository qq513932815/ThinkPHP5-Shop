<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/4/8
 * Time: 11:51
 */

namespace app\api\controller;


use think\Controller;

class ProductController extends Controller
{
    public function listAction()
    {
        $data = ['success' => 'ok','msg' => '666'];
        return json($data);
    }
}