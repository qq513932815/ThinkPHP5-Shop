<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 11:20
 */

namespace app\back\validate;


use think\Validate;

class MemberValidate extends Validate
{
    protected $rule = [

    ];

    protected $field = [
        
        'id' => '主键',
        'name' => '用户名',
        'mark' => '备注',
        'sort' => '排序',
        'create_time' => '创建时间',
        'update_time' => ' 修改时间',
    ];
}