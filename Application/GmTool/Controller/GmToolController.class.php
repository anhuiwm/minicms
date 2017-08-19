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
class GmToolController extends Controller {
    public function _initialize(){
        header("Content-Type:text/html; charset=utf-8");
        // 用户登录权限认证
        // 如果模块要加载登录认证，必须加载认证方法 user_auth();
        $is_login=user_auth();
        if (!$is_login) {
            $this->redirect('/Auth/Index/login','',2, '亲，还未登录,2s后跳转到登录界面');
        }

        // 获取节点URL，只用在侧栏
        $this->PUB_URL_NODE=$Think.MODULE_NAME.'/'.$Think.CONTROLLER_NAME;

        // 获取节点
        $this->PUB_NODE=$Think.MODULE_NAME.'/'.$Think.CONTROLLER_NAME.'/'.$Think.ACTION_NAME;
        if (($Think.CONTROLLER_NAME =='Index' or $Think.CONTROLLER_NAME =='index') and ($Think.ACTION_NAME =='Index' or $Think.ACTION_NAME =='index')) {
            $this->PUB_NODE=$Think.MODULE_NAME;
        }

        // 公共判断，用户是否有该节点权限。有的话才能访问
        $this->thisNode=auth_action($this->PUB_NODE);
        if (!$this->thisNode) {
            $this->redirect('/System','',5, '对不起，你没有操作权限,5秒后跳转至首页...');
        }


        // 这里做路径导航
        $path_nav="";
        $path_nav='<li><a href="'.__ROOT__.'/System/Index/index">系统首页</a></li>';
        if ($Think.MODULE_NAME) {
            $thisdd=get_menu(array('node_name'=>$Think.MODULE_NAME),'find');
            $path_nav=$path_nav.'<li><a href="'.__ROOT__.'/'.$Think.MODULE_NAME.'">'.$thisdd['title'].'</a></li>';
        }
        if ($Think.CONTROLLER_NAME) {
            $thisdd=get_menu(array('node_name'=>$Think.MODULE_NAME.'/'.$Think.CONTROLLER_NAME),'find');
            $path_nav=$path_nav.'<li><a>'.$thisdd['title'].'</a></li>';
        }

        if ($Think.ACTION_NAME) {
            $thisdd=get_menu(array('node_name'=>$Think.MODULE_NAME.'/'.$Think.CONTROLLER_NAME.'/'.$Think.ACTION_NAME),'find');
            $path_nav=$path_nav.'<li><a href="'.__ROOT__.'/'.$Think.MODULE_NAME.'/'.$Think.CONTROLLER_NAME.'/'.$Think.ACTION_NAME.'">'.$thisdd['title'].'</a></li>';
        }
        $this->path_nav=$path_nav;

        // 判断用户是否为配置文件中的超级管理员
        // 如果是配置文件中的超级管理员，才能访问其他模块，否则只能访问商户中心模块



        $map=array('type'=>'0');
        $this->PUB_TOPMENU_LIST=get_menu($map);

        $this->PUB_THISMENU=get_menu(array('node_name'=>$this->PUB_NODE),'find');//当前菜单

        // 当前顶部菜单
        $this->PUB_TOPMENU=get_top_menu($this->PUB_THISMENU);
        // 当前侧栏一级菜单列表
        $this->SEC_MENU=get_menu(array('pid'=>$this->PUB_TOPMENU['id'],'type'=>'1'));

        //节点所在的当前模型
        $this->THIS_MODEL=get_model($this->PUB_THISMENU['model']);
        $this->dbs=get_dbs();
        $this->db = get_db();
    }

    public function index(){

        $log_map=array();
        if (!$_SESSION['user']['is_super']==1) {
          $log_map['user_id']=$_SESSION['user']['user_id'];
        }
        $this->log_lists=M('user_log')->where($log_map)->order('id desc')->limit('0,18')->select();
        $this->shortcut=M('shortcut')->limit(0,12)->select();
        $this->display('GmTool:index');
    }

    /**
     * 发送HTTP请求方法
     * @param  string $url    请求URL
     * @param  array  $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @return array  $data   响应数据
     */
    function http($url, $params, $method = 'GET', $header = array(), $multi = false){
        $opts = array(
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                dump($error);
            // throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) {
            dump($error);
            //throw new Exception('请求发生错误：' . $error);
        }
        return  $data;
    }

}
