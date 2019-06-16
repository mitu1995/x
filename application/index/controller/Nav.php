<?php


namespace app\index\controller;

use app\index\model\Nav as model;
class Nav extends Base
{
    //导航列表
    public function index(){

        $pid = (int)input('get.pid');
        if ($back_id =(int)input('get.back_id') ){
            $p_data = model::get($back_id);
            $pid =$p_data['pid'];
        }
        $data = model::where('pid',$pid)->order('order','asc')->order('id','asc')->select();
//        $c_menu =0;
//        if ($data_c = Mmodel::where('pid','>',0)->column('pid')){
//            $c_menu =1;
//        }
        // 获取所有有子菜单的ID
        // 获取所有的ID
        $data_c = model::column('id');
        $list=[];
        foreach ($data_c as $k=>$v){
            // dump($v);
            //获取所有有子ID的ID
            if ($p_data = model::where('pid',$v)->column('id')){
                //dump($p_data);
                //创建索引与值相同的数组
                $list[$v]= $v;
                //  dump($v);
            }

        }

        $this->assign('list',$list);
        $this->assign('pid',$pid);
        $this->assign('data',$data);
//        $this->assign('c_menu',$c_menu);
        return $this->fetch();
    }
    //导航添加
    public function add(){

        if ( $data = input('post.')){
            $validate = validate('menu');
            if (!$validate->check($data)) {
                return ['code'=>1,'message'=>$validate->getError()];
            }
            if(!$menu = model::create($data)){
                return ['code'=>2,'message'=>'数据添加失败'];
            }
            return ['code'=>0,'message'=>'导航添加成功'];
        }else{
            return $this->fetch();
        }


    }
    //添加子导航
    public function add_c(){
        if ($pid = input('get.id')) {
            $menu = model::get($pid);
            $this->assign('menu',$menu);
            return $this->fetch();
        }
        if ($data = input('post.')){
            $validate = validate('Menu');
            if (!$validate->check($data)) {
                return ['code'=>1,'message'=>$validate->getError()];
            }
            if (!$menu= model::create($data)){
                return ['code'=>2,'message'=>'子栏目添加失败'];
            }
            return ['code'=>0,'message'=>'子栏目添加成功'];
        }

    }
    //删除分类
    public function del(){
        $id = (int)input('post.id');
        if (!model::destroy($id)){
            return ['code'=>1,'message'=>'删除失败请重试'];
        }
        //查找当前分类下是否有子类有就删除
        if ($c_menu =model::where('pid', $id)->find()){
            if (!model::where('pid',$id)->delete()){
                return ['code'=>2,'message'=>'子分类删除成功'];
            }
        }

        return ['code'=>0,'message'=>'删除成功'];
    }
    //编辑分类
    public function edit(){
        if ($id=  input('get.id')) {
            $menu = model::get($id);
            $this->assign('menu',$menu);
            return $this->fetch();
        }
        if ($data = input('post.')){
            if (!$menu = model::get($data['id'])){
                return ['code'=>2,'message'=>'栏目修改失败'];
            }
            if (!isset($data['status'])){
                $data['status']=0;
            }

            if($menu->save($data)){
                return ['code'=>0,'message'=>'栏目修改成功'];
            }
            return ['code'=>5,'message'=>'栏目未做修改'];
//         if (!$menu= model::create($data)){
//             return ['code'=>2,'message'=>'菜单修改失败'];
//         }
        }

    }
}