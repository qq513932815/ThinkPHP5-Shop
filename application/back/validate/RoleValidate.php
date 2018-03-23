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
        
        'id' => '',
        'title' => '标题',
        'content' => '评论',
        'role' => '权限',
    ];
}