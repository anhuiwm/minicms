<?php
namespace GmTool\Controller;
use Think\Controller;
class IndexController extends GmToolController {
    public function index(){
      file_put_contents('wmgmlog.txt', "Gmtool IndexController:".PHP_EOL, FILE_APPEND);
      $log_map=array();
      if (!$_SESSION['user']['is_super']==1) {
        $log_map['user_id']=$_SESSION['user']['user_id'];
      }

      $this->log_lists=M('user_log')->where($log_map)->order('id desc')->limit('0,18')->select();
      $this->shortcut=M('shortcut')->limit(0,12)->select();
      $this->display('GmTool:index');
    }

}
