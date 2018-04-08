<?php
/**
 * Created by PhpStorm.
 * User: LXT
 * Date: 2018/3/12
 * Time: 11:12
 */

namespace app\back\controller;


use app\back\model\Category;
use app\back\validate\ProductValidate;
use app\back\model\Product;
use think\Controller;
use think\Db;
use think\Session;
use think\Cache;

class ProductController extends Controller
{

    public function indexAction()
    {
        $model = new Product;
        //筛选
        //拿到传递数据
        $filter = input('filter/a');
        $filter_order = [];

                    //判断是否有title条件
            if(isset($filter['title']) && ''!=$filter['title'])
            {
            $model->where('title','like','%'.$filter['title'].'%');
            $filter_order['filter[title]'] = $filter['title'];
            }            //判断是否有upc条件
            if(isset($filter['upc']) && ''!=$filter['upc'])
            {
            $model->where('upc','like','%'.$filter['upc'].'%');
            $filter_order['filter[upc]'] = $filter['upc'];
            }            //判断是否有price条件
            if(isset($filter['price']) && ''!=$filter['price'])
            {
            $model->where('price','like','%'.$filter['price'].'%');
            $filter_order['filter[price]'] = $filter['price'];
            }            //判断是否有status条件
            if(isset($filter['status']) && ''!=$filter['status'])
            {
            $model->where('status','like','%'.$filter['status'].'%');
            $filter_order['filter[status]'] = $filter['status'];
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
        Product::destroy($selected);
        return $this->redirect('index');
    }
    const CACHE_TREE_KEY = 'category_tree';
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
                    $data = Db::name('product')->find($id);
                }
            } else {
                $message = Session::get('message');
                $data = Session::get('data');
            }
            //分配单位到view页面
            $this->assign('unit_list',Db::name('unit')->select());
            $this->assign('brand_list',Db::name('brand')->select());

            $tree = [];
            if (!($tree = Cache::get(self::CACHE_TREE_KEY)))
            {
                $tree = (new Category())->getTree();
                //没有缓存,去数据库取数据
                Cache::set(self::CACHE_TREE_KEY,$tree);
            }
            $this->assign('category_list',$tree);


            $this->assign('message', $message);
            $this->assign('data', $data);
            return $this->fetch();
        } elseif ($request->isPost()) {
            //POST请求,数据入库
            $post_result = input('post.');

//            dump($post_result);die;

            $validate = new ProductValidate;
            if (!$validate->batch(true)->check($post_result)) {
                return $this->redirect('set', [], 302, [
                    'message' => $validate->getError(),
                    'data' => $post_result
                ]);
            } else {
                //保存数据
                $model = new Product;
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

    public function uploadAction()
    {
        $file = request()->file('file');
        $info = $file->validate(['size' => 1*1024*1024,'ext' => 'jpg,png,gif'])->move(ROOT_PATH.'public/upload/product');
        if ($info)
        {
            //上传成功
            return [
                'success' => 'success'
            ];
        }else{
            return [
                'error' => $file->getError()
            ];
        }
    }

}