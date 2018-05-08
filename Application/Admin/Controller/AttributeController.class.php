<?php
namespace Admin\Controller;
use \Admin\Controller\IndexController;
class AttributeController extends IndexController 
{
    public function add()
    {
    	if(IS_POST)
    	{
    		$model = D('Admin/Attribute');
    		if($model->create(I('post.'), 1))
    		{
    			if($id = $model->add())
    			{
    				$this->success('添加成功！', U('lst?p='.I('get.p')));
    				exit;
    			}
    		}
    		$this->error($model->getError());
    	}
        $typeModel = D('type');
    	$typeList = $typeModel->getTypeList();
    	$type_id = I('get.tid');
    	$this->assign('type_id',$type_id);
        $this->assign('typeList',$typeList);
		$this->setPageBtn('添加属性', '属性列表', U('lst?p='.I('get.p').'&tid='.$type_id));
		$this->display();
    }
    public function edit()
    {
    	$id = I('get.id');
    	if(IS_POST)
    	{
    		$model = D('Admin/Attribute');
    		if($model->create(I('post.'), 2))
    		{
    			if($model->save() !== FALSE)
    			{
    				$this->success('修改成功！', U('lst', array('p' => I('get.p', 1))));
    				exit;
    			}
    		}
    		$this->error($model->getError());
    	}
    	$model = M('Attribute');
    	$data = $model->find($id);
        $typeModel = D('type');
        $typeList = $typeModel->getTypeList();
        $this->assign('typeList',$typeList);
    	$this->assign('data', $data);
		$this->setPageBtn('修改属性', '属性列表', U('lst?p='.I('get.p').'&tid='.$data['type_id']));
		$this->display();
    }
    public function delete()
    {
    	$model = D('Admin/Attribute');
    	if($model->delete(I('get.id', 0)) !== FALSE)
    	{
    		$this->success('删除成功！', U('lst', array('p' => I('get.p', 1))));
    		exit;
    	}
    	else 
    	{
    		$this->error($model->getError());
    	}
    }
    public function lst()
    {
        $type_id = I('get.tid');
    	$model = D('Admin/Attribute');
    	$data = $model->search();
    	$this->assign(array(
    		'data' => $data['data'],
    		'page' => $data['page'],
    	));
        $typeModel = D('type');
        $typeList = $typeModel->getTypeList();
        $this->assign('typeList',$typeList);
		$this->setPageBtn('属性列表', '添加属性', U('add?tid='.$type_id));
		$this->assign('type_id',$type_id);
    	$this->display();
    }
}