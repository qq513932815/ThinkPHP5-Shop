<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 11:20
 */

namespace app\back\validate;


use think\Validate;

class CategoryValidate extends Validate
{
    protected $rule = [

    ];

    protected $field = [
        
        'id' => 'id',
        'title' => '分类',
        'parent_id' => '上级分类',
        'sort' => '排序',
        'is_used' => '启用状态',
        'create_time' => '创建时间',
        'update_time' => '修改时间',
    ];
}