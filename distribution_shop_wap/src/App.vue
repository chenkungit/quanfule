<template>
  <div id="app">
    <loading v-if='loadingshow'></loading>
    <transition :name="transitionName">
      <div>
        <keep-alive>
          <router-view v-if="$route.meta.keepAlive" class="child-view"></router-view>
        </keep-alive>
        <router-view v-if="!$route.meta.keepAlive" class="child-view"></router-view>
      </div>
    </transition>
  
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
// import "/src/assets/js/ntkfstat.js";

export default {
  computed: mapGetters(["loadingshow"]),
  data() {
    return {
      transitionName: "slide-left"
    };
  },

  mounted() {
     var that = this;
    if (navigator.userAgent.indexOf("HCApp") > -1) {
      var timer = setInterval(() => {
        if (localStorage.getItem("key") != undefined) {
          clearInterval(timer)
        } else {
          JSBridge.getData("userToken", function(data) {
            var token = data["token"];
            localStorage.setItem("key", token);
          });
        }
      }, 100);
    }
    // smallneng();

  },
  //监听路由的路径，可以通过不同的路径去选择不同的切换效果
  watch: {
    $route(to, from) {
      if (to.path == "/") {
        this.transitionName = "slide-right";
      } else {
        this.transitionName = "slide-left";
      }
    }
  }
};
</script>
<!--<script type="text/javascript" src="http://dl.ntalker.com/js/xn6/ntkfstat.js?siteid=ho_1000" charset="utf-8"></script>-->

<style lang="less">
@import "~vux/src/styles/reset.less";
.child-view {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  transition: all 0.3s cubic-bezier(0.55, 0, 0.1, 1);
}

.slide-left-enter,
.slide-right-leave-active {
  opacity: 0;
  -webkit-transform: translate(100%, 0);
  transform: translate(100%, 0);
}

.slide-left-leave-active,
.slide-right-enter {
  opacity: 0;
  -webkit-transform: translate(-100%, 0);
  transform: translate(-100%, 0);
}
</style>
