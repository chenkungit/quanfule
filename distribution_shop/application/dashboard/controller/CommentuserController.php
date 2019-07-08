<?php
namespace app\dashboard\controller;

use app\common\controller\WebController;
use think\Db;
use think\Request;
use app\dashboard\model\Commentuser;
use lang;


class CommentuserController extends WebController
{

    protected  $Commentuser;

    public function __construct(Request $request,Commentuser $Commentuser)
    {
        parent::__construct($request);
        $this->Commentuser = $Commentuser;
    }

    public function lists(){

        $res = $this->Commentuser->Commentuser_list();

        return $this->respondWithArray($res);
    }

    public function info(){

        $this->_validate('comment_id');

        $res = $this->Commentuser->info($this->data['comment_id']);

        return $this->respondWithArray($res);
    }

    public function edit(){
        $this->_validate('comment_id');

        try{
            $this->Commentuser->allowField(true)->save($this->data,['comment_id'=>$this->data['comment_id']]);
            $this->Log->record_new(__FUNCTION__,__METHOD__);
        }catch (\Exception $exception){
            abort(500,$exception->getMessage());
        }
        return $this->respondWithArray(null,'编辑成功');
    }

    public function send(){

        $this->_validate('comment_id','user_name','content');

        $insert = [
            'parent_id'=>intval($this->data['comment_id']),
            'user_name'=>$this->data['user_name'],
            'content'=>$this->data['content'],
            'to_fd'=>isset($this->data['to_fd'])? $this->data['to_fd'] : 0,
            'ip_address'=>$this->ip,
            'user_id'=>1,
            'is_official'=>1,
            'add_time'=>time()
        ];
        try{
            $id = $this->Commentuser->name('comment')->insertGetId($insert);
            $this->Log->record_new(__FUNCTION__,__METHOD__,$id);
        }catch (\Exception $exception){
            abort(500,$exception->getMessage());
        }
        return $this->respondWithArray(null,\lang::comment);

    }

    public function enable(){
        $this->_validate('comment_id');
        try{
            $update['status'] = ['exp','1-status'];
            $this->Commentuser->name('comment')->where('comment_id',$this->data['comment_id'])->update($update);
            $this->Log->record_new(__FUNCTION__,__METHOD__,$this->data['comment_id']);
        }catch (\Exception $exception){
            abort(500,$exception->getMessage());
        }
        return $this->respondWithArray(null,\lang::update);
    }

    public function delete(){

        $this->_validate('comment_id');

        try{
            Db::name('comment')->where('comment_id' ,'in',$this->data['comment_id'])->delete();
            $this->Log->record_new(__FUNCTION__,__METHOD__,json_encode($this->data['comment_id']));
        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }

        return $this->respondWithArray(NULL,lang::delete);
    }
}