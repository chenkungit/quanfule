<?php

namespace app\api\controller\v2;

use app\common\controller\ApiController;
use think\Request;
use app\common\model\Category;

class CategoryController extends ApiController
{
    protected $category;

    public function __construct(Request $request,Category $category)
    {
        parent::__construct($request);
        $this->category = $category;
        $this->checkBasicAuth();
    }


    public function product_list($cat_id,$brand=0,$beon=0,$filter=0,$sort=0,$order=0,$page=1,$limit=10,$price_range=null,$format=false){


        if($page == 1){
            $arr['swiper'] = $this->category->swiper($cat_id);
            $arr['item'] = $this->category->product_list($cat_id,$brand,$beon,$filter,$sort,$order,$page,$limit,$price_range,$format);
        }else{
            $arr['item'] = $this->category->product_list($cat_id,$brand,$beon,$filter,$sort,$order,$page,$limit,$price_range,$format);
        }

        return $this->respondWithArray($arr);
    }

}