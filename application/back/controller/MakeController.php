<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/20
 * Time: 9:01
 */

namespace app\back\controller;


use think\Config;
use think\Controller;
use think\Db;

class MakeController extends Controller
{
    //
    public function  tableAction()
    {
        return $this->fetch();
    }

    public function infoAction()
    {
        $table = input('table');
        //获取备注
        $sql = "SELECT TABLE_COMMENT FROM information_schema.`TABLES` WHERE TABLE_SCHEMA=? AND TABLE_NAME=?";
        $table_schema = Config::get('database.database');
        $table_name = Config::get('database.prefix').$table;
        $tb_result = Db::query($sql,[$table_schema,$table_name]);
        //

        $sql = "SELECT COLUMN_NAME,COLUMN_COMMENT FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA=? AND TABLE_NAME=?";
        $col = Db::query($sql,[$table_schema,$table_name]);

        return [
            'comment' => $tb_result[0]['TABLE_COMMENT'],
            'field'=>$col,
            'table_name'=>$table
        ];
    }

    public function generateAction()
    {
        $input = input('post');
        dump($input);
    }
}