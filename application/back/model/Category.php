<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/13
 * Time: 9:06
 */

namespace app\back\model;


use think\Model;
use think\Db;

class Category extends Model
{
    public function getTree()
    {
        //获取数据树的代码
        $rows = Db::name('category')->select();
        //带有层级关系的树
        $tree = $this->treeAction($rows,0,0);
        return $tree;
    }

    protected function treeAction($rows,$id=0,$level=0)
    {
        //递归算法
        static $tree = [];
        foreach ($rows as $row)
        {
            if ($id==$row['parent_id'])
            {
                $row['level']=$level;
                $tree[] = $row;
                $this->treeAction($rows,$row['id'],$level+1);
            }
        }

        return $tree;
    }
}