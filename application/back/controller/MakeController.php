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

    protected $input = [];
    protected  $label = [];

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
        $this->input['table'] = input('table');
        $this->input['comment'] = input('comment');
        $this->input['fields'] = input('fields/a');

        $this->controller();
    }

    //生成控制器
    public function controller()
    {
        $model = $this->mkModel();
        $controller = $this->mkController();
        $validate = $this->mkValidate();

        //文件读取
        $template = file_get_contents(APP_PATH.'back/code/controller.php');
        //替换里面的内容
        $search = ['%model%','%controller%','%validate%','%table%'];
        $replace = [$model,$controller,$validate,$this->input['table']];
        $content = str_replace($search,$replace,$template);

        //重新生成一个文件
        $file = APP_PATH.'back/controller/'.$controller.'.php';
        file_put_contents($file,$content);
        echo '控制器已经生成',$file,'</br>';
    }

    public function mkModel()
    {
        $table = $this->input['table'];
        if (!isset($this->label['model']))
        {
            $this->label['model'] = implode(array_map('ucfirst',explode('_',$table)));
        }
        return $this->label['model'];
    }

    public function mkController()
    {
        $table = $this->input['table'];
        if (!isset($this->label['controller']))
        {
            $this->label['controller'] = implode(array_map('ucfirst',explode('_',$table))).'Controller';
        }
        return $this->label['controller'];
    }

    public function mkValidate()
    {
        $table = $this->input['table'];
        if (!isset($this->label['validate']))
        {
            $this->label['validate'] = implode(array_map('ucfirst',explode('_',$table))).'Validate';
        }
        return $this->label['validate'];
    }
}