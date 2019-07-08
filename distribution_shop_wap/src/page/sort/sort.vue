<template>
  <div class="wt">
      <div class="header">
          <router-link :to="{name:'search'}">
            分类
              <i class="iconfont">&#xe684;</i>
          </router-link>
      </div>
      <div class="c_main">
          <div class="left">
              <scroller lock-x height="calc(100% - 2rem)"  >
                  <div class="con">
                      <div class="item" :class="{active:v.cat_id==cat_id}" v-for="(v,i) in parent_list" :key="i" @click="tab_sort(v.cat_id,v.banner,v.cat_name)">
                          {{v.cat_name}}
                      </div>
                  </div>
              </scroller>       
              
          </div>
          <div class="right">
              <scroller lock-x height="calc(100% - 2rem)"  >
                  <div class="con">
                      <!-- <div class="Bgoodimg" v-if="Bgoodimg!=''"><img :src="Bgoodimg"/></div> -->
                      <div class="cat_name">{{cat_name}}</div>
                      <div class="sort_list">
                           <div class="item animated zoomIn"  v-for="(v,i) in right_data" :key="i" @click="go_category(v.cat_id,v.cat_name)">
                                <div class="img_box">
                                    <img :src="v.mobile_icon"/>                             
                                </div>
                                <p class="name">{{v.cat_name}}</p>
                            </div>
                      </div>
                     
                  </div>
              </scroller>  
          </div>
      </div>
      <foot></foot>
  </div>
</template>
<script>

import foot from "../../components/footer/Footer";
import { Scroller } from "vux";
import API from "../../api/api.js";

export default {
  data() {
    return {
      parent_list: [],
      cat_id: "",
      Bgoodimg: "",
      cat_name: "",
      right_data: []
    };
  },
  components: {
    Scroller,foot
  },
  created() {
    // console.log("在这里呀")
    this.fetch().then(res => {
      this.parent_list = res;
      this.cat_id = res[0].cat_id;
      this.Bgoodimg = res[0].banner.img_url;
      this.cat_name = res[0].cat_name;
      this.fetch(this.cat_id).then(res => {
        this.right_data = res;
      });
    });
  },
  methods: {
    fetch(parent_id) {
      return new Promise((resolve, reject) => {
        this.$http
          .get(API.parent_list, {
            params: {
              parent_id: parent_id
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
    tab_sort(cat_id, Bgoodimg, cat_name) {
      if (this.cat_id != cat_id) {
        this.cat_id = cat_id;
        this.Bgoodimg = Bgoodimg.img_url;
        this.cat_name = cat_name;
        this.right_data = [];
        this.fetch(this.cat_id).then(res => {
          this.right_data = res;
        });
      } else {
        return;
      }
    },
    go_category(cat_id, cat_name) {
      this.$router.push({
        name: "category",
        query: { cat_id: cat_id, parent_id: this.cat_id, cat_name: cat_name }
      });
    }
  }
};
</script>
<style lang="less" scoped>
.wt {
  background: white;
  overflow: hidden;
}
.c_main {
  display: flex;
  height: calc(100% - 2rem);
  .left {
    width: 1.68rem;
    border-right: 1px solid #f5f5f5;
    .con {
      .item {
        height: 0.88rem;
        line-height: 0.88rem;
        text-align: center;
        font-size: 0.24rem;
        position: relative;
      }
      .active {
        color: #FF0036;
        font-size: 0.32rem;
        font-weight: 600;
      }
    }
  }
  .right {
    flex: 1;
    .con {
      .Bgoodimg {
        width: 100%;
        img {
          width: 5.42rem;
          height: 1.64rem;
          margin: 0.2rem auto;
        }
      }
      .cat_name {
        text-align: center;
        height: 0.8rem;
        line-height: 0.8rem;
        position: relative;
      }
      .cat_name::before {
        content: "";
        width: .4rem;
        height: 2px;
        background: #e0e0e0;
        position: absolute;
        left: 30%;
        top: 50%;
      }
      .cat_name::after {
        content: "";
        width: 0.4rem;
        height: 2px;
        background: #e0e0e0;
        position: absolute;
        right: 30%;
        top: 50%;
      }
      .sort_list {
        display: flex;
        flex-wrap: wrap;
      }
      .item {
        width: 33.3%;
        display: flex;
        flex-direction: column;
        align-items: center;

        .img_box {
          width: 1.2rem;
          height: 1.2rem;
          img {
            width: 100%;
          }
        }
        .name {
          font-size: 0.24rem;
        }
      }
    }
  }
}
.header {
  height: 1rem;
  border-bottom: 1px solid #f5f5f5;
  background: #f2f2f2;
  overflow: hidden;
  text-align: center;
  line-height: 1rem;
  color: #666;
  font-size: 0.35rem;
  i{
    font-size: .4rem;
    line-height: 1rem;
    float: right;
    margin-right:0.3rem; 
  }
}
</style>
    