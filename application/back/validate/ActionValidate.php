<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 11:20
 */

namespace app\back\validate;


use think\Validate;

class ActionValidate extends Validate
{
    protected $rule = [

    ];

    protected $field = [
        
        'id' => 'id',
        'title' => '名称',
        'rule' => '动作路径',
        'sort' => '排序',
        'create_time' => '创建时间',
        'update_time' => '修改时间',
    ];
}