<?php
/**
 * Created by PhpStorm.
 * User: losingbattle
 * Date: 2019/6/15
 * Time: 20:09
 */

namespace app\api\controller\v1\member;


use app\api\exception\IllegalException;
use app\api\validate\Member\QrCodeValidate;
use app\common\controller\ApiController;
use app\common\Entity\DsRelation;
use app\service\Distribution\DsRelationService;
use app\service\Umbrella\UmRelationService;
use Endroid\QrCode\QrCode;
use think\Db;
use think\Request;

class QrCodeController extends ApiController
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
    }

    public function share()
    {
        $this->_validate('redirect_url');
        if (!$this->is_vip) {
            throw new IllegalException('您还不是会员');
        }
        //页面跳转
        $arr['parent_id'] = $this->data['user_id'];

        $encode = base64_encode(json_encode($arr));

        $url = $this->data['redirect_url'] . '?encode=' . $encode;

        $qrCode = new QrCode($url);

        return $this->respondWithArray(['img' => $qrCode->writeDataUri()]);
    }

    //关联上级
    public function relate()
    {
        $qrCodeValidate = new QrCodeValidate();
        $qrCodeValidate->scene('relate')->check($this->data);

        $data = json_decode(base64_decode($this->data['encode']), true);
        if (!$data) {
            throw new IllegalException('解析失败');
        }
        if ($data['parent_id'] == $this->data['user_id']) {
            throw new IllegalException('您不能成为自己的下级');
        }

        //已绑定过上级的跳过
        if (DsRelation::getCollectionByUserId($this->data['user_id'], 1)) {
            throw new \Exception('您已经有上级了');
        }

        Db::startTrans();
        try {
            DsRelationService::service()->maintain($data['parent_id'], $this->data['user_id']);
            UmRelationService::service()->maintain($data['parent_id'], $this->data['user_id']);
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            throw $exception;
        }

        return $this->respondWithArray([], '关联成功');
    }
}