<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/4/8
 * Time: 10:21
 */

namespace app\back\controller;


use think\Controller;

class PageController extends Controller
{
    public function indexAction()
    {
        //通过后台生成静态页面

        $content = $this->fetch('index@site/index');

        file_put_contents(ROOT_PATH.'public/html/index.html',$content);
        echo '生成成功';
    }
}