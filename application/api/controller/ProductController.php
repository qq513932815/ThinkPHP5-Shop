<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/4/8
 * Time: 11:51
 */

namespace app\api\controller;


use think\Controller;
use app\api\model\Product;

class ProductController extends Controller
{
    public function listAction()
    {
        $product = new Product();
        //首页要加载商上架的前四条数据
        $limit = input('limit',4);
        $type = input('type','all');
        switch ($type)
        {
            case 'new':
                $status = 1;
                break;
            case 'old':
                $status = 0;
                break;
            default:
                    break;
        }
        $status = 1;
        $result = $product->where([
            'status' => $status
        ])->paginate($limit);
        return json($result);
    }
}