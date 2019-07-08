<template>
  <header :class="{'Android':is_Android}">
  
    <div class="h_left" @click="close_win()"><i class="iconfont">&#xe622;</i><span></span></div>
  
    <div class="h_center">{{title}}</div>
  
    <div class="h_right"><i class="iconfont"></i></div>
  
  </header>
</template>
<script>
export default {
  props: {
    title: {
      type: String,

      default: ""
    },

    swt: {
      type: Boolean,

      default: false
    },
    urlname: {
      type: String,

      default: ""
    }
  },

  data() {
    return {
      is_Android: false
    };
  },

  methods: {
    close_win() {
      if (this.urlname) {
        this.$router.replace({
          name: this.urlname
        });
      } else {
        if (this.swt) {
          window.close();
        } else {
          // console.log(window.history.length)
          // console.log(this.$router.currentRoute)
          if (window.history.length <= 1 || !localStorage.getItem("key")) {
            this.$router.push({ name: "index" });
          } else {
            window.history.go(-1);
          }
        }
      }
    }
  },

  mounted() {
    // console.log("上一页："+document.referrer)
    // console.log(window.history.length)
    this.is_Android = navigator.userAgent.indexOf("Android") > -1;
  }
};
</script>
<style lang='less' scoped>
.Android {
  .h_left {
    flex: 1;

    display: flex;

    // justify-content: center;
    line-height: 1.2rem;
  }

  .h_center {
    flex: 8.5;

    display: flex;

    // justify-content: center;
    line-height: 1.2rem;
  }
}

header {
  display: flex;

  height: 0.9rem;

  background: white;

  border-bottom: 0.01rem solid #f1f1f1;

  position: fixed;

  top: 0;

  width: 100%;

  z-index: 2;

  .h_left {
    flex: 1;

    display: flex;

    justify-content: center;

    align-items: center;

    i {
      font-size: 0.4rem;
    }
  }

  .h_center {
    flex: 8.5;

    display: flex;

    justify-content: center;

    align-items: center;
  }

  .h_right {
    flex: 1;

    display: flex;

    justify-content: center;

    align-items: center;

    i {
      font-size: 0.4rem;
    }
  }
}
</style>
