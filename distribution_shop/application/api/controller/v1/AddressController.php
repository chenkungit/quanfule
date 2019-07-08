<?php

namespace app\api\controller\v1;

use app\common\controller\ApiController;
use app\common\model\Users;
use think\Db;
use think\Exception;
use think\Request;

class AddressController extends ApiController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
    }

    public function lists(){

        $join = [
            ['ecs_users u', 'u.user_id=ua.user_id','LEFT'],
        ];
       $consignee_list = db('user_address')->alias('ua')->field("ua.address_id,ua.province,ua.city,ua.district,ua.consignee,ua.address,ua.tel,IF(u.address_id=ua.address_id,'1','0') as is_default")
           ->join($join)->where('ua.user_id',$GLOBALS['user_id'])->order('is_default desc')->select();

        if($consignee_list){
            foreach ($consignee_list AS $region_id => $consignee)
            {

                $pcd_id_string = sprintf('%s,%s,%s',$consignee['province'],$consignee['city'],$consignee['district']);
//                $pcd_array = db('region')->where(['region_id'=>['in',$pcd_id_string]])->column('region_name');
                $province = db('region')->where('region_id',$consignee['province'])->value('region_name');
                $city = db('region')->where('region_id',$consignee['city'])->value('region_name');
                $district_name = db('region')->where('region_id',$consignee['district'])->value('region_name');
                $consignee_list[$region_id]['province_name'] = $province?$province:'未知省份';
                $consignee_list[$region_id]['city_name'] = $city?$city:'未知市';
                $consignee_list[$region_id]['district_name'] = $district_name?$district_name:'未知区';
                $consignee_list[$region_id]['is_default'] =  intval($consignee_list[$region_id]['is_default']);
            }
        }else{
            $consignee_list = [];
        }


        return $this->respondWithArray($consignee_list);
    }

    public function add(){
        $validate = $this->validate($this->data, 'app\api\validate\AddressValidate.add');

        if($validate !== true){
            return json_error('',$validate,self::INVALID_PARAMETER);
        }

        $is_default = isset($this->data['is_default'])?$this->data['is_default']:0;

        $insert['user_id'] = $GLOBALS['user_id'];
        $insert['country'] = 1;
        $insert['province'] = $this->data['province'];
        $insert['city'] = $this->data['city'];
        $insert['district']= $this->data['district'];
        $insert['consignee']= $this->data['consignee'];
        $insert['tel']= $this->data['tel'];
        $insert['address']= $this->data['address'];

        Db::startTrans();
        try{
            $address_id = Db::name('user_address')->insertGetId($insert);

            /*-设为默认地址-*/
            if($is_default == 1){
                Db::name('users')->where('user_id',$GLOBALS['user_id'])->update(['address_id'=>$address_id]);
            }
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            abort(400,$e->getMessage());
        }
        return $this->respondWithArray('','新增地址成功');
    }

    public function info(){

        $validate = $this->validate($this->data, 'app\api\validate\AddressValidate.info');

        if($validate !== true){
            return json_error('',$validate,self::INVALID_PARAMETER);
        }
        $this->checkAddress();

        $field = "ua.consignee,ua.address_id,ua.tel,ua.province,ua.city,ua.district,ua.address,ua.zipcode,IF(u.address_id=ua.address_id,'1','0') as is_default";
        $arr = Db::name('user_address')->alias('ua')->field($field)
            ->where('ua.address_id',$this->data['address_id'])->join('ecs_users u','ua.user_id = u.user_id','LEFT')->find();

        $pcd_id_string = sprintf('%s,%s,%s',$arr['province'],$arr['city'],$arr['district']);
        $pcd_array = Db::name('region')->where('region_id','in',$pcd_id_string)->column('region_name');
        $arr['province_name'] = $pcd_array[0];
        $arr['city_name'] = $pcd_array[1];
        $arr['district_name'] = $pcd_array[2];
        $arr['is_default'] = intval($arr['is_default']);
        return $this->respondWithArray($arr);
    }

    public function edit(Users $users){

        $validate = $this->validate($this->data, 'app\api\validate\AddressValidate.edit');

        if($validate !== true){
            return json_error('',$validate,self::INVALID_PARAMETER);
        }
        $res = $this->checkAddress();

        $this->data['is_default'] = isset($this->data['is_default'])?$this->data['is_default']:0;

        $update['country'] = 1;//中国
        $update['province'] = $this->data['province'];
        $update['city'] = $this->data['city'];
        $update['district']= $this->data['district'];
        $update['consignee']= $this->data['consignee'];
        $update['tel']= $this->data['tel'];
        $update['address']= $this->data['address'];

        $this->data['is_default'] = isset($this->data['is_default'])?$this->data['is_default']:0;



        try{
            Db::name('user_address')->where('address_id',$this->data['address_id'])->update($update);

            /*-设为默认地址-*/
            if($this->data['is_default'] == 1){
                Db::name('users')->where('user_id',$GLOBALS['user_id'])->update(['address_id'=>$this->data['address_id']]);
            }

            if($this->data['is_default'] == 0){
                $default_address_id = $users->user_info('','','address_id');
                if($default_address_id == $res['address_id']){
                    Db::name('users')->where('user_id',$GLOBALS['user_id'])->update(['address_id'=>'']);
                }
            }

        }catch (\Exception $e){
            abort(400,$e->getMessage());
        }
        return $this->respondWithArray(NULL,'更新地址成功');
    }

    public function setDefault(){

        $validate = $this->validate($this->data, 'app\api\validate\AddressValidate.setDefault');

        if($validate !== true){
            return json_error('',$validate,self::INVALID_PARAMETER);
        }
        $this->checkAddress();


        $update['address_id'] = $this->data['address_id'];
        try{
            Db::name('users')->where('user_id',$GLOBALS['user_id'])->update($update);
        }catch (\Exception $e){
            abort(400,$e->getMessage());
        }
        return $this->respondWithArray('','设为默认地址成功');
    }

    public function delete(){

        $validate = $this->validate($this->data, 'app\api\validate\AddressValidate.delete');

        if($validate !== true){
            return json_error('',$validate,self::INVALID_PARAMETER);
        }
        $res = $this->checkAddress();


        try{
            /*-如果删除的是默认地址 将用户表中关联的address_id置为0-*/
            if($res['address_id'] == Db::name('users')->where('user_id',$GLOBALS['user_id'])->value('address_id')){
                Db::name('users')->where('user_id',$GLOBALS['user_id'])->update(['address_id'=>0]);
            }

            Db::name('user_address')->where('address_id',$this->data['address_id'])->delete();
        }catch (\Exception $e){
            abort(400,$e->getMessage());
        }
        return $this->respondWithArray('','删除地址成功');
    }

    public function get_regions($type = 1,$parent_id=1){

        $where = [
            ['region_type','=',$type],
            ['parent_id','=',$parent_id]
        ];
        $arr = Db::name('region')->field('region_id, region_name')->where($where)->select();

        return $this->respondWithArray($arr);
    }

    private function checkAddress(){

        $res = db('user_address')->where([['address_id','=',$this->data['address_id']],['user_id','=',$GLOBALS['user_id']]])->find();
        if(!$res){
            abort(404,'该地址不存在');
        }
        return $res;
    }


}