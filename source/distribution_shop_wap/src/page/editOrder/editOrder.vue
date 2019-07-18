<template>
  <div class="class">
      <header-view :title="'确认订单'"></header-view>
      <div class="adr" @click="go_adr_lsit">
        <div class="box">
          <div class="no_adr" v-if="default_address==''">
            <i class="iconfont">&#xe601;</i><span>{{default_address}}添加收货地址</span>
          </div>
          <div class="is_adr" v-else>
            <div class="is_l">
                <i class="iconfont">&#xe620;</i>
            </div>

            <div class="is_c">
               <div class="name">
                 <span>收货人:{{default_address.consignee}}</span>
                 <span>{{default_address.tel}}</span>
               </div>
               <div class="addres">
                 收货地址:{{default_address.province_name}}省{{default_address.city_name}}市{{default_address.district_name}}{{default_address.address}}
               </div>
            </div>
            <div class="is_r">
                <i class="iconfont">&#xe713;</i>
            </div>

          </div>
        </div>
        <div class="line"></div>
      </div>
      <div class="goods">
        <!-- <div class="send_adr">发货地:{{order_list.address}}</div> -->
        <div class="list">
          <div class="item" v-for="(v,i) in order_list.goods_detail" :key="i">
            <div class="left">
              <img :src="v.goods_thumb" alt="">
            </div>
            <div class="right">
              <p>{{v.goods_name}}</p>
              <p v-if="v.goods_attr">{{v.goods_attr}}</p>
              <p><span>{{v.goods_price_format}}</span><i>x{{v.goods_number}}</i></p>
            </div>
          </div>
        </div>
        <div class="goos_to">共{{order_list.subnumber}}件商品，重量<em v-if="order_list.subweight">{{order_list.subweight.toFixed(2)}}</em>kg<br><span>小计{{order_list.subtotal_format}}</span></div>
      </div>
      <div class="send_way">
        <!-- <div class="cell" @click="dialog_swt=!dialog_swt">
          <div class="left">配送方式</div>
          <div class="right">
            {{shipping_name==''?'选择快递':shipping_name}}
              <i class="iconfont">&#xe713;</i>
          </div>
        </div> -->
        <!-- <div class="cell" @click="dialog_red=!dialog_red">
          <div class="left">使用红包</div>
          <div class="right">
           {{bonus_red_name==''?'无可用':bonus_red_name}}
              <i class="iconfont">&#xe713;</i>
          </div>
        </div>
        
        <div class="cell" v-if="freight_show==false" >
          <div class="left">运费券</div>
          <div class="right">{{bonus_freight_name==''?'无可用':bonus_freight_name}}
              <i class="iconfont">&#xe713;</i>
          </div>
        </div>

        <div v-else  class="cell" @click="dialog_freight=true">
          <div class="left">运费券</div>
          <div class="right">{{bonus_freight_name==''?'无可用':bonus_freight_name}}
              <i class="iconfont">&#xe713;</i>
          </div>
        </div> -->
        <div class="item">
          <div class="left">
            买家留言
          </div>
          <div class="right">
            <input type="text" maxlength="20" placeholder="选填：对本次交易的说明" v-model="leave_msg"/>
          </div>
        </div>
        <div class="item1">
          <p>优惠信息</p>
          <div class="left" v-if="order_list.difference!=0">-折扣</div>
          <div class="right" v-if="order_list.difference!=0">-{{order_list.difference_format}}</div>
        </div>
        



      </div>
      <div class="pay_now">
          <div class="up">
            <div>使用积分<span>：({{data.user_money_format}})</span></div>
            <!-- <x-switch></x-switch> -->
          </div>
          <div class="dowm">
            <div class="left">
              应付：<kbd style="color:#c00;"></kbd><span v-if="order_list.subtotal_format">{{order_list.subtotal_format}}</span>
            </div>
            <div class="right" @click="done()">
              立即付款
            </div>
          </div>
      </div>
  </div>
