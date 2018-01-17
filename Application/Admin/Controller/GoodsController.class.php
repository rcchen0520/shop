<?php
namespace Admin\Controller;
use Think\Controller;

class GoodsController extends Controller
{
    public function add ()
    {
        if(IS_POST)
        {
            $model = D('Goods');
            if($model->create(I('post.')))
            {
                if($model->add())
                {
                    $this->success('操作成功！',u('lst'));
                    exit;
                }
            }
            $error = $model->getError();
            $this->error($error);
        }
        $this->display();
    }

    public function lst()
    {
        $model = D('Goods');
        $data = $model->search();
        if($data)
        {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }
        $this->display();
    }

    public function delete()
    {
        $model = D('goods');
        $model->delete(I('get.id'));
        $this->success('操作成功！',U('lst?p='.I('get.p')));
    }

    public function edit()
    {
        if(IS_POST)
        {
            $model = D('goods');
            if($model->create(I('post.'),2))
            {
                if(false !== $model->save())
                {
                    $this->success('操作成功！', U('lst?p='.I('get.p')));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        //接受商品id
        $id = I('get.id');
        //从数据库中获取商品信息
        $model = M('goods');
        $info = $model->find($id);
        $this->assign('info',$info);
        $this->display();
    }
}