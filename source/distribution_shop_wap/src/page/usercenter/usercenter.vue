<template>
    <div class="center_page">
       <router-link to="/myinfo" class="info">
          <div class="pic">
            <img v-if="userinfo.avatar"  :src="userinfo.avatar" alt="">
            <img v-else src="../../assets/image/toux.jpg" alt="">
            <!-- <span v-else class="iconfont">&#xe677;</span> -->
          </div>
          <span>{{userinfo.user_name?'昵称:'+userinfo.nick:'请登录'}}</span><br>
          <span v-if="userinfo.user_name">账号:{{userinfo.user_name}}</span><br>
          <span v-if="vip_info.vip_code">ID:{{vip_info.vip_code}}</span>
          <!-- <span class="iconfont">&#xe632;</span> -->
       </router-link>
       <div class="myorder">
            <router-link :to="{ name: 'userorder', params: {status:-1} }" class="p">我的订单<span class="iconfont">全部订单 &#xe713;</span></router-link>
            <div>
               <!-- <router-link :to="{ name: 'userorder', params: {status:0} }">
                 <i class="iconfont">&#xe607;</i>
                 待付款<span v-if="ordercount.dfk!=0">{{ordercount.dfk}}</span>
               </router-link> -->
               <router-link :to="{ name: 'userorder', params: {status:1} }">
                 <i class="iconfont">&#xe601;</i>
                 待发货<span v-if="ordercount.dfh!=0">{{ordercount.dfh}}</span>
               </router-link>
               <!-- <router-link :to="{ name: 'userorder', params: {status:3} }">
                 <i class="iconfont">&#xe601;</i>
                 部分发货<span v-if="ordercount.bffh!=0">{{ordercount.bffh}}</span>
               </router-link> -->
               <router-link :to="{ name: 'userorder', params: {status:4} }">
                 <i class="iconfont">&#xe637;</i>
                 待收货<span v-if="ordercount.dsh!=0">{{ordercount.dsh}}</span>
               </router-link>
            </div>
       </div>
       <div class="myorder myorder2">
         <p>我的服务</p>
         <div>
            <router-link to="/amount">
              <i class="iconfont">&#xe638;</i>
              账户
            </router-link>
            <router-link to="/adr_list">
              <i class="iconfont">&#xe623;</i>
              地址
            </router-link>
            <router-link to="/myshare" v-if="userinfo.is_vip">
              <i class="iconfont">&#xe61c;</i>
              二维码
            </router-link>
            <router-link to="/mycard">
              <i class="iconfont">&#xe734;</i>
              银行卡
            </router-link>
            <router-link to="/allocate" v-if="userinfo.is_vip">
              <i class="iconfont">&#xe6b7;</i>
              下拨积分
            </router-link>
            <router-link to="/mycurrent">
              <i class="iconfont">&#xe621;</i>
              收支流水
            </router-link>
            <router-link to="/myumbrella" v-if="userinfo.is_vip">
              <i class="iconfont">&#xe64e;</i>
              伞下会员
            </router-link>
         </div>
       </div>
       <nologin></nologin>
        <foot></foot>
    </div>
