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
        '阿里106.15.198.179'=>array(
            'DB_TYPE'=>'mysql',
            'DB_HOST'=>'106.15.198.179',
            'DB_NAME'=>'fishgame',
            'DB_USER'=>'root',
            'DB_PWD'=>'!@#dmq1987.',
            'DB_PORT'=>'3306',
            'url'=> "http://106.15.198.179:2680/gm_command_cl.clkj",
            ),
    )
);