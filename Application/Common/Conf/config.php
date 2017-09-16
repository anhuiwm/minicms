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
// 基础配置文件
//----------------------------------
return array(
	//'配置项'=>'配置值'
	// 'LAYOUT_ON'=>true,
	'URL_MODEL' => '1',
	'VIEW_PATH'=>'./Public/',
	'STATIC_ROOT'=>'../Public/',
	'DEFAULT_MODULE'=>'Auth',

	// 入口路径
	'URL_INDEX'=>'index.php?s=',
	'TMPL_PARSE_STRING'  =>array(
		'__SHARE_VIEW__'=>'./Application/Public/View',
	),
	'DEFAULT_THEME' 	=> 'default',
	'SESSION_AUTO_START' => true,

	'LOAD_EXT_CONFIG' => 'db,data', //配置列表 各类数据 文件

        'DBS' =>array(
         '10.0.0.99'=>array(
            'DB_TYPE'=>'mysql',
            'DB_HOST'=>'10.0.0.99',
            'DB_NAME'=>'fishgame',
            'DB_USER'=>'root',
            'DB_PWD'=>'root',
            'DB_PORT'=>'3306',
            'url'=> "http://10.0.0.99:2680/gm_command_cl.clkj",
            ),
		'10.0.0.88'=>array(
            'DB_TYPE'=>'mysql',
            'DB_HOST'=>'10.0.0.99',
            'DB_NAME'=>'fishgame',
            'DB_USER'=>'root',
            'DB_PWD'=>'root',
            'DB_PORT'=>'3306',
            'url'=> "http://10.0.0.88:2680/gm_command_cl.clkj",
            ),

        '10.0.0.168'=>array(
            'DB_TYPE'=>'mysql',
            'DB_HOST'=>'10.0.0.168',
            'DB_NAME'=>'fishgame',
            'DB_USER'=>'root',
            'DB_PWD'=>'root',
            'DB_PORT'=>'3306',
            'url'=> "http://10.0.0.168:2680/gm_command_cl.clkj",
            ),

        '测试106.15.198.179'=>array(
            'DB_TYPE'=>'mysql',
            'DB_HOST'=>'106.15.198.179',
            'DB_NAME'=>'fishgame',
            'DB_USER'=>'root',
            'DB_PWD'=>'!@#dmq1987.',
            'DB_PORT'=>'3306',
            'url'=> "http://106.15.198.179:2680/gm_command_cl.clkj",
            ),
        '正式139.196.96.86'=>array(
            'DB_TYPE'=>'mysql',
            'DB_HOST'=>'rm-uf6mozrl6o240v725o.mysql.rds.aliyuncs.com',
            'DB_NAME'=>'fishgame',
            'DB_USER'=>'dumingqing',
            'DB_PWD'=>'!@#dmq1987',
            'DB_PORT'=>'3306',
            'url'=> "http://139.196.96.86:2680/gm_command_cl.clkj",
            ),
    )
);
