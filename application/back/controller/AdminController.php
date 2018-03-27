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

    public function loginAction()
    {

        $request = request();
        if ($request->isGet())
        {
            if (Session::get('admin'))
            {
                return $this->redirect('site/index');
            }
            //GET请求
            if (Session::get('message') == '' && Session::get('data') == '') {
                $message = '';
                $data = [];
            } else {
                $message = Session::get('message');
                $data = Session::get('data');
            }
            $this->assign('message', $message);
            $this->assign('data', $data);
            return $this->fetch();
        }elseif ($request->isPost()){
            $username = input('username');
            $password = input('password');

            $condition = [
                'username' => $username,
                'password' => md5(md5($password))
            ];
            $admin = Admin::where($condition)->find();

            if ($admin)
            {
                //设置登录状态
                Session::set('admin',$admin);
                //跳转首页

                //定义路由，判断session是否储存了用户之前访问的网址
                //Session::pull('route') get&&del
                $route = Session::has('route')?Session::pull('route'):'site/index';
                return $this->redirect($route);
            }else{
                //跳转到登录页面 错误信息返回到login-get方法
                $this->redirect('login',[],302,[
                    'message'=>'管理员信息错误',
                    'data' => input()
                ]);

            }

        }
    }

    public function  logoutAction()
    {
        Session::delete('admin');
        return $this->redirect('login');

    }

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
            if (!is_null($id))
            {
                $validate->scene('update');
            }
            if (!$validate->batch(true)->scene('update')->check($post_result))
            {
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

                $model->allowField(true)->save($post_result);
                if ($request) {
                    return $this->redirect('index');
                } else {
                    return $this->redirect('create');
                }
            }
        }

        return $this->fetch();
    }

    public function repasswordAction()
    {
        $request = request();
        $id = input('id');
        $this->assign('id',$id);
        if ($request->isGet())
        {
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

        }elseif ($request->isPost()) {

            //POST请求,数据入库
            $post_result = input('post.');
            $validate = new AdminValidate;
            if (!is_null($id))
            {
                $validate->scene('repassword');
            }
            if (!$validate->batch(true)->scene('repassword')->check($post_result))
            {
                return $this->redirect('repassword?id='.$post_result['id'], [], 302, [
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

                $model->allowField(true)->save($post_result);
                if ($request) {
                    return $this->redirect('index');
                } else {
                    return $this->redirect('repassword?');
                }
            }
        }
    }

}