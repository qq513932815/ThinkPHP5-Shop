<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 11:20
 */

namespace app\back\validate;


use think\Validate;

class ProductValidate extends Validate
{
    protected $rule = [
        'title' => 'require',

    ];

    protected $field = [
        
        'id' => '',
        'title' => '商品标题',
        'upc' => '条码',
        'image' => '缩略图',
        'inventory' => '库存',
        'mininum' => '最小起售',
        'price' => '售价',
        'price_orign' => '原价',
        'is_shopping' => '配送支持',
        'date_avaliable' => '起售时间',
        'status' => '商品状态',
        'brind_id' => '品牌id',
        'description' => '描述',
        'category_id' => '类别',
        'sort' => '排序',
        'create_time' => '创建时间',
        'update_time' => '修改时间',
    ];
}