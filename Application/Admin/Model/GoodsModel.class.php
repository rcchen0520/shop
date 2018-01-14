<?php
namespace Admin\Model;
use Think\Model;
use Think\Think;

class GoodsModel extends Model
{
    //在调用create方法时允许接收的字段、
    protected $insertFields = array('goods_name','price','goods_desc','is_on_sale');
    //定义表单验证的规则，控制器中的create方法使用
    protected $_validate = array(
        array('goods_name','require','商品名称不能为空！',1),
        array('goods_name','1,45','商品名称必须是1-45个字符',1,'length'),
        array('price','currency','价格必须是货币格式',1),
        array('is_on_sale','0,1','是否上架只能是0,1两个值',1,'in'),
    );
    //在执行add()插入数据时先执行的前置方法
    protected function _before_insert(&$data,$option)
    {
        //获取当前函数
        $data['addtime'] = time();
        //上传logo
        if($_FILES['logo']['error'] == 0)
        {
            $rootPath = C('IMG_rootPath');
            $upload = new \Think\Upload(array(
                'rootPath'=>$rootPath,
            ));//实例化上传类
            $upload->maxSize = (int)C('IMG_maxsize')*1024*1024;//上传图片限制大小
            $upload->exts = C('IMG_exts');//设置附件上传类型
            $upload->savePath = 'Goods/';//图片二级目录名称
            //上传文件
            $info  = $upload->upload();
            if(!$info)
            {
                $this->error = $upload->getError();
                return false;//返回控制器
            }
            else
            {
                $logoName =  $info['logo']['savepath'].$info['logo']['savename'];
                //拼出缩略图文件名
                $smLogoName = $info['logo']['savepath'].'thumb_'.$info['logo']['savename'];
                //生成缩略图
                $image = new \Think\Image();
                //打开要处理的图片
                $image->open($rootPath.$logoName);
                $image->thumb(150,150)->save($rootPath.$smLogoName);
                //把图片保存数据放到表单保存数据中
                $data['logo'] = $logoName;
                $data['sm_logo'] = $smLogoName;
            }
        }
    }

    //搜索商品
    public function search()
    {
        $where = array(); //默认情况下搜索条件为空
        //商品名称搜索
        $goodsName = I('get.goods_name');
        if($goodsName)
        {
            $where['goods_name'] = array('like',"%$goodsName%");
        }
        //价格搜索
        $startPrice = I('get.start_price');
        $endPrice = I('get.end_price');
        if($startPrice && $endPrice){
            $where['price'] = array('between',array($startPrice,$endPrice));
        }
        elseif ($startPrice)
        {
            $where['price'] = array('egt',$startPrice);
        }
        elseif($endPrice)
        {
            $where['price'] = array('elt',$endPrice);
        }
        $isOnSale = I('get.is_on_sale',-1);
        if($isOnSale != -1)
        {
            $where['is_on_sale'] = array('eq',$isOnSale);
        }
        $isDelete  = I('get.is_delete',-1);
        if($isDelete != -1)
        {
            $where['is_delete'] = array('eq',$isDelete);
        }
        /******排序******/
        $orderby = 'id';
        $orderway = 'asc';
        $obby = I('get.obby');
        if($obby && in_array($obby,array('id_asc','id_desc','price_asc','price_desc')))
        {
            if($obby == 'id_desc')
            {
                $orderway = 'desc';
            }
            elseif($obby == 'price_asc')
            {
                $orderby = 'price';
            }
            elseif($obby == 'price_desc')
            {
                $orderby = 'price';
                $orderway = 'desc';
            }
        }
        /*************翻页***************/
        //总记录数
        $count =  $this->where($where)->count();
        //生成翻页对象
        $page = new \Think\Page($count,2);
        //获取翻页字符串
        $pageString = $page->show();
        //取出当前页数据
        $data = $this->where($where)->limit($page->firstRow.','.$page->listRows)->order("$orderby $orderway")->select();
        return array(
            'page'  =>  $pageString,
            'data'  =>  $data,
        );
    }
}