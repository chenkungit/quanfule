<?php
namespace app\dashboard\controller;

use app\common\controller\WebController;
use app\common\Enums\UploadEnums;
use app\common\Factory\UploadFactory;
use think\Request;
use app\dashboard\model\Log;
use app\dashboard\model\Hometheme;
use lang;

class HomethemeController extends WebController
{
    protected $Hometheme;

    public function __construct(Request $request,Hometheme $hometheme)
    {
        parent::__construct($request);
        $this->Hometheme = $hometheme;
    }

    public function lists()
    {
        $page = isset($this->data['page'])?  $this->data['page'] :1;
        $limit = isset($this->data['limit'])?  $this->data['limit'] :10;
        $carousel_type = isset($this->data['carousel_type'])? $this->data['carousel_type'] : 1;



        $arr['item'] = $this->Hometheme->field('*,'.concat_img('theme_banner','',self::qinniu_url))
                        ->where('carousel_type',$carousel_type)->page("$page,$limit")->order('sort asc')->select();
        $count = $this->Hometheme->count();
        $arr['pagecount'] = ceil($count/$limit);
        return $this->respondWithArray($arr);
    }

    public function info()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\HomethemeValidate.info');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        $arr = $this->Hometheme->where('t_id',$this->data['id'])->find();

        return $this->respondWithArray($arr);
    }

    public function add()
    {

        $validate = $this->validate($this->data, 'app\dashboard\validate\HomethemeValidate.add');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        if(!isset($_FILES['img'])){
            return json_error(NULL,'请上传banner图',self::INVALID_PARAMETER);
        }


        $upload = UploadFactory::getService(UploadEnums::LOCAL);

        $this->data['theme_banner'] = $upload->singleUpload($_FILES['img'],'theme_banner');

        try{
            $this->Hometheme->allowField(true)->save($this->data);
            Log::record($this->Hometheme->getLastInsID(),$this->ip);
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }
        return $this->respondWithArray(NULL,lang::insert);
    }

    public function edit()
    {

        $validate = $this->validate($this->data, 'app\dashboard\validate\HomethemeValidate.edit');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        if(isset($_FILES['img'])){
            $upload = UploadFactory::getService(UploadEnums::LOCAL);

            $this->data['theme_banner'] = $upload->singleUpload($_FILES['img'],'theme_banner');
        }

        try{
            $this->Hometheme->allowField(true)->save($this->data, ['t_id' => $this->data['t_id']]);
            Log::record($this->data['t_id'],$this->ip);
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }
        return $this->respondWithArray(NULL,lang::update);
    }


    public function delete()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\HomethemeValidate.delete');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        try{
            $this->Hometheme->where('t_id',$this->data['id'])->delete();
            Log::record($this->data['id'],$this->ip);
            /*-选择是否删除七牛图片-*/
            //$upload->delete($img_url);
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::delete);
    }
}