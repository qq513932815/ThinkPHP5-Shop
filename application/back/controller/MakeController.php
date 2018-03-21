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

        $this->createController();
        $this->createModel();
        $this->createValidate();
        $this->createViewIndex();

    }

    protected function createViewIndex()
    {
        //生成一堆代码  路径：view/member/index.heml

        //搜索区域
        $search_field_list = '';
        $template_search = file_get_contents(APP_PATH.'back/code/viewIndexSearchField.html');

        //表头区域
        $table_head_list = '';
        $template_head = file_get_contents(APP_PATH.'back/code/viewIndexTableHead.html');
        $template_head_order = file_get_contents(APP_PATH.'back/code/viewIndexTableHeadOrder.html');

        //表格主体区域
        $table_data = '';
        $template_data = file_get_contents(APP_PATH.'back/code/viewIndexTableData.html');

        //所有的三部分，都是基于在前台勾选的复选框
    }

    //公用的替换方法
    protected function replace($template,$search,$replace,$file)
    {
        $template_content = file_get_contents($template);
        $content = str_replace($search,$replace,$template_content);

        //判断一下要生成的文件是否存在
        $path = dirname($file);//获取文件目录
        if(!is_dir($path))
        {
            mkdir($path);
        }
        //写入文件
        file_put_contents($file,$content);

    }

    public function createValidate()
    {
        //获取验证器的名称
        $validate = $this->mkValidate();

        $label_list = '';
        $template = APP_PATH.'back/code/validateField.php';
        $template_content = file_get_contents($template);
        foreach ($this->input['fields'] as $field)
        {
            $search = ['%field%','%comment%'];
            $replace = [$field['name'],$field["comment"]];
            $label_list .=str_replace($search,$replace,$template_content);
        }

        //替换文件
        $template = APP_PATH.'back/code/validate.php';
        $search = ['%validate%','%label_list%'];
        $replace = [$validate,$label_list];
        $file = APP_PATH.'back/validate/'.$validate.'.php';

        $this->replace($template,$search,$replace,$file);
        echo '验证器已经生成',$file,'</br>';
    }

    public function createModel()
    {
        $model = $this->mkModel();

        $template = APP_PATH.'back/code/model.php';
        $search = ['%model%'];
        $replace = [$model];
        $file = APP_PATH.'back/model/'.$model.'.php';
        $this->replace($template,$search,$replace,$file);
        echo '模型已经生成',$file,'</br>';
    }

    //生成控制器
    public function createController()
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

        //重新生成一个控制器文件
        $file = APP_PATH.'back/controller/'.$controller.'.php';
        file_put_contents($file,$content);
        echo '控制器已经生成',$file,'</br>';

        //
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