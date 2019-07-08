<?php
namespace app\dashboard\validate;

use think\Validate;

class CarouselValidate extends Validate
{
    protected $rule = [
        'id'=> 'require',
        'carousel_id'=>'require',
        'carousel_type'=>'require|number',
        'carousel_position'=>'require|number',
        'redirect_type'=>'require|number',
        'redirect_id'=>'require',
        'sort'=>'require',
        'enabled'=>'require',
        'add_time'=>'date|check_date_type',
        'end_time'=>'date',
    ];

    protected $message = [
        'id.require' => '请输入id',
        'redirect_type.require'=>'跳转类型不能为空',
        'redirect_id'=>'跳转id不能为空',
        'sort'=>'请输入排序'
    ];

    protected $scene = [
        'info'=>['id'],
        'add' =>['carousel_type','carousel_position','redirect_type','redirect_id','sort','add_time','end_time'],
        'edit'=>['carousel_id','add_time','end_time'],
        'delete'=>['id']
    ];

    protected function check_date_type($value,$rule,$data){

        if(!empty($data['add_time']) && !empty($data['end_time']) ){
            if(strtotime($data['add_time']) < strtotime(date("Y-m-d"))){
                return '开始时间必须大于今天';
            }
            if(strtotime($data['end_time']) <= strtotime($data['add_time'])){
                return '结束时间必须大于开始时间';
            }
        }else{
            return '请填写开始/结束时间';
        }

        return true;
    }


}