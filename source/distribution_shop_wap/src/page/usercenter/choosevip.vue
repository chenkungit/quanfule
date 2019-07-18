<template>
  <div class="choosevip">
        <div class="main">
            正在绑定。。。
        </div>
  </div>
</template>
<style lang="less">
.main {
  margin-top: 2rem;
  text-align: center;
  img {
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
      if (localStorage.getItem("key")) {
        _this.$http
          .post(API.qrcoderelate, {
            key: localStorage.getItem("key"),
            encode: this.$route.query.encode
          })
          .then(res => {
            console.log(res);
            if (res.data.code == 200) {
              _this.$vux.toast.text(res.data.msg, "middle");
              setTimeout(function() {
                _this.$router.push({ name: "usercenter" });
              }, 1500);
            } else {
              _this.$vux.toast.text(res.data.msg, "middle");
              setTimeout(function() {
                var index = localStorage.getItem("beforeindex");
                if (index) {
                  window.location.href = index;
                } else {
                  _this.$router.push({ name: "usercenter" });
                }
              }, 1500);
            }
          });
      } else {
        _this.$vux.toast.text("未登录，正在跳转登陆", "middle");
        localStorage.setItem("beforeindex", window.location.href);
        setTimeout(function() {
          _this.$router.push({ name: "login" });
        }, 1500);
      }
    }
  }
};
</script>
