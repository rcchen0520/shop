<?php
namespace Admin\Controller;
use \Admin\Controller\IndexController;
class AdminController extends IndexController 
{
    public function add()
    {
    	if(IS_POST)
    	{
    		$model = D('Admin/Admin');
    		$post = I('post.');
    		$post['password'] = md5($post['password'].C('MD5_KEY'));
            if($model->create($post, 1))
    		{
    			if($id = $model->add())
    			{
    			    $adminRoleModel = M('admin_role');
    			    $ids['admin_id'] = $id;
    			    $ids['role_id'] = $post['roleid'];
    			    $res = $adminRoleModel->add($ids);
    			    if($res)
                    {
                        $this->success('添加成功！', U('lst?p='.I('get.p')));
                        exit;
                    }
    			}
    		}
    		$this->error($model->getError());
    	}
        $roleMidel = D('Role');
    	$roleData = $roleMidel->getAllRoles();
    	$this->assign("roleData",$roleData);
		$this->setPageBtn('添加管理员', '管理员列表', U('lst?p='.I('get.p')));
		$this->display();
    }
    public function edit()
    {
    	$id = I('get.id');
        $model = M('Admin');
        $data = $model->find($id);
    	if(IS_POST)
    	{
    		$model = D('Admin/Admin');
    		if($model->create(I('post.'), 2))
    		{
    			if($model->save() !== FALSE)
    			{
    			    $arModel = M('admin_role');
    			    $upRes = $arModel->where(array('admin_id'=>$id))->save(array('role_id'=>I('post.roleid')));
    			    if($upRes !== false)
                    {
                        $this->success('修改成功！', U('lst', array('p' => I('get.p', 1))));
                        exit;
                    }
    			}
    		}
    		$this->error($model->getError());
    	}

    	$adminRoleModel = M('admin_role');
    	$ardata = $adminRoleModel->alias('a')->where(array("admin_id"=>$id))->join("LEFT JOIN rc_role b ON a.role_id = b.id")->find();
        $roleMidel = D('Role');
        $roleData = $roleMidel->getAllRoles();
        $this->assign("roleData",$roleData);
    	$this->assign('data', $data);
        $this->assign('arData',$ardata);
		$this->setPageBtn('修改管理员', '管理员列表', U('lst?p='.I('get.p')));
		$this->display();
    }
    public function delete()
    {
    	$model = D('Admin/Admin');
    	if($model->delete(I('get.id', 0)) !== FALSE)
    	{
    	    $arModel = M('admin_role');
    	    if($arModel->where(array('admin_id'=>I('get.id')))->delete() !== FALSE)
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
    	$model = D('Admin/Admin');
    	$data = $model->search();
    	$this->assign(array(
    		'data' => $data['data'],
    		'page' => $data['page'],
    	));
		$this->setPageBtn('管理员列表', '添加管理员', U('add'));
    	$this->display();
    }
}