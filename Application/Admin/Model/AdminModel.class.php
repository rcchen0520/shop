<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model
{
    protected $insertFields = array('username','password','cpassword','is_use');
    protected $updateFields = array('id','username','password','cpassword','is_use');
    // 登录时表单验证的规则
    public $_login_validate = array(
        array('username', 'require', '用户名不能为空！', 1),
        array('password', 'require', '密码不能为空！', 1),
//        array('chkcode', 'require', '验证码不能为空！', 1),
//        array('chkcode', 'chk_chkcode', '验证码不正确！', 1, 'callback'),
    );
    // 添加修改管理员时用
    protected $_validate = array(
        array('username', 'require', '账号不能为空！', 1, 'regex', 3),
        array('username', '1,30', '账号的值最长不能超过 30 个字符！', 1, 'length', 3),
        array('password', 'require', '密码不能为空！', 1, 'regex', 1),
        array('password', '1,32', '密码的值最长不能超过 32 个字符！', 1, 'length', 1),
        array('is_use', 'number', '是否启用 1：启用0：禁用必须是一个整数！', 2, 'regex', 3),
        array('username','','用户名已存在',1,'unique',3),
        array('cpassword','password',"两次输入的密码不一致",1,'confirm',3),
    );
    public function chk_chkcode($code)
    {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }
    public function login()
    {
        // 获取表单中的用户名密码
        $username = $this->username;
        $password = $this->password;
        // 先查询数据库有没有这个账号
        $user = $this->where(array(
            'username' => array('eq', $username),
        ))->find();
        // 判断有没有账号
        if($user)
        {
            // 判断是否启用(超级管理员不能禁用）
            if($user['id'] == 1 || $user['is_use'] == 1)
            {
                // 判断密码
                if($user['password'] == md5($password.C('MD5_KEY')))
                {
                    // 把ID和用户名存到session中
                    session('id', $user['id']);
                    session('username', $user['username']);
                    return TRUE;
                }
                else
                {
                    $this->error = '密码不正确！';
                    return FALSE;
                }
            }
            else
            {
                $this->error = '账号被禁用！';
                return FALSE;
            }
        }
        else
        {
            $this->error = '用户名不存在！';
            return FALSE;
        }
    }
    public function search($pageSize = 20)
    {
        /**************************************** 搜索 ****************************************/
        $where = array();
        if($username = I('get.username'))
            $where['username'] = array('like', "%$username%");
        $is_use = I('get.is_use');
        if($is_use != '' && $is_use != '-1')
            $where['is_use'] = array('eq', $is_use);
        /************************************* 翻页 ****************************************/
        $count = $this->alias('a')->where($where)->count();
        $page = new \Think\Page($count, $pageSize);
        // 配置翻页的样式
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        $data['page'] = $page->show();
        /************************************** 取数据 ******************************************/
        $data['data'] = $this->alias('a')->join("left join rc_admin_role b on a.id = b.admin_id left join rc_role c on b.role_id = c.id")->where($where)->limit($page->firstRow.','.$page->listRows)->field('a.id,a.username,a.is_use,c.role_name')->select();
        return $data;
    }
    // 添加前
    protected function _before_insert(&$data, $option)
    {
    }
    // 修改前
    protected function _before_update(&$data, $option)
    {
        if(($option['where']['id'] == 1) && ($data['is_use'] == 0))
        {
            $this->error = "超级管理员不能禁用";
            return false;
        }
        if($data['password'] === '')
        {
            unset($data['password']);
        }
        else
        {
            $data['password'] = md5($data['password'].C('MD5_KEY'));
        }
    }
    // 删除前
    protected function _before_delete($option)
    {
        if(is_array($option['where']['id']))
        {
            $this->error = '不支持批量删除';
            return FALSE;
        }
        if($option['where']['id'] == 1)
        {
            $this->error = '超级管理员不能删除';
            return false;
        }
    }
    /************************************ 其他方法 ********************************************/
}