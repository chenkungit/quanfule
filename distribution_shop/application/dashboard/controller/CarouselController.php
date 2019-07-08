<?php

namespace app\dashboard\controller;

use app\common\controller\WebController;
use app\common\Enums\UploadEnums;
use app\common\Factory\UploadFactory;
use app\dashboard\model\Carousel;
use app\dashboard\model\Log;
use lang;
use think\Request;

class CarouselController extends WebController
{

    protected $Carousel;

    public function __construct(Request $request, Carousel $carousel)
    {
        parent::__construct($request);
        $this->Carousel = $carousel;
    }


    /**
     * @param   int $type 1:商城|2:社区
     * @param   int $position 0:轮播图|1:四个banner|2:头条|3:|4:今日大牌
     * @return  array
     */
    public function lists()
    {
        $page = isset($this->data['page']) ? $this->data['page'] : 1;
        $limit = isset($this->data['limit']) ? $this->data['limit'] : 10;
        $carousel_type = isset($this->data['carousel_type']) ? $this->data['carousel_type'] : 1;
        $position = isset($this->data['position']) ? $this->data['position'] : 0;

        $where = [
            ['carousel_type', '=', $carousel_type],
            ['carousel_position', '=', $position],
        ];

        $arr['item'] = $this->Carousel->field('*,' . concat_img('img_url', '', self::qinniu_url))
            ->where($where)->page("$page,$limit")->order('sort asc')->select();
        $count = $this->Carousel->where($where)->count();
        $arr['pagecount'] = ceil($count / $limit);
        return $this->respondWithArray($arr);
    }

    public function info()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\CarouselValidate.info');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        $arr = $this->Carousel->where('carousel_id', $this->data['id'])->find();

        return $this->respondWithArray($arr);
    }

    public function add()
    {

        $validate = $this->validate($this->data, 'app\dashboard\validate\CarouselValidate.add');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }
        if ($this->data['carousel_position'] != 2) {
            if (!isset($_FILES['img'])) {
                return json_error(NULL, '请上传图片', self::INVALID_PARAMETER);
            }
            $upload = UploadFactory::getService(UploadEnums::LOCAL);
            $this->data['img_url'] = $upload->singleUpload($_FILES['img'], $this->position_name($this->data['carousel_position']));
        }


        try {
            $this->Carousel->allowField(true)->save($this->data);
            Log::record($this->Carousel->getLastInsID(), $this->ip);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        return $this->respondWithArray(NULL, lang::insert);
    }

    public function edit(Carousel $carousel)
    {

        $validate = $this->validate($this->data, 'app\dashboard\validate\CarouselValidate.edit');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        if (isset($_FILES['img'])) {
            $upload = UploadFactory::getService(UploadEnums::LOCAL);
            $this->data['img_url'] = $upload->singleUpload($_FILES['img'], $this->position_name($this->data['carousel_position']));
        }

        try {
            $carousel->allowField(true)->save($this->data, ['carousel_id' => $this->data['carousel_id']]);
            Log::record($this->data['carousel_id'], $this->ip);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        return $this->respondWithArray(NULL, lang::update);
    }


    public function delete()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\CarouselValidate.delete');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        try {
            $this->Carousel->where('carousel_id', $this->data['id'])->delete();
            Log::record($this->data['id'], $this->ip);
            /*-选择是否删除七牛图片-*/
//            $upload->delete($img_url);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return $this->respondWithArray(NULL, lang::delete);
    }

    private function position_name($position)
    {
        switch ($position) {
            case 0:
                return 'carousel';
            case 1:
                return 'four_type';
//            case 2:return 'toutiao';
            case 3:
                return 'banner';
            case 4:
                return 'jrdp';
            default:
                return 'carousel';
        }
    }

}