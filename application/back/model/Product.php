<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 9:06
 */

namespace app\back\model;


use think\Model;

class Product extends Model
{
    protected function setUpcAttr($value)
    {
        if (is_null($value)||$value == '')
        {
            return uniqid();
        }
    }
}