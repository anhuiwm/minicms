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

    public function online(){
     $this->count = '未查询';
       if(IS_POST){
           $db_config = get_db_config();
           $res = null;
           $res =  M("accountinfo",null,$db_config)->query("select count(*) as count from accountinfo where IsOnline=1 and IsRobot!=1;");
           $this->count = $res[0]['count'];
        }
       $this->display("GmTool:online");
    }

    public function add_cashpoint(){
        $curuserid = get_current_userid();
        if(isset($curuserid)){
            $this->curuser = $curuserid;
        }
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
        $this->display('GmTool:index');
    }

    public function add_cashpoint_result(){
         $gameid=I('post.gameid');
         if(empty($gameid))
         {
             $this->redirect('GmTool:add_cashpoint','',2, '亲，参数为空!!!');
         }
         $shopid=I('post.shopid');
         $arr_db_url = get_db_config_url();
         $data['type']=self::$GT_Charge;
         $data['id']=$gameid;
         $data['num']=$shopid;

         $count = I('post.count');
         for($i = 0; $i < $count;$i++){
             $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
             if($httpstr == "SUCCESS")
             {
                 //$this->redirect('GmTool:add_cashpoint','',3, '亲，发送命令成功!稍后请查询!!');
             }
             else
             {
                 $this->redirect('GmTool:add_cashpoint','',3, '亲，充值失败!!!');
             }
         }
         $this->redirect('GmTool:add_cashpoint','',3, '亲，发送命令成功!稍后请查询!!');
    }

    public function base(){
       $gameid = $_POST["gameid"];
       $userid = $_POST["userid"];
       if(IS_POST){
           $db_config = get_db_config();
           $res = null;
           if(!empty($gameid)){
               $res =  M("accountinfo",null,$db_config)->query("call FishQueryUserInfoByGameID(".$gameid.")");
           }else  if(!empty($userid)){
               $res =  M("accountinfo",null,$db_config)->query("call FishQueryUserInfoByUserID(".$userid.")");
           }
           if($res){
               $this->title_lists = array("属性"=>"值");
               $this->list_data = $res[0];

               set_current_userid($res[0]["userid"]);
           }
        }
       $curuserid = get_current_userid();
       if(isset($curuserid)){
           $this->curuser = $curuserid;
       }
       $this->display("GmTool:base");
    }

   public function stock(){
           $db_config = get_logdb_config();

           $res =  M("fishstocklog",null,$db_config)->query("select * from fishstocklog");

           if($res){
              
               $arr_score = array(0,0,0,0);
               foreach($res as $key=>$value){
                   $arr_score[$value["tabletype"]] += $value["stockscore"];
                }
  
                $this->detail_title_lists=array("服务器ID","房间类型","StockScore","LogTime");
                $this->title_lists = array("房间类型","房间类型总库存");
                $this->detail_list_data = $res; 
                $this->list_data = $arr_score;
           }
       $this->display("GmTool:stock");
    }

    public function cdkey(){
        $db_config = get_db_config();
        $sql = "select cdkey,name,rewardid,platform,userid,starttime,validdate,gotdate,creator,ispublic from cdkey";

        //$res =  M("cdkey",null,$db_config)->query($sql);
        //if($res){
            $this->title_lists = array(
                "cdkey"    ,
                "name"     ,
                "rewardid" ,
                "platform" ,
                "userid"   ,
                "starttime",
                "validdate",
                "gotdate"  ,
                "creator"  ,
                "ispublic"
                );
        //   $this->list_data = $res;
        //}

        //dump($res);
        //$this->display("GmTool:cdkey");
        //$listRows=20;
        //$count = 1;
        if(IS_POST){
            $cdkey = $_POST["cdkey"];
            $userid = $_POST["userid"];
            $rewardid = &$_POST["rewardid"];
            $platform  = $_POST["platform"];
            $public    = $_POST["ispublic"];
            if(!empty($cdkey)){
                $sql = $sql." where "."cdkey='".$cdkey."'";
            }else  if(!empty($userid)){
                $sql = $sql." where "."userid=".$userid;
            }else  if(!empty($rewardid)){
                $sql = $sql." where "."rewardid=".$rewardid;
            }
            else  if(!empty($platform)){
                $sql = $sql." where "."platform=".$platform;
            }
            else  if(!empty($public)){
                $sql = $sql." where "."ispublic=".$public;
            }
        }
        //else
        //{
        //    $count=M("cdkey",null,$db_config)->count();
        //}

        //$Page= new \Think\Page($count);// 实例化分页类 传入总记录数和每页显示的记录数
        //$Page->lastSuffix = false;
        //$Page->setConfig('first','首页');
        //$Page->setConfig('last','尾页');
        //$Page->setConfig('prev','上一页');
        //$Page->setConfig('next','下一页');
        //$this->page = $Page->show();// 分页显示输出
        $this->list_data= M("cdkey",null,$db_config)->query($sql);
        //dump($this->list_data);
        //echo M("cdkey",null,$db_config)->getLastSql();
        // 实现分页###########################################################

        $this->display('GmTool:cdkey');
    }

    public function use_cdkey(){
        $cdkey = $_POST["cdkey"];
        $userid = $_POST["userid"];
        $db_config = get_db_config();
        $singleConMode = M('cdkey',null,$db_config);
        $publicConMode = M('cdkey_public_user',null,$db_config);

        $selectcdkey = array();
        $selectpublic = array();

        $this->title_lists = array(
            "cdkey"    ,
            "userid"   ,
            "gotdate"  ,
            );
        if(IS_POST){
            //$public    = $_POST["ispublic"];
            if(!empty($cdkey)){
                $selectcdkey = $singleConMode->field("cdkey,userid,gotdate")->where("cdkey='{$cdkey}'")->select();
                $selectpublic = $publicConMode->field("cdkey,userid,gotdate")->where("cdkey='{$cdkey}'")->select();
            }else  if(!empty($userid)){
                $selectcdkey = $singleConMode->field("cdkey,userid,gotdate")->where("userid='{$userid}'")->select();
                $selectpublic = $publicConMode->field("cdkey,userid,gotdate")->where("userid='{$userid}'")->select();
            }
        }
        else
        {
            $selectcdkey = $singleConMode->field("cdkey,userid,gotdate")->where("userid!=0")->select();
            $selectpublic = $publicConMode->field("cdkey,userid,gotdate")->where("userid!=0")->select();
        }
        $selectpublic= array_merge($selectpublic,$selectcdkey);
        //$count = count($selectpublic);
        //$Page= new \Think\Page(1000,100);// 实例化分页类 传入总记录数和每页显示的记录数
        //$Page->lastSuffix = false;
        //$Page->setConfig('first','首页');
        //$Page->setConfig('last','尾页');
        //$Page->setConfig('prev','上一页');
        //$Page->setConfig('next','下一页');
        //$this->page = $Page->show();// 分页显示输出
        $this->list_data= $selectpublic;
        $this->display('GmTool:use_cdkey');
    }

    public function add_cdkey(){
        //CREATE  `sp_generate_cdkey`(
        //IN  _count      int,
        //IN  _name        varchar(32),
        //IN  _gift       int,
        //IN  _batch      int,
        //in  _starttime   datetime,
        //IN  _validdate   DATETIME,
        //IN  _public     int,
        //IN  _creator    varchar(32),
        //IN  _platform     varchar(32))

        if(IS_POST){
            dump($_POST);
            $count     = $_POST["count"]    ;
            $name      = $_POST["name"]     ;
            $gift      = $_POST["gift"]     ;
            $starttime = $_POST["starttime"];
            $validdate = $_POST["endtime"];
            $public    = $_POST["ispublic"]   ;
            $creator   = $_SESSION['user']['username'];
            $platform  = $_POST["platform"] ;
            if( empty($count) || empty($name)|| empty($gift)|| empty($starttime) || empty($validdate) || empty($platform) || !isset($public) )
            {
                $this->redirect('GmTool:cdkey','',1, '亲，参数为空!!!');
            }

            //if(!($count) || !is_int($gift))
            //{
            //    $this->redirect('GmTool:cdkey','',3, '亲，整数!!!');
            //}

            $sql = "call sp_generate_cdkey(".$count.",'".$name."',".$gift.",'".$starttime."','".$validdate."',".$public.",'".$creator."','".$platform."')";
            //dump($sql);
            $db_config = get_db_config();
            $res =  M("cdkey",null,$db_config)->execute($sql);
            //dump($res);
            if($res){
               return   $this->redirect('GmTool:cdkey','',1, '亲，添加成功!');
            }
        }
        $this->redirect('GmTool:cdkey','',1, '亲，添加失败!');
    }

    public function mail(){
        if(IS_POST){
            $RewardID=I('post.RewardID');
            $RewardNum=I('post.RewardNum');
            $DestUserID = I('post.DestUserID');
            $Context = I('post.Context');
            if(empty($RewardID)||empty($RewardNum)||empty($DestUserID))
            {
                $this->redirect('GmTool:mail','',3, '亲，参数为空!!!');
            }
            if(empty($Context))
            {
                $Context = "System Send!";
            }
            $arr_db_url = get_db_config_url();
            $data['type']= self::$GT_Mail;
            $data['id']=$RewardID;
            $data['num']=$RewardNum;
            file_put_contents('wmgmlog.txt', "con:".$Context.PHP_EOL, FILE_APPEND);
            $data['content']=$Context;// urlencode($Context);
            $data['target']=$DestUserID;
            $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
            if($httpstr == "SUCCESS")
            {
                $this->redirect('GmTool:mail','',3, '亲，发送命令成功!稍后请查询!!');
            }
            else
            {
                $this->redirect('GmTool:mail','',3, '亲，发送失败!!!');
            }
        }
        else{
            $curuserid = get_current_userid();
            if(isset($curuserid)){
                $this->curuser = $curuserid;
            }
            $this->display('GmTool:mail');
        }
    }

    public function broadcast(){
        if(IS_POST){
            dump($_POST);
            dump($_POST);
            $RewardNum=I('post.RewardNum');
            $Context = I('post.Context');
            if(empty($RewardNum)||empty($Context))
            {
                $this->redirect('GmTool:broadcast','',3, '亲，参数为空!!!');
            }

            $arr_db_url = get_db_config_url();
            $data['type']= self::$GT_SendBroad;
            $data['id']=0;
            $data['num']=$RewardNum;
            $data['content']=$Context;
            $data['target']=0;
            $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
            if($httpstr == "SUCCESS")
            {
                $this->redirect('GmTool:broadcast','',3, '亲，发送命令成功!稍后请查询!!');
            }
            else
            {
                $this->redirect('GmTool:broadcast','',3, '亲，发送失败!!!');
            }
        }
        else{
            $this->display('GmTool:broadcast');
        }
    }

    public function kick(){
        if(IS_POST){
            $RewardNum=I('post.RewardNum');
            $DestUserID = I('post.DestUserID');
            //$Context = I('post.Context');
            if(empty($RewardNum)||empty($DestUserID))
            {
                $this->redirect('GmTool:kick','',3, '亲，参数为空!!!');
            }
            //if(empty($Context))
            //{
            //    $Context = "System Send!";
            //}
            $arr_db_url = get_db_config_url();
            $data['type']= self::$GT_Kick;
            $data['id']=0;
            $data['num']=$RewardNum;
            $data['content']=0;
            $data['target']=$DestUserID;
            $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
            if($httpstr == "SUCCESS")
            {
                $this->redirect('GmTool:kick','',3, '亲，发送命令成功!稍后请查询!!');
            }
            else
            {
                $this->redirect('GmTool:kick','',3, '亲，发送失败!!!');
            }
        }
        else{
            $curuserid = get_current_userid();
            if(isset($curuserid)){
                $this->curuser = $curuserid;
            }
            $this->display('GmTool:kick');
        }
    }

    public function silent(){
        if(IS_POST){
            $RewardNum=I('post.RewardNum');
            $DestUserID = I('post.DestUserID');
            $Context = I('post.Context');
            if(empty($RewardNum)||empty($DestUserID))
            {
                $this->redirect('GmTool:silent','',3, '亲，参数为空!!!');
            }
            if(empty($Context))
            {
                $Context = "System Send!";
            }
            $arr_db_url = get_db_config_url();
            $data['type']= self::$GT_Silent;
            $data['id']=0;
            $data['num']=$RewardNum;
            $data['content']=0;
            $data['target']=$DestUserID;
            $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
            if($httpstr == "SUCCESS")
            {
                $this->redirect('GmTool:silent','',3, '亲，发送命令成功!稍后请查询!!');
            }
            else
            {
                $this->redirect('GmTool:silent','',3, '亲，发送失败!!!');
            }
        }
        else{
            $this->display('GmTool:silent');
        }
    }

    public function reloadconfig(){
        if(IS_POST){
            //$RewardID=I('post.RewardID');
            //$RewardNum=I('post.RewardNum');
            //$DestUserID = I('post.DestUserID');
            //$Context = I('post.Context');

            if(empty($Context))
            {
                $Context = "System Send!";
            }
            $arr_db_url = get_db_config_url();
            $data['type']= self::$GT_ReloadConfig;
            $data['id']=0;
            $data['num']=0;
            $data['content']=0;
            $data['target']=0;
            $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
            if($httpstr == "SUCCESS")
            {
                $this->redirect('GmTool:reloadconfig','',3, '亲，发送命令成功!稍后请查询!!');
            }
            else
            {
                $this->redirect('GmTool:reloadconfig','',3, '亲，发送失败!!!');
            }
        }
        else{
            $this->display('GmTool:reloadconfig');
        }
    }

    public function handleentity(){
        if(IS_POST){
            $RewardID=I('post.RewardID');
            $Context = I('post.Context');
            if(empty($RewardID)||empty($Context))
            {
                $this->redirect('GmTool:handleentity','',3, '亲，参数为空!!!');
            }

            $arr_db_url = get_db_config_url();
            $data['type']= self::$GT_HandleEntityItem;
            $data['id']=$RewardID;
            $data['num']=0;
            $data['content']=$Context;
            $data['target']=0;
            $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
            if($httpstr == "SUCCESS")
            {
                $this->redirect('GmTool:handleentity','',3, '亲，发送命令成功!稍后请查询!!');
            }
            else
            {
                $this->redirect('GmTool:handleentity','',3, '亲，发送失败!!!');
            }
        }
        else{
            $this->display('GmTool:handleentity');
        }
    }
}
