<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/12
 * Time: 11:12
 */

namespace app\back\controller;


use app\back\validate\MemberValidate;
use app\back\model\Member;
use think\Controller;
use think\Db;
use think\Session;

class MemberController extends Controller
{

    public function indexAction()
    {
        $model = new Member;
        //筛选
        //拿到传递数据
        $filter = input('filter/a');
        $filter_order = [];

                    //判断是否有telephone条件
            if(isset($filter['telephone']) && ''!=$filter['telephone'])
            {
            $model->where('telephone','like','%'.$filter['telephone'].'%');
            $filter_order['filter[telephone]'] = $filter['telephone'];
            }            //判断是否有email条件
            if(isset($filter['email']) && ''!=$filter['email'])
            {
            $model->where('email','like','%'.$filter['email'].'%');
            $filter_order['filter[email]'] = $filter['email'];
            }            //判断是否有username条件
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
        Member::destroy($selected);
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
                    $data = Db::name('member')->find($id);
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
            $validate = new MemberValidate;
            if (!$validate->batch(true)->check($post_result)) {
                return $this->redirect('create', [], 302, [
                    'message' => $validate->getError(),
                    'data' => $post_result
                ]);
            } else {
                //保存数据
                $model = new Member;
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