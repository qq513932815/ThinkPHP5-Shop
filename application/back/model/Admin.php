<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 9:06
 */

namespace app\back\model;


use think\Model;

class Admin extends Model
{
    //密码修改器
    public function setPasswordAttr($value)
    {
        return md5(md5($value));
    }
}