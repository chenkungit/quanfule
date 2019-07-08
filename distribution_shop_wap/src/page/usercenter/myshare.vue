<template>
  <div class="share">
      <header-view :title="'我的二维码'"></header-view>
        <div class="main">
            <img :src="img" alt="">
        </div>
  </div>
</template>
<style lang="less">
.main{
    margin-top: 2rem;
    text-align: center;
    img{
        margin: 0;
        width: 100%;
    }
}
</style>
<script>
import Header from "../../components/header/Header.vue";
import API from "../../api/api.js";
export default {
  data() {
    return {
      img: ""
    };
  },
  components: {
    "header-view": Header
  },
  mounted() {
    this.getshare();
  },
  methods: {
    getshare() {
      var _this = this;
      _this.$http
        .get(API.qrcodeshares, {
          params: { key: localStorage.getItem("key"),redirect_url:'http://'+window.location.host+'/mobile/choosevip' }
        })
        .then(res => {
          if (res.data.code == 200) {
            _this.img = res.data.data.img;
          } else {
            var index = localStorage.getItem("beforeindex");
            if (index) {
              window.location.href = index;
            } else {
              _this.$router.push({ name: "usercenter" });
            }
          }
        });
    }
  }
};
</script>
