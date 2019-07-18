<template>
    <div class="index_page">
        <header-view :title="title"></header-view>
        <div style="margin-top:0.8rem;background:#F4F4F4;" class="nouse">
        <!-- <div class="couponnav">
          <div :style="'width:'+coupon.length*1.5+'rem'">
            <div class="item" v-for="(v,i) in coupon"  :key="i" :class="{active:v.isactive}">{{v.name}}</div>
          </div>
        </div> -->
        <scroller lock-y :bounce="true" class="couponnav">
            <div class="tab_wrap" :style="'width:'+coupon.length*1.5+'rem'">
                <div :class="{active:v.isactive}" v-for="(v,i) in coupon" @click="changestatus(i)" :key="i"> <span>{{v.name}}</span> </div>
            </div>
        </scroller>
       <!-- 列表 -->
      <vscorll :on-refresh="onRefresh" :on-infinite="onInfinite" class="cont">  
        <router-link :to="{name:'orderdetail',params:{id:item.order_id}}" v-for="(item,index) in data" :key="index" class="order">
            <p v-if="item.order_sn">订单号：{{item.order_sn}}<span>{{item.order_status}}</span></p>
            <p v-else>订单号：{{item.refund_sn}}<span v-if="item.status==0">退货中</span><span v-else>退货完成</span></p>
            <div class="ordergood" v-for="(v,i) in item.goods_list" :key="i">
              <div class="pic">
                <img :src="v.img" alt="">
              </div>
              <div class="txt">
                <h3>{{v.goods_name}}</h3>
                <h6>{{v.goods_attr}}</h6>
              </div>
              <div class="right">
                {{v.goods_price_format}}<br>
                <del>{{v.market_price_format}}</del>
                <span v-if="v.goods_number">x{{v.goods_number}}</span>
                <span v-else>x{{v.refund_number}}</span>
              </div>
            </div>
            <div class="total">共{{item.goods_sum}}件商品 <span v-if="item.total_fee">合计：<span>{{item.total_fee}}</span></span></div>

            <div class="btnbox" v-if="item.o_status==0&&item.is_delete!=1">
               <span @click.prevent="cancelorder(item.order_id)">取消订单</span>
               <span>去支付</span>
            </div>

            <div class="btnbox" v-if="item.refund_id||item.o_status==6">
               <span v-if="item.status==0" @click.prevent="cancelcancel(item.order_id)">取消退货</span>
               <!-- <span v-else @click.prevent="cancelcancel(item.order_id)">取消退货</span> -->
            </div>

            <div class="btnbox" v-if="item.o_status==4">
                <router-link :to="{name:'track',params:{id:item.order_id}}">查看物流</router-link>
                <span @click.prevent="sureorder(item.order_id)">确认收货</span>
            </div>

        </router-link>  
        <div class="nomore" v-show="!havedata">暂无更多商品</div>
      </vscorll>
      
     
       </div>
    </div>
