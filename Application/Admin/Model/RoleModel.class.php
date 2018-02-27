<?php
namespace Admin\Model;
use Think\Model;
class RoleModel extends Model 
{
	protected $insertFields = array('role_name');
	protected $updateFields = array('id','role_name');
	protected $_validate = array(
	        array('role_name','require','角色名称不能为空',1,'regex',3),
	        array('role_name','1,30','角色名称长度不能超过30个字符',1,'length',3),
	);
	public function search($pageSize = 20)
	{
		/**************************************** 搜索 ****************************************/
		$where = array();
		if($role_name = I('get.role_name'))
			$where['role_name'] = array('like', "%$role_name%");
		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		/************************************** 取数据 ******************************************/
		$data['data'] = $this->field('a.*,GROUP_CONCAT(c.pri_name) pri_name')->alias('a')->join('LEFT JOIN rc_role_privilege b on a.id = b.role_id LEFT JOIN rc_privilege c on b.pri_id=c.id')->where($where)->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();
		return $data;
	}
	// 添加前
	protected function _before_insert(&$data, $option)
	{
	}
	// 修改前
	protected function _before_update(&$data, $option)
	{
	}
	// 删除前
	protected function _before_delete($option)
	{
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
	}
	//添加后
    protected function _after_insert($data,$option){
	    $priId = I('post.pri_id');
	    if($priId){
            $rpModel = M('RolePrivilege');
            foreach($priId as $k => $v){
                $rpModel->add(
                    array(
                        'pri_id' => $v,
                        'role_id' => $data['id'],
                    )
                );
            }
        }
    }
	/************************************ 其他方法 ********************************************/
	function getAllRoles()
    {
        return $this->select();
    }
}