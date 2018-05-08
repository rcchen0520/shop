<?php
namespace Admin\Controller;
use Think\Controller;

class IndexController extends Controller{

    public function __construct()
    {
        parent::__construct();

        if(!session('id')){
            redirect(U('/Admin/Login/login'));
        }
        $adminId = session('id');
        //验证当前用户是否有权限访问当前页面
        $url = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $where = 'module_name="'.MODULE_NAME.'" AND controller_name="'.CONTROLLER_NAME.'" AND action_name="'.ACTION_NAME.'"';
        if(CONTROLLER_NAME != 'Index'){
            if($adminId == 1)
            {
                $sql = 'SELECT COUNT(*) has FROM rc_privilege WHERE '.$where;
            }else
            {
                $sql = 'SELECT COUNT(*) has FROM rc_role_privilege a LEFT JOIN rc_privilege as b ON a.pri_id=b.id LEFT JOIN rc_admin_role as c ON a.role_id=c.role_id WHERE c.admin_id='.$adminId.' AND '.$where;
            }
            $db = M();
            $pri = $db->query($sql);
            if($pri[0]['has'] < 1){
                $this->error('无权访问！');
            }
        }
    }

    public function index(){
        $this->display();
    }
    public function main(){
        $this->display();
    }
    public function menu(){
        $adminId = session('id');
        if($adminId == 1)
        {
            $sql = 'SELECT * FROM rc_privilege';
        }
        else
        {
            $sql = 'SELECT b.* FROM rc_role_privilege a LEFT JOIN rc_privilege b ON a.pri_id = b.id LEFT JOIN rc_admin_role c ON a.role_id = c.role_id WHERE c.admin_id='.$adminId;
        }
        $db = M();
        $pri = $db->query($sql);
        foreach($pri as $k => $v)
        {
            if($v['parent_id'] == 0)
            {
                foreach($pri as $k1 => $v1)
                {
                    if($v1['parent_id'] == $v['id'])
                    {
                        $v['children'][] = $v1;
                    }
                }
                $btn[] = $v;
            }
        }
        $this->assign('btn',$btn);
        $this->display();
    }
    public function top(){
        $this->display();
    }

    function setPageBtn($title,$btnName,$btnLink)
    {
        $this->assign('_page_title',$title);
        $this->assign('_page_btn_name',$btnName);
        $this->assign('_page_btn_link',$btnLink);
    }
}