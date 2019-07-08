<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2017/12/4
 * Time: 8:46
 */

namespace app\dashboard\controller;
use app\common\controller\WebController;
use think\Db;
use think\Request;
use app\dashboard\model\Goodstype;
use lang;


class GoodstypeController extends WebController
{
    private $Goodstype;


    public function __construct(Request $request,Goodstype $Goodstype)
    {
        parent::__construct($request);
        $this->Goodstype = $Goodstype;

    }

    public function lists(){

        $page = isset($this->data['page'])?  $this->data['page'] :1;
        $limit = isset($this->data['limit'])?  $this->data['limit'] :30;

        $res = $this->Goodstype->get_Goodstypelist($page,$limit);

        return $this->respondWithArray($res);
    }

    public function add(){
        $attr_group = $this->data['attr_group'];
        if(empty($attr_group)){
            $attr_group = '';
        }

        try{
            $data = ['attr_group' =>$attr_group, 'cat_name' =>$this->data['cat_name'],'enabled'=>1];
            Db::table('ecs_goods_type')->insert($data);

        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::insert);

    }

    public function edit(){

        $validate = $this->validate($this->data, 'app\dashboard\validate\GoodstypeValidate.edit');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        $attr_group = $this->data['attr_group'];
        if(empty($attr_group)){
            $attr_group = '';
        }

        try{

            Db::name('goods_type')->where('cat_id',$this->data['cat_id'])->update(['attr_group' =>$attr_group, 'cat_name' =>$this->data['cat_name'],]);

        }catch (\Exception $e) {
            abort(400, $e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::update);

    }

    public function delete(){

        $validate = $this->validate($this->data, 'app\dashboard\validate\GoodstypeValidate.delete');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        try{

            Db::name('goods_type')->where('cat_id',$this->data['cat_id'])->delete();

        }catch (\Exception $e) {
            abort(400, $e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::delete);

    }

}