</template>
<script>
import Header from "../../components/header/Header.vue";
import dialog_bottom from "../../components/dialog_bottom/dialog_bottom.vue";
import { XSwitch, Group, Radio } from "vux";
import API from "../../api/api.js";
import { setTimeout } from 'timers';
import { Dialog } from "vant";
export default {
  components: {
    "header-view": Header,
    "dialog-bottom": dialog_bottom,
    XSwitch,
    Group,
    Radio
  },
  data() {
    return {
      data: {},
      default_address: "",
      order_list: {},
      sendaddress: 0,
      dialog_swt: false,
      pay_swt: false,
      shipping_list: [],
      shipping_id: 1,
      shipping_name: "",
      shipping_fee: "",
      // vipCard: [],
      is_choose_card: null,
      leave_msg: "",
      is_balance: 1,
      payway:[{name:'支付宝',id:3,iconfont:'&#xe621;',class:'',vshow:true},{name:'微信',id:4,iconfont:'&#xe67e;',class:'iconfont icon-w',vshow:true}],
      FCardNumber: 0,
      balance_swt: false,
      // 优惠券
      dialog_red:false,
      dialog_freight:false,
      old_bonus:[],
      bonus:[],
      bonus_red_name:'',
      bonus_red_id:0,
      bonus_red_fee:0,
      bonus_freight_name:'',
      bonus_freight_id:0,
      bonus_freight_fee:0,
      is_wechatBrows:false,
      obj:[],
      configdata:[],
      url:'',
      coup_num:0,
      freight_num:0,
      normal_bonus_length:0,//普通优惠券长度
      normal_bonus_unable_length:0,//不可用普通优惠券长度
      shipping_bonus_length:0,//运费券长度
      shipping_bonus_unable_length:0,//不可用运费券长度
      freight_show:true,
      order_id:'',
      is_inapp:false,
    };
  },
  created: function() {
    window.JsPaySuccessNative = this.JsPaySuccessNative;
    window.JsPayFailedNative = this.JsPayFailedNative;
  },

  mounted() {
    var _this = this;
    var str = sessionStorage.getItem("data");
    // _this.order_id = localStorage.getItem("done_orderid") || '';
    var obj = JSON.parse(str);
    _this.url = window.location.href;
    this.obj = obj;
    this.getData(obj);
    if (this.$route.query.choose_r_data) {
      var d = JSON.parse(this.$route.query.choose_r_data);
      this.default_address = d;
      var e = this.obj;
       var obj = this.obj;
      
      obj.address_id = d.address_id;
      obj.once = sessionStorage.getItem("once");
      _this.getkuaidi(obj);
      
    }
    // this.get_user_vipcard();
    if (this.data.available_money == 0) {
      this.balance_swt = true;
    }
    if( navigator.userAgent.indexOf("HCApp") > -1){ //在app内部
        _this.is_inapp = true;
    }
    // 判断浏览器属性
    var ua = window.navigator.userAgent.toLowerCase();
      if (ua.match(/MicroMessenger/i) == "micromessenger") {
        this.is_wechatBrows = true;
        this.payway[0].vshow = false;
      } else {
        this.is_wechatBrows = false;
      }

    // this.$http.get('/web/wechat/js_config', {
    //     params: {url: _this.url}
    //   })
    //   .then(res => {
    //      this.configdata= res.data.data;
         
    //     //  console.log(this.configdata);
    //   });


  },

  methods: {
    check_coup(i){
      var _this =  this;
      _this.coup_num = i;
    },
    check_freight(i){
      var _this =  this;
      _this.freight_num = i;
    },
    JsPaySuccessNative() {

      setTimeout(() => {
        if(is_inapp){
          window.location.href = "bridge://openWindow/order/"+window.orderId;
        }else{
          this.$router.replace({ name: "orderdetail",params:{id:window.orderId}});
        }
      }, 2000);

    },

    JsPayFailedNative(){

      this.$vux.toast.text('支付取消', "middle");

      this.$router.replace({ name: "orderdetail",params:{id:window.orderId}});

    },
  getData(e) {
      var _this = this;
      // console.log(res.data.data)
      _this.data = JSON.parse(localStorage.getItem("order_data"));
      if (
        _this.data.default_address != "" &&
        !_this.$route.query.choose_r_data
      ) {
        // console.log("走到了这里")
        _this.default_address = _this.data.default_address;
      }

      _this.order_list = _this.data.order_list;
      _this.sendaddress = _this.data.order_list.sendaddress;
      
      if (_this.$route.query.choose_r_data) {
	      var d = JSON.parse(this.$route.query.choose_r_data);
	      this.default_address = d;
	      var obj = {};
	      obj.address_id = d.address_id;
	      obj.once = this.once;
	      	obj.sendaddress = this.sendaddress;
	      	obj.devide_item = this.devide_item;
	      	obj.is_group = localStorage.getItem('is_group');
	      	console.log(this.sendaddress)
	      	_this.getkuaidi(obj);
      
    	}
      
      
      
      if (_this.data.order_list.shipping_list != "") {
        _this.shipping_list = _this.data.order_list.shipping_list;
      }

      // 配送方式默认第一个
      console.log(_this.shipping_list)
      if (_this.shipping_list != "") {
        _this.shipping_name =
          _this.order_list.shipping_list[0].shipping_name +
          "(" +
          _this.order_list.shipping_list[0].shipping_fee +
          ")";
        // _this.shipping_id = _this.order_list.shipping_list[0].shipping_id;
        _this.shipping_fee = parseFloat(
          _this.order_list.shipping_list[0].shipping_fee
        );
        if (_this.order_list.shipping_list[0].shipping_fee == 0) {
          _this.freight_show = false;
        }
      }else{
      	_this.freight_show = false;
      }

      //优惠券
      // _this.data.user_bonus.shipping_bonus.push({type_name:"不使用",bonus_id:0,type_money:0})
      // _this.data.user_bonus.normal_bonus.push({type_name:"不使用",bonus_id:0,type_money:0})
      _this.bonus = _this.data.user_bonus;
      _this.old_bonus = _this.data.user_bonus;
      if (_this.bonus.normal_bonus&&_this.bonus.normal_bonus.length) {
        _this.normal_bonus_length = _this.bonus.normal_bonus.length;
        var coup_normal = _this.bonus.normal_bonus;
        var bestcoup = 0;
        var bestcoup_i = 0;
        coup_normal.forEach(function(v, i) {
          if (
            parseFloat(v.type_money) > bestcoup &&
            parseFloat(v.type_money) <= parseFloat(_this.order_list.subtotal)
          ) {
            bestcoup = v.type_money;
            bestcoup_i = i;
          }
        });

        _this.bonus_red_id = _this.bonus.normal_bonus[bestcoup_i].bonus_id;
        
        var coupon_cateid = '';
				if(_this.bonus.normal_bonus[bestcoup_i].act_range==1){
					coupon_cateid = _this.bonus.normal_bonus[bestcoup_i].act_range_ext;
					 var coupon_cateall = 0;
            _this.order_list.goods_detail.forEach(function(v,i){
                if(coupon_cateid.indexOf(v.parent_id)>-1 ||coupon_cateid.indexOf(v.category)>-1){
                    coupon_cateall += parseFloat(v.subtotal);
                }
            })
            
            if(_this.bonus.normal_bonus[bestcoup_i].type_money>coupon_cateall){
                _this.bonus_red_fee = coupon_cateall;
            }else{
                _this.bonus_red_fee = _this.bonus.normal_bonus[bestcoup_i].type_money;
            }
				}else if(_this.bonus.normal_bonus[bestcoup_i].act_range==3){
            // 商品优惠券
            coupon_cateid = _this.bonus.normal_bonus[bestcoup_i].act_range_ext;
            var coupon_cateall = 0;
            _this.order_list.goods_detail.forEach(function(v,i){
                if(coupon_cateid.indexOf(v.gsup_id)>-1 || coupon_cateid.indexOf(v.sup_id)>-1){
                    coupon_cateall += parseFloat(v.subtotal);
                }
            })
            console.log(coupon_cateall)
             if(_this.bonus.normal_bonus[bestcoup_i].type_money>coupon_cateall){
                _this.bonus_red_fee = coupon_cateall;
            }else{
                _this.bonus_red_fee = _this.bonus.normal_bonus[bestcoup_i].type_money;
            }

        }else{
						if(_this.bonus.normal_bonus[bestcoup_i].type_money>parseFloat(_this.order_list.subtotal)){
                _this.bonus_red_fee = parseFloat(_this.order_list.subtotal);
            }else{
                _this.bonus_red_fee = _this.bonus.normal_bonus[bestcoup_i].type_money;
            }
					
				}
        
        _this.bonus_red_name = "-" + parseFloat(_this.bonus_red_fee).toFixed(2);
      }
      
      if (_this.bonus.normal_bonus_unable &&_this.bonus.normal_bonus_unable.length) {
        _this.normal_bonus_unable_length =
          _this.bonus.normal_bonus_unable.length;
      }
      if(_this.shipping_list!=""){
      	// _this.choose_best_freight();
      }else{
      	_this.freight_show = false;
      }
      
    },
    go_adr_lsit() {
      this.$router.push({
        name: "adr_list",
        query: {
          sendaddress: this.sendaddress
        }
      });
    },
    hide_dialog() {
      this.dialog_swt = !this.dialog_swt;
    },
    //  hide_dialog_freight() {
    //   this.dialog_freight = !this.dialog_freight;
    // },
    // hide_dialog_red() {
    //   this.dialog_red = !this.dialog_red;
    // },
    // hide_dialog_pay() {
    //   this.pay_swt = !this.pay_swt;
    // },
    // get_user_vipcard() {
    //   var _this = this;
    //   this.$http.get(API.vipCard, {params:{key:localStorage.getItem("key")}}).then(res => {
    //     // console.log(res.data.data);
    //     _this.vipCard = res.data.data;
    //   });
    // },
    // choose_pay_way(i, FCardNumber) {
    //   this.is_choose_card = i;
    //   this.FCardNumber = FCardNumber;
    // },

    // app_pay() {
    //   var data = {
    //     address_id: this.default_address.address_id,
    //     sendaddress: this.sendaddress,
    //     shipping_id: this.shipping_id,
    //     msg_to_shop: this.leave_msg,
    //     bonus_id:this.bonus_red_id,
    //     bonus_ship_id:this.bonus_freight_id,
    //     is_balance: this.is_balance,
    //     once: sessionStorage.getItem("once"),
    //     key:localStorage.getItem("key")
    //   };
    //   if(this.order_id!=""){
    //     data = {order_id:this.order_id}
    //   }
    //   // console.log(data)
    //   switch (this.is_choose_card) {
    //     case 0: //员工卡
    //       data.pay_id = 8;
    //       data.card_number = this.FCardNumber;
    //       this.done(data,1);
    //       break;
    //     case 1: //会员卡
    //       data.pay_id = 8;
    //       data.card_number = this.FCardNumber;
    //       this.done(data,1);
    //       break;
    //     case 3: //支付宝
    //       data.pay_id = 5;
    //       this.pay(data,"alipay");
    //       break;
    //     case 4: //微信
    //     if (this.is_wechatBrows) {
    //        data.pay_id = 6;
    //     }else{
    //        data.pay_id = 7;
    //     }
         
    //       this.pay(data,"weixin");
    //       break;
    //     default:
    //   }
    // },
    // 拉起支付(支付宝，微信)
    // pay(data, pay_str) {
    //   var _this = this;
    //   let wx = require('weixin-js-sdk');

    //   if (this.pay_id != 0) {
    //     this.done(data)
    //     .then(res => {
          
    //       if(res.order_id){
    //         var orderid = res.order_id;
    //       }else{
    //         var orderid = _this.order_id;
    //       }
    //       window.orderId = orderid;
    //       // console.log("orderid:"+res);
    //         if (_this.is_wechatBrows) {
    //           // console.log("走到了这里啦:"+res)
    //           var sign = res.sign;
    //           // console.log(sign.timeStamp,sign.nonceStr,sign.package,sign.paySign);
    //           var d = this.configdata;
    //            wx.config(d)
    //           wx.ready(function(){
    //               wx.chooseWXPay({
    //                 timestamp: sign.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
    //                 nonceStr: sign.nonceStr, // 支付签名随机串，不长于 32 位
    //                 package: sign.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
    //                 signType: sign.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
    //                 paySign: sign.paySign, // 支付签名
    //                 success: function(res) {
    //                   // 支付成功后的回调函数
    //                   _this.$vux.toast.text('支付成功', "middle");
                      
    //                   if (res.errMsg === 'chooseWXPay:ok') {
    //                     // localStorage.setItem("done_orderid","");
    //                     _this.order_id = "";
    //                     setTimeout(() => {
    //                       if(_this.is_inapp){
    //                           window.location.href = "bridge://openWindow/order/"+orderid;
    //                       }else{
    //                         _this.$router.replace({ name: "orderdetail",params:{id:orderid}})
    //                       }
                          
    //                     }, 2000);
                            
    //                   } else {
                        
    //                     _this.$vux.toast.text('支付取消', "middle");
    //                   }
                      
    //                 },cancel() {
    //                     _this.$vux.toast.text('支付取消', "middle");
    //                 },
    //                 error(res) {
    //                     _this.$vux.toast.text('支付错误'+JSON.stringify(res), "middle");
    //                 },
    //                 fail: function (res) {
    //                   _this.$vux.toast.text('支付失败'+JSON.stringify(res), "middle");
    //                 }
    //               });
    //           })
    //         } else {
    //           //  console.log(res)
    //           if(navigator.userAgent.indexOf("Android") > -1){
    //           	JSBridge.pay(pay_str, res.sign, function(result){
	  //               if (result["success"]) {
	  //                 window.JsPaySuccessNative();
	  //               } else {
	  //                 window.JsPayFailedNative();
	  //               }
	  //             });
    //           }else{
              	
              
    //           if(pay_str=="weixin"){
    //             // console.log('eee')
    //             var sign = res.sign;
    //               window.location.href =
    //                 res.sign.mweb_url +
    //                 "&redirect_url=" +
    //                 encodeURIComponent(
    //                   "http://" +
    //                     window.location.host +
    //                     "/mobile/usercenter"
    //                 );
                
                
    //           }else{
    //             window.location.href = res.sign.redirect_url;
    //           }
    //           }
              
    //         }
    //     })
    //     .catch(err => {
    //     });
    //   } else {
    //     this.$vux.toast.text("请选择支付方式", "middle");
    //   }
    // },
    // pay_now() {
     
    //   if(this.default_address==""){
    //           this.$vux.toast.text("请选择收货地址！", "middle");
    //   }else{
         
     
    //         if (this.is_balance == 1) {
    //           if (this.data.available_money < this.order_list.goods_total) {
    //             this.pay_swt = !this.pay_swt;
    //           } else {
                
    //             var data = {
    //               address_id: this.default_address.address_id,
    //               sendaddress: this.sendaddress,
    //               shipping_id: this.shipping_id,
    //               msg_to_shop: this.leave_msg,
    //               is_balance: this.is_balance,
    //               bonus_id:this.bonus_red_id,
    //               bonus_ship_id:this.bonus_freight_id,
    //               // is_dingjin: 0,
    //               once: sessionStorage.getItem("once"),
    //               // is_group: 1
    //             };
    //             // console.log(data);
    //                 this.done(data,1);
                
    //           }
    //         } else {
    //           this.pay_swt = !this.pay_swt;
    //         }
    //    }
       
    // },
    done(e) {
            var _this = this;
      if(this.default_address==""){
         this.$vux.toast.text("请选择收货地址！", "middle");
      }        Dialog.confirm({
          title: "确认",
          message:
            "此次购买需要" +
            this.order_list.subtotal_format +
            ",确认购买吗？"
        }).then(() => {
      var data = {
        address_id: this.default_address.address_id,
        sendaddress: this.sendaddress,
        shipping_id: this.shipping_id,
        msg_to_shop: this.leave_msg,
        bonus_id:this.bonus_red_id,
        bonus_ship_id:this.bonus_freight_id,
        is_balance: this.is_balance,
        once: sessionStorage.getItem("once"),
        key:localStorage.getItem("key")
      };
      var api = API.done;
      return new Promise((resolve, reject) => {
        this.$http
          .post(api, data)
          .then(res => {
            if (res.data.code == 200) {
                  _this.order_id = "";
                  _this.$vux.toast.text("支付成功", "middle");
                  setTimeout(function(){
                          _this.$router.replace({ name: "orderdetail",params:{id:res.data.data.order_id}});            
                  },1500)
            }else if(res.data.code == 2040){
              _this.$vux.toast.text("支付成功", "middle");
                setTimeout(function(){
                      _this.$router.replace({ name: "orderdetail",params:{id:_this.order_id}});
                },1500)
            }else{
              _this.$vux.toast.text(res.data.msg, "middle");
            }
            if(res.data.data.order_id){
                _this.order_id = res.data.data.order_id;
            }
            
            resolve(res.data.data);
          })
          .catch(res => {
            reject(res);
          });
      });
        })

    }
  }
};
</script>

