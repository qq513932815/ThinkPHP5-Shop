<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/12
 * Time: 11:12
 */

namespace app\back\controller;


use app\back\validate\AdminValidate;
use app\back\model\Admin;
use think\Controller;
use think\Db;
use think\Session;

class AdminController extends Controller
{

    public function indexAction()
    {
        $model = new Admin;
        //筛选
        //拿到传递数据
        $filter = input('filter/a');
        $filter_order = [];

                    //判断是否有username条件
            if(isset($filter['username']) && ''!=$filter['username'])
            {
            $model->where('username','like','%'.$filter['username'].'%');
            $filter_order['filter[username]'] = $filter['username'];
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

    public function multiAction()
    {
        $selected = input('selected/a',[]);
        if(empty($selected))
        {
            return $this->redirect('index');
        }
        //批量删除
        Admin::destroy($selected);
        return $this->redirect('index');
    }

    public function setAction()
    {
        //获取当前id
        $id = input('get.id');
        $this->assign('id',$id);
        $request = request();
        if ($request->isGet()) {
            //GET请求
            if (Session::get('message') == '' && Session::get('data') == '') {
                $message = [];
                $data = [];
                if (!empty($id))
                {
                    $data = Db::name('admin')->find($id);
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
            $post_result = input('post.');
            $validate = new AdminValidate;
            if (!$validate->batch(true)->check($post_result)) {
                return $this->redirect('set', [], 302, [
                    'message' => $validate->getError(),
                    'data' => $post_result
                ]);
            } else {
                //保存数据
                $model = new Admin;
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