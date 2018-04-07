<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/12
 * Time: 11:12
 */

namespace app\back\controller;


use app\back\validate\BrandValidate;
use think\Controller;
use app\back\model\Brand as BrandModel;
use think\Db;
use think\Session;

class BrandController extends Controller
{

    public function indexAction()
    {
        $model = new BrandModel();
        //筛选
        //拿到传递数据
        $filter = input('filter/a');
        $filter_order = [];
        if(isset($filter['title']) && ''!=$filter['title'])
        {
            $model->where('title','like','%'.$filter['title'].'%');
            $filter_order['filter[title]'] = $filter['title'];
        }
        if(isset($filter['site']) && ''!=$filter['site'])
        {
            $model->where('site','like','%'.$filter['site'].'%');
            $filter_order['filter[title]'] = $filter['site'];
        }

        //排序
        $order = input('order/a');
        if(!empty($order))
        {
            $model->order([$order['field']=>$order['type']]);
        }

        //分页
        $model->where(null);
       $limit = 3;
       $list = $model->paginate($limit);
       $start = $list->listRows() * ($list->currentPage()-1) +1;
//       $end = min($list->total(),$list->currentPage()*$list->listRows());
        $end = $start + $list->listRows()-1;
       $this->assign('start',$start);
       $this->assign('end',$end);
       $this->assign('list',$list);

       $this->assign('filter',$filter);
       $this->assign('order',$order);
        $this->assign('filter_order',$filter_order);
       return $this->fetch();
    }

    public function createAction()
    {
        $request = request();
        if ($request->isGet()) {
            //GET请求
            if (Session::get('message') == '' && Session::get('data') == '') {
                $message = [];
                $data = [];
            } else {
                $message = Session::get('message');
                $data = Session::get('data');
            }
            $this->assign('message', $message);
            $this->assign('data', $data);
            return $this->fetch();
        } elseif ($request->isPost()) {
            //POST请求,数据入库
            $post_result = input('post.');
            $brand_validate = new BrandValidate();
            if (!$brand_validate->batch(true)->check($post_result)) {
                return $this->redirect('create', [], 302, [
                    'message' => $brand_validate->getError(),
                    'data' => $post_result
                ]);
            } else {
                $result = new BrandModel();
                $result->save($post_result);
                if ($request) {
                    return $this->redirect('index');
                } else {
                    return $this->redirect('create');
                }
            }
        }
    }

    public function updateAction()
    {
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
        BrandModel::destroy($selected);
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
                    $data = Db::name('brand')->find($id);
                }
            } else {
                $message = Session::get('message');
                $data = Session::get('data');
            }
            $this->assign('message', $message);
            $this->assign('data', $data);
            return $this->fetch();
        } elseif ($request->isPost()) {
            //POST请求,数据入库

            $post_result = $request->post();
            $post_result['logo'] = $request->file('logo');

            $brand_validate = new BrandValidate();
            if (!$brand_validate->batch(true)->check($post_result)) {
                return $this->redirect('create', [], 302, [
                    'message' => $brand_validate->getError(),
                    'data' => $post_result
                ]);
            } else {

                //转存文件
                if ($post_result['logo'])
                {
                    $info = $post_result['logo']->move(ROOT_PATH.'public/upload/brand');
                    $post_result['logo'] = $info->getSaveName();
                }

                //保存数据
                $model = new BrandModel();
                if (isset($post_result['id']))
                {
                    $model = $model->find($post_result['id']);
                }

                $model->save($post_result);
                if ($request) {
                    return $this->redirect('index');
                } else {
                    return $this->redirect('create');
                }
            }
        }

        return $this->fetch();
    }

}