<?php
namespace app\dashboard\controller;

use app\common\controller\WebController;
use app\common\Enums\UploadEnums;
use app\common\Factory\UploadFactory;
use com\Tree;
use think\Db;
use think\Request;
use app\dashboard\model\Category;
use lang;


class CategoryController extends WebController
{
    private $Category;

    private $type;

    public function __construct(Request $request,Category $category)
    {
        parent::__construct($request);
        $this->Category = $category;
        $this->type = isset($this->data['type']) ? $this->data['type'] : 1;

    }


    public function lists(){

        $page = isset($this->data['page'])?  $this->data['page'] :1;
        $limit = isset($this->data['limit'])?  $this->data['limit'] :10;

        if($this->type == 1){
            $arr = Db::name('category')->order('sort_order asc')->select();
        }

        foreach ($arr as &$item) {
            $item['mobile_icon'] = build_img_uri($item['mobile_icon']);
//            $item['mobile_icon'] = $item['mobile_icon'];
            $item['Bgoodimg'] =empty($item['Bgoodimg']) ? '':$item['Bgoodimg'];
        }

        $tree = new Tree();
        $res = $tree->list_to_tree($arr,'cat_id','parent_id');

//        $res = memuLevelClear($res);
        return $this->respondWithArray(['item'=>$res,'pagecount'=>0]);

    }


    public function add(){
        $img_ext='category';
        if(isset($_FILES['mobile_icon'])){
            $upload = UploadFactory::getService(UploadEnums::LOCAL);
            $this->data['mobile_icon'] = $upload->singleUpload($_FILES['mobile_icon'],$img_ext);
        }
        try{
            $this->Category->allowField(true)->save($this->data);
            $this->Log->record_new(__CLASS__,__FUNCTION__,$this->Category->getLastInsID());
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::insert);
    }


    public function cat_id(){

        $categoty_id = $this->Category->alias('c')->where('parent_id',$this->data['cat_id'])->field('cat_id')->select();
//
        foreach ($categoty_id as $key=>$val){
            $data[]= Db::name('goods_sup')->field("id as good_id")->where('category',$val['cat_id'])->select();

        }
        $res=$this->arr_foreach($data);

        return $this->respondWithArray($res);
    }

    function arr_foreach ($arr)
    {
        static $tmp=array();
        if (!is_array ($arr))
        {
            return false;
        }
        foreach ($arr as $val )
        {
            if (is_array ($val))
            {
                $this->arr_foreach ($val);
            }
            else
            {
                $tmp[]=$val;
            }
        }
        return $tmp;
    }

    public function edit(){
        unset($this->data['mobile_icon']);

        if($this->type == 1){
            $validate = $this->validate($this->data, 'CategoryValidate.edit');

            if($validate !== true){
                return json_error(NULL,$validate,self::INVALID_PARAMETER);
            }
            $img_ext='category';
        }


        if(isset($_FILES['mobile_icon'])){
            $upload = UploadFactory::getService(UploadEnums::LOCAL);
            $this->data['mobile_icon'] = $upload->singleUpload($_FILES['mobile_icon'],$img_ext);
        }
//        if(isset($_FILES['Bgoodimg'])){
//            $this->data['Bgoodimg'] ='http://qiniu.huacaijia.com/'. $upload->single_upload($_FILES['Bgoodimg'],$img_ext);
//        }

        try{
            $this->Category->allowField(true)->save($this->data,['cat_id'=>$this->data['cat_id']]);
            $this->Log->record_new(__CLASS__,__FUNCTION__,$this->data['cat_id']);
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }
        return $this->respondWithArray(NULL,lang::update);
    }

    public function delete(){

        $validate = $this->validate($this->data, 'app\dashboard\validate\CategoryValidate.delete');
        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        /*判断商品子类是否存在*/
        $flag = $this->Category->where('parent_id',$this->data['id'])->select();
        if(count($flag)){
            return json_error(NULL,'该分类含有子类',self::INVALID_PARAMETER);
        }
        $goods=$this->Category->name('goods_sup')->where('category','in',$this->data['id'])->select();
        if(count($goods)){
            return json_error(NULL,'分类里存在商品!',self::INVALID_PARAMETER);
        }

        try{
            $this->Category->where('cat_id',$this->data['id'])->delete();
            $this->Log->record_new(__CLASS__,__FUNCTION__,$this->data['id']);
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }
        return $this->respondWithArray(NULL,lang::delete);
    }


}