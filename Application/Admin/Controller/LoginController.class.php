<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller{
    public function login(){
        if(IS_POST){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $chkcode = $_POST['chkcode'];
            $data['username'] = $username;
            $data['password'] = $password;
            $model = D('Admin');
//            $result = $model
//                ->where(array('username'=>$username))
//                ->find();
//            if($result['password'] === md5($password.C('MD5_KET'))){
//                return $this->redirect('Index/index');
//            }else{
//                return $this->error('用户名或密码错误！');
//            };
//        }
            if(empty($chkcode)){
                $this->error('验证码不能为空！');
            }
            if($model->chk_chkcode($chkcode) == false){
                return $this->error('验证码错误！');
            }
            if($model->validate($model->_login_validate)->create($data)){
                if($model->login() === true){
                    redirect(U('Admin/Index/index'));
                }
            }
            $this->error($model->getError());
        }
        $this->display();
    }

    //生成验证码
    public function chkcode(){
        $Verify = new \Think\Verify(array(
            'length'=> 4,
            'useNoise' => false,
        ));
        $Verify->entry();
    }
}