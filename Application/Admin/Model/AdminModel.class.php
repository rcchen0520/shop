<?php
namespace Admin\Model;
use Think\Model;

class AdminModel extends Model{
    public $_login_validate = array(
        array ('username','require','用户名不能为空',1),
        array ('password','require','密码不能为空',1),
//        array('chkcode','require','验证码不能为空',1),
//        array('chkcode','chk_chkcode','验证码错误',1,'callback'),
    );

//    public $_validata = array(
//
//    );

    public $_validate = array(

    );

    public function chk_chkcode($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    public function login(){
        $username = $this->username;
        $password = $this->password;
        $user = $this->where(array('username'=>$username))->find();
        if($user){
            if($user['id'] == 1 || $user['is_use'] == 1 ){
                if($user['password'] == md5($password.C('MD5_KET'))){
                    session('id',$user['id']);
                    session('username',$user['username']);
                    return true;
                } else{
                    $this->error = '密码错误！';
                    return false;
                }
            } else{
                $this->error = '该用户已禁用';
                return false;
            }

        }else{
            $this->error ='用户名不存在';
            return false;
        }
    }


}