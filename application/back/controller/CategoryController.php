<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/12
 * Time: 11:12
 */

namespace app\back\controller;


use app\back\validate\CategoryValidate;
use app\back\model\Category;
use think\Cache;
use think\Controller;
use think\Db;
use think\Session;

class CategoryController extends Controller
{
    const CACHE_TREE_KEY = 'category_tree';
    public function indexAction()
    {
        $model = new Category;

        if (!($tree = Cache::get(self::CACHE_TREE_KEY)))
        {
            $tree = $model->getTree();
            //没有缓存,去数据库取数据
            Cache::set(self::CACHE_TREE_KEY,$tree);
        }

        $this->assign('rows',$tree);
        return $this->fetch();
    }

    public function multiAction()
    {
        $selected = input('selected/a',[]);
        if(empty($selected))
        {
            return $this->redirect('index');
        }
        //批量删除
        Category::destroy($selected);
        return $this->redirect('index');
    }

    public function setAction()
    {
        //获取当前id
        $id = input('id');
        $this->assign('id',$id);
        $request = request();
        if ($request->isGet()) {
            //GET请求
            if (Session::get('message') == '' && Session::get('data') == '') {
                $message = [];
                $data = [];
                if (!empty($id))
                {
                    $data = Db::name('category')->find($id);
                }
            } else {
                $message = Session::get('message');
                $data = Session::get('data');
            }

            //分配一下全部的category
            $this->assign('category_list',(new Category())->getTree());
            $this->assign('message', $message);
            $this->assign('data', $data);
            return $this->fetch();
        } elseif ($request->isPost()) {
            //POST请求,数据入库
            $post_result = input('post.');
            $validate = new CategoryValidate;
            if (!$validate->batch(true)->check($post_result)) {
                return $this->redirect('set', [], 302, [
                    'message' => $validate->getError(),
                    'data' => $post_result
                ]);
            } else {
                //保存数据
                $model = new Category;
                if (isset($post_result['id']))
                {
                    $model = $model->find($post_result['id']);
                }

                $model->save($post_result);
                if ($request) {
                    return $this->redirect('index');
                } else {
                    return $this->redirect('set');
                }
            }
        }

        return $this->fetch();
    }

}