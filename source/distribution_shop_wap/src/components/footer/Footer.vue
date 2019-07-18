<template>
  <footer>
    <div class="footnav clearfix">
      <router-link
        v-for="(item,i) in footer"
        :key="i"
        :class="{active:item.isactive}"
        :to="item.urlname"
      >
        <kbd>
          <i class="iconfont" v-html="item.icon"></i>
          <em
            v-show="item.shownum&&cartnum!=0"
            v-if="cartnumnew==0"
            :class="{'none':cartnumnew==0}"
          >0</em>
          <em
            v-show="item.shownum&&cartnum!=0"
            v-else-if="cartnumnew!=0&&cartnumnew!=''&&cartnumnew"
          >{{cartnumnew}}</em>
          <em v-show="item.shownum&&cartnum!=0" v-else>{{cartnum}}</em>
        </kbd>
        <span>{{item.name}}</span>
      </router-link>
    </div>
  </footer>
</template>
<script>
import { setTimeout } from "timers";
export default {
  props: ["cartnumnew"],
  data() {
    return {
      cartnum: 0,
      footer: [
        {
          name: "首页",
          urlname: "../index",
          title: "/index",
          icon: "&#xe61f;",
          isactive: true,
          shownum: false
        },
        {
          name: "分类",
          urlname: "../sort",
          title: "/sort",
          icon: "&#xe602;",
          isactive: false,
          shownum: false
        },
        {
          name: "购物车",
          urlname: "../shopcart",
          title: "/shopcart",
          icon: "&#xe801;",
          isactive: false,
          shownum: true
        },
        {
          name: "会员中心",
          urlname: "../usercenter",
          title: "/usercenter",
          icon: "&#xe64e;",
          isactive: false,
          shownum: false
        }
      ]
    };
  },
  created() {
    // setTimeout(function(){
    // console.log('cartnumnew=='+this.cartnumnew)
    this.cartnum = localStorage.getItem("cartnum") || 0;
    // localStorage.setItem("cartnum",this.cartnum)
    // },1000)

    for (var i = 0; i < this.footer.length; i++) {
      if (this.footer[i].title == this.$route.path) {
        this.footer[i].isactive = true;
      } else {
        this.footer[i].isactive = false;
      }
    }
  },
  methods: {}
};
</script>
<style lang='less' scoped>
footer {
  position: fixed;
  left: 0;
  bottom: 0;
  z-index: 10000;
  height: 0.98rem;
  box-sizing: border-box;
  padding: 0.15rem;
  width: 100%;
  background: #f2f2f2;
  text-align: center;
  font-family: Microsoft;
  -webkit-overflow-scrolling: touch; 
}
.none {
  display: none !important;
}
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

.clearfix {
  *zoom: 1;
}
.clearfix:after {
  content: "";
  display: block;
  clear: both;
}

.footnav {
  // overflow: hidden;
  // position: fixed;
  // left: 0;
  // bottom: 0;
  // z-index: 10000;
  // height: 0.98rem;
  // box-sizing: border-box;
  // padding: 0.15rem;
  // width: 100%;
  // background: #f2f2f2;
  // text-align: center;
  // font-family: "微软雅黑";
  a {
    width: 25%;
    float: left;
    display: inline-block;
    font-size: 0.2rem;
    color: #333;
  }
  i {
    display: inline-block;
    font-size: 0.4rem;
    line-height: 1;
    color: #6b6b6b;
  }
  span {
    display: block;
    margin-top: -0.07rem;
  }
  .active {
    * {
      color: #ff0036;
    }
    em {
      color: #fff;
    }
  }
  img {
    height: 0.4rem;
    display: block;
    margin: auto;
  }
  kbd {
    position: relative;
    display: inline-block;
  }
  em {
    font-style: normal;
    display: inline-block;
    display: inline-block;
    background: #d21018;
    font-family: cursive;
    font-size: 12px;
    color: #fff;
    border-radius: 50%;
    width: 0.35rem;
    height: 0.35rem;
    line-height: 0.35rem;
    position: absolute;
    right: -0.18rem;
    top: -0.18rem;
  }
}
</style>
