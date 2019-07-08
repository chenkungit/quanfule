<?php

namespace app\api\controller\v1;

use app\common\controller\ApiController;
use app\common\Enums\RedisKeyEnums;
use think\Request;
use app\common\model\Category;

class CategoryController extends ApiController
{
    protected $category;

    public function __construct(Request $request, Category $category)
    {
        parent::__construct($request);
        $this->category = $category;
        $this->checkBasicAuth();
    }

    public function parent_list($parent_id = 0, $cat_id = 0)
    {
        $string = sprintf(RedisKeyEnums::CACHE_CATEGORY, $parent_id, $cat_id);
        if (!($parent_categories_tree = CacheGet($string))) {
            $parent_categories_tree = $this->category->parent_categories_tree($parent_id, $cat_id);
            return $this->respondWithArray(remember($string, $parent_categories_tree));
        }
        return $this->respondWithArray($parent_categories_tree);
    }

    public function product_list($cat_id, $brand = 0, $beon = 0, $filter = 0, $sort = 0, $order = 0, $page = 1, $limit = 10, $price_range = null, $format = false)
    {

        return $this->respondWithArray($this->category->product_list($cat_id, $brand, $beon, $filter, $sort, $order, $page, $limit, $price_range, $format));

    }


    public function filter($cat_id)
    {
        $this->_validate('cat_id');
        $cat_id = (int)$this->data['cat_id'];
        return $this->respondWithArray($this->category->filter($cat_id));
    }


}