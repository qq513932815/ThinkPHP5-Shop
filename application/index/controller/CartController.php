<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/4/9
 * Time: 9:57
 */

namespace app\index\controller;

use cart\Cart;
use think\Controller;

class CartController extends Controller
{
    public function addAction()
    {

        Cart::getInstance()->add(input('product_id'),input('buy_quantity'));
        return [
            'success' => 'ok'
        ];
    }

    //获取购物车中商品
    public function infoAction()
    {

        $product_list = Cart::getInstance()->exportInfo();
        return $product_list;
    }
}