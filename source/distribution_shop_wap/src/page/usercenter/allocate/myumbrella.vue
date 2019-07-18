<template>
  <div class="share">
      <header-view :title="chooseumbrellaData.user_id?chooseumbrellaData.user_name+'的下级会员':'我的下级会员'"></header-view>
        <div class="main">
            <div class="umbrellalists" v-for="(item,i) in umbrellaLists" :key="i" @click="chooseumbrella(item)">
              <van-swipe-cell :right-width="90">
                <van-cell
                  :border="false"
                  title="ID"
                  :value="item.vip_code"
                />            
                <van-cell
                  :border="false"
                  title="账号"
                  :value="item.user_name"
                />
                <van-cell
                  :border="false"
                  title="等级"
                  :value="item.vip_setting_name"
                />
                <van-button
                  square
                  slot="right"
                  type="info"
                  text="查看下一级"
                  @click="getumbrella(item)"
                />
              </van-swipe-cell>
            </div>
        </div>
  </div>
</template>
<style lang="less">
.main {
  margin-top: 1rem;
  text-align: center;
  .umbrellalists {
    border-bottom: 1px solid #ccc;
    text-align: left;
    .van-swipe-cell__right button {
      height: 100%;
    }
    .van-swipe-cell__left button {
      height: 100%;
    }
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
      umbrellaLists: [],
      chooseumbrellaData: {
        user_id: 0,
        user_name: 0
      },
      num: 1
    };
  },
  components: {
    "header-view": Header
  },
  mounted() {
    this.getumbrella();
  },
  methods: {
    chooseumbrella(item) {
      if (this.$route.query.chooseumbrella) {
        this.$router.push({
          name: "allocate",
          query: {
            choose_umbrella_data: JSON.stringify(item)
          }
        });
      }
    },
    getumbrella(item) {
      if (item) {
        var params = { down_user_id: item.user_id };
      } else {
        var params = {};
      }
      var _this = this;
      _this.$http.get(API.get_down_use, { params: params }).then(res => {
        if (res.data.code == 200) {
          if (res.data.data.collection.length < 1) {
            _this.$vux.toast.text("没有下一级了", "middle");
          } else {
            if (item) {
              this.chooseumbrellaData.user_id = item.user_id;
              this.chooseumbrellaData.user_name = item.user_name;
            }
            _this.umbrellaLists = res.data.data.collection;
          }
        }
      });
    }
  }
};
</script>
