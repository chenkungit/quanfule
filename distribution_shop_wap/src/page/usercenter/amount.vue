<template>
    <div class="index_page">
        <header-view title="账户积分"></header-view>
        <div class="amountbox">
          <p>您的账户积分</p>
          <i class="iconfont">&#xe638;</i>
          <span>{{userinfo.user_money}}</span>
          <p>您的账户余额</p>
          <i class="iconfont">&#xe62a;</i>
          <span>{{userinfo.prize_money}}</span>
          <router-link to="/withdraw" v-if="userinfo.is_vip">去提现</router-link>
          <a class="btn" v-if="userinfo.is_vip" @click="show=true">余额换积分</a>
       </div>
        <van-dialog
        v-model="show"
        :title="'余额换积分'"
        show-cancel-button
        :lazy-render="false"
        @confirm='duihuan'
      ><br/>
        <van-field label="兑换余额" v-model="money" placeholder="请输入要兑换余额" />
        <br/>
        <div style="text-align:center;font-size:0.24rem">*手续费为{{rate}}%*</div>
        <br/>
        
      </van-dialog>
    </div>
</template>
<script>
import API from "../../api/api.js";
import Header from "../../components/header/Header.vue";
export default {
  components: { "header-view": Header },

  data() {
    return {
      userinfo: [],
      show: false,
      money: "",
      rate: 0
    };
  },

  created() {
    var _this = this;
    _this.getlist();
    var system_info = localStorage.getItem("system_info");
    if (system_info) {
      this.rate = JSON.parse(system_info).convert_service_charge_rate * 100;
    }
  },

  methods: {
    getlist() {
      var _this = this;
      _this.$http.get(API.withdrawinfo, {}).then(res => {
        _this.userinfo = res.data.data;
      });
    },
    duihuan() {
      var _this = this;
      _this.$http
        .post(API.treasureconvert, { money: _this.money })
        .then(res => {
          _this.$vux.toast.text(res.data.msg, "middle");
          _this.money = "";
          if (res.data.code == 200) {
            _this.getlist();
            _this.show = false;
          }
        });
    }
  }
};
</script>
<style lang="less">
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
.index_page {
  background: white;
}
.amountbox {
  margin-top: 1rem;
  text-align: center;
  font-size: 0.26rem;
}
.amountbox * {
  display: block;
}
.amountbox a {
  background: #f60;
  color: #fff;
  width: 6.8rem;
  height: 0.88rem;
  line-height: 0.88rem;
  margin: auto;
  margin-bottom: 0.25rem;
}
.amountbox p {
  color: #323232;
  font-size: 0.26rem;
  margin: 0rem auto 0.5rem;
}
.amountbox i {
  font-size: 0.6rem;
  color: #f60;
}
.amountbox span {
  margin: 0.5rem 0;
  font-size: 0.3rem;
}
</style>