<style lang="less" scoped>
em{font-style: normal;}
kbd{font-family: "微软雅黑"}
.adr {
  margin-top: 0.9rem;
  background: white;
  .box {
    .no_adr {
      height: 0.88rem;
      line-height: 0.88rem;
      padding: 0 0.3rem;
      i {
        color: #FF0036;
        font-size: 0.36rem;
      }
      span {
        margin-left: 0.12rem;
      }
    }
    .is_adr {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 1.5rem;
      .is_l {
        flex: 1;
        height: 100%;
        text-align: center;
      }
      .is_c {
        flex: 8;

        .name {
          display: flex;
          justify-content: space-between;
        }
      }
      .is_r {
        flex: 1;
        text-align: center;
      }
    }
  }
}
.goos_to{
  padding: 0.2rem;text-align: right;
  span{color: #c00;}
}
.line {
  height: 0.12rem;
  background: repeating-linear-gradient(
    -45deg,
    #8fc9f5 0,
    #8fc9f5 50%,
    #f58f8f 50%,
    #f58f8f 100%
  );
}
.goods {
  padding: 0 0.3rem;
  background: white;
  margin-top: 0.1rem;
  .send_adr {
    height: 0.73rem;
    line-height: 0.73rem;
    border-bottom: 1px solid #f1f1f1;
  }
  .list {
    .item {
      display: flex;
      padding: 0.2rem 0;
      border-bottom: 1px solid #f1f1f1;
      .left {
        width: 1.72rem;
        height: 1.72rem;
        border: 1px solid #f1f1f1;
        display: flex;
        align-items: center;
        justify-content: center;
        img {
          width: 1.48rem;
          height: 1.48rem;
          border-radius: 0.05rem;
        }
      }
      .right {
        flex: 1;
        margin-left: 0.2rem;
        p {
          text-align: justify;
          span {
            color: #c00;
          }
          i{
            font-style: normal;float: right;color: #999
          }
        }
      }
    }
  }
}

.send_way {
  margin-top: 0.16rem;
  .cell {
    display: flex;
    height: 0.89rem;
    align-items: center;
    justify-content: space-between;
    border-bottom: 0.01rem solid #f1f1f1;
    background: white;
    padding: 0 0.3rem;

  }
  .item {
    display: flex;
    height: 0.89rem;
    align-items: center;
    border-bottom: 0.01rem solid #f1f1f1;
    background: white;
    padding: 0 0.3rem;

    .right {
      flex: 1;
      margin-left: 0.2rem;
      input {
        border: 0;
        width: 100%;
        height: 100%;
        outline: none;
      }
    }
  }
  .item1{
    background: #fff;padding:0.15rem 0.3rem;line-height: 0.5rem;overflow: hidden;font-size:12px;margin-top:0.2rem;
    p{margin-bottom: 0.1rem;color: #555;}
    .left{float: left;width: 50%;}
    .right{float: right;color: #c00;}
  }
}
.pay_now {
  position: fixed;
  width: 100%;
  bottom: 0;
  .up {
    width: 100%;
    line-height: 0.9rem;
    font-size: 0.35rem;
    padding-left:0.3rem; 
    background: white;
  }
  .dowm {
    // padding: 0 .3rem;
    background: white;
    height: 0.98rem;
    display: flex;
    align-items: center;
    justify-content: space-between;

    .left {
      padding-left: 0.3rem;
      span {
        color: #c00;
        font-size: 0.5rem;
      }
    }
    .right {
      width: 2.1rem;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #FF0036;
      color: white;
    }
  }
}
.kuaidi {
  .k_title {
    height: 1rem;
    line-height: 1rem;
    border-bottom: 1px solid #f1f1f1;
    font-size: 0.3rem;
    text-align: center;
  }
  .k_list {
    .cell {
      height: 0.8rem;
      line-height: 0.8rem;
      padding: 0 0.3rem;
      display: flex;
      justify-content: space-between;
      border-bottom: 1px solid #f1f1f1;
      i {
        color: #FF0036;
      }
      small{
          width: 60%;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
      }
      
    }
    .cell1{
      line-height: 0.4rem;
      padding: 0.2rem;
      border-bottom: 1px solid #f1f1f1;
      i {
        color: #FF0036;
        float: right;
      }
      span{float: left;}
      small{display: block;font-size: 12px;color: #999}
    }
  }
}
.pay {
  .k_title {
    height: 1rem;
    line-height: 1rem;
    border-bottom: 1px solid #f1f1f1;
    font-size: 0.3rem;
    text-align: center;
  }
  .k_list {
    .cell {
      height: 0.8rem;
      line-height: 0.8rem;
      padding: 0 0.3rem;
      display: flex;
      justify-content: space-between;
      border-bottom: 1px solid #f1f1f1;

      .icon-z {
        margin-right: 0.2rem;
        color: #1296db;
      }
      .icon-w {
        margin-right: 0.2rem;
        color: #1afa29;
      }
      .icon-c {
        margin-right: 0.2rem;
        color: #FF0036;
      }
      i {
        margin-right: 0.2rem;
        color: #FF0036;
      }
    }
  }
  .pay_s {
    position: fixed;
    bottom: 0;
    height: 1rem;
    line-height: 1rem;
    width: 100%;
    background: #FF0036;
    color: white;
    text-align: center;
  }
}
.weui-switch-cp__input:checked ~ .weui-switch-cp__box,
.weui-switch:checked {
  border-color: #FF0036 !important;
  background-color: #FF0036 !important;
}
.child-view{margin-bottom: 2.4rem;}
.class{position: static !important;}
.coupon-no{
  padding:0.2rem 0.2rem;
  .list{
    margin-bottom: 0.3rem;border:1px dashed #999;
    .left{
      border-radius: 0 0 0 0.2rem;float:left;width: 1.8rem;border-right: 0.04rem dashed #f4f4f4;text-align: center;line-height: 0.5rem;padding: 0.3rem 0;font-size:0.26rem;background: #fff;color:#121212;    margin-left: 0.05rem;
      p{color: #FF0036;font-size: 0.26rem;
      span{    font-size: 0.6rem;font-weight: bold;}}
    }
    .right{
      width: 5.2rem;padding:0.24rem 0.24rem 0;background: #fff; border-radius: 0 0 0.2rem 0;float: left;
      p{color: #323232;font-size: 0.28rem;line-height: 0.42rem; height: 0.84rem;overflow: hidden;}
      kbd{font-family: "微软雅黑";font-size:12px;
      i{border-radius: 50%;margin-left:0.1rem;width: 0.25rem;height: 0.25rem;display: inline-block;background: url(../../assets/image/coupon_down.png) no-repeat top center;background-size: auto 0.25rem;}
      }
      a{width: 1rem;float:right;height: 0.4rem;line-height: 0.4rem;margin-top:0.1rem;text-align: center;color: #fff;font-size: 0.22rem;display: block;background: #FF0036;border-radius: 0.1rem;}
      span{display: inline-block;padding: 0 0.1rem;color:#fff;background: #FF0036;border-radius: 0.1rem;height: 0.37rem;line-height: 0.37rem;font-size: 12px; margin-right: 0.1rem;}
      
    }
    .bottom{width: 98%;margin-left: 0.05rem;border-top: 0.04rem dashed #f4f4f4;line-height: 0.7rem;height: 0.68rem;padding: 0 0.1rem;border-radius: 0.2rem 0.2rem 0 0; background: #fff;color: #8A8A8A;font-size: 12px;}
  }
  .yunf{
    .left{
      p{color:#2BBC69; }
    }
    .right{
      p{
        span{
          background: #2BBC69;
        }
      }
      a{
        background: #2BBC69;
      }
    }
  }
  .list-yes{
    height: 1.6rem;
  .left{
    background: transparent;width: 1.6rem;border-right: 0;font-size: 0.22rem;color:#7F7F7F;
    p {
      color: #7F7F7F;
      span{font-size: 0.4rem;}
  }
    }
  .right{background: transparent;padding:0.3rem;p{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;line-height: 0.45rem;height: 0.45rem;color: #7F7F7F;}span{background: #ccc;color: #999;}}
  // .bottom{background: transparent;}
}
.list-yes{background: url(../../assets/image/coupon.png) no-repeat top center;background-size: 100% 1.6rem; }
}
.coup_title{
  border-bottom: 0.02rem solid #ebebeb;line-height: 0.9rem;height: 0.9rem;text-align:center;
  span{display: inline-block;box-sizing: border-box;padding: 0 0.2rem;font-size: 0.3rem;height: 0.88rem;}
  span:nth-of-type(1){margin-right: 0.6rem;}
  span.active{color: #FF0036;border-bottom: 0.02rem solid #FF0036;}
}
.kd_coup{
  .k_list{height: 6rem;overflow-y: scroll;    margin-bottom: 2.2rem;}
  .k_title{border: 0;line-height: 0.6rem;height: 0.8rem;padding: 0.2rem 0.3rem 0;text-align: left;font-size: 0.34rem;font-weight: bold;
    i{float: right;font-weight: normal;font-size: 0.4rem;}
  }
  .coupon-no{
    .list{
      .right{
        i{display: block;border-radius: 50%;width: 0.35rem;height: 0.35rem;line-height: 0.35rem;font-size: 12px;text-align: center;border: 0.02rem solid #ccc;color: #ccc;    float: right;margin: 0.15rem 0.3rem 0 0;}
        i.active{color: #fff;background: #FF4C4C;border-color: #FF4C4C;}
      }
    }
    
  } 
  button{background: #f60;color: #fff;border: 0;width: 100%;height: 1rem;position: absolute;bottom: 1.3rem;left: 0.2rem;    width: 7.1rem;}  
}
.nouse_coupon{

*{color: #7f7f7f !important;}
.right{
  span{background: #7f7f7f !important;color: #fff !important;}
}
} 
// .weui-label{width: 7rem !important;}
.up{font-size:12px;}
.weui-cells{font-size: 14px;}
</style>
