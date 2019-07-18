<template>
    <div class="index_page">
        <header-view title="物流信息"></header-view>
        <div style="margin-top:0.8rem;background:#F4F4F4;" class="nouse">
        <!-- <scroller  lock-y :bounce="true" class="couponnav"> -->
            <!-- <div class="tab_wrap" :style="'width:'+coupon.length*1.5+'rem'">
                <div :class="{active:v.isactive}" v-for="(v,i) in coupon" @click="changestatus(i)" :key="i"> <span>{{v.name}}</span> </div>
            </div> -->
        <!-- </scroller> -->
       <!-- 列表 -->
      <!-- <vscorll :on-refresh="onRefresh" :on-infinite="onInfinite" class="cont">   -->
       <div class="trackbox">
         <div class="is_adr">
            <div class="is_l">
                <i class="iconfont">&#xe620;</i>
            </div>
            <div class="is_c">
               <div class="name">
                 <span>收货人:{{address.consignee}}</span>
                 <span>{{address.phone}}</span>
               </div>
               <div class="addres">
                 收货地址:{{address.province_name}}省{{address.city_name}}市{{address.district_name}}{{address.address}}
               </div>
            </div>
            <div class="is_r"></div>
         </div>
          <p v-if="data!=''">快递公司：{{nowdata.logistics_name}}</p>
          <p v-if="data!=''">快递单号：{{nowdata.invoice_no}}</p>
       </div>
        <!-- <div class="nomore" v-show="!havedata">暂无物流信息</div> -->
      <!-- </vscorll> -->
      
     
       </div>
    </div>
</template>
<script>
import API from "../../api/api.js";
import { Scroller } from "vux";
import Header from "../../components/header/Header.vue";
import vscorll from "../../components/b_scorll/b_scorll";
export default {
  components: {"header-view": Header,vscorll,Scroller},
  data() {
    return {
      data:[],
      nowdata:{},
      address:[],
      invoice_msg:[],
      havedata:false,
      isnomore:true,
      coupon:[],
      contshow:0,
    };
  },
  created() {
    var _this = this;
    var id = this.$route.params.id;
    _this.gettrack(id)
  },

  methods: {

    gettrack(order){
      //  console.log(order)
      var _this = this;
       _this.$http.get(API.track, {
        params: {key:localStorage.getItem("key"),order_id:order}
       }).then(res => {
         _this.address =  res.data.data.consignee;
         var data = res.data.data.delivery;
         
         _this.data = data;
         if(data!=""){
           _this.nowdata = data[0];
           _this.invoice_msg = data[0].invoice_msg.data;
         }
         
         var isactive = false;
        //  console.log(data)
         data.forEach(function(v,i){
           if(i==0){
             isactive = true;
           }else{
             isactive = false;
           }
           _this.coupon.push({name:"包裹"+(i+1),isactive:isactive})
         })
         
      });
    },
  
    // 点击切换状态
    changestatus(e){
      var _this = this;
      for(var i=0;i<_this.coupon.length;i++){
        _this.coupon[i].isactive = false;
      }
      _this.coupon[e].isactive = true;
      _this.nowdata = _this.data[e];
      _this.nowdata = _this.data[e].invoice_msg.data;
    },
    onInfinite(){
     
    },
    onRefresh(){

    }
  }
};
</script>
<style lang="less" scoped>
*{margin: 0;padding: 0;box-sizing: border-box;}
.index_page {
  background: white;
}
.cont {background: #fff;}
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
.is_adr {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 1.5rem;
          border-bottom: 0.1rem solid #eee;
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
.trackbox{
  background: #fff;overflow: hidden;
  >p{line-height: 0.88rem;color: #333;font-size: 0.26rem;border-bottom: 0.02rem solid #eee;padding: 0 0.3rem;}
  >p:nth-of-type(2){margin-bottom: 0.3rem;}
  .left{float: left;margin-left: 0.3rem;}
  .right{
    float: right;width: 80%;margin-right: 0.3rem;font-size: 0.26rem;color: #555;line-height: 0.4rem;text-align: justify;
    div{
      margin-bottom: 0.2rem;font-size:0.24rem;
      p:nth-of-type(1){margin-bottom: 0.1rem;font-size:0.28rem;}
      p:nth-of-type(2){font-size: 0.24rem;}
    }
  }
}
.nomore{color: #777;position: relative;z-index: 100;text-align: center;font-size: 0.24rem;line-height: 0.4rem;margin: 0.3rem;position: relative;}
.nomore:before,.nomore:after{content: "";width: 30%;height: 1px;background: #999;display: inline-block;position: absolute;top: 50%;}
.nomore:before{left: 0;}
.nomore:after{right: 0;}
.yo-scroll .pull-refresh{background: #f4f4f4;height: 1.7rem;}
// .nouse>div:nth-of-type(1){z-index: 100}
</style>
