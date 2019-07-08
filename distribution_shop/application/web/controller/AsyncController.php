<?php

namespace app\web\controller;

use app\common\controller\ApiController;
use think\Request;

class AsyncController extends ApiController
{
    const webHook_Password = 'SiedyCsiXeBWiBxp';

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function webHook(){

        if($this->data['password'] == self::webHook_Password){
            if($this->data['hook_name'] == 'push_hooks'){
                $fields  = 'cd /data/wwwroot/api;';
                $fields .= 'sudo -u www git pull;'; /*-更新-*/
                $fields .= 'sudo -u www git push local_git;';
                $fields .= 'sudo -u www php think optimize:schema;'; /*-更新数据库缓存文件-*/
                $fields .= 'sudo -u www php think optimize:route;'; /*-更新路由缓存-*/
//                $fields .= 'sudo -u www-data php think optimize:config;'; /*-更新配置缓存-*/
                $fields .= 'sudo -u www php think optimize:autoload;';
                $result = shell_exec($fields);
//                redis()->set('result',$result);
                return 'success';
            }
        }

    }
}