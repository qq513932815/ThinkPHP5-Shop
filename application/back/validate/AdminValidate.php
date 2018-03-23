<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 11:20
 */

namespace app\back\validate;


use think\Validate;

class AdminValidate extends Validate
{
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
        'password-confirm' => 'require|confirm:password'
    ];

    protected $field = [
        
        'id' => 'ID',
        'username' => '用户名',
        'password' => '密码',
        'sort' => '排序',
        'create_time' => '创建时间',
        'update_time' => '修改时间',
        'password-confirm' => '确认密码',
    ];
}