<template>
  <div class="index_page">
    <div class="header_wrap">
      <div class="index_header">
        <div class="logo">
          全福乐
        </div>
        <div class="search" @click="go_search">
          <div class="search_box">
            <i class="iconfont">&#xe684;</i>
            <span>搜索商品名称</span>
          </div>
        </div>
        <div class="logo">
          <router-link :to="{name:'usercenter'}">
            <i class="iconfont" style="font-size:0.5rem;">&#xe64e;</i>
          </router-link>
        </div>
      </div>
      <scroller lock-y :bounce="false">
        <div class="tab_wrap" :style="'white-space:nowrap;  '">
          <div class="item active">
            <span>推荐</span>
          </div>
          <div
            class="item"
            v-for="(v,i) in index_data.nav"
            @click="tab(v.redirect_type,v.redirect_id)"
            :key="i"
          >
            <span>{{v.n_name}}</span>
          </div>
        </div>
      </scroller>
    </div>
    <div style="margin-top:1.9rem;">
      <!-- <scroller lock-x height="calc(100% - 1.9rem)" :bounce="false"> -->
      <v-scroll :on-refresh="onRefresh" :on-infinite="onInfinite" style="margin-bottom:0.98rem;">
        <div>
          <swiper dots-position="center" :auto="true" :loop="true">
            <swiper-item v-for="(v,i) in  index_data.carousel" :key="i">
              <img
                :src="v.img_url"
                class="animated fadeIn"
                style="width:100%;height:auto;"
                @click="go_page(v.redirect_type,v.redirect_id)"
              >
            </swiper-item>
          </swiper>

          <div v-show="toutiao!=''" class="toutiao">
            <span></span>

            <swiper
              :auto="true"
              direction="vertical"
              :show-dots="false"
              class="t_sw"
              :loop="true"
              :interval="2000"
              height="40px"
            >
              <swiper-item v-for="(v,i) in index_data.toutiao" :key="i">
                <p
                  class="t_item"
                  @click="go_page(v.redirect_type,v.redirect_id)"
                >{{v.carousel_name}}</p>
              </swiper-item>
            </swiper>
          </div>

          <div v-show="index_data.middle_banner" class="middle_banner">
            <div
              class="item animated pulse"
              v-for="(v,i) in index_data.middle_banner"
              @click="go_page(v.redirect_type,v.redirect_id,v.carousel_name)"
              :key="i"
            >
              <img v-lazy="v.img_url">
            </div>
          </div>

          <div class="jrdp" v-show="jrdp!=''">
            <div class="jrdp_header">限时折扣</div>

            <swiper dots-position="center" :auto="true" :loop="true">
              <swiper-item v-for="(v,i) in jrdp" :key="i">
                <img
                  :src="v.img_url"
                  class="animated fadeIn"
                  style="width:100%;height:auto;"
                  @click="go_page(v.redirect_type,v.redirect_id)"
                >
              </swiper-item>
            </swiper>

            <!-- <div class="jrdp_list">
    
                <div class="item" v-for="(v,i) in jrdp" @click="go_page(v.redirect_type,v.redirect_id)" :key="i">
    
                    <img v-lazy="v.img_url" alt="" class="animated bounceInLeft">
    
                </div>
    
            </div>-->
          </div>
          <div class="theme">
            <div class="item" v-for="(v,i) in theme" :key="i">
              <div class="theme_header">
                <span>{{v.theme_name}}</span>
              </div>

              <div class="theme_banner" @click="go_page(v.redirect_type,v.redirect_id)">
                <img :src="v.theme_banner">
              </div>

              <div class="theme_goods">
                <div class="t_goods" v-for="(val,j) in v.children_goods" :key="j">
                  <router-link :to="{ name: 'goodsDetail', query:{gshp_id:val.id}}">
                    <div class="g_img">
                      <img v-lazy="val.thumbnail">
                    </div>
                    <p class="g_name">
                      <span
                        :style="{color:val.mark.color?val.mark.color:'red'}"
                      >{{val.mark.name?"【"+val.mark.name+"】":""}}</span>
                      {{val.name?val.name:"暂无描述"}}
                    </p>
                    <p class="g_price">
                      <span class="buy_price">{{val.price}}</span>
                      <!-- <span class="market_price">{{val.market_price}}</span> -->
                      <!-- <span class="y_h" v-for="(vs,k) in val.after_mark" :key="k">{{vs}}</span> -->
                    </p>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- </scroller>-->
      </v-scroll>
      <foot></foot>
    </div>
  </div>
