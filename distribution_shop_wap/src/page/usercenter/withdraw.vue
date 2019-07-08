<template>
  <div class="share">
    <header-view :title="'提现'"></header-view>
    <button type="button" class="add" @click="withdrawlist()">申请记录</button>
    <div class="main">
      <div class="choose" @click="choosecard">
        <div class="nochoose" v-if="JSON.stringify(carddata) == '{}'">选择银行卡</div>
        <div class="haschoose" v-else>
          <van-swipe-cell>
            <van-cell
              :border="false"
              title="类型"
              :value="carddata.type==1?'支付宝':'银行卡'"
              v-show="carddata.type==1"
            />
            <van-cell
              :border="false"
              title="银行名称"
              :value="carddata.bank_name"
              v-show="carddata.type==2"
            />
            <van-cell
              :border="false"
              :title="carddata.type==1?'支付宝号':'银行卡号'"
              :value="carddata.type==1?carddata.alipay_account:carddata.bank_number"
            />
          </van-swipe-cell>
        </div>
      </div>
      <div class="line"></div>
      <van-cell-group>
        <van-field v-model="money" placeholder="请输入提现金额"/>
      </van-cell-group>
      <span style="text-align:left;font-size:0.24rem">*手续费为{{rate}}%*</span>
      <button type="button" class="withdraw" @click="withdraw">提现</button>
    </div>
  </div>
</template>
<style lang="less">
.withdraw {
  background: #f60;
  color: #fff;
  width: 90%;
  line-height: 1rem;
  border: 0;
  border-radius: 0.2rem;
  margin: 1rem 5%;
}
.main {
  margin-top: 1rem;
  text-align: center;
  //   border-bottom: 1px solid #ccc;
  text-align: left;
  .line {
    height: 0.12rem;
    background: repeating-linear-gradient(
      -45deg,
      #8fc9f5 0,
      #8fc9f5 50%,
      #f58f8f 50%,
      #f58f8f 100%
    );
    margin-bottom: 0.5rem;
  }
  .nochoose {
    line-height: 1rem;
    text-align: center;
  }
}
.add {
  border: 0;
  background: transparent;
  color: #6b6b6b;
  padding: 0 0.3rem;
  height: 0.5rem;
  line-height: 0.5rem;
  font-size: 0.3rem;
  position: fixed;
  right: 0.3rem;
  top: 0.2rem;
  z-index: 100;
}
</style>
<script>
import Header from "../../components/header/Header.vue";
import API from "../../api/api.js";
import { Dialog } from "vant";
export default {
  data() {
    return {
      carddata: {},
      money: "",
      rate:0
    };
  },
  components: {
    "header-view": Header
  },
  mounted() {
    var system_info = localStorage.getItem("system_info");
    if (system_info) {
      this.rate = JSON.parse(system_info).withdraw_service_charge_rate * 100;
    }
    if (this.$route.query.choose_card_data) {
      var d = JSON.parse(this.$route.query.choose_card_data);
      this.carddata = d;
    }
  },
  methods: {
    withdrawlist() {
      this.$router.push({
        name: "withdrawlist"
      });
    },
    withdraw() {
      var _this = this;
      if (JSON.stringify(_this.carddata) != "{}") {
        Dialog.confirm({
          title: "确认",
          message:
            "此次提现" +
            _this.money +
            ",需要" +
            _this.money * 0.1 +
            "手续费,确认提现吗？"
        }).then(() => {
          _this.$http
            .post(API.withdraw, {
              member_card_id: _this.carddata.id,
              money: _this.money
            })
            .then(res => {
              _this.$vux.toast.text(res.data.msg, "middle");
              if (res.data.code == 200) {
                setTimeout(function() {
                  _this.$router.push({
                    name: "amount"
                  });
                }, 1500);
              }
            });
        });
      } else {
        _this.$vux.toast.text("请选择提现账户", "middle");
      }
    },
    choosecard() {
      this.$router.push({ name: "mycard", query: { choosecard: 1 } });
    }
  }
};
</script>
