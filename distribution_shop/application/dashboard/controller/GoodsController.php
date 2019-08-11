<?php

namespace app\dashboard\controller;

use app\common\controller\WebController;
use app\common\Enums\UploadEnums;
use app\common\Factory\UploadFactory;
use app\common\model\ErpModel;
use app\dashboard\model\Gallery;
use think\Db;
use think\Request;
use app\dashboard\model\Sup;
use app\dashboard\model\Message_system;
use app\common\model\Goods;
use com\Tree;

class GoodsController extends WebController
{
    protected $sup;
    protected $gallery;
    protected $goods;

    public function __construct(Request $request, Sup $sup, Gallery $gallery, Goods $goods)
    {
        parent::__construct($request);
        $this->sup = $sup;
        $this->gallery = $gallery;
        $this->goods = $goods;
    }

    public function lists()
    {
        $type = isset($this->data['type']) ? $this->data['type'] : 0;
        switch ($type) {
            case 1:
                $this->_validate('goods_id');
                $res = $this->gallery->get_gallery($this->data['goods_id']);
                break;
            default:
                $res = $this->sup->get_sup_list();
        }
        return $this->respondWithArray($res);
    }

    public function sup_delete()
    {
        $is_delete = Db::name('goods_sup')->where('id', $this->data['id'])->value('is_delete');
        if ($is_delete == 0) {
            try {
                Db::name('goods_sup')->where('id', $this->data['id'])->update(['is_delete' => 1]);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
        } elseif ($is_delete == 1) {
            $update = [
                'is_delete' => 0,
                'is_sale' => 1,
            ];
            try {
                Db::name('goods_sup')->where('id', $this->data['id'])->update($update);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
        }
        return $this->respondWithArray(null, \lang::set);
    }


    public function info()
    {
        $this->_validate('goods_id');
        return $this->respondWithArray($this->sup->sup_info($this->data['goods_id']));
    }

    public function update_sale()
    {
        $validate = $this->validate($this->data, 'GoodsValidate.update_sale');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }
        $req = array();
        foreach ($this->data['gsup_id_arr'] as $key => $val) {
            $arr = array(
                'id' => $val,
                'is_sale' => $this->data['is_sale'],
            );
            $req[$key] = $arr;
        }
        try {
            $this->sup->allowField(true)->saveAll($req);
            $this->Log->record_new(__CLASS__, __FUNCTION__, $this->data['gsup_id_arr']);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        return $this->respondWithArray(null, \lang::update);
    }

    public function order()
    {
        /*商品排序*/
        $goods_id = empty($this->data['id']) ? 0 : $this->data['id'];
        if ($this->data['ord']) {
            $ord = intval($this->data['ord']);
            try {
                Db::name('goods_sup')->where('id', $goods_id)->update(['ord' => $ord]);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
        }
        return $this->respondWithArray(null, \lang::set);
    }

    public function edit()
    {
        $validate = $this->validate($this->data, 'GoodsValidate.edit');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        Db::startTrans();
        try {
            $goods_id = empty($this->data['id']) ? 0 : $this->data['id'];
            $url_img = Db::name('goods_sup')->where('id', $goods_id)->value('img');
            /*商品详情*/
            $sup = [
                'name' => empty($this->data['name']) ? '未命名商品' : addslashes(trim($this->data['name'])),
//                'tbsm' => empty($this->data['tbsm']) ? '' : trim($this->data['tbsm']),
//                'code' => empty($this->data['code']) ? '' : trim($this->data['code']),
                'ord' => intval($this->data['ord']),
                'category' => empty($this->data['category'][0]) ? 0 : intval($this->data['category'][0]),
                'vs_id' => empty($this->data['vs_id']) ? 0 : intval($this->data['vs_id']),
//                'brand' => empty($this->data['brand']) ? 0 : intval($this->data['brand']),
//                'supplier' => empty($this->data['supplier']) ? 0 : intval($this->data['supplier']),
                'price' => empty($this->data['price']) ? 0 : trim(floatval($this->data['price'])),
                'weight' => empty($this->data['weight']) ? 0 : floatval($this->data['weight']),
                'type' => empty($this->data['cat_id']) ? 0 : intval($this->data['cat_id']),
                'img' => empty($this->data['img']) ? '' : $this->data['img'],
                'thumbnail' => empty($this->data['img']) ? '' : $this->data['img'],
                'is_sale' => intval($this->data['is_sale']),
//                'is_fei5zhe' => intval($this->data['is_fei5zhe']),
//                'start_num' => empty($this->data['start_num']) ? '' : intval($this->data['start_num']),
//                'Nbei' => empty($this->data['N_bei']) ? '' : intval($this->data['N_bei']),
//                'youhui_type' => empty($this->data['youhui_type']) ? '' : trim($this->data['youhui_type']),
                'tishi_desc' => empty($this->data['tishi_desc']) ? '' : trim($this->data['tishi_desc']),
                'shipping_desc' => empty($this->data['shipping_desc']) ? '' : trim($this->data['shipping_desc']),
                'descpt' => $this->data['descpt'],
//                'asked_question' => $this->data['asked_question'],
//                'is_limit' => $this->data['is_limit']
            ];

            if (!empty($this->data['img']) && $this->data['img'] != $url_img) {
                $insert = [
                    'goods_id' => $goods_id,
                    'img_url' => $this->data['img'],
                    'thumb_url' => $this->data['img'],
                    'img_original' => $this->data['img'],
                    'sort' => 1,
                ];
                try {
                    Db::name('goods_gallery')->insert($insert);
                } catch (\Exception $e) {
                    abort(500, $e->getMessage());
                }
            }

            try {
                Db::name('goods_sup')->where('id', $goods_id)->update($sup);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }

//            //更新入驻商信息
//            $shp = [
//                'goods_id' => $goods_id,
//                'suppliers_id' => $this->data['supplier'],
//                'shops_id' => 2,
//                'new' => 0,
//                'hot' => 0,
//                'fine' => 0,
//                'is_sale' => 0,
//                'is_freeshipping' => 0,
//                'status' => 0,
//                'base_sale' => rand(100, 500),
//            ];
//
//            try {
//                Db::name('goods_shp')->where('goods_id', $goods_id)->update($shp);
//            } catch (\Exception $e) {
//                abort(500, $e->getMessage());
//            }

            //如果有促销商品信息
//            if (!empty($this->data['pro_price'])) {
//                if ($this->data['beg_time'] == '' || $this->data['end_time'] == '') {
//                    return json_error('', '请添加活动开始和结束时间', self::INVALID_PARAMETER);
//                }
//                $beg_time = strtotime($this->data['beg_time']);
//                $end_time = strtotime($this->data['end_time']);
//                $promote = [
//                    'gsup_id' => $goods_id,
//                    'goods_name' => $this->data['name'],
//                    'beg_time' => $beg_time,
//                    'end_time' => $end_time,
//                    'price' => $this->data['pro_price'],
//                ];
//
//                try {
//                    Db::name('promote')->where('gsup_id', $goods_id)->delete();
//                    Db::name('promote')->where('gsup_id', $goods_id)->insert($promote);
//                } catch (\Exception $e) {
//                    abort(500, $e->getMessage());
//                }
//            }
            //是否限购商品
//            if ($this->data['is_limit']) {
//                $limit_number = intval($this->data['limit_number']);
//                if ($limit_number > 0) {
//                    $limit_starttime = strtotime($this->data['limit_beg_time']);
//                    $limit_endtime = strtotime($this->data['limit_end_time']);
//                    $insert = [
//                        'goods_id' => $goods_id,
//                        'number' => $limit_number,
//                        'start_time' => $limit_starttime,
//                        'end_time' => $limit_endtime
//                    ];
//                    try {
//                        Db::name('goods_limit')->where('goods_id', $goods_id)->delete();
//                        Db::name('goods_limit')->insert($insert);
//                    } catch (\Exception $e) {
//                        abort(500, $e->getMessage());
//                    }
//                } else {
//                    try {
//                        Db::name('goods_sup')->where('id', $goods_id)->update(['is_limit' => 0]);
//                    } catch (\Exception $e) {
//                        abort(500, $e->getMessage());
//                    }
//                }
//            }

            /*相册新增,编辑,删除*/
            if (isset($this->data['flag'])) {
                if ($this->data['flag'] == 'edit') {
                    $gallery = [
                        'is_show' => $this->data['is_show'],
                        'sort' => $this->data['sort']
                    ];
                    try {
                        $this->gallery->where('img_id', $this->data['img_id'])->update($gallery);
                    } catch (\Exception $e) {
                        abort(500, $e->getMessage());
                    }
                } elseif ($this->data['flag'] == 'add') {
                    $upload = UploadFactory::getService(UploadEnums::LOCAL);
                    $img_arr = $upload->multiArrange($_FILES['album']);
                    foreach ($img_arr as $file) {
                        $this->data['album'] = $upload->singleUpload($file, 'album');
                        $this->data['album_thumbnail'] = $this->data['album'];
                        $gallery = [
                            'goods_id' => $goods_id,
                            'img_url' => $this->data['album'],
                            'thumb_url' => $this->data['album_thumbnail'],
                            'img_original' => $this->data['album_thumbnail'],
                            'sort' => $this->data['sort'],
                        ];
                        /*插入相册图片*/
                        try {
                            Db::name('goods_gallery')->insert($gallery, 'img_id');
                        } catch (\Exception $e) {
                            abort(500, $e->getMessage());
                        }
                    }
                } elseif ($this->data['flag'] == 'delete') {
                    $img_id_del = $this->data['img_id'];
                    try {
                        $this->gallery->where('img_id', $this->data['img_id'])->delete();
                    } catch (\Exception $e) {
                        abort(500, $e->getMessage());
                    }
                }
            }

            //处理商品属性,属性图片
            $original_cat_id = $this->data['original_cat_id'];
            $cat_id = $this->data['cat_id'];
            //如果更改商品属性分类,则删除原来所有的属性 和 货品
            if ($original_cat_id != $cat_id) {
                $this->change_goods_type($goods_id, $cat_id);
            }
            $goods_attr_list = [];
            $goods_attr_arr = Db::name('goods_attr')->field('goods_attr_id,attr_id,attr_value,img_id')->where('goods_id', $goods_id)->select();
            //编辑前后相册图片是否有删除
            foreach ($goods_attr_arr as $key => $item) {
                $goods_attr_list[$item['attr_value']][$item['attr_id']] = $item['goods_attr_id'] . '-delete';
            }
            $attr_id_list = [];
            $attr_value_list = [];
            $img_id_list = [];
            $img_url_list = [];
            $goods_attr_id_list = [];
            $attr = json_decode($this->data['attr'], true);
            foreach ($attr as $value) {
                if (!empty($value['attr_value'])) {
                    $attr_id_list[] = $value['attr_id'];
                    $attr_value_list[] = $value['attr_value'];
                    $goods_attr_id_list[] = $value['goods_attr_id'];
                    $img_id_list[] = $value['img_id'];
                    if (isset($img_id_del) && $img_id_del == $value['img_id']) {
                        $img_url_list[] = '';
                    } else {
                        $img_url_list[] = $value['img_url'];
                    }
                }
            }

            foreach ($attr_value_list as $key => $attr_value) {
                $attr_id = $attr_id_list[$key];
                $img_id = $img_id_list[$key];
                $img_url = $img_url_list[$key];
                $goods_attr_id = $goods_attr_id_list[$key];

                if (!empty($attr_value)) {
                    if (isset($goods_attr_list[$attr_value][$attr_id])) {
                        // 如果原来有，则更新
                        $goods_attr_list[$attr_value][$attr_id] = $goods_attr_id . '-update';
                        array_merge($goods_attr_list, [$goods_attr_list[$attr_value][$attr_id] => $goods_attr_id . '-update']);
                        if (!empty($img_url)) {
                            $gallery = [
                                'goods_id' => $goods_id,
                                'img_url' => $img_url,
                                'thumb_url' => $img_url,
                                'img_original' => $img_url,
                                'is_show' => 0,
                            ];
                            try {
                                Db::name('goods_gallery')->where('img_id', $img_id)->delete();
                                $img_id_new = Db::name('goods_gallery')->insertGetId($gallery);
                            } catch (\Exception $e) {
                                abort(500, $e->getMessage());
                            }

                            $goods_attr = [
                                'goods_id' => $goods_id,
                                'attr_id' => $attr_id,
                                'attr_value' => trim($attr_value),
                                'img_id' => $img_id_new
                            ];
                            try {
                                Db::name('goods_attr')->where('goods_attr_id', $goods_attr_id)->update($goods_attr);
                            } catch (\Exception $e) {
                                abort(500, $e->getMessage());
                            }
                        } else {
                            $goods_attr = [
                                'goods_id' => $goods_id,
                                'attr_id' => $attr_id,
                                'attr_value' => trim($attr_value),
                                'img_id' => 0
                            ];
                            try {
                                Db::name('goods_attr')->where('goods_attr_id', $goods_attr_id)->update($goods_attr);
                            } catch (\Exception $e) {
                                abort(500, $e->getMessage());
                            }
                        }
                    } else {
                        // 如果原来没有，则新增
                        $goods_attr_list[$attr_value][$attr_id] = $goods_attr_id . '-insert';
                        array_merge($goods_attr_list, [$goods_attr_list[$attr_value][$attr_id] => $goods_attr_id . '-insert']);
                        if (!empty($img_url)) {
                            $gallery = [
                                'goods_id' => $goods_id,
                                'img_url' => $img_url,
                                'thumb_url' => $img_url,
                                'img_original' => $img_url,
                                'is_show' => 0,
                            ];
                            try {
                                $id = Db::name('goods_gallery')->insertGetId($gallery);
                            } catch (\Exception $e) {
                                abort(500, $e->getMessage());
                            }

                            $goods_attr = [
                                'goods_id' => $goods_id,
                                'attr_id' => $attr_id,
                                'attr_value' => trim($attr_value),
                                'img_id' => $id
                            ];
                            try {
                                Db::name('goods_attr')->insert($goods_attr);
                            } catch (\Exception $e) {
                                abort(500, $e->getMessage());
                            }
                        } else {
                            $goods_attr = [
                                'goods_id' => $goods_id,
                                'attr_id' => $attr_id,
                                'attr_value' => trim($attr_value),
                                'img_id' => 0
                            ];
                            try {
                                Db::name('goods_attr')->insert($goods_attr);
                            } catch (\Exception $e) {
                                abort(500, $e->getMessage());
                            }
                        }
                    }
                }
            }

            /*删除多余属性,属性图片 和 对应的货品 并 统计商品总库存*/
            foreach ($goods_attr_list as $key => $value) {
                foreach ($value as $k => $val) {
                    if (substr($val, -6, 6) == 'delete') {
                        $id = substr($val, 0, -7);
                        $img_id = Db::name('goods_attr')->where('goods_attr_id', $id)->value('img_id');
                        if ($img_id) {
                            Db::name('goods_gallery')->where('img_id', $img_id)->delete();
                        }
                        try {
                            Db::name('goods_attr')->where('goods_attr_id', $id)->delete();
                            $this->set_product($goods_id, $id);
                        } catch (\Exception $e) {
                            abort(500, $e->getMessage());
                        }
                    }
                }
            }

            /*更新商品编辑时间*/
            try {
                Db::name('goods_sup')->where('id', $goods_id)->update(['edit_time' => time()]);
                $this->Log->record_new(__CLASS__, __FUNCTION__, $goods_id);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
            /*同步搜索*/
            $this->search_flash($goods_id);
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            abort(500, $exception->getMessage());
        }

        return $this->respondWithArray(null, \lang::update);
    }

//    public function push(GoodsSpecPush $goodsSpecPush,ErpModel $erpModel){
//        $this->_validate('gsup_id');
//
//        $data = $erpModel->good_spec_list($this->data['gsup_id']);
//
//        $res  = $goodsSpecPush->setGoodsSpecList($data['goods_list'])->send(); //推送自有平台商品
//
//        if($res['code'] == 0){
//            Db::name('goods_sup')->where('id','in',$data['ids'])->update(['push_time'=>date('Y-m-d H:i:s')]);
//            return $this->respondWithArray(null,sprintf("新增了%s个商品,修改了%s个商品",$res['new_count'],$res['chg_count']));
//        }else{
//            return $this->respondWithArray(null,$res['message']);
//        }
//
//    }

//    public function sendsms(){
//        $this->_validate('gsup_id');
//        $res = Db::name('goods_remind')->field('*')->where([['send','=', 0],['gsup_id','=', $this->data['gsup_id']]])->select();
//        $success=0;
//        $error=0;
//        if(count($res)){
//            $this->Message_system->add_message_system_goods(8,$res,$res[0]['label']);
//        }
//        foreach ($res as $item) {
//            try {
//                \Sms::send_active_sms($item['mobile'], sprintf(\Sms::goods_remind, $item['label']));
//                Db::name('goods_remind')->where([['mobile','=', $item['mobile']],['send','=', 0],['gsup_id','=', $this->data['gsup_id']]])->update(['send' => 1]);
//                $success++;
//            } catch (\Exception $e) {
//                (new Appchat())->send($e->getMessage());
//                Db::name('goods_remind')->where(['mobile','=', $item['mobile']],['send','=', 0],['gsup_id','=', $this->data['gsup_id']])->update(['send' => 2]);
//                $error++;
//            }
//        }
//        return $this->respondWithArray(null,'预定未发送共'.count($res).'个，发送成功'.$success.'个，失败'.$error.'个');
//    }

    public function sup_add()
    {
        /*商品分类*/
        $arr = Db::name('category')->order('sort_order asc')->select();
        $tree = new Tree();
        $cat_list = $tree->list_to_tree($arr, 'cat_id', 'parent_id');
        /*商品品牌*/
        $brand = Db::name('ecs_brand')->field('brand_name')->where('is_show', '=', 1)->order('szm', 'ASC')->select();
        /*商品类型列表*/
        $goods_type = Db::name('goods_type')->field('cat_id', 'cat_name', '')->where('enabled', '=', 1)->select();

        return $this->respondWithArray([$cat_list, $brand, $goods_type]);
    }

    public function suppliers()
    {
        /*商品对应的供应商*/
        $supplier = Db::name('suppliers')->field('name as label,id as value')->where('status', '=', 0)->select();
        $sup_lists = [];
        foreach ($supplier as $k => $v) {
            $sup_lists[] = $v;
        }
        return $this->respondWithArray($sup_lists);
    }

    public function goods_attr()
    {
        /*关联商品类属性*/
        $cat_id = isset($this->data['cat_id']) ? $this->data['cat_id'] : '';
        if (!empty($cat_id)) {
            $attr = Db::name('attribute')->alias('a')->leftJoin('ecs_goods_type t ', 'a.cat_id=t.cat_id')->field('a.*,t.cat_id,t.cat_name')->where('a.cat_id', $this->data['cat_id'])->select();
        }

        return $this->respondWithArray($attr);
    }

    public function sup_insert()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\GoodsValidate.sup_insert');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }
        /*商品图片,商品缩略图*/
        if (!empty($_FILES['img'])) {
            $upload = UploadFactory::getService(UploadEnums::LOCAL);
            $this->data['img'] = $upload->singleUpload($_FILES['img'], 'img');
            $this->data['thumbnail'] = $this->data['img'];
        } else {
            $this->data['img'] = '';
            $this->data['thumbnail'] = '';
        }
        /*商品基本信息*/
        $sup = [
            'name' => empty($this->data['name']) ? '未命名商品' : addslashes(trim($this->data['name'])),
//            'tbsm' => empty($this->data['tbsm']) ? '' : trim($this->data['tbsm']),
//            'code' => empty($this->data['code']) ? '' : trim($this->data['code']),
            'ord' => intval($this->data['ord']),
            'category' => empty($this->data['category']) ? 0 : intval($this->data['category']),
            'vs_id' => empty($this->data['vs_id']) ? 0 : intval($this->data['vs_id']),
//            'brand' => empty($this->data['brand']) ? 0 : intval($this->data['brand']),
//            'supplier' => empty($this->data['supplier']) ? 0 : intval($this->data['supplier']),
            'price' => empty($this->data['price']) ? 0 : floatval($this->data['price']),
            'descpt' => $this->data['descpt'],
            'weight' => empty($this->data['weight']) ? 0 : floatval($this->data['weight']),
            'type' => empty($this->data['cat_id']) ? 0 : intval($this->data['cat_id']),
            'img' => empty($this->data['img']) ? '' : $this->data['img'],
            'thumbnail' => empty($this->data['thumbnail']) ? '' : $this->data['thumbnail'],
            'is_sale' => intval($this->data['is_sale']),
//            'is_fei5zhe' => intval($this->data['is_fei5zhe']),
            'add_time' => time(),
            'edit_time' => time(),
//            'start_num' => empty($this->data['start_num']) ? '' : intval($this->data['start_num']),
//            'Nbei' => empty($this->data['Nbei']) ? '' : intval($this->data['Nbei']),
//            'youhui_type' => empty($this->data['youhui_type']) ? '' : trim($this->data['youhui_type']),
            'tishi_desc' => empty($this->data['tishi_desc']) ? '' : trim($this->data['tishi_desc']),
            'shipping_desc' => empty($this->data['shipping_desc']) ? '' : trim($this->data['shipping_desc']),
//            'is_limit' => $this->data['is_limit']
        ];

        /*插入数据*/
        try {
            $goods_id = Db::name('goods_sup')->insertGetId($sup, 'id');
            if (!empty($this->data['img'])) {
                $insert = [
                    'goods_id' => $goods_id,
                    'img_url' => $this->data['img'],
                    'thumb_url' => $this->data['thumbnail'],
                    'img_original' => $this->data['img'],
                ];
                Db::name('goods_gallery')->insert($insert);
            }
            $this->Log->record_new(__CLASS__, __FUNCTION__, $goods_id);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        /*插入授权园艺家入驻商*/
        $shp = [
            'goods_id' => $goods_id,
            'suppliers_id' => 1,
            'shops_id' => 2,
            'new' => 0,
            'hot' => 0,
            'fine' => 0,
            'is_sale' => 0,
            'is_freeshipping' => 0,
            'status' => 0,
            'add_time' => time(),
            'base_sale' => rand(100, 500),
        ];
        try {
            Db::name('goods_shp')->insert($shp, 'id');
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }


        /*如果有促销价*/
//        if (!empty($this->data['pro_price'])) {
//            $beg_time = strtotime($this->data['beg_time']);
//            $end_time = strtotime($this->data['end_time']);
//            $promote = [
//                'gsup_id' => $goods_id,
//                'goods_name' => $this->data['name'],
//                'beg_time' => $beg_time,
//                'end_time' => $end_time,
//                'price' => $this->data['pro_price'],
//            ];
//
//
//            /*插入促销信息*/
//            try {
//                Db::name('promote')->insert($promote, 'id');
//            } catch (\Exception $e) {
//                abort(500, $e->getMessage());
//            }
//        }

        /*是否限购商品*/
//        if ($this->data['is_limit']) {
//            $limit_number = intval($this->data['limit_number']);
//            if ($limit_number > 0) {
//                $limit_starttime = strtotime($this->data['limit_beg_time']);
//                $limit_endtime = strtotime($this->data['limit_end_time']);
//                $insert = [
//                    'goods_id' => $goods_id,
//                    'number' => $limit_number,
//                    'start_time' => $limit_starttime,
//                    'end_time' => $limit_endtime
//                ];
//                try {
//                    Db::name('goods_limit')->insert($insert);
//                } catch (\Exception $e) {
//                    abort(500, $e->getMessage());
//                }
//            } else {
//                try {
//                    Db::name('goods_sup')->where('id', $goods_id)->update(['is_limit' => 0]);
//                } catch (\Exception $e) {
//                    abort(500, $e->getMessage());
//                }
//            }
//        }

        /*商品相册图*/
        if (isset($_FILES['album'])) {
            $upload = UploadFactory::getService(UploadEnums::LOCAL);
            $img_arr = $upload->multiArrange($_FILES['album']);
            foreach ($img_arr as $file) {
                $this->data['album'] = $upload->singleUpload($file, 'album');
                $this->data['album_thumbnail'] = $this->data['album'];
                $gallery = [
                    'goods_id' => $goods_id,
                    'img_url' => $this->data['album'],
                    'thumb_url' => $this->data['album_thumbnail'],
                    'img_original' => $this->data['album_thumbnail'],
                ];
                /*插入相册图片*/
                try {
                    Db::name('goods_gallery')->insert($gallery, 'img_id');
                } catch (\Exception $e) {
                    abort(500, $e->getMessage());
                }
            }
        }

        /*插入商品属性,属性图片*/
        if (!empty($this->data['attr'])) {
            $attr_arr = json_decode($this->data['attr'], true);
            foreach ($attr_arr as $value) {
                if ($value['attr_type'] == 0) {
                    $attr[] = $value;
                } else {
                    $attr1[] = $value;
                }
            }

            $goods_attr = [];
            if (!empty($attr)) {
                foreach ($attr as $item) {
                    if (!empty($item['attr_value'])) {
                        $goods_attr[] = [
                            'goods_id' => $goods_id,
                            'attr_id' => $item['attr_id'],
                            'attr_value' => trim($item['attr_value'], "\r"),
                            'img_id' => 0
                        ];
                    }
                }
            }

            try {
                Db::name('goods_attr')->insertAll($goods_attr);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }

            if (!empty($attr1)) {
                foreach ($attr1 as $key => $val) {
                    if ($val['attr_value'] != '') {
                        if (isset($val['attr_img'])) {
                            $img_url = $val['attr_img'];
                            $attr_img = [
                                'goods_id' => $goods_id,
                                'img_url' => $img_url,
                                'thumb_url' => $img_url,
                                'img_original' => $img_url,
                                'is_show' => 0
                            ];

                            try {
                                $img_id = Db::name('goods_gallery')->insertGetId($attr_img, 'img_id');
                            } catch (\Exception $e) {
                                abort(500, $e->getMessage());
                            }
                            $goods_attr1 = [
                                'goods_id' => $goods_id,
                                'attr_id' => $val['attr_id'],
                                'attr_value' => trim($val['attr_value'], "\r"),
                                'img_id' => $img_id
                            ];
                        } else {
                            $goods_attr1 = [
                                'goods_id' => $goods_id,
                                'attr_id' => $val['attr_id'],
                                'attr_value' => trim($val['attr_value'], "\r"),
                                'img_id' => 0
                            ];
                        }

                        try {
                            Db::name('goods_attr')->insert($goods_attr1);
                        } catch (\Exception $e) {
                            abort(500, $e->getMessage());
                        }
                    }
                }
            }
        }
        /*同步搜索*/
        $this->search_flash($goods_id);

        return $this->respondWithArray(null, \lang::insert);
    }

    public function products()
    {
        $this->_validate('goods_id');
        $attribute = Db::name('goods_attr')->alias('g')->leftJoin('ecs_attribute a', 'a.attr_id=g.attr_id')->field('g.goods_attr_id, g.attr_value, g.attr_id, a.attr_name,a.attr_type')->where('g.goods_id', $this->data['goods_id'])->order('g.attr_id ASC')->select();

        if (!empty($attribute)) {
            foreach ($attribute as $value) {
                //转换成数组
                $_attr[$value['attr_id']]['attr_type'] = $value['attr_type'];
                $_attr[$value['attr_id']]['attr_id'] = $value['attr_id'];
                $_attr[$value['attr_id']]['attr_name'] = $value['attr_name'];
                $_attr[$value['attr_id']]['attr_values'][] = [
                    'id' => (string)$value['goods_attr_id'],
                    'value' => $value['attr_value']
                ];
            }
        } else {
            return $this->respondWithArray(null);
        }
        return $this->respondWithArray($_attr);
    }

    public function product_list()
    {
        /*获取货品*/
        $this->_validate('goods_id');
        $product_list = Db::name('products')->field('product_id, goods_id, goods_attr, product_sn, stock,price,weight')->where('goods_id', $this->data['goods_id'])->select();

        $goods = Db::name('goods_sup')->field('id as goods_id, code as product_sn, name, type, price, stock')->where('id', $this->data['goods_id'])->select();

        $res = Db::name('goods_attr')->field('goods_attr_id,attr_value')->where('goods_id', $this->data['goods_id'])->select();

        $goods_attr = [];
        foreach ($res as $item) {
            $goods_attr[$item['goods_attr_id']] = $item['attr_value'];
        }

        foreach ($product_list as $key => $val) {
            $goods_attr_array = explode('|', $val['goods_attr']);
            //var_dump($goods_attr_array);die;
            if (is_array($goods_attr_array)) {
                $_temp = [];
                foreach ($goods_attr_array as $attr_value) {
                    $attr_name = Db::name('goods_attr')->alias('ga')->leftJoin('attribute a', 'a.attr_id=ga.attr_id')->where('ga.goods_attr_id', '=', $attr_value)->value('a.attr_name');
                    $_temp[] = ["attr" => $attr_name, 'value' => $goods_attr[$attr_value]];
                }

                $product_list[$key]['goods_attr'] = $_temp;
            }
        }

        return $this->respondWithArray(['product_list' => $product_list, 'goods' => $goods]);
    }

    public function products_delete()
    {
        $this->_validate('product_id');
        $product = Db::name('products')->where('product_id', $this->data['product_id'])->find();
        if (!empty($product)) {
            try {
                Db::name('products')->where('product_id', $this->data['product_id'])->delete();
                $this->Log->record_new(__CLASS__, __FUNCTION__, $this->data['product_id']);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
            /*更新商品库存*/
            $stock = $this->product_number_count($product['goods_id']);
            try {
                Db::name('goods_sup')->where('id', $product['goods_id'])->update(['stock' => $stock, 'edit_time' => time()]);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
            /*插入库存操作记录表*/
            $this->insert_stock_action($product['goods_id'], $product['product_sn'], $this->data['product_id'], $product['stock'], 0, 0 - $product['stock'], 1, '删除货品');
        }

        return $this->respondWithArray(null, \lang::delete);
    }

    public function products_add()
    {
        $product = empty($this->data['products']) ? '' : $this->data['products'];
        $goods_id = empty($this->data['goods_id']) ? 0 : intval($this->data['goods_id']);
        /*先查询该商品有没有货品，如果没有，就记录删除原来的商品库存*/
        if ($goods_id != 0) {
            $res = Db::name('products')->where('goods_id', '=', $goods_id)->count('*');
            if ($res == 0) {
                $row = Db::name('goods_sup')->where('id', $goods_id)->find();
                $this->insert_stock_action($goods_id, $row['code'], 0, $row['stock'], 0, 0 - $row['stock'], 1, $beizu = '新增货品后删除商品库存');
            }
        }

        if (!empty($product)) {
            foreach ($product as $item) {
                $product_sn = $item['product_sn'];
                $attr = $item['attr_id'];
                $stock = $item['stock'];
                $price = $item['price'];
                $weight = $item['weight'];

                /*新增货品*/
                $goods_attr = [];
                $goods_attr_sou = [];
                if (!empty($attr)) {
                    foreach ($attr as $value) {
                        if (empty($value)) {
                            return json_error('', '选择属性不全', self::INVALID_PARAMETER);
                        }
                        $goods_attr[] = $value;
                        $goods_attr_sou[] = $value;
                    }
                }


                rsort($goods_attr_sou);

                $goods_attr = implode('|', $goods_attr);
                $goods_attr_sou = implode('|', $goods_attr_sou);
                /*货品规格是否重复*/
                if ($this->check_goods_attr_exist($goods_attr, $goods_id)) {
                    continue;
                }

                $products = [
                    'goods_id' => $goods_id,
                    'goods_attr' => $goods_attr,
                    'goods_attr_sou' => $goods_attr_sou,
                    'product_sn' => $product_sn,
                    'stock' => $stock,
                    'price' => $price,
                    'weight' => $weight,
                    'K3_name' => ''
                ];

                /*插入货品*/
                try {
                    $insert_id = Db::name('products')->insertGetId($products);
                } catch (\Exception $e) {
                    abort(500, $e->getMessage());
                }

                /*插入库存操作记录表*/
                $this->insert_stock_action($goods_id, $product_sn, $insert_id, 0, $stock, $stock, $type = 1, $beizu = '新增商品');

                /*同步商品库存表*/
                $stock = $this->product_number_count($goods_id);
                try {
                    Db::name('goods_sup')->where('id', $goods_id)->update(['stock' => $stock, 'edit_time' => time()]);
                    $this->Log->record_new(__CLASS__, __FUNCTION__, $goods_id);
                } catch (\Exception $e) {
                    abort(500, $e->getMessage());
                }
            }

        }

        return $this->respondWithArray(null, \lang::insert);
    }

    public function products_edit()
    {

        $id = empty($this->data['goods_id']) ? 0 : intval($this->data['goods_id']);
        $product_id = empty($this->data['product_id']) ? 0 : intval($this->data['product_id']);
        $number = empty($this->data['number']) ? 0 : intval($this->data['number']);
        $product_sn = empty($this->data['product_sn']) ? '' : $this->data['product_sn'];
        $price = empty($this->data['price']) ? 0 : sprintf("%.2f", $this->data['price']);
        $weight = empty($this->data['weight']) ? 0 : sprintf("%.3f", $this->data['weight']);

        $goods = Db::name('goods_sup')->where('id', $id)->find();
        $product = Db::name('products')->where('product_id', $product_id)->find();

        /*修改货品库存,货号,价格,重量*/
        if ($product) {
            $update = [
                'stock' => $number,
                'product_sn' => $product_sn,
                'price' => $price,
                'weight' => $weight,
                'K3_name' => ''
            ];

            try {
                Db::name('products')->where('product_id', $product_id)->update($update);
                $this->Log->record_new(__CLASS__, __FUNCTION__, $product_id);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }

            /*插入库存操作记录表*/
            $this->insert_stock_action($product['goods_id'], $product['product_sn'], $product_id, $product['stock'], $number, $number - $product['stock'], 1, '修改货品数量');
            /*修改商品库存*/
            if ($goods) {
                $stock = $this->product_number_count($product['goods_id']);
                try {
                    Db::name('goods_sup')->where('id', $product['goods_id'])->update(['stock' => $stock, 'edit_time' => time()]);
                    $this->Log->record_new(__CLASS__, __FUNCTION__, $id);
                } catch (\Exception $e) {
                    abort(500, $e->getMessage());
                }

                /*插入商品库存操作记录*/
                $this->insert_stock_action($goods['id'], $goods['code'], 0, $goods['stock'], $stock, $stock - $goods['stock'], 1, '修改商品库存');
            }
        } else {   /*无规格商品修改库存*/
            try {
                Db::name('goods_sup')->where('id', $id)->update(['stock' => $number, 'edit_time' => time()]);
                $this->Log->record_new(__CLASS__, __FUNCTION__, $id);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
            /*插入商品库存操作记录*/
            $this->insert_stock_action($goods['id'], $goods['code'], 0, $goods['stock'], $number, $number - $goods['stock'], 1, '修改商品库存');
        }


        return $this->respondWithArray(null, \lang::update);
    }

    public function insert_stock_action($goods_id, $goods_sn, $product_id = 0, $stock_from = 0, $stock_to = 0, $stock = 0, $type = 1, $beizu = '', $presale_date = '')
    {
        $stock_action = [
            'goods_id' => $goods_id,
            'product_id' => $product_id,
            'stock_from' => $stock_from,
            'stock_to' => $stock_to,
            'stock' => $stock,
            'add_time' => time(),
            'operator' => '',
            'goods_sn' => $goods_sn,
            'type' => $type,
            'presale_date' => $presale_date,
            'beizu' => $beizu
        ];
        //var_dump($stock_action);die;
        try {
            Db::name('stock_action')->insert($stock_action);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function check_goods_attr_exist($goods_attr, $goods_id, $product_id = 0)
    {
        if (strlen($goods_attr) == 0 || empty($goods_id)) {
            return true;
        }

        if (empty($product_id)) {
            $res = Db::name('products')->where('goods_attr', $goods_attr)->select();
        } else {
            $res = Db::name('products')->where([['goods_attr', $goods_attr], ['goods_id', $goods_id], ['product_id', '<>', $product_id]])->select();
        }

        if (!empty($res)) {
            return true;
        } else {
            return false;
        }
    }

    public function handle_goods_attr($goods_id, $id_list, $is_spec_list, $value_price_list)
    {
        $goods_attr_id = array();
        /* 循环处理每个属性 */
        foreach ($id_list AS $key => $id) {
            $is_spec = $is_spec_list[$key];
            if ($is_spec == 'false') {
                $value = $value_price_list[$key];
                $price = '';
            } else {
                $value_list = array();
                $price_list = array();
                if ($value_price_list[$key]) {
                    $vp_list = explode(chr(13), $value_price_list[$key]);
                    foreach ($vp_list AS $arr) {
                        $value_list[] = $arr[0];
                        $price_list[] = $arr[1];
                    }
                }
                $value = join(chr(13), $value_list);
                $price = join(chr(13), $price_list);
            }

            // 插入或更新记录
            $where = [
                ['goods_id', '=', $goods_id],
                ['attr_id', '=', $id],
                ['attr_value', '=', $value]
            ];

            $result_id = Db::name('goods_attr')->field('goods_attr_id')->where($where)->limit(0, 1)->find();
//var_dump($result_id);die;
            if (!empty($result_id)) {
                $goods_attr_id[$id] = $result_id;
                $where = [
                    ['goods_id', '=', $goods_id],
                    ['attr_id', '=', $id],
                    ['goods_attr_id', '=', $result_id]
                ];

                try {
                    Db::name('goods_attr')->where($where)->update(['attr_value' => $value]);
                    $this->Log->record_new(__CLASS__, __FUNCTION__, $this->data['goods_id']);
                } catch (\Exception $e) {
                    abort(500, $e->getMessage());
                }
            } else {
                $arr = [
                    'goods_id' => $goods_id,
                    'attr_id' => $id,
                    'attr_value' => $value,
                    'attr_price' => $price
                ];
                try {
                    $insert_id = Db::name('goods_attr')->insertGetId($arr);
                } catch (\Exception $e) {
                    abort(500, $e->getMessage());
                }
            }

            if ($goods_attr_id[$id] == '') {
                $goods_attr_id[$id] = $insert_id;
            }
        }

        return $goods_attr_id;
    }

    public function sort_goods_attr_id($goods_attr_id)
    {
        if (empty($goods_attr_id)) {
            return $goods_attr_id;
        }
        //重新排序
        $row = Db::name('attribute')->alias('a')->leftJoin('ecs_goods_attr ga', 'ga.attr_id = a.attr_id and a.attr_type = 1')->field('a.attr_type, v.attr_value, v.goods_attr_id')->where('ga.goods_attr_id', 'in', $goods_attr_id)->order('a.attr_id ASC')->select();
        $return_arr = array();
        foreach ($row as $value) {
            $return_arr['sort'][] = $value['goods_attr_id'];
            $return_arr['row'][$value['goods_attr_id']] = $value;
        }

        return $return_arr;
    }

    public function product_number_count($goods_id)
    {
        if (empty($goods_id)) {
            return -1;
        }
        $num = Db::name('products')->where('goods_id', $goods_id)->sum('stock');
        return $num;
    }

    public function sup_edit()
    {
        $goods_id = empty($this->data['id']) ? 0 : $this->data['id'];
        //商品信息
        $field = 'gs.id,gs.vs_id, gs.name, gs.tbsm, gs.code, gs.ord, gs.category, gs.brand, gs.supplier, gs.price, gs.weight, gs.img, gs.type as cat_id, gs.is_sale, gs.is_fei5zhe,gs.start_num, gs.Nbei, gs.youhui_type, gs.tishi_desc, gs.shipping_desc, gs.asked_question, gs.descpt, gs.is_limit, gl.number as limit_number, gl.start_time as limit_beg_time, gl.end_time as limit_end_time';
        $goods = Db::name('goods_sup')->alias('gs')->field($field)->leftJoin('ecs_goods_limit gl', 'gl.goods_id=gs.id')->where('gs.id', $goods_id)->find();
        //老后台is_limit全部为 1 ,实际去商品限购表查询是否为限购商品
        if ($goods['is_limit'] == 1 && $goods['limit_number'] > 0) {
            $goods['is_limit'] = 1;
            $goods['limit_beg_time'] = date("Y-m-d H:i:s", $goods['limit_beg_time']);
            $goods['limit_end_time'] = date("Y-m-d H:i:s", $goods['limit_end_time']);
        } else {
            $goods['is_limit'] = 0;
            $goods['limit_beg_time'] = '';
            $goods['limit_end_time'] = '';
        }
        //商品属性
        $attr = Db::name('goods_attr')->alias('g')->field('t.cat_name,g.goods_attr_id,a.attr_name,a.attr_type,g.attr_id,g.attr_value,gg.img_id,gg.img_url')->leftJoin('ecs_attribute a', 'a.attr_id=g.attr_id')->leftJoin('ecs_goods_type t', 't.cat_id=a.cat_id')->leftJoin('ecs_goods_gallery gg', 'gg.img_id=g.img_id')->where('g.goods_id', $goods_id)->select();
        //商品图片
        $gallery = Db::name('goods_gallery')->where('goods_id', $goods_id)->select();

        $img_list = [];
        foreach ($gallery as $key => $gallery_img) {
            $img_list[$key]['img_id'] = $gallery_img['img_id'];
            $img_list[$key]['img_url'] = $gallery_img['img_url'];
            $img_list[$key]['img_desc'] = $gallery_img['img_desc'];
            $img_list[$key]['is_show'] = $gallery_img['is_show'];
            $img_list[$key]['sort'] = $gallery_img['sort'];
        }

        //促销商品信息
        $promote = Db::name('promote')->where('gsup_id', $goods_id)->find();
        if ($promote) {
            $goods['beg_time'] = date("Y-m-d H:i:s", $promote['beg_time']);
            $goods['end_time'] = date("Y-m-d H:i:s", $promote['end_time']);
            $goods['promote_price'] = $promote['price'];
        } else {
            $goods['beg_time'] = '';
            $goods['end_time'] = '';
            $goods['promote_price'] = '';
        }

        return $this->respondWithArray(['goods_info' => $goods, 'goods_img' => $img_list, 'goods_attr' => $attr]);
    }

    public function set_product($goods_id, $goods_attr_id)
    {
        $res = Db::name('products')->field('product_id,goods_attr,stock')->where('goods_id', $goods_id)->select();
        if (empty($res)) {
            return true;
        }
        foreach ($res as $val) {
            $goods_attr_arr = explode("|", $val['goods_attr']);
            if (in_array($goods_attr_id, $goods_attr_arr)) {
                $row = 0;
                try {
                    $row = Db::name('products')->where('product_id', $val['product_id'])->delete();
                    $this->Log->record_new(__CLASS__, __FUNCTION__, $goods_id);
                } catch (\Exception $e) {
                    abort(500, $e->getMessage());
                }

                if ($row) {
                    $stock = $this->product_number_count($goods_id);
                    try {
                        Db::name('goods_sup')->where('id', $goods_id)->update(['stock' => $stock]);
                        $this->Log->record_new(__CLASS__, __FUNCTION__, $goods_id);
                    } catch (\Exception $e) {
                        abort(500, $e->getMessage());
                    }
                }
            }
        }
        return true;
    }

    public function change_goods_type($goods_id, $cat_id)
    {
        $goods_attr = Db::name('goods_attr')->where('goods_id', $goods_id)->select();
        $product = Db::name('products')->where('goods_id', $goods_id)->select();
        //删除商品原有的属性
        if (!empty($goods_attr)) {
            try {
                Db::name('goods_attr')->where('goods_id', $goods_id)->delete();
                $this->Log->record_new(__CLASS__, __FUNCTION__, $goods_id);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
        }
        //删除商品对应的货品
        if (!empty($product)) {
            try {
                Db::name('products')->where('goods_id', $goods_id)->delete();
                $this->Log->record_new(__CLASS__, __FUNCTION__, $goods_id);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
        }
        //更改商品类型
        try {
            Db::name('goods_sup')->where('id', $goods_id)->update(['type' => $cat_id]);
            $this->Log->record_new(__CLASS__, __FUNCTION__, $goods_id);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return true;
    }

    public function search_flash($goods_id)
    {
        /*先删除旧的*/
        try {
            Db::name('goods_search')->where('good_id', $goods_id)->delete();
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        /*重新插入*/
        $goods = Db::name('goods_sup')->field('id,name,shortname,K3_name,code,category,brand,supplier,keywords')->where('id', $goods_id)->find();
        $category = Db::name('category')->where('cat_id', $goods['category'])->find();
        $brand_name = Db::name('brand')->where('brand_id', $goods['brand'])->value('brand_name');
        $supplier_name = Db::name('suppliers')->where('id', $goods['supplier'])->value('name');
        $goods_attr = Db::name('goods_attr')->where('goods_id', $goods['id'])->column('attr_value');
        $presale_id = Db::name('goods_presale')->where('goods_id', $goods['id'])->value('id');
        $category_name = '';
        if ($category['parent_id'] > 0) {
            $category_name = Db::name('category')->where('cat_id', $category['parent_id'])->value('cat_name');
        }
        $cat_name = $category['cat_name'] . "," . $category_name;

        if ($presale_id) {
            $presale = 1;
        } else {
            $presale = 0;
        }

        $attrs = '';
        foreach ($goods_attr as $item) {
            $attrs .= $item . ",";
        }
        $search_str = $goods['name'] . "," . $goods['shortname'] . "," . $goods['K3_name'] . "," . $goods['code'] . "," . $goods['keywords'] . "," . $cat_name . "," . $brand_name . "," . $supplier_name . "," . $attrs;

        $insert = [
            'good_id' => $goods['id'],
            'search' => addslashes($search_str),
            'cou' => 0,
            'presale' => $presale,
        ];
        try {
            Db::name('goods_search')->insert($insert);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        return true;
    }

    public function cat_tree_list()
    {
        $arr = Db::name('category')->field('cat_name,cat_id,father_id,parent_id,is_show')->where('enable', '=', 0)->select();
        $tree = new Tree();
        $res = $tree->list_to_tree($arr, 'cat_id', 'father_id');
        return $this->respondWithArray($res);
    }

//    public function copy(){
//        $goods_id = $this->data['id'];
//        $field = 'gs.id, gs.name, gs.tbsm, gs.code, gs.ord, gs.category, gs.brand, gs.supplier, gs.price, gs.weight, gs.img, gs.type, gs.thumbnail, gs.is_sale, gs.is_fei5zhe,gs.start_num, gs.Nbei, gs.youhui_type, gs.tishi_desc, gs.shipping_desc, gs.descpt, gs.is_limit, gl.number as limit_number, gl.start_time as limit_beg_time, gl.end_time as limit_end_time,gs.regions,p.goods_name,p.beg_time,p.end_time,p.pro_price';
//        $goods_info = Db::name('goods_sup')->alias('gs')->field($field)->leftJoin('ecs_goods_limit gl','gl.goods_id=gs.id')->leftJoin('promote p','p.gsup_id=gs.id')->where('gs.id',$goods_id)->find();
//        $goods_info['album'] = Db::name('goods_gallery')->where('goods_id',$goods_id)->select()->toArray();
//        $goods_info['attr'] = Db::name('goods_attr')->alias('ga')->field('ga.*,gg.img_url,gg.thumb_url,gg.img_original')->leftJoin('ecs_goods_gallery gg','gg.img_id=ga.img_id')->where('goods_id',$goods_id)->select()->toArray();
//        /*商品基本信息*/
//        $sup = [
//            'name' => $goods_info['name'],
//            'tbsm' => $goods_info['tbsm'],
//            'code' => $goods_info['code'],
//            'ord' => $goods_info['ord'],
//            'category' => $goods_info['category'],
//            'brand' => $goods_info['brand'],
//            'supplier' => $goods_info['supplier'],
//            'price' => $goods_info['price'],
//            'descpt' => $goods_info['descpt'],
//            'weight' => $goods_info['weight'],
//            'type' => $goods_info['type'],
//            'img' => $goods_info['img'],
//            'thumbnail' => $goods_info['thumbnail'],
//            'is_sale' => $goods_info['is_sale'],
//            'is_fei5zhe' => $goods_info['is_fei5zhe'],
//            'add_time' => time(),
//            'edit_time' => time(),
//            'start_num' => $goods_info['start_num'],
//            'Nbei' => $goods_info['Nbei'],
//            'youhui_type' => $goods_info['youhui_type'],
//            'tishi_desc' => $goods_info['tishi_desc'],
//            'shipping_desc' => $goods_info['shipping_desc'],
//            'is_limit' => $goods_info['is_limit'],
//            'regions' => $goods_info['regions'],
//        ];
//        /*插入数据*/
//        try
//        {
//            $id = Db::name('goods_sup')->insertGetId($sup, 'id');
//            if(!empty($goods_info['img'])){
//                $insert = [
//                    'goods_id' => $id,
//                    'img_url' => $goods_info['img'],
//                    'thumb_url' => $goods_info['thumbnail'],
//                    'img_original' => $goods_info['img'],
//                ];
//                Db::name('goods_gallery')->insert($insert);
//            }
//            $this->Log->record_new(__CLASS__,__FUNCTION__,$id);
//        }
//        catch (\Exception $e)
//        {
//            abort(500, $e->getMessage());
//        }
//
//        /*插入授权园艺家入驻商*/
//        $shp = [
//            'goods_id' => $id,
//            'suppliers_id' => $goods_info['supplier'],
//            'shops_id' => 2,
//            'new' => 0,
//            'hot' => 0,
//            'fine' => 0,
//            'is_sale' => 0,
//            'is_freeshipping' => 0,
//            'status' => 0,
//            'add_time' => time(),
//            'base_sale' => rand(100, 500),
//        ];
//        try
//        {
//            Db::name('goods_shp')->insert($shp, 'id');
//        }
//        catch (\Exception $e)
//        {
//            abort(500, $e->getMessage());
//        }
//
//
//        /*如果有促销价*/
//        if (!empty($goods_info['pro_price']))
//        {
//            $promote = [
//                'gsup_id' => $id,
//                'goods_name' => $goods_info['goods_name'],
//                'beg_time' => $goods_info['beg_time'],
//                'end_time' => $goods_info['end_time'],
//                'price' => $goods_info['pro_price'],
//            ];
//
//            /*插入促销信息*/
//            try {
//                Db::name('promote')->insert($promote, 'id');
//            } catch (\Exception $e) {
//                abort(500, $e->getMessage());
//            }
//        }
//
//        /*是否限购商品*/
//        if($goods_info['is_limit']){
//            if($goods_info['limit_number']>0){
//                $insert = [
//                    'goods_id' => $id,
//                    'number' => $goods_info['limit_number'],
//                    'start_time' => $goods_info['limit_beg_time'],
//                    'end_time' => $goods_info['limit_end_time']
//                ];
//                try{
//                    Db::name('goods_limit')->insert($insert);
//                }catch(\Exception $e){
//                    abort(500,$e->getMessage());
//                }
//            }
//        }
//
//        /*商品相册图*/
//        if ($goods_info['album']) {
//            foreach ($goods_info['album'] as $img) {
//                $gallery = [
//                    'goods_id' => $id,
//                    'img_url' => $img['img_url'],
//                    'thumb_url' => $img['thumb_url'],
//                    'img_original' => $img['img_original'],
//                ];
//                /*插入相册图片*/
//                try {
//                    Db::name('goods_gallery')->insert($gallery);
//                } catch (\Exception $e) {
//                    abort(500, $e->getMessage());
//                }
//            }
//        }
//
//        /*插入商品属性,属性图片*/
//        if(!empty($goods_info['attr'])){
//            foreach($goods_info['attr'] as $item){
//                $goods_attr = [
//                    'goods_id' => $id,
//                    'attr_id' => $item['attr_id'],
//                    'attr_value' => $item['attr_value'],
//                    'img_id' => $item['img_id']
//                ];
//                try {
//                    Db::name('goods_attr')->insert($goods_attr);
//                } catch (\Exception $e) {
//                    abort(500, $e->getMessage());
//                }
//
//                if($item['img_id']){
//                    $attr_img = [
//                        'goods_id' => $id,
//                        'img_url' => $goods_info['img_url'],
//                        'thumb_url' => $goods_info['thumb_url'],
//                        'img_original' => $goods_info['img_original'],
//                        'is_show' => 0
//                    ];
//
//                    try {
//                        Db::name('goods_gallery')->insertGetId($attr_img, 'img_id');
//                    } catch (\Exception $e) {
//                        abort(500, $e->getMessage());
//                    }
//                }
//            }
//        }
//
//        return $this->respondWithArray(null, \lang::insert);
//    }

}