</template>
<script>
import API from "../../api/api.js";
import { Scroller } from "vux";
import { Dialog } from 'vant';
import Header from "../../components/header/Header.vue";
import vscorll from "../../components/b_scorll/b_scorll";
export default {
  components: {"header-view": Header,vscorll,Scroller},
  data() {
    return {
      data:[],
      havedata:false,
      page:1,
      status:-1,
      isnomore:true,
      title:"全部订单",
      coupon:[
        {name:"全部",id:-1,isactive:true},
        // {name:"待付款",id:0,isactive:false},
        {name:"待发货",id:1,isactive:false},
        // {name:"部分发货",id:3,isactive:false},
        {name:"待收货",id:4,isactive:false},
        // {name:"退货中",id:5,isactive:false},
        {name:"退货",id:6,isactive:false},
        {name:"交易完成",id:7,isactive:false},
      ],
      contshow:0,
    };
  },
  created() {
    var _this = this;
    _this.status = this.$route.params.status;
    for(var i=0;i<_this.coupon.length;i++){
      if(_this.coupon[i].id ==  _this.status){
        _this.coupon[i].isactive = true;
        _this.title = _this.coupon[i].name+'订单'
      }else{
        _this.coupon[i].isactive = false;
      }
    }
    _this.getlist(_this.status,_this.page);
  },

  methods: {
   getlist(status,page) {
      var _this = this;
       _this.$http.get(API.orderlist, {
         params: {key:localStorage.getItem("key"),o_status:status,page:page,limit:4}
       }).then(res => {
         var data = res.data.data;
          if(data.length>=4){
              _this.havedata = true;
          }else{
            _this.havedata = false;
          }
              data.forEach(function(val,i){
                  _this.data.push(val)
              })
      });
    },
    //取消退货
    cancelcancel(order){
      var _this = this;

      Dialog.confirm({
        title: '确认',
        message: '确认取消退货？'
      }).then(() => {
        _this.$http.put(API.cancelcancel, {
              key:localStorage.getItem("key"),order_id:order
            }).then(res => {
                  var d = res.data;
                  if(d.code == 200){
                    this.$vux.toast.text(d.msg, "middle");
                    setTimeout(function(){
                        window.location.reload();
                    },1500)
                  }
            });
      })

       
    },
    // 取消订单
    cancelorder(order){
      var _this = this;

        Dialog.confirm({
          title: '取消',
          message: '确认取消订单？'
        }).then(() => {
          _this.$http.put(API.cancelorder, {
                key:localStorage.getItem("key"),order_id:order
              }).then(res => {
                    var d = res.data;
                    if(d.code == 200){
                      this.$vux.toast.text(d.msg, "middle");
                      setTimeout(function(){
                          window.location.reload();
                      },1500)
                    }
              });
        }).catch(() => {
          // on cance
        });


       
    },
    // 确定收货
    sureorder(order){
      var _this = this;
      Dialog.confirm({
        title: '确认',
        message: '确认收货？'
      }).then(() => {
         _this.$http.put(API.sure_receive, {
          key:localStorage.getItem("key"),order_id:order
        }).then(res => {
              var d = res.data;
              if(d.code == 200){
                this.$vux.toast.text(d.msg, "middle");
                setTimeout(function(){
                    window.location.reload();
                },1500)
              }
        });
      }).catch(() => {
        // on cancel
      });
      
    },
    // 点击切换状态
    changestatus(e){
      var _this = this;
      for(var i=0;i<_this.coupon.length;i++){
        _this.coupon[i].isactive = false;
      }
      _this.coupon[e].isactive = true;
      var oStatus = _this.coupon[e].id;
      _this.page = 1;
      _this.data = [];
      _this.status = _this.coupon[e].id;
      _this.title = _this.coupon[e].name + "订单";
      _this.getlist(oStatus,1);
    },
    onInfinite(){
      var _this = this;
      if(_this.havedata){
        _this.page++;
        _this.getlist(_this.status,_this.page);
      }
      
      _this.havedata = false;
    },
    onRefresh(){

    }
  }
};
</script>
<style lang="less">
*{margin: 0;padding: 0;box-sizing: border-box;}

.index_page {
  background: white;
}
.couponnav{
  height: 0.88rem;line-height: 0.88rem;font-size: 0.26rem;color: #333;background:#fff;position: relative;z-index:100;border-top:1px solid #f4f4f4;overflow: scroll;
  div{
    width:1.5rem;float: left;text-align: center;
    span{display: inline-block;border-bottom: 0.02rem solid #fff;}
  }
  .active{
    color: #FF0036;
    span{border-bottom-color:#FF0036;}
  }
}
.cont{background: #F4F4F4;}

.order{
  background: #fff;margin-top: 0.2rem;display:block;
  p{
    height: 0.73rem;line-height: 0.73rem;color: #333;font-size: 0.26rem;padding: 0 0.3rem;background:#fff;
    span{color: #FF0036;float: right;}
  }
  .ordergood{
    background:#fff;border-top: 1px solid #ddd;overflow: hidden;padding:0.2rem 0.2rem 0.2rem 0.3rem;
    .pic{
      width: 1.25rem;height: 1.25rem;overflow: hidden;border: 1px solid #ddd;float: left;text-align:center;margin:0;
      img{height: 100%;}
    }
    .txt{
      float: left;width: 4.4rem;padding:0 0 0 0.2rem;margin:0;
      h3{
        font-size: 0.28rem;color: #333;line-height: 0.4rem;font-weight: normal;height: 0.8rem;overflow: hidden;margin-bottom: 0.1rem;    text-align: justify;
      }
      h6{font-size: 12px;color: #999;font-weight: normal;line-height: 0.3rem;height: 0.3rem;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
    }
    .right{
      float: right;font-size: 12px;color:#333;text-align:right;
      del{display: block;color: #999}
      span{display: block;color: #999}
    }
  }
  .total{color: #333;font-size: 0.26rem;text-align: right;border-top: 1px solid #ddd;height: 0.9rem;line-height: 0.9rem;padding: 0 0.3rem;background: #fff;}
  .btnbox{
    padding: 0.2rem;text-align:right;
    span,a{display: inline-block;height: 0.55rem;line-height: 0.55rem;padding: 0 0.15rem;font-size: 0.26rem;margin-left: 0.4rem;border: 1px solid #ddd;color: #555;border-radius: 0.1rem;}
    span:nth-last-child(1){color: #FF0036;border-color: #FF0036;}
  }
}

.nomore{color: #777;position: relative;z-index: 100;text-align: center;font-size: 0.24rem;line-height: 0.4rem;margin: 0.3rem;position: relative;}
.nomore:before,.nomore:after{content: "";width: 30%;height: 1px;background: #999;display: inline-block;position: absolute;top: 50%;}
.nomore:before{left: 0;}
.nomore:after{right: 0;}

.yo-scroll .pull-refresh{background: #f4f4f4;height: 1.7rem;}

.nouse>div:nth-of-type(1){z-index: 100}
</style>
