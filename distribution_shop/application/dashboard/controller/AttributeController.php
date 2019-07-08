<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2017/12/4
 * Time: 13:47
 */

namespace app\dashboard\controller;
use app\common\controller\WebController;
use think\Db;
use think\Request;
use app\dashboard\model\Attribute;
use lang;


class AttributeController extends WebController
{
    private $Attibute;


    public function __construct(Request $request,Attribute $Attibute)
    {
        parent::__construct($request);
        $this->Attibute = $Attibute;

    }

    public function lists(){

        $page = isset($this->data['page'])?  $this->data['page'] :1;
        $limit = isset($this->data['limit'])?  $this->data['limit'] :30;
        $goods_type= empty($this->data['goods_type']) ? 0 : intval($this->data['goods_type']);
        $sort_by = empty($this->data['sort_by']) ? 'sort_order' : trim($this->data['sort_by']);
        $sort_order = empty($this->data['sort_order']) ? 'DESC' : trim($this->data['sort_order']);

        $res = $this->Attibute->get_Attributelist($page,$limit,$goods_type,$sort_by,$sort_order);

        return $this->respondWithArray($res);
    }


    public function selected(){

        $res = Db::table('ecs_goods_type')->where('enabled',1)->field('cat_id as value,cat_name as label')->select();

        return $this->respondWithArray(['list'=>$res]);

    }

    public function selected_list(){

        $res = Db::table('ecs_goods_type')->where('cat_id',$this->data['cat_id'])->field('attr_group')->find();

        if(empty($res['attr_group']))
        {
            $info =  [];
        } else {

            $info = explode("\r\n", $res['attr_group']);

        }

        return $this->respondWithArray(['grouplist'=>$info]);

    }

    public function add(){

        $validate = $this->validate($this->data, 'app\dashboard\validate\AttributeValidate.add');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }
        if($this->data['attr_input_type'] != 1)
        {
            $this->data['attr_values'] = '';
        }

        $data = [
            'cat_id'          => $this->data['goodstype_id'],
            'attr_name'       => $this->data['attr_name'],
            'attr_index'      => $this->data['attr_index'],
            'attr_input_type' => $this->data['attr_input_type'],
            'is_linked'       => $this->data['is_linked'],
            'attr_values'     => isset($this->data['attr_values']) ? $this->data['attr_values'] : '',
            'attr_type'       => empty($this->data['attr_type']) ? '0' : intval($this->data['attr_type']),
            'attr_group'      => isset($this->data['attr_group']) ? intval($this->data['attr_group']) : 0
        ];

        try{
            Db::table('ecs_attribute')->insert($data);

        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::insert);

    }

    public function edit(){

        $validate = $this->validate($this->data, 'app\dashboard\validate\AttributeValidate.edit');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        if($this->data['attr_input_type'] != 1)
        {
            $this->data['attr_values'] = '';
        }

        $data = [
            'cat_id'          => $this->data['goodstype_id'],
            'attr_name'       => $this->data['attr_name'],
            'attr_index'      => $this->data['attr_index'],
            'attr_input_type' => $this->data['attr_input_type'],
            'is_linked'       => $this->data['is_linked'],
            'attr_values'     => isset($this->data['attr_values']) ? $this->data['attr_values'] : '',
            'attr_type'       => empty($this->data['attr_type']) ? '0' : intval($this->data['attr_type']),
            'attr_group'      => isset($this->data['attr_group']) ? intval($this->data['attr_group']) : 0
        ];

        try{
            Db::name('attribute')->where('attr_id',$this->data['attr_id'])->update($data);

        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::update);

    }

    public function delete(){

//        $validate = $this->validate($this->data, 'AttributeValidate.delete');
//        if($validate !== true){
//            return json_error(NULL,$validate,self::INVALID_PARAMETER);
//        }

        try{
            Db::name('attribute')->where('attr_id' ,'in',$this->data['attr_id'])->delete();

        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::delete);

    }


}