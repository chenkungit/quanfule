<?php
namespace app\dashboard\controller;

use app\common\controller\WebController;
use think\Db;
use think\Request;
use lang;
use app\dashboard\model\Nav;
use app\dashboard\model\Log;

class NavController extends WebController
{
    protected $Nav;

    public function __construct(Request $request,Nav $nav)
    {
        parent::__construct($request);
        $this->Nav = $nav;
    }

    public function lists()
    {
        $page = isset($this->data['page'])?  $this->data['page'] :1;
        $limit = isset($this->data['limit'])?  $this->data['limit'] :10;

        $arr['item'] = $this->Nav->page("$page,$limit")->order('sort asc')->select();
        $count = $this->Nav->count();
        $arr['pagecount'] = ceil($count/$limit);
        return $this->respondWithArray($arr);
    }

    public function info()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\NavValidate.info');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        $arr = $this->Nav->where('n_id',$this->data['id'])->find();

        return $this->respondWithArray($arr);
    }

    public function add()
    {

        $validate = $this->validate($this->data, 'app\dashboard\validate\NavValidate.add');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        try{
            $this->Nav->allowField(true)->save($this->data);
            Log::record($this->Nav->getLastInsID(),$this->ip);
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }
        return $this->respondWithArray(NULL,lang::insert);
    }

    public function edit()
    {

        $validate = $this->validate($this->data, 'app\dashboard\validate\NavValidate.edit');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        try{

            $this->Nav->allowField(true)->save($this->data, ['n_id' => $this->data['n_id']]);
            Log::record($this->data['n_id'],$this->ip);
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }
        if(!empty($n_id)){
            $this->Nav->where('n_id','=',$n_id['n_id'])->data(['first' => 0])->update();
        }
        return $this->respondWithArray(NULL,lang::update);
    }


    public function delete()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\NavValidate.delete');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        try{
            $this->Nav->where('n_id',$this->data['id'])->delete();
            Log::record($this->data['id'],$this->ip);
            /*-选择是否删除七牛图片-*/
            //$upload->delete($img_url);
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::delete);
    }
}