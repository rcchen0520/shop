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
}