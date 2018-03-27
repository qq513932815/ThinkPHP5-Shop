<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/12
 * Time: 11:12
 */

namespace app\back\controller;


use app\back\model\Action;
use app\back\validate\RoleValidate;
use app\back\model\Role;
use think\Controller;
use think\Db;
use think\Session;

class RoleController extends Controller
{

    public function indexAction()
    {
        $model = new Role;
        //筛选
        //拿到传递数据
        $filter = input('filter/a');
        $filter_order = [];

                    //判断是否有title条件
            if(isset($filter['title']) && ''!=$filter['title'])
            {
            $model->where('title','like','%'.$filter['title'].'%');
            $filter_order['filter[title]'] = $filter['title'];
            }            //判断是否有description条件
            if(isset($filter['description']) && ''!=$filter['description'])
            {
            $model->where('description','like','%'.$filter['description'].'%');
            $filter_order['filter[description]'] = $filter['description'];
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
        Role::destroy($selected);
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
                    $data = Db::name('role')->find($id);
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
            $validate = new RoleValidate;
            if (!$validate->batch(true)->check($post_result)) {
                return $this->redirect('set', [], 302, [
                    'message' => $validate->getError(),
                    'data' => $post_result
                ]);
            } else {
                //保存数据
                $model = new Role;
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

    public function setactionAction()
    {
        $request = request();
        $id=input('id');
        $role = Role::get($id);
        $this->assign('role',$role);

        //获取用户已有的权限
        $owner_action = Db::name('role_action')->where('role_id',$id)->column('action_id');

        if ($request->isGet())
        {
            //获取全部权限
            $action_list = Action::order('id')->select();

            $this->assign('action_list',$action_list);
            $this->assign('checked_list',$owner_action);//已有的权限

            return $this->fetch();
        }elseif ($request->isPost()){
            //获取用户传递的值
            $action = input('actions/a',[]);
            //更新role_action

            //计算两个数组的差集 array_diff(array1,array2)去array2中寻找array1中没有的子项
            $deletes = array_diff($owner_action,$action);//需要删除的权限
            Db::name('role_action')->where([
                'role_id' => $id,
                'action_id' => ['in',$deletes]
            ])->delete();


            $inserts = array_diff($action,$owner_action);//需要新增的权限
            $rows = array_map(function ($action_id) use ($id)
            {
                return [
                    'role_id' => $id,
                    'action_id' => $action_id
                ];
            },$inserts);
            Db::name('role_action')->insertAll($rows);

            return $this->redirect('setaction',['id'=>$id]);
        }
    }

}