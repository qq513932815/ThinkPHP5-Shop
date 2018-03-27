<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 11:20
 */

namespace app\back\validate;


use think\Validate;

class RoleValidate extends Validate
{
    protected $rule = [

    ];

    protected $field = [
        
        'id' => 'id',
        'title' => '名称',
        'description' => '描述',
        'is_super' => '是否为超管，0不是，1是',
        'sort' => '排序',
        'create_time' => '创建时间',
        'update_time' => '修改时间',
    ];
}