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

    public function reversebytes_uint32t($value){
   // dump($value);
        return ($value & 0x000000FF) << 24 | ($value & 0x0000FF00) << 8 | ($value & 0x00FF0000) >> 8 | ($value & 0xFF000000) >> 24;
    }
    public function GetIpLookup($ip = ''){
            if(empty($ip)){
                $ip = GetIp();
            }
            $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
            if(empty($res)){ return false; }
            $jsonMatches = array();
            preg_match('#\{.+?\}#', $res, $jsonMatches);
            if(!isset($jsonMatches[0])){ return false; }
            $json = json_decode($jsonMatches[0], true);
            if(isset($json['ret']) && $json['ret'] == 1){
                $json['ip'] = $ip;
                unset($json['ret']);
            }else{
                return false;
            }
            //dump($json);
            $res = $json["country"].$json["province"].$json["city"];
            return $res;
        }

  public  function hostip2netip($ip){
   list($ip1,$ip2,$ip3,$ip4)=explode(".",$ip);
    //return($ip4<<24)|($ip3<<16)|($ip2<<8)|($ip1);
    //return ($ip1<<24)|($ip2<<16)|($ip3<<8)|($ip4);;
    return $ip4.".".$ip3.".".$ip2.".".$ip1;
}

    public function base(){

       if(IS_POST){
              $gameid = $_POST["gameid"];
       $userid = $_POST["userid"];
       $account = $_POST["accountname"];
              $nick = $_POST["nickname"];
           $db_config = get_db_config();
           $res = null;
           if(!empty($gameid)){
               $sql = "	select a.AccountName,a.FishExp,a.LastLogonTime,a.Production, a.IsRobot,a.FreezeEndTime,a.RsgIP,a.UserID,a.NickName,a.FishLevel,a.FaceID,a.Gender,a.IsOnline,a.AchievementPoint,a.TitleID,
					a.CharmArray ,a.LastLogonIp,a.IsShowIpAddress, a.VipLevel,a.TotalRechargeSum,a.MonthCardID,a.MonthCardEndTime,
					b.GameID as 'GameID', a.CashPointNum, a.UsingLauncher,a.MaxRateValue,a.CurrencyNum,a.GlobalNum,a.MedalNum,
					c.MonthRewardSum,c.MonthFirstSum,c.MonthSecondSum,c.MonthThreeSum,c.CatchFishSum,c.GeGlobelSum,c.RoleMonthSigupSum,c.NonMonthGameSec,c.TotalGameSec,
					c.CatchFish9,c.CatchFish18,c.CatchFish20,c.CatchFish1,c.CatchFish3,c.CatchFish19,c.MaxComboSum,
a.GameTime,a.TitleID,a.OnlineSec,a.GoldBulletNum,a.NobilityPoint,a.AddupCheckNum,a.DayTaskActiviness,a.WeekTaskActiviness,a.WeekGlobeNum,a.IsCheckToday,a.SendGoldBulletNum,a.SendSilverBulletNum,a.SendBronzeBulletNum,a.GuideStep

	                from accountinfo as a
	                left join fishgameid as b on a.UserID = b.UserID
	                left join fishgamedata as c on c.UserID = b.UserID
	                where b.GameID= {$gameid};
                ";
               $res =  M("accountinfo",null,$db_config)->query($sql);
           }else  if(!empty($userid)){
               $sql = "select	a.AccountName,a.FishExp,a.LastLogonTime,a.Production, a.IsRobot,a.FreezeEndTime,a.RsgIP, a.UserID,a.NickName,a.FishLevel,a.FaceID,a.Gender,a.IsOnline,a.AchievementPoint,a.TitleID,
					a.CharmArray ,a.LastLogonIp,a.IsShowIpAddress, a.VipLevel,a.TotalRechargeSum,a.MonthCardID,a.MonthCardEndTime,
					b.GameID as 'GameID', a.CashPointNum, a.UsingLauncher,a.MaxRateValue,a.CurrencyNum,a.GlobalNum,a.MedalNum,
					c.MonthRewardSum,c.MonthFirstSum,c.MonthSecondSum,c.MonthThreeSum,c.CatchFishSum,c.GeGlobelSum,c.RoleMonthSigupSum,c.NonMonthGameSec,c.TotalGameSec,
					c.CatchFish9,c.CatchFish18,c.CatchFish20,c.CatchFish1,c.CatchFish3,c.CatchFish19,c.MaxComboSum ,
a.GameTime,a.TitleID,a.OnlineSec,a.GoldBulletNum,a.NobilityPoint,a.AddupCheckNum,a.DayTaskActiviness,a.WeekTaskActiviness,a.WeekGlobeNum,a.IsCheckToday,a.SendGoldBulletNum,a.SendSilverBulletNum,a.SendBronzeBulletNum,a.GuideStep

	                from accountinfo as a
	                left join fishgameid as b on a.UserID = b.UserID
	                left join fishgamedata as c on c.UserID = b.UserID
	                where a.UserID= {$userid};
                ";
               $res =  M("accountinfo",null,$db_config)->query($sql);
           }else  if(!empty($account)){
               $sql = "select	a.AccountName,a.FishExp,a.LastLogonTime,a.Production, a.IsRobot,a.FreezeEndTime,a.RsgIP, a.UserID,a.NickName,a.FishLevel,a.FaceID,a.Gender,a.IsOnline,a.AchievementPoint,a.TitleID,
					a.CharmArray ,a.LastLogonIp,a.IsShowIpAddress, a.VipLevel,a.TotalRechargeSum,a.MonthCardID,a.MonthCardEndTime,
					b.GameID as 'GameID', a.CashPointNum, a.UsingLauncher,a.MaxRateValue,a.CurrencyNum,a.GlobalNum,a.MedalNum,
					c.MonthRewardSum,c.MonthFirstSum,c.MonthSecondSum,c.MonthThreeSum,c.CatchFishSum,c.GeGlobelSum,c.RoleMonthSigupSum,c.NonMonthGameSec,c.TotalGameSec,
					c.CatchFish9,c.CatchFish18,c.CatchFish20,c.CatchFish1,c.CatchFish3,c.CatchFish19,c.MaxComboSum,
a.GameTime,a.TitleID,a.OnlineSec,a.GoldBulletNum,a.NobilityPoint,a.AddupCheckNum,a.DayTaskActiviness,a.WeekTaskActiviness,a.WeekGlobeNum,a.IsCheckToday,a.SendGoldBulletNum,a.SendSilverBulletNum,a.SendBronzeBulletNum,a.GuideStep

	                from accountinfo as a
	                left join fishgameid as b on a.UserID = b.UserID
	                left join fishgamedata as c on c.UserID = b.UserID
	                where a.AccountName= '{$account}';
                ";
               $res =  M("accountinfo",null,$db_config)->query($sql);
           }else  if(!empty($account)){
               $sql = "select	a.AccountName,a.FishExp,a.LastLogonTime,a.Production, a.IsRobot,a.FreezeEndTime,a.RsgIP, a.UserID,a.NickName,a.FishLevel,a.FaceID,a.Gender,a.IsOnline,a.AchievementPoint,a.TitleID,
					a.CharmArray ,a.LastLogonIp,a.IsShowIpAddress, a.VipLevel,a.TotalRechargeSum,a.MonthCardID,a.MonthCardEndTime,
					b.GameID as 'GameID', a.CashPointNum, a.UsingLauncher,a.MaxRateValue,a.CurrencyNum,a.GlobalNum,a.MedalNum,
					c.MonthRewardSum,c.MonthFirstSum,c.MonthSecondSum,c.MonthThreeSum,c.CatchFishSum,c.GeGlobelSum,c.RoleMonthSigupSum,c.NonMonthGameSec,c.TotalGameSec,
					c.CatchFish9,c.CatchFish18,c.CatchFish20,c.CatchFish1,c.CatchFish3,c.CatchFish19,c.MaxComboSum,
a.GameTime,a.TitleID,a.OnlineSec,a.GoldBulletNum,a.NobilityPoint,a.AddupCheckNum,a.DayTaskActiviness,a.WeekTaskActiviness,a.WeekGlobeNum,a.IsCheckToday,a.SendGoldBulletNum,a.SendSilverBulletNum,a.SendBronzeBulletNum,a.GuideStep

	                from accountinfo as a
	                left join fishgameid as b on a.UserID = b.UserID
	                left join fishgamedata as c on c.UserID = b.UserID
	                where a.NickName= '{$nick}';
                ";
               $res =  M("accountinfo",null,$db_config)->query($sql);
           }
           if($res){
          // dump($res);
           $arr_name = array(
                 "userid" => "userid",
                 "gameid" => "客户端ID",
                 "accountname"=>"账户名",
                 "nickname" => "昵称",
                 "fishlevel" => "等级",
                 "faceid" => "头像ID",
                 "gender" => "性别",
                 "isonline" => "在线",
                 "cashpointnum" => "点券",
                 "currencynum" => "钻石",
                 "globalnum" => "金币",
                 "medalnum" => "红包",
                 "viplevel" => "VIP",
                 "totalrechargesum"=>"充值金额",
                 "usinglauncher" => "当前炮台",
                 "maxratevalue" => "最大倍率",
                 "achievementpoint" => "成就点",
                 "titleid" => "称号",
                 "lastlogonip" => "最后登录IP",
                 "isshowipaddress" => "是否显示地址",
                 "monthcardid" => "月卡ID",
                 "monthcardendtime" => "月卡结束时间",
                 "catchfishsum" => "捕鱼总数",
                 "geglobelsum" => "金币总数",
                 "totalgamesec" => "游戏总秒数",
                 "catchfish9" => "catchfish9" ,
                 "catchfish18" => "catchfish18",
                 "catchfish20" => "catchfish20",
                 "catchfish1" => "catchfish1" ,
                 "catchfish3" => "catchfish3" ,
                 "catchfish19" => "catchfish19",
                 "maxcombosum" => "最大连击",
                 "FishExp"=>"经验",
                 "lastlogontime"=>"最后登录时间",
                 "production"=>"个人产出",
                 "isrobot"=>"机器人",
                 "freezeendtime"=>"冻结时间",
                 "rsgip"=>"注册IP",
                 "GameTime"=>"GameTime","titleid"=>"titleid",
"onlinesec"=>"onlinesec",
"goldbulletnum"=>"goldbulletnum",
"nobilitypoint"=>"nobilitypoint",
"addupchecknum"=>"addupchecknum",
"daytaskactiviness"=>"daytaskactiviness",
"weektaskactiviness"=>"weektaskactiviness",
"weekglobenum"=>"weekglobenum",
"ischecktoday"=>"ischecktoday",
"sendgoldbulletnum"=>"sendgoldbulletnum",
"sendsilverbulletnum"=>"sendsilverbulletnum",
"sendbronzebulletnum"=>"sendbronzebulletnum",
"guidestep"=>"guidestep",
                );

                //dump($arr_name);
                $arr_name_res = array();
                foreach($arr_name as $key=>$value){
               // dump($key);dump($value);
                    $tempres = $res[0][$key];
                   //dump($tempres);
                    if(isset($tempres)){

                        if($key=="lastlogonip")
                       {
                        //dump($tempres);
                        //dump($tempres);
                        //$net = $this->reversebytes_uint32t($tempres);
                        //$netip = sprintf('%u',$net);
                        $ip = long2ip($tempres);
                       // dump($ip);
                        //$ip = "106.15.198.179";//
                        $ip = $this->hostip2netip($ip);
                        //dump($netip);
                       // dump($ip);
                        $ipInfos = $this->GetIpLookup($ip);
                            $tempres .="=>".$ip."=>".$ipInfos;
                        }

                        if($key=="rsgip"){
                            $ipInfos = $this->GetIpLookup($tempres);
                            $tempres .="=>".$ipInfos;
                               // dump($tempres);
                        }
                        $arr_name_res[$value] = $tempres;
                    }
                }
               $this->title_lists = array("属性"=>"值");
               $this->list_data = $arr_name_res;

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
           $res =  M("fishstocklog",null,$db_config)->query("select `ServerID` , `TableType`,  `StockScore`,(StockScore-100000000+TaxScore) as Income,  `TaxScore`,  `LogTime` from fishstocklog");
           if($res){
               $arr_score = array();
               foreach($res as $key=>$value){
               if($value["stockscore"] != 0){
                      $arr_score[$value["tabletype"]]["type"] = $value["tabletype"];
                      $arr_score[$value["tabletype"]]["stock"] += $value["stockscore"];
                      $arr_score[$value["tabletype"]]["tax"] += $value["taxscore"];
                   }
                   else{
                        unset($res[$key]);
                    }
                }

                $this->detail_title_lists=array("服务器ID","房间类型","实时库存","净收入","税收","LogTime");
                $this->title_lists = array("房间类型","房间类型总库存","房间类型总税收");
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
                $sql = $sql." where "."rewardid='".$rewardid."'";
            }
            else  if(!empty($platform)){
                $sql = $sql." where "."platform='".$platform."'";
            }
            else  if(!empty($public)){
                $sql = $sql." where "."ispublic=".$public;
            }
        }
        //dump($sql);


        $this->list_data= M("cdkey",null,$db_config)->query($sql);
        //dump($this->list_data);
        //dump( M("cdkey",null,$db_config)->getLastSql() );
        $this->display('GmTool:cdkey');
    }

     public function item(){
        if(IS_POST){
                    $userid = $_POST["userid"];
            $itemid = $_POST["itemid"];
                    $db_config = get_db_config();
                    if(!empty($itemid) && $itemid<900 && $itemid>910){
                       $sql = "SELECT itemid ,SUM(itemsum) AS sum FROM `fishitem` WHERE ( itemid>=900 and itemid <=910  or itemid={$itemid} and userid>11000) GROUP BY itemid";
                    }
                    else{
                       $sql = "SELECT itemid ,SUM(itemsum) AS sum FROM `fishitem` WHERE ( itemid>=900 and itemid <=910 and userid>11000) GROUP BY itemid";
                    }

                   $this->title_lists_sum = array(
                                "itemid",
                                "sum"
                                 );
                    $this->list_data_sum= M("fishitem",null,$db_config)->query($sql);

                                // dump($this->list_data_sum);
                                 //dump($this->list_data_sum);
                                 //dump(M("fishitem",null,$db_config)->getLastSql());
            $this->title_lists = array(
                                "userid",
                                "itemid",
                                "itemsum",
                                "endtime"
                                 );


            $map = array();
            if(!empty($userid)){
                $map['UserID'] = array('eq',$userid);
                 $map['userid'] = array('gt',11000);
                if(!empty($itemid)){
                    $map['ItemID'] = array('eq',$itemid);
                }
                $this->list_data= M("fishitem",null,$db_config)->field($this->title_lists)->where($map)->order('itemsum desc')->select();
            }
            else{
                if(!empty($itemid)){
                    $map['ItemID'] = array('eq',$itemid);
                    $map['userid'] = array('gt',11000);
                    $this->list_data= M("fishitem",null,$db_config)->field($this->title_lists)->where($map)->order('itemsum desc')->select();
                }
            }

          $sql = "SELECT SUM(GlobalNum) AS sum FROM `accountinfo` WHERE IsRobot != 0";
          $goldres= M("fishitem",null,$db_config)->query($sql);
          $this->gold = $goldres[0]["sum"];
          //dump($this->gold);
        }

        $this->display('GmTool:item');
    }


       public function itemlog(){
        $curuserid = get_current_userid();
        if(isset($curuserid)){
            $this->curuser = $curuserid;
        }
        $this->title_lists = array(
                        "id",
                        "userid",
                        "typeid",
                        "typesum",
                        "param",
                        "endparam",
                        "info",
                        "logtime",
                        );

        if(IS_POST)
        {
       // dump($_POST);
                    $userid = $_POST["userid"];
                    $startdate = $_POST["starttime"];
                    $enddate = $_POST["endtime"];
                    $typeid = $_POST["typeid"];
                    $typesum = $_POST["typesum"];
                    if( empty($startdate) || empty($enddate) )
                    {
                    $startdate = date('Y-n-j');
                    $enddate = date('Y-n-j');
                        //$this->redirect('GmTool:itemlog','',1, '亲，日期不能为空!!!');
                    }

                    $db_config = get_logdb_config();
                    $stimestamp = strtotime($startdate);
                    $etimestamp = strtotime($enddate);
                    $days = ($etimestamp-$stimestamp)/86400+1;
                    $date = array();
                    $map = array();
                    if(!empty($userid)){
                          $map['UserID'] = array('eq',$userid);
                    }
                     if(!empty($typeid)){
                          $map['TypeID'] = array('eq',$typeid);
                    }
                     if(!empty($typesum)){
                          $map['TypeSum'] = array('eq',$typesum);
                    }
                   // dump($map);
                    $arr_res = array();
                    for($i=0; $i<$days; $i++){
                        $day = date('Y-n-j', $stimestamp+(86400*$i));
                        $day = str_replace("-","_",$day);
                        $fishlogday = "fishlog_".$day;

                        //dump($fishlogday);
                        try{
                           $res= M($fishlogday,null,$db_config)->field($this->title_lists)->where($map)->select();
                        }catch(\Exception $e){
                            //$res = array("code" => "error", "message" => "数据库错误");
                        }
                        //dump($res);
                        if(isset($res))
                        {
                          // dump($res);
                           $arr_res = array_merge($arr_res,$res);
                           //dump($arr_res);
                        }
                         unset($res);
                        //$this->list_data= M($fishlogday,null,$db_config)->field($this->title_lists)->where($map)->select();
                        //dump(M($fishlogday,null,$db_config)->getLastSql());
                       // dump(M($fishlogday,null,$db_config)->getLastSql());
                       // dump(M($fishlogday,null,$db_config)->getLastSql());
                        //dump($this->list_data);
                    }
                    //dump($arr_res);
                    $this->list_data = $arr_res;
       }
       $this->display('GmTool:itemlog');
    }

    public function chargelog(){
            if(IS_POST){
        $db_config = get_logdb_config();
//        $sql = " select
//OrderStates   ,
//UserID        ,
//Price         ,
//orderid       ,
//ShopItemID    ,
//ChannelCode   ,
//LogTime       ,
//AddRewardID
//from fishrechargelog
//";

        $fieilds = " OrderStates   ,
UserID        ,
Price         ,
orderid       ,
ShopItemID    ,
ChannelLabel   ,
ChannelCode   ,
LogTime       ,
AddRewardID   ";

            $this->title_lists = array(
//"id",
"orderstates",
"userid",
"price",
//"freeprice",
//"oldglobelnum",
//"oldcurrceynum",
"orderid",
//"channelorderid",
//"channellabel",
"shopitemid",
"channellabel",
"channelcode",
//"addglobelsum",
//"addcurrceysum",
"logtime",
"addrewardid"
                );

           $userid = $_POST["userid"];
           $starttime = &$_POST["starttime"];
           $endtime = $_POST["endtime"];
           $rangeid = $_POST["range_id"];
           $rangevalue = get_platform_range_value($rangeid);

           $map = array();
           if(!empty($userid)){
                $map['UserID'] = array('eq',$userid);
                }
            if($rangeid =="1"){//全部
            }
            elseif($rangeid == "2")//非GM
            {
              $map['channelLabel'] = array('neq',$rangevalue);;
            }
            elseif(!empty($rangeid) && !empty($rangevalue)){
            $map['channelLabel'] = array(array('eq',$rangeid),array('eq',$rangevalue), 'or') ;
            }
           if(!empty($starttime) && !empty($endtime)){
              $map['LogTime'] = array('between',"{$starttime},{$endtime}");
           }
           else{
               if(!empty($starttime)){
                    $map['LogTime'] = array('gt',$starttime);
                    }
               if(!empty($endtime)){
                    $map['LogTime'] = array('lt',$endtime);
                    }
            }

             $this->list_data= M("fishrechargelog",null,$db_config)->field($fields)->where($map)->select();
             $total = 0;
             foreach($this->list_data as $key => $value){
             //dump($value["price"]);
                $total += $value["price"]/100;
                //dump($total);
             }
             //dump($total);
             //dump($total);
             //dump($total);
             //dump( M("fishrechargelog",null,$db_config)->GetLastSql());
             $this->total = $total;
        }
        $this->ranges = get_platform_range_names();
        $this->display('GmTool:chargelog');
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
            $IsAll = I('post.isall');
            if($IsAll == 0){
                if(empty($RewardID)||empty($RewardNum)||empty($DestUserID))
                {
                    $this->redirect('GmTool:mail','',3, '亲，参数为空!!!');
                }
            }
            else{
                $DestUserID = 0;
                if(empty($RewardID)||empty($RewardNum))
                {
                    $this->redirect('GmTool:mail','',3, '亲，参数为空!!!');
                }
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
            $IP = I('post.ip');
            $arrID=array();
            if(empty($RewardNum))
            {
                $this->redirect('GmTool:kick','',3, '亲，分钟参数为空!!!');
            }
            if(!empty($IP))//id
            {
                $db_config = get_db_config();
                $map['RsgIP'] = $IP;
                $res =  M("accountinfo",null,$db_config)->field("UserID")->where($map)->select();
                foreach($res as $key => $value)
                {
                    array_push($arrID,$value['userid']);
                }
                //dump($res);
                //$data['target']=$DestUserID;
            }
            elseif(!empty($DestUserID))//id
            {
                $data['id']=0;
                array_push($arrID,$DestUserID);//$data['target']=$DestUserID;
            }
            else
            {
                 $this->redirect('GmTool:kick','',3, '亲，IP和UserID参数为空!!!');
            }
            $arr_db_url = get_db_config_url();

            foreach($arrID as $key => $value)
            {
               // dump($value);
                $data['type']= self::$GT_Kick;
                $data['num']=$RewardNum;
                $data['content']=0;
                $data['id']=0;
                $data['target']=$value;
                $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            }

            //file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
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


        public function unkick(){
        if(IS_POST){
            //$RewardNum=I('post.RewardNum');
            $DestUserID = I('post.DestUserID');
            $IP = I('post.ip');
            $arrID=array();

            $db_config = get_db_config();
            if(!empty($IP))//id
            {
                $map['RsgIP'] = $IP;
                $res =  M("accountinfo",null,$db_config)->field("UserID")->where($map)->select();
                foreach($res as $key => $value)
                {
                    array_push($arrID,$value['userid']);
                }
                //dump($res);
                //$data['target']=$DestUserID;
            }
            elseif(!empty($DestUserID))//id
            {
                array_push($arrID,$DestUserID);//$data['target']=$DestUserID;
            }
            else
            {
                 $this->redirect('GmTool:kick','',3, '亲，IP和UserID参数为空!!!');
            }

            foreach($arrID as $key => $value)
            {
            dump($value);
                //$map['UserID'] = $value;
                //$data['IsFreeze'] = 0;
                $sql = "UPDATE `accountinfo` SET `IsFreeze`=0 WHERE `UserID` = 20268";
                $res =  M("accountinfo",null,$db_config)->execute($sql);
                //dump(M("accountinfo",null,$db_config)->GetLastSql());
                //dump($res);
                if($res)
                {
                    $this->redirect('GmTool:kick','',3, '亲，解封成功!!');
                }
                else
                {
                    $this->redirect('GmTool:kick','',3, '亲，解封失败!!!');
                }
            }

            //file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);

        }
        else{
            $curuserid = get_current_userid();
            if(isset($curuserid)){
                $this->curuser = $curuserid;
            }
            $this->display('GmTool:kick');
        }
    }


    public function states(){
        if(IS_POST){
            $typeid=I('post.typeid');
            $DestUserID = I('post.DestUserID');
            if(empty($DestUserID))
            {
                $this->redirect('GmTool:states','',3, '亲，参数为空!!!');
            }

            $arr_db_url = get_db_config_url();
            $data['type']= self::$GT_Clear_States;
            $data['id']=0;
            $data['num']=1;
            $data['content']=$typeid;
            $data['target']=$DestUserID;
            $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
            if($httpstr == "SUCCESS")
            {
                $this->redirect('GmTool:states','',3, '亲，发送命令成功!稍后请查询!!');
            }
            else
            {
                $this->redirect('GmTool:states','',3, '亲，发送失败!!!');
            }
        }
        else{
            $curuserid = get_current_userid();
            if(isset($curuserid)){
                $this->curuser = $curuserid;
            }
            $this->display('GmTool:states');
        }
    }

    public function set_black(){
        if(IS_POST){
            $DestUserID = I('post.DestUserID');
            if(empty($DestUserID))
            {
                $this->redirect('GmTool:set_black','',3, '亲，参数为空!!!');
            }
            $arr_db_url = get_db_config_url();
            $data['type']= self::$GT_SetBlack;
            $data['id']=0;
            $data['num']=0;
            $data['content']=0;
            $data['target']=$DestUserID;
            $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
            if($httpstr == "SUCCESS")
            {
                $this->redirect('GmTool:set_black','',3, '亲，发送命令成功!稍后请查询!!');
            }
            else
            {
                $this->redirect('GmTool:set_black','',3, '亲，发送失败!!!');
            }
        }
        else{
            $curuserid = get_current_userid();
            if(isset($curuserid)){
                $this->curuser = $curuserid;
            }
            $this->display('GmTool:set_black');
        }
    }

    public function unset_black(){
        if(IS_POST){
            $DestUserID = I('post.DestUserID');
            if(empty($DestUserID))
            {
                $this->redirect('GmTool:unset_black','',3, '亲，参数为空!!!');
            }
            $arr_db_url = get_db_config_url();
            $data['type']= self::$GT_UnSetBlack;
            $data['id']=0;
            $data['num']=0;
            $data['content']=0;
            $data['target']=$DestUserID;
            $httpstr = $this->http($arr_db_url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
            file_put_contents('wmgmlog.txt', "httpstr:".$httpstr.PHP_EOL, FILE_APPEND);
            if($httpstr == "SUCCESS")
            {
                $this->redirect('GmTool:unset_black','',3, '亲，发送命令成功!稍后请查询!!');
            }
            else
            {
                $this->redirect('GmTool:unset_black','',3, '亲，发送失败!!!');
            }
        }
        else{
            $curuserid = get_current_userid();
            if(isset($curuserid)){
                $this->curuser = $curuserid;
            }
            $this->display('GmTool:unset_black');
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
           $this->title_lists = array(
                 "id" => "ID",
                 "userid" => "服务端ID",
                 "itemid"=>"物品ID",
                 "itemsum" => "物品数量",
                 "address" => "地址",
                 "phone" => "电话",
                 "name" => "姓名",
                 "shoptime" => "兑换时间",
                 "medalnum" => "红包",
                 "nowmedalnum" => "剩余红包",
                 "identity"=>"身份证号",
                 "ischeck" => "审核(0审核中1已发货)",
                );

       if(IS_POST){
           $userid = $_POST["userid"];
           $db_config = get_db_config();

            $fields = "`id`,
                      `userid`,
                      `itemid`,
                      `itemsum`,
                      `address`,
                      `phone`,
                      `name`,
                      `shoptime`,
                      `medalnum`,
                      `nowmedalnum`,
                      `identity`,
                      `ischeck`
                       ";// where  UserID= {$userid}";
           if(!empty($userid)){
               $map["UserID"] = $userid;//$sql .= " where  UserID= {$userid}";
           }
          $IsCheck = $_POST["IsCheck"];
          if(isset($IsCheck)){
               $map["IsCheck"] = $IsCheck;//$sql .= " where  UserID= {$userid}";
           }
           // $map["IsCheck"] = 0;
           $res =  M("fishentityitem",null,$db_config)->field($fields)->where($map)->select();
           //dump(M("fishentityitem",null,$db_config)->GetLastSql());
           //dump(M("fishentityitem",null,$db_config)->GetLastSql());
           $this->list_data = $res;
        }
         $curuserid = get_current_userid();
            if(isset($curuserid)){
                $this->curuser = $curuserid;
            }
        //$this->gameid = $_SESSION['user']['gameid'];
        $this->display('GmTool:handleentity');
    }

    public function handleentity_shen(){
           $this->title_lists = array(
                 "id" => "ID",
                 "userid" => "服务端ID",
                 "itemid"=>"物品ID",
                 "itemsum" => "物品数量",
                 "address" => "地址",
                 "phone" => "电话",
                 "name" => "姓名",
                 "shoptime" => "兑换时间",
                 "medalnum" => "红包",
                 "nowmedalnum" => "剩余红包",
                 "identity"=>"身份证号",
                 "ischeck" => "审核(0审核中1已审核)",
                );

          $id = $_GET["id"];
           $userid = $_GET["userid"];
           $db_config = get_db_config();
           $sql = "update fishentityitem set IsCheck = 1 where ID = {$id}";
           $res =  M("fishentityitem",null,$db_config)->execute($sql);

            $sql = "select
                      `id`,
                      `userid`,
                      `itemid`,
                      `itemsum`,
                      `address`,
                      `phone`,
                      `name`,

                      `medalnum`,
                      `nowmedalnum`,
                      `identity`,
                      `shoptime`,
                      `ischeck`
                      from fishentityitem ";// where  UserID= {$userid}";
            if(!empty($userid)){
               $sql .= " where  UserID= {$userid}";
           }
           $res =  M("fishentityitem",null,$db_config)->query($sql);
                      $this->list_data = $res;
        $this->curuser = $userid;
        $this->display('GmTool:handleentity');
    }


    public function handleentity_fa(){
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
           $id = $_GET["id"];
           $userid = $_GET["userid"];
           $this->id = $id;
           $this->userid = $userid;
           $this->display('GmTool:handleentity_fa');
        }
    }

          public function userentity(){

           $this->title_lists = array(
  "userid"              =>"服务端ID",
  "name"                =>"姓名",
  "phone"               =>"手机",
  "email"               =>"邮箱",
  "identityid"          =>"身份证",
  "entityitemusename"   =>"认证名",
  "entityitemusephone"  =>"认证手机",
  "entityitemuseaddress"=>"认证地址",
  "emailcode"           =>"emailCode",
  "entityitemstates"    =>"states",
  "entityitemcity"      =>"city",
  "gender"              =>"性别"
                );

       if(IS_POST){
           $userid = $_POST["userid"];
           $db_config = get_db_config();

            $fields = "
 `UserID`              ,
  `Name`                ,
  `Phone`               ,
  `Email`               ,
  `IdentityID`          ,
  `EntityItemUseName`   ,
  `EntityItemUsePhone`  ,
  `EntityItemUseAddress`,
  `EmailCode`           ,
  `EntityItemStates`    ,
  `EntityItemCity`      ,
  `Gender`
                       ";
           if(!empty($userid)){
               $map["UserID"] = $userid;
           }

           $res =  M("fishuserentity",null,$db_config)->field($fields)->where($map)->select();
             //      dump(M("fishuserentity",null,$db_config)->GetLastSql());
           //dump(M("fishentityitem",null,$db_config)->GetLastSql());
           //dump($res);
           $this->list_data = $res;
        }
         $curuserid = get_current_userid();
            if(isset($curuserid)){
                $this->curuser = $curuserid;
            }
        //$this->gameid = $_SESSION['user']['gameid'];
        $this->display('GmTool:userentity');
    }

        public function setuserentity(){

        // $id = $_GET["id"];
         $userid = $_GET["userid"];
         $db_config = get_db_config();

  $data["Name"] =  "8888";
  $data["Phone"] = "18888888888";
  $data["Email"] = "123@123.com";
  $data["IdentityID"] = "388888888888888888";
  $data["EntityItemUseName"] =  "8888";
  $data["EntityItemUsePhone"] = "18888888888";
  $data["EntityItemUseAddress"] = "18888888888";
  $data["EmailCode"] = "1";
  $data["EntityItemStates"] = "1";
  $data["EntityItemCity"] = "1";
         $res =  M("fishuserentity",null,$db_config)->where("userid={$userid}")->save($data);
         //dump(M("fishuserentity",null,$db_config)->GetLastSql());
         //dump(M("fishuserentity",null,$db_config)->GetLastSql());
         //dump(M("fishuserentity",null,$db_config)->GetLastSql());
        //$this->curuser = $userid;
        $this->userentity();
    }
      public function shopnormalitem(){
           $this->title_lists = array(
                 "id" => "ID",
                 "userid" => "服务端ID",
                 "itemid"=>"物品ID",
                 "itemsum" => "物品数量",
                 //"address" => "地址",
                 //"phone" => "电话",
                 //"name" => "姓名",
                 "shoptime" => "兑换时间",
                 "medalnum" => "红包",
                 "nowmedalnum" => "剩余红包",
                // "identity"=>"身份证号",
                // "ischeck" => "审核(0审核中1已发货)",
                );

       if(IS_POST){
           $userid = $_POST["userid"];
           $db_config = get_db_config();

            $fields = "`id`,
                      `userid`,
                      `itemid`,
                      `itemsum`,

                      `medalnum`,
                      `nowmedalnum`,
                      `shoptime`,
                      `ischeck`
                       ";// where  UserID= {$userid}";
           if(!empty($userid)){
               $map["UserID"] = $userid;//$sql .= " where  UserID= {$userid}";
           }
          //$IsCheck = $_POST["IsCheck"];
          //if(isset($IsCheck)){
          //     $map["IsCheck"] = $IsCheck;//$sql .= " where  UserID= {$userid}";
          // }
           // $map["IsCheck"] = 0;
           $res =  M("fishshopnormalitem",null,$db_config)->field($fields)->where($map)->select();
           //dump(M("fishentityitem",null,$db_config)->GetLastSql());
           //dump(M("fishentityitem",null,$db_config)->GetLastSql());
           $this->list_data = $res;
        }
         $curuserid = get_current_userid();
            if(isset($curuserid)){
                $this->curuser = $curuserid;
            }
        //$this->gameid = $_SESSION['user']['gameid'];
        $this->display('GmTool:shopnormalitem');
    }

    public function addaccount(){
           $this->title_lists = array(
                 "date" => "日期",
                 "count" => "新增人数",
                );

       if(IS_POST){
           $startdate = $_POST["starttime"];
           if(empty($startdate)){
                $startdate=date("Y-m-d");
            }
           $enddate = $_POST["endtime"];
                      if(empty($enddate)){
                $enddate=date("Y-m-d");
            }
           $userid = $_POST["userid"];
           $db_config = get_db_config();



           $sql  = "select date(RsgLogTime) as date,count(UserID) as count  from accountinfo where date(RsgLogTime) >= '{$startdate}' and date(RsgLogTime) <= '{$enddate}'  group by date(RsgLogTime)";

           $res =  M("accountinfo",null,$db_config)->query($sql);
          // dump(M("accountinfo",null,$db_config)->GetLastSql());
          // dump(M("accountinfo",null,$db_config)->GetLastSql());
           $this->list_data = $res;
        }
        $this->display('GmTool:online');
    }

        public function relevel(){
        if(IS_POST){
            $fileContent = array(
 0  =>0       ,
 1  =>600     ,
 2  =>1400    ,
 3  =>2400    ,
 4  =>3600    ,
 5  =>6000    ,
 6  =>7600    ,
 7  =>9400    ,
 8  =>11400   ,
 9  =>13900   ,
 10 =>16900   ,
 11 =>20900   ,
 12 =>25900   ,
 13 =>31900   ,
 14 =>38900   ,
 15 =>46900   ,
 16 =>56900   ,
 17 =>68900   ,
 18 =>82900   ,
 19 =>100900  ,
 20 =>120900  ,
 21 =>145900  ,
 22 =>175900  ,
 23 =>210900  ,
 24 =>250900  ,
 25 =>295900  ,
 26 =>345900  ,
 27 =>405900  ,
 28 =>475900  ,
 29 =>555900  ,
 30 =>655900  ,
 31 =>785900  ,
 32 =>945900  ,
 33 =>1135900 ,
 34 =>1345900 ,
 35 =>1595900 ,
 36 =>1895900 ,
 37 =>2245900 ,
 38 =>2645900 ,
 39 =>3095900 ,
 40 =>3595900 ,
 41 =>3595900 ,
 42 =>4745900 ,
 43 =>5395900 ,
 44 =>6095900 ,
 45 =>6845900 ,
 46 =>7645900 ,
 47 =>8495900 ,
 48 =>9395900 ,
 49 =>10345900,
 50 =>11345900,
 51 =>13345900,
 52 =>16345900,
 53 =>20345900,
 54 =>25345900,
 55 =>31345900,
 56 =>38345900,
 57 =>46345900,
 58 =>55345900,
 59 =>65345900,
 60 =>85345900
);

            //file_put_contents("weixinpaylog.txt",$fileContent.PHP_EOL,FILE_APPEND);
            //libxml_disable_entity_loader(true);

            //$json_str = json_encode(simplexml_load_string($fileContent, 'SimpleXMLElement', LIBXML_NOCDATA));

            $old_value_array = $fileContent;//json_decode($json_str, true);

                 $fileContent =
array(
    0   =>0         ,
    1   =>500       ,
    2   =>2000      ,
    3   =>5000      ,
    4   =>10000     ,
    5   =>20000     ,
    6   =>40000     ,
    7   =>70000     ,
    8   =>100000    ,
    9   =>150000    ,
    10  =>200000    ,
    11  =>300000    ,
    12  =>450000    ,
    13  =>600000    ,
    14  =>800000    ,
    15  =>1000000   ,
    16  =>1300000   ,
    17  =>1650000   ,
    18  =>2100000   ,
    19  =>2500000   ,
    20  =>3100000   ,
    21  =>3800000   ,
    22  =>4800000   ,
    23  =>6000000   ,
    24  =>7400000   ,
    25  =>9000000   ,
    26  =>11000000  ,
    27  =>13000000  ,
    28  =>15000000  ,
    29  =>17000000  ,
    30  =>19000000  ,
    31  =>21000000  ,
    32  =>24000000  ,
    33  =>25000000  ,
    34  =>27000000  ,
    35  =>29000000  ,
    36  =>31000000  ,
    37  =>33000000  ,
    38  =>35000000  ,
    39  =>37000000  ,
    40  =>40000000  ,
    41  =>45000000  ,
    42  =>50000000  ,
    43  =>55000000  ,
    44  =>60000000  ,
    45  =>65000000  ,
    46  =>70000000  ,
    47  =>75000000  ,
    48  =>80000000  ,
    49  =>85000000  ,
    50  =>90000000  ,
    51  =>95000000  ,
    52  =>100000000 ,
    53  =>200000000 ,
    54  =>300000000 ,
    55  =>400000000 ,
    56  =>500000000 ,
    57  =>600000000 ,
    58  =>700000000 ,
    59  =>800000000 ,
    60  =>900000000 ,
);

            //file_put_contents("weixinpaylog.txt",$fileContent.PHP_EOL,FILE_APPEND);
            //libxml_disable_entity_loader(true);

           // $json_str = json_encode(simplexml_load_string($fileContent, 'SimpleXMLElement', LIBXML_NOCDATA));

            $new_value_array = $fileContent;//json_decode($json_str, true);
           // dump($new_value_array);

           $db_config = get_db_config();
           $res =  M("accountinfo",null,$db_config)->query("select UserID,FishLevel,FishExp from accountinfo where IsRobot != 1");

            //dump($res);
           //dump(count($res));
            for($i=0; $i < count($res);$i++){
                $Userid = $res[$i]["userid"];
                $Level = $res[$i]["fishlevel"];
                $Exp = $res[$i]["fishexp"];

            //dump($Userid);
            //dump($Level);
            //dump($Exp);
                $AllExp = $Exp;
                foreach($old_value_array as $key=>$value){
            //dump($key);
            //dump($value);
                    $oldlevel = $key;
                    $oldexp = $value;

                   if($oldlevel >= $Level){
                       break;
                    }
                    $AllExp += $oldexp;
                    //dump($AllExp);
                }
                file_put_contents("level.txt","AllExp:".$AllExp.PHP_EOL,FILE_APPEND);


                $AllLevel =0;
           foreach($new_value_array as $key=>$value){
            //dump($key);
            //dump($value);
                    $newlevel = $key;
                    $newexp = $value;

                   if($AllExp < $newexp){
                   $AllLevel = $newlevel;
                       break;
                    }
                    $AllExp -= $newexp;


                }
                //dump($AllLevel);
                //dump($AllExp);
                file_put_contents("level.txt","userid:".$Userid."  ".$Level."  ".$Exp."=>".$AllLevel."  ".$AllExp.PHP_EOL,FILE_APPEND);

                file_put_contents("level.sql","update accountinfo set FishLevel=".$AllLevel.",FishExp=".$AllExp."  where UserID=".$Userid.";".PHP_EOL,FILE_APPEND);

            }
        }

            $this->display('GmTool:reloadconfig');

    }
}
