var info = JSON.parse(localStorage.getItem("info"));
var uid = '';
var uname = '';
if(info){
        uid = info.user_id;
        uname = info.user_name;
}
global.NTKF_PARAM = {
        siteid:"ho_1000",                   
        // sellerid:"nt_XXXX",                 //sellerid：商户ID，集成商户时填写(**当有商户时，集成使用该字段**)
        settingid:"ho_1000_1493198121588",           
        uid:uid,                            
        uname:uname,                          
        isvip:"0",                         //isvip：是否为vip用户，0代表非会员，1代表会员，取值显示到小能客户端上
        userlevel:"0",                      //userlevel：网站自定义会员级别，0-N，可根据选择判断，取值显示到小能客户端上
        erpparam:"erp",                      //erpparam：erp功能的扩展字段，可选，购买erp功能后用于erp功能集成
}