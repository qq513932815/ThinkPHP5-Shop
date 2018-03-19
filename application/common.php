<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function myUrl($route,$params=[],$order=[],$field=null)
{
    \think\Config::set('url_common_param',true);
    $params['order[field]'] = $field;

    $params['order[type]'] = ($order['field']==$field && $order['type']=='asc')?'desc':'asc';
    return url($route,$params);
}

function classOrder($order,$field)
{
    //验证代码严谨性，用户只传入了title没传递desc|asc
    if (isset($order['field'])&&!isset($order['type'])) return '';

    if ($order['field']==$field)
    {
        return $order['type']=='asc'?'desc':'asc';
    }else{
        return '';
    }
}