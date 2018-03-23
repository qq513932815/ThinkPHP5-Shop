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
        
        'id' => ' id',
        'telephone' => '电话',
        'email' => '邮箱',
        'username' => '用户名',
        'password' => '密码',
        'hash_str' => '哈希值',
        'active_time' => '激活时间',
        'status' => '状态',
        'sort' => '排序',
        'create_time' => '创建时间',
        'update_time' => '修改时间',
    ];
}