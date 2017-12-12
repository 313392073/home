<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/21
 * Time: 12:03
 */
namespace Admin\Controller;
use Think\Controller;
class SystemController extends Controller {
    public function clearCache(){
        //rulAutoAdd(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,'15','删除缓存');
        $dir=APP_PATH.'/Runtime/';
        if(is_dir($dir)){
            $this->delDir($dir);
        }
        $result['message']='删除缓存成功!';
        $result['status']=true;
        $this->success('删除缓存成功');
    }

    private function delDir($dir){
        import("Common.Org.Dir");
        \Dir::delDir($dir);
    }
}