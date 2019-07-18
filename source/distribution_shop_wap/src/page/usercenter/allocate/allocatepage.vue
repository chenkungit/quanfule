<template>
  <div class="share">
    <header-view :title="'下发积分'" :urlname="'usercenter'"></header-view>
    <button type="button" class="add" @click="transferlist()">下发记录</button>
    <div class="main">
      <div class="choose" @click="chooseumbrella">
        <div class="nochoose" v-if="JSON.stringify(umbrelladata) == '{}'">选择要下拨用户</div>
        <div class="haschoose" v-else>
          <van-swipe-cell>
            <van-cell :border="false" title="ID" :value="umbrelladata.vip_code"/>
            <van-cell :border="false" title="用户名" :value="umbrelladata.user_name"/>
            <van-cell :border="false" title="等级" :value="umbrelladata.vip_setting_name"/>
          </van-swipe-cell>
        </div>
      </div>
      <div class="line"></div>
      <van-cell-group>
        <van-field v-model="point" placeholder="请输入下发积分"/>
      </van-cell-group>
      <span style="text-align:left;font-size:0.24rem">*手续费为{{rate}}%*</span>
      <button type="button" class="transfer" @click="transfer">下发</button>
    </div>
  </div>
</template>
<style lang="less">
.transfer {
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
import Header from "../../../components/header/Header.vue";
import API from "../../../api/api.js";
import { Dialog } from "vant";
export default {
  data() {
    return {
      umbrelladata: {},
      point: "",
      rate: 0
    };
  },
  components: {
    "header-view": Header
  },
  mounted() {
   var system_info = localStorage.getItem("system_info");
    if (system_info) {
      this.rate=JSON.parse(system_info).transfer_service_charge_rate*100;
    }

    if (this.$route.query.choose_umbrella_data) {
      var d = JSON.parse(this.$route.query.choose_umbrella_data);
      this.umbrelladata = d;
    }
  },
  methods: {
    transferlist() {
      this.$router.push({
        name: "allocatelist"
      });
    },
    transfer() {
      var _this = this;
      if (JSON.stringify(_this.umbrelladata) != "{}") {
        Dialog.confirm({
          title: "确认",
          message:
            "此次下发" +
            _this.point +
            "积分,需要" +
            _this.point * 0.05 +
            "积分手续费,确认下发吗？"
        }).then(() => {
          _this.$http
            .post(API.transfer, {
              to_user_id: _this.umbrelladata.user_id,
              point: _this.point
            })
            .then(res => {
              _this.$vux.toast.text(res.data.msg, "middle");
              if (res.data.code == 200) {
                _this.point = "";
              }
            });
        });
      } else {
        _this.$vux.toast.text("请选择下发账户", "middle");
      }
    },
    chooseumbrella() {
      this.$router.push({ name: "myumbrella", query: { chooseumbrella: 1 } });
    }
  }
};
</script>
