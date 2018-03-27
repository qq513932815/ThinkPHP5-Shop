<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/27
 * Time: 9:21
 */
//执行任意一个控制器里面的方法都会触发的操作
return [
    'action_begin' => [
        'app\\back\\behavior\\CheckAuth'
    ]
];