</template>
<script>
import API from "../../api/api.js";
import foot from "../../components/footer/Footer";
import nologin from "../../components/nologin/nologin";
import { Swiper, SwiperItem, Scroller } from "vux";
import { setTimeout } from "timers";
export default {
  components: { foot, nologin },

  data() {
    return {
      userinfo: [],
      ordercount: [],
      data_swt: true,
      vip_info:[],
      system_info:[]
    };
  },

  mounted() {
    // console.log(555)
    var _this = this;
    localStorage.setItem("beforeindex", window.location.href);
    if (localStorage.getItem("key")) {
      _this.$http
        .get(API.userinfo, {
          params: { key: localStorage.getItem("key") }
        })
        .then(res => {
          if (res.data.data.code == 2001) {
            _this.$vux.toast.text(res.data.data.msg, "middle");

            // setTimeout(function(){
            //     _this.$router.push({ name: "login"})
            // },2000)
          } else {
            localStorage.setItem("cartnum", res.data.data.cart_sum);
            _this.userinfo = res.data.data.info;
            _this.vip_info = res.data.data.vip_info;
            localStorage.setItem("system_info",JSON.stringify(res.data.data.system_info));
            _this.ordercount = res.data.data.order_count;
          }
        });
    } else {
      _this.$vux.toast.text("您尚未登录，请先登录！", "middle");
      // setTimeout(function(){
      //     _this.$router.push({ name: "login"})
      // },2000)
    }
  },

  methods: {}
};
</script>
<style lang="less">
.center_page {
  background: white;
  padding-bottom:0.98rem;
  height: auto !important;
}
.info {
  background: -webkit-linear-gradient(
    left,
    #ffa503,
    #ff0036
  ); /* Safari 5.1 - 6.0 */
  background: -o-linear-gradient(
    right,
    #ffa503,
    #ff0036
  ); /* Opera 11.1 - 12.0 */
  background: -moz-linear-gradient(
    right,
    #ffa503,
    #ff0036
  ); /* Firefox 3.6 - 15 */
  background: linear-gradient(to right, #ffa503, #ff0036); /* 标准的语法 */
  display: block;
  height: 3.2rem;
  padding: 0.7rem 0.3rem;
  box-sizing: border-box;
  overflow: hidden;
  .pic {
    height: 1.4rem;
    width: 1.4rem;
    border-radius: 50%;
    overflow: hidden;
    float: left;
    background: #e5cc63;
    position: relative;
    span {
      display: block;
      text-align: center;
      margin: 0;
      font-size: 0.3rem;
      color: #ffefde;
    }
    img {
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      margin: auto;
      width: 100%;
    }
  }
  span {
    margin-left: 0.2rem;
    color: #ffefde;
    line-height: 0.6rem;
  }
  span:nth-of-type(2) {
    color: hsla(0, 0%, 100%, 0.6);
  }
  span:nth-of-type(4) {
    float: right;
    font-size: 0.4rem;
    line-height: 0rem;
  }
}
.myorder {
  background: #fff;
  color: #323232;
  .p {
    text-align: left;
    font-size: 0.26rem;
    height: 0.88rem;
    line-height: 0.88rem;
    padding: 0 0.3rem;
    border-bottom: 1px solid rgba(221, 221, 221, 1);
    display: block;
    span {
      float: right;
      color: #323232;
    }
  }
  p {
    font-size: 0.26rem;
    height: 0.88rem;
    line-height: 0.88rem;
    padding: 0 0.3rem;
    border-bottom: 1px solid rgba(221, 221, 221, 1);
    span {
      float: right;
      color: #323232;
    }
  }
  div {
    display: flex;
    justify-content: space-around;
    height: 1.86rem;
    box-sizing: border-box;
    padding: 0.25rem 0;
    border-bottom: 0.16rem solid #eee;
    a,
    kbd {
      display: inline-block;
      text-align: center;
      position: relative;
      font-style: normal;
      font-family: -apple-system-font, "Helvetica Neue", sans-serif;
      i {
        display: block;
        font-size: 0.45rem;
        height: 0.7rem;
        line-height: 0.7rem;
      }
      span {
        display: inline-block;
        background: #d21018;
        font-size: 12px;
        color: #fff;
        border-radius: 50%;
        position: absolute;
        right: 3px;
        top: 0;
        width: 0.35rem;
        height: 0.35rem;
        line-height: 0.35rem;
      }
    }
  }
}
.myorder2 {
  overflow: hidden;
  // margin-bottom: 1rem;
  p {
    border-bottom: 0;
  }
  div {
    border-bottom: 0;
    padding: 0.5rem 0 0.3rem;
    height: auto;
    border-top: 1px solid #dadada;
    display: block;
  }

  a,
  kbd {
    width: 33.3%;
    height: 1.8rem;
    float: left;
    color: #949494;
    i {
      color: #ff0036;
    }
  }
}
</style>
