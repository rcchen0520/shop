<?php
namespace Admin\Controller;
use \Admin\Controller\IndexController;
class RoleController extends IndexController 
{
    public function add()
    {
    	if(IS_POST)
    	{
    		$model = D('Admin/Role');
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
        $priModel = D("Admin/Privilege");
    	$priData = $priModel->getTree();
    	$this->assign("priData",$priData);
		$this->setPageBtn('添加角色', '角色列表', U('lst?p='.I('get.p')));
		$this->display();
    }
    public function edit()
    {
    	$id = I('get.id');
    	if(IS_POST)
    	{
    		$model = D('Admin/Role');
    		if($model->create(I('post.'), 2))
    		{
    			if($model->save() !== FALSE)
    			{
    			    $rpModel = M('role_privilege');
    			    $rpModel->where(array('role_id'=>I('post.id')))->delete();
    			    $pri_id = I('post.pri_id');
    			    if($pri_id)
                    {
                        foreach($pri_id as $v)
                        {
                            $res = $rpModel->add(
                                array(
                                    'pri_id' => $v,
                                    'role_id' => I('post.id')
                                )
                            );
                        }
                    }
                    if($res !== FALSE)
                    {
                        $this->success('修改成功！', U('lst', array('p' => I('get.p', 1))));
                        exit;
                    }
    			}
    		}
    		$this->error($model->getError());
    	}
    	$model = M('Role');
    	$data = $model->find($id);
    	$rpModel = M('role_privilege');
    	$rpData = $rpModel->alias('a')->where(array('role_id'=>$id))->join('LEFT JOIN rc_privilege b ON a.pri_id = b.id')->select();
    	$priModel = D('Admin/Privilege');
    	$priData = $priModel->getTree();

    	foreach($priData as $k => $v)
        {
            foreach($rpData as $k0 => $v0)
            {
                if($v0['pri_id'] == $v['id'])
                {
                    $priData[$k]['is_selected'] = 1;
                    break;
                }
                else
                {
                    $priData[$k]['is_selected'] = 0;
                }
            }
        }
    	$this->assign('rpData',$rpData);
    	$this->assign('priData',$priData);
    	$this->assign('data', $data);

		$this->setPageBtn('修改角色', '角色列表', U('lst?p='.I('get.p')));
		$this->display();
    }
    public function delete()
    {
    	$model = D('Admin/Role');
    	$arModel = M('admin_role');
    	$is_exist = $arModel->where(array('role_id'=>I('get.id')))->find();
    	if(!empty($is_exist))
        {
            $this->error('存在后台管理员属于该角色，不能删除！');
            exit;
        }
    	if($model->delete(I('get.id', 0)) !== FALSE)
    	{
    	    $rpModel = M('role_privilege');
    	    $delRes = $rpModel->where(array('role_id'=>I('get.id')))->delete();
    	    if($delRes)
            {
                $this->success('删除成功！', U('lst', array('p' => I('get.p', 1))));
                exit;
            }
    	}
    	else 
    	{
    		$this->error($model->getError());
    	}
    }
    public function lst()
    {
    	$model = D('Admin/Role');
    	$data = $model->search();
    	$this->assign(array(
    		'data' => $data['data'],
    		'page' => $data['page'],
    	));

		$this->setPageBtn('角色列表', '添加角色', U('add'));
    	$this->display();
    }
}