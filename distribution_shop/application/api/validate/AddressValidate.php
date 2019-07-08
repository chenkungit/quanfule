<?php
namespace app\api\validate;

use think\Validate;

class AddressValidate extends Validate
{
    protected $rule = [
        'consignee'=>'require',
        'tel'=>'require|regex:/^1[34758]{1}\d{9}$/',
        'province'=>'require',
        'city'=>'require',
        'district'=>'require',
        'address_id'=>'require',
        'is_default'=>'between:0,1'
    ];

    protected $message = [
        'province.require' => '请选择省|市|区',
        'city.require' => '请选择省|市|区',
        'district.require' => '请选择省|市|区',
        'address.require' => '请填写详细地址',
        'consignee.require' => '请填写姓名',
        'tel.require' => '请输入手机号码',
        'tel.regex'   => '请填写正确的手机号码',
        'address_id.require'=>'请填写地址id',
        'is_default'=>'设为默认参数错误',
    ];

    protected $scene = [
        'add'  =>  ['tel','consignee','province','city','district','address','is_default'],
        'info'  => ['address_id'],
        'delete'=> ['address_id'],
        'setDefault'=>['address_id'],
        'edit' =>['tel','consignee','province','city','district','address','is_default'],
    ];


}