</template>
<script>
import foot from "../../components/footer/Footer";
import API from "../../api/api.js";
import utils from "../../utils/utils.js";
import v_scorll from "../../components/b_scorll/b_scorll";
import { Dialog } from "vant";

import { Swiper, SwiperItem, Scroller } from "vux";

export default {
  components: {
    Swiper,
    foot,
    SwiperItem,
    Scroller,
    "v-scroll": v_scorll
  },

  data() {
    return {
      index_data: {},
      toutiao: "",
      jrdp: [],
      theme: [],
      page: 1,
      limit: 2,
      num: 0, //多个tab
      data_swt: true
    };
  },

  created() {
    // console.log($)
    this.fetch(this.page, this.limit).then(res => {
      this.index_data = res;
      this.toutiao = res.toutiao;
      this.jrdp = res.jrdp;
      this.num = res.nav.length;
      this.theme = res.theme;
    });
    // Dialog.confirm({
    //   title: "APP下载",
    //   message: "APP功能更完善，体验更流畅哦！",
    // }).then(() => {
    //    window.location.href = "";
    // })
    // console.log(this.index_data.toutiao);
    var _this = this;
  },

  methods: {
    fetch(page, limit) {
      return new Promise((resolve, reject) => {
        this.$http
          .get(API.index, {
            params: {
              page: page,
              limit: limit
            }
          })
          .then(res => {
            resolve(res.data.data);
          })
          .catch(res => {
            reject(res);
          });
      });
    },

    tab(redirect_type, redirect_id) {
      this.go_page(redirect_type, redirect_id);
    },

    go_apge(url, name) {
      window.location.href = url;
    },
    go_page(redirect_type, redirect_id, name) {
      // console.log(redirect_type, redirect_id);
      switch (redirect_type) {
        // 商品详情
        case 1:
          this.$router.push({
            name: "goodsDetail",
            query: {
              gshp_id: redirect_id
            }
          });
          break;
        // 分类
        case 2:
          this.$router.push({
            name: "category",
            query: { cat_id: redirect_id }
          });

          break;
        // 活动页 webview
        case 3:
          this.go_apge(redirect_id, name);
          break;
        default:
          break;
      }
    },
    go_search() {
      this.$router.push({ name: "search" });
    },
    onRefresh() {
      // this.fetch(1, this.limit);
    },
    onInfinite() {
      if (this.data_swt) {
        this.data_swt = false;
        this.page = ++this.page;
        this.fetch(this.page, 2).then(res => {
          if (res.theme.length < 2) {
            this.data_swt = false;
          } else {
            this.data_swt = true;
          }

          for (var i in res.theme) {
            // let arr=[];
            // if(this.page%2==0){
            // res.theme[i].children_goods=res.theme[i].children_goods[0]
            // }

            this.theme.push(res.theme[i]);
          }
          // console.log(this.theme)
        });
      }
    }
  }
};
</script>
<style lang="less">
.index_page {
  background: #f2f2f2;
}
.header_wrap {
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 3;
}
.index_header {
  height: 1rem;
  background: #f2f2f2;
  display: flex;
  .logo {
    width: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    img {
      width: 0.9rem;
      height: 0.9rem;
    }
  }
  .search {
    flex: 1;
    display: flex;
    align-items: center;
    .search_box {
      width: 96%;
      margin: auto;
      height: 0.7rem;
      border: 1px solid #e5e5e5;
      border-radius: 0.1rem;
      display: flex;
      align-items: center;
      i {
        margin-left: 0.1rem;
        color: #dadada;
      }
      span {
        margin-left: 0.2rem;
        color: #999;
      }
      background: white;
    }
  }
}

