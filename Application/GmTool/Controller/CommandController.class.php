<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.uminicmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: www
// +----------------------------------------------------------------------
// | Created by: 2015-10-11 00:00:00
// +----------------------------------------------------------------------

//----------------------------------
// 后台-重要控制器-基础操作管理
//----------------------------------

/*
公共变量命名规则
必须以PUB开头
如果是数组，后缀必须是_LIST
常用常量：
当前顶部菜单：         PUB_TOPMENU，
顶部菜单列表：         PUB_TOPMENU_LIST，
当前菜单：             PUB_THISMENU
当前同级菜单列表       PUB_MENU_LIST
// {$Think.MODULE_NAME}
// {$Think.CONTROLLER_NAME}
// {$Think.ACTION_NAME}
*/
// 公共方法：
// lists add  update  submit_update submit_add query delete 等方法
namespace GmTool\Controller;
use Think\Controller;
class CommandController extends GmToolController {
    public function add_cashpoint(){
        $this->display('GmTool:add_cashpoint');
    }

    public function set_db(){
        $key=I('get.key');
        if(!empty($key)){
            set_db($key);
        }
        $this->_initialize();
        $log_map=array();
        if (!$_SESSION['user']['is_super']==1) {
            $log_map['user_id']=$_SESSION['user']['user_id'];
        }
        $this->log_lists=M('user_log')->where($log_map)->order('id desc')->limit('0,18')->select();
        $this->shortcut=M('shortcut')->limit(0,12)->select();

        //$arr_db = get_db_config();// $dbs[$db];
        //dump($arr_db);

        //$arr_db_url = get_db_config_url();//$arr_db["url"];
        //dump($arr_db_url);
        $this->display('GmTool:Index');
    }

    public function add_cashpoint_result(){
         $gameid=I('post.gameid');
         if(empty($gameid))
         {
             $this->redirect('GmTool:add_cashpoint','',2, '亲，参数为空!!!');
         }
         $shopid=I('post.shopid');
         $arr_db_url = get_db_config_url();
         //http://10.0.0.168:2680/gm_command_cl.clkj
         $data['type']=1;
         $data['id']=$gameid;
         $data['num']=$shopid;
         $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
         //file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
         if($httpstr == "SUCCESS")
         {
             $this->redirect('GmTool:add_cashpoint','',5, '亲，发送命令成功!稍后请查询!!');
         }
         else
         {
             $this->redirect('GmTool:add_cashpoint','',5, '亲，充值失败!!!');
         }
    }

    public function base(){
       $gameid = $_POST["gameid"];
       if(IS_POST && !empty($gameid)){
           $db_config = get_db_config();
           $res =  M("accountinfo",null,$db_config)->query("call FishQueryUserInfoByGameID(".$gameid.")");
           if($res){
               $this->title_lists = array("属性"=>"值");
               $this->list_data = $res[0];
           }
        }
       $this->display("GmTool:base");
    }

    public function cdkey(){
        $gameid = $_POST["gameid"];
        if(IS_POST && !empty($gameid)){
            $db_config = get_db_config();
            $res =  M("accountinfo",null,$db_config)->query("call FishQueryUserInfoByGameID(".$gameid.")");
            if($res){
                $this->title_lists = array("属性"=>"值");
                $this->list_data = $res[0];
            }
        }
        $this->display("GmTool:cdkey");
    }
}
