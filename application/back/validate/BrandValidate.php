<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 11:20
 */

namespace app\back\validate;


use think\Validate;

class BrandValidate extends Validate
{
    protected $rule = [
        'title' => 'require|max:32',
        'site' => 'url',
        'sort' => 'integer'
    ];

    protected $field = [
        'title' => '品牌名称',
        'site' => '品牌官网',
        'sort' => '排序输入'
    ];
}