.theme {
  .item {
    background: white;

    .theme_header {
      text-align: center;
      height: 0.8rem;
      line-height: 0.8rem;
      font-size: 0.35rem;
      position: relative;
      color: #333;
      letter-spacing: 0.08rem;
      border-top: 0.15rem solid #eee;
      font-weight: 600;
      box-sizing: content-box;
      span {
        position: relative;
      }
    }
    .theme_header span::after {
      right: -1rem;
    }

    .theme_banner {
      width: 100%;
      // margin-top: .16rem;
      img {
        width: 100%;
      }
    }

    .theme_goods {
      display: flex;

      flex-wrap: wrap;

      .t_goods {
        width: 50%;

        margin-bottom: 0.1rem;

        .g_img {
          margin: 0.04rem;
          margin-top: 0.12rem;
          height: 3.67rem;
          display: -webkit-box;
          display: -webkit-flex;
          display: flex;
          -webkit-justify-content: space-around;
          justify-content: space-around;
          overflow: hidden;
          img {
            display: block;
            height: inherit;
          }
        }

        .g_name {
          text-overflow: ellipsis;

          overflow: hidden;

          display: -webkit-box;

          -webkit-line-clamp: 2;

          -webkit-box-orient: vertical;

          font-size: 0.24rem;

          min-height: 0.8rem;
          padding: 0 0.2rem;
        }

        .g_price {
          padding-left: 0.2rem;
          height: 0.5rem;
          overflow: hidden;
          .buy_price {
            color: #c00;
          }

          .market_price {
            text-decoration: line-through;
            margin-left: 0.5rem;
          }
          .y_h {
            background: #ff0036;
            color: white;
            font-size: 12px;
            border-radius: 4px;
            margin-right: 10px;
            padding: 0 4px;
            display: inline-block;
          }
        }
      }
    }
  }
}

.jrdp {
  .jrdp_header {
    font-size: 0.34rem;

    position: relative;

    text-indent: 0.2rem;

    height: 0.8rem;

    line-height: 0.8rem;
    background: white;
  }

  .jrdp_header::after {
    position: absolute;

    content: "";

    width: 2px;

    background: #ff0036;

    height: 0.4rem;

    left: 0.01rem;

    top: 0.2rem;
  }

  .jrdp_list {
    .item {
      padding: 0 0.04rem;

      img {
        width: 100%;
      }
    }
  }
}

.tab_wrap {
  z-index: 2;
  height: 0.6rem;
  line-height: 0.6rem;
  background: #f2f2f2;

  .item {
    min-width: 40px;
    padding: 0 10px;
    text-align: center;
    display: inline-block;
  }
  .active {
    color: #ff0036;
    span {
      border-bottom: 1px solid #ff0036;
      display: inline-block;
      line-height: 0.5rem;
    }
  }
}

.toutiao {
  display: flex;

  height: 40px;

  background: white;

  span {
    line-height: 40px;

    font-size: 0.34rem;

    color: #ff0036;

    padding: 0 0.1rem;

    position: relative;
  }

  span::after {
    position: absolute;

    content: "";

    width: 2px;

    height: 20px;

    background: #ff0036;

    top: 10px;

    right: 0;
  }

  .t_sw {
    flex: 1;

    .t_item {
      padding: 0 0.15rem;

      height: 40px;

      line-height: 40px;

      font-size: 0.26rem;

      overflow: hidden;

      text-overflow: ellipsis;

      white-space: nowrap;
    }
  }
}

.middle_banner {
  // display: flex;

  // align-items: left;

  // justify-content: space-around;

  // flex-wrap: wrap;
  overflow: hidden;

  .item {
    width: 3.7rem;
    height: 2.88rem;
    margin-bottom: 0.04rem;
    float: right;
    img {
      width: 100%;
      height: 100%;
    }
  }
  .item:first-of-type {
    float: left;
    height: 5.8rem;;
  }
}
.header_wrap div:nth-of-type(2) {
  background: #f2f2f2;
}
.vux-slider > .vux-swiper {
  height: 2.9rem !important;
}
</style>
