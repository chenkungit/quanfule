<?php
namespace app\dashboard\model;


use think\Model;
use think\Request;

class Log  extends Model
{
    protected $table = 'ecs_admin_log';

    public static function record($id='',$ip){

        $log_info = self::log_info($id);

        $insert = [
            'log_time'=>time(),
            'user_id'=>$GLOBALS['user_id'],
            'log_info'=>$log_info,
            'ip_address'=>$ip
        ];
        self::insert($insert);
    }



    private static function log_info($id){


        switch($GLOBALS['action']){
            case 'add'   : $action = '新增'; break;
            case 'edit'   : $action = '编辑'; break;
            case 'delete'    : $action = '删除'  ; break;
            default:$action = '未知action'; break;
        }

        switch($GLOBALS['controller']){
            case 'carouselcontroller'   : $content = '广告'; break;
            case 'navcontroller'   : $content = '导航栏'; break;
            case 'homethemecontroller' : $content = '首页主题';break;
            case 'searchhotcontroller': $content = '热门搜索';break;
            case 'categorycontroller': $content = '分类';break;
            case 'bonuscontroller': $content = '优惠券';break;
            case 'privilegecontroller': $content='管理员'; break;
            case 'promotecontroller': $content='促销';break;
            case 'activitypagecontroller': $content='活动页';break;
            case 'freeshippingcontroller' : $content='包邮活动';break;
            default:return '未知controller'; break;
        }

        return $action.$content.$id;

    }

    public function record_new($function,$method,$operate_id=null){


        if(is_array($operate_id)){

            $insertall = [];
            foreach ($operate_id as $item){
                $insertall[] = [
                    'log_time'=>date('Y-m-d H:i:s'),
                    'user_id'=>$GLOBALS['user_id'],
                    'function'=>$function,
                    'method'=>$method,
                    'operate_id'=>$item,
                    'ip_address'=>Request()->ip()
                ];
            }
            $this->table('ecs_admin_log_new')->insertAll($insertall);
        }else{

            $insert = [
                'log_time'=>date('Y-m-d H:i:s'),
                'user_id'=>$GLOBALS['user_id'],
                'function'=>$function,
                'method'=>$method,
                'operate_id'=>$operate_id,
                'ip_address'=>Request()->ip()
            ];

            $this->table('ecs_admin_log_new')->insert($insert);
        }

    }


}