<template>
  <div class="wt">
      <header-view :title="title"></header-view>
      <div class="cate_tab_wrap">
        <div class="tab_category" v-show="$route.query.parent_id">
          <scroller lock-y height=".8rem">
              <div class="category_tab" :style="'width:'+num*100+'px'">
                  <div class="item" :class="{cur:v.cat_id==cat_id}" v-for="(v,i) in parent_list" :key="i" @click="change_cat_id(v.cat_id,v.cat_name)">{{v.cat_name}}</div>
              </div>
          </scroller>
        </div>
        <div class="fliter">
          <div class="fliter_tab">
            <div class="item" :class="{active:fliter_id==1}" @click="change_fliter(1)">综合<i class="iconfont">&#xe614;</i></div>
            <div class="item" :class="{active:fliter_id==2}" @click="change_fliter(2)">销量</div>
            <!-- <div class="item canbuy" :class="{active:fliter_id==5}" @click="change_fliter(5)"><kbd><i class="iconfont" v-show="canbuy">&#xe604;</i></kbd>可订购</div> -->
            <div class="item chang_icon" :class="{active:fliter_id==3}" @click="change_fliter(3)">
              <i class="iconfont" v-if="hs_swt">&#xe67e;</i>
              
              <i class="iconfont" v-else>&#xe663;</i>
            </div>
            <!-- <div class="item" :class="{active:fliter_id==4}" @click="change_fliter(4)">
              筛选<i class="iconfont">&#xe61f;</i>
            </div> -->
          </div>
          <div class="tab_box">
            <div class="tab_item animated fadeIn" v-show="price_swt">
              <div class="cell" @click="blank('price',0,0)"><span>综合</span><i class="iconfont" v-show="sort==0&&order==0">&#xe604;</i></div>
              <div class="cell" @click="blank('price',2,0)"><span>价格降序</span><i class="iconfont" v-show="sort==2&&order==0">&#xe604;</i></div>
              <div class="cell" @click="blank('price',2,1)"><span>价格升序</span><i class="iconfont" v-show="sort==2&&order==1">&#xe604;</i></div>	
            </div>
            <!-- <div class="tab_item animated fadeIn" v-show="time_swt">
              <div class="cell" @click="blank('time',0,0)"><span>全部商品</span><i class="iconfont" v-show="beon==0">&#xe604;</i></div>
              <div class="cell" @click="blank('time',1,0)"><span>上架中</span><i class="iconfont" v-show="beon==1">&#xe604;</i></div>
              <div class="cell" @click="blank('time',3,0)"><span>即将上架</span><i class="iconfont" v-show="beon==3">&#xe604;</i></div>
              
            </div> -->
          </div>

        </div>
     
      </div>
       <scroller lock-x :height="Scroller_hight"  ref="scrollerEvent" @on-scroll-bottom="onScroll_buttom" >
        <div :class="{cate_goods:!hs_swt,cate2_goods:hs_swt}">
          <span class="cate_item" v-for="(v,i) in goods_data"  :key="i">

          <p class="categorybox" v-show="false">{{v.cat_name}}</p>

            <router-link :to="{ name: 'goodsDetail', query:{gshp_id:v.shp_id}}">
              <div class="g_img"><img v-lazy="v.thumbnail"/></div>
              <div class="gs_des">
                <p class="g_name"><span :style="{color:v.mark.color}">{{v.mark.name?"【"+v.mark.name+"】":""}}</span><span style="color:rgb(219, 59, 65)" v-if="v.is_sale==3">【售罄】</span>
                {{v.name}}</p>
                <p class="g_price">
                  <span class="price">{{v.price}}</span>
                  <!-- <span class="mt_price">{{v.market_price}}</span> -->
                  <!-- <span class="y_h" v-for="(vs,k) in v.after_mark"  :key="k">{{vs}}</span> -->

                </p>
              </div>
            </router-link>  
          </span>
        </div>
      </scroller>
  </div>
</template>
<script>
import Header from "../../components/header/Header.vue";
import { Scroller } from "vux";
import API from "../../api/api.js";

export default {
  data() {
    return {
      title: "专题",
      parent_list: [],
      num: 0,

      cat_id: 0,
      beon: 0,
      canbuy:true,
      sort: 0,
      order: 0,
      page: 1,
      limit: 10,
      nowcat:'',
      fliter_id: 1,
      goods_data: [],
      Scroller_hight: "calc(100% - 1.8rem)",
      data_swt: true,
      price_swt: false,
      time_swt: false,
      hs_swt: false,
      nowcate:0,
    };
  },
  components: {
    "header-view": Header,
    Scroller
  },
  mounted() {
    var _this = this;
    if (this.$route.query.cat_name) {
      this.title = this.$route.query.cat_name;
    }

    if (this.$route.query.parent_id) {
      this.Scroller_hight = "calc(100% - 2.6rem)";
      this.fetch(this.$route.query.parent_id).then(res => {
        this.parent_list = res;
        this.num = res.length;
      });
    }
    if (this.$route.query.cat_id) {
      this.cat_id = this.$route.query.cat_id;
      this.get_goods(
        this.cat_id,
        this.beon,
        this.sort,
        this.order,
        this.page,
        this.limit
      ).then(res => {
        res.forEach(function(v,i){
          if(i==0 && _this.goods_data==[]){
              _this.nowcate = v.category;
          }else if(v.category != _this.nowcate){
              _this.nowcate = v.category;
              v.isshow = true;
          }else{
            v.isshow = false;
          }
          _this.goods_data.push(v)
        })
         if (res.length < 10 || res == "") {
            _this.data_swt = false;
          } else {
            _this.data_swt = true;
          }
        // this.goods_data = res;
      });
    }
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
    get_goods(cat_id, beon, sort, order, page, limit) {
      return new Promise((resolve, reject) => {
        this.$http
          .get(API.product_list, {
            params: {
              cat_id: cat_id,
              beon: beon,
              sort: sort,
              order: order,
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
    blank(str, paixu, order) {
      var _this = this;
       this.goods_data = [];
      this.$refs.scrollerEvent.reset({ top: 0 });
      this.data_swt = true;
      this.page = 1;
      if (str == "price") {
        this.sort = paixu;
        this.order = order;
        this.price_swt = false;
      }
      if (str == "time") {
        this.beon = paixu;
        this.order = order;
        this.time_swt = false;
        _this.canbuy = false;
      }
      this.get_goods(
        this.cat_id,
        this.beon,
        this.sort,
        this.order,
        this.page,
        this.limit
      ).then(res => {
        res.forEach(function(v,i){
          if(i==0 && _this.goods_data==[]){
              _this.nowcate = v.category;
          }else if(v.category != _this.nowcate){
              _this.nowcate = v.category;
              v.isshow = true;
          }else{
            v.isshow = false;
          }
          _this.goods_data.push(v)
        })
         if (res.length < 10 || res == "") {
            this.data_swt = false;
          } 
      });
    },
    change_fliter(fliter_id) {
      this.fliter_id = fliter_id;
      var _this = this;
      switch (fliter_id) {
        case 1:
          this.price_swt = !this.price_swt;
          this.time_swt = false;
          break;
        case 2:
          this.time_swt = false;
          this.price_swt = false;
          this.sort = 1;
          this.order = 0;
          this.page=1;
           this.goods_data = [];
          this.$refs.scrollerEvent.reset({ top: 0 });
          
          this.data_swt=true;
          this.get_goods(
            this.cat_id,
            this.beon,
            this.sort,
            this.order,
            this.page,
            this.limit
          ).then(res => {res.forEach(function(v,i){
              if(i==0 && _this.goods_data==[]){
                  _this.nowcate = v.category;
              }else if(v.category != _this.nowcate){
                  _this.nowcate = v.category;
                  v.isshow = true;
              }else{
                v.isshow = false;
              }
              _this.goods_data.push(v)
          })
             if (res.length < 10 || res == "") {
              this.data_swt = false;
            } 
          });
          break;
        case 3:
          this.hs_swt = !this.hs_swt;
          this.time_swt = false;
          this.price_swt = false;
          break;
        case 4:
          this.time_swt = !this.time_swt;
          this.price_swt = false;
          break;
        case 5://是否可订购
            this.time_swt = false;
            this.price_swt = false;
            this.sort = 1;
            this.order = 0;
            this.page=1;
            this.goods_data = [];
            this.$refs.scrollerEvent.reset({ top: 0 });
            
            this.data_swt=true;
            this.canbuy = !this.canbuy;
            if(this.canbuy){
              this.beon = 0;
            }else{
              this.beon = 5;
            }
            this.get_goods(
              this.cat_id,
              this.beon,
              this.sort,
              this.order,
              this.page,
              this.limit
            ).then(res => {res.forEach(function(v,i){
                if(i==0 && _this.goods_data==[]){
                    _this.nowcate = v.category;
                }else if(v.category != _this.nowcate){
                    _this.nowcate = v.category;
                    v.isshow = true;
                }else{
                  v.isshow = false;
                }
                _this.goods_data.push(v)
            })
              if (res.length < 10 || res == "") {
                this.data_swt = false;
              } 
            });
          break;
      }
    },
    change_cat_id(cat_id, cat_name) {
      var _this = this;
      if(cat_id!=this.cat_id){
      this.cat_id = cat_id;
      this.title = cat_name;
      
      this.$refs.scrollerEvent.reset({ top: 0 });
      this.goods_data = [];
      this.page = 1;
      this.data_swt = true;
      this.get_goods(
        cat_id,
        this.beon,
        this.sort,
        this.order,
        this.page,
        this.limit
      ).then(res => {
        res.forEach(function(v,i){
          if(i==0 && _this.goods_data==[]){
              _this.nowcate = v.category;
          }else if(v.category != _this.nowcate){
              _this.nowcate = v.category;
              v.isshow = true;
          }else{
            v.isshow = false;
          }
          _this.goods_data.push(v)
        })
         if (res.length < 10 || res == "") {
            this.data_swt = false;
          } 
      });
      }else{
        return;
      }
    },

    onScroll_buttom() {
      var _this = this;
      if (_this.data_swt) {
        this.page = ++this.page;
        this.data_swt = false;
        this.get_goods(
          this.cat_id,
          this.beon,
          this.sort,
          this.order,
          this.page,
          this.limit
        ).then(res => {
          if (res.length < 10 || res == "") {
            this.data_swt = false;
          } else {
            this.data_swt = true;
          }
          res.forEach(function(v,i){
            if(i==0 && _this.goods_data==[]){
                _this.nowcate = v.category;
            }else if(v.category != _this.nowcate){ 
                _this.nowcate = v.category;
                v.isshow = true;
            }else{
              v.isshow = false;
            }
            _this.goods_data.push(v);
          })
        });
      }
    }
  }
};
</script>
<style lang="less" >
.wt {
  background: white;
}
.cate_tab_wrap {
  margin-top: 0.9rem;
}
.cate2_goods {
  .cate_item {
    a {
      display: flex;
      margin-top: 0.1rem;
      width: 100%;
      .g_img {
        width: 2.4rem;
        height: 2.4rem;
        img {
          width: 100%;
          height: 100%;
        }
      }
      .gs_des {
        flex: 1;
        margin-left: 0.1rem;
       
        .g_name {
          min-height: 0.8rem;
          font-size: 0.24rem;
          text-overflow: ellipsis;
          overflow: hidden;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          text-align: justify;
        }
        .g_price {
          .price {
            color: #c00;
          }
          .mt_price {
            margin-left: 0.4rem;
            text-decoration: line-through;
          }
          .y_h{
             background: #FF0036;
            color: white;
            font-size: 12px;
            border-radius: 4px;
            margin-right: 10px;
            padding: 0 2px;
          }
        }
      }
    }
  }
}
.cate_goods {
  // display: flex;
  // flex-wrap: wrap;
  overflow: hidden;
  .cate_item {
    overflow: hidden;
    // font-size: 0px;
    text-align:left;
    
    a{
      width: 49%;
      display: inline-block;
      overflow: hidden;
      margin-top: 0.1rem;

    .g_img {
      height: 3.72rem;
      display: flex;
      justify-content: space-around;
    }
    .gs_des {
      // padding: 0 0.05rem;
       width:3.72rem;
        margin: 0 auto;
        padding: 0.1rem 0.15rem 0;
      .g_name {
        min-height: 0.8rem;
        font-size: 0.24rem;
        text-overflow: ellipsis;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
          text-align: justify;
      }
      .g_price {
        .price {
          color: #c00;
        }
        .mt_price {
          margin-left: 0.4rem;
          text-decoration: line-through;
        }
        .y_h{
             background: #FF0036;
            color: white;
            font-size: 12px;
            border-radius: 4px;
            margin-right: 10px;
            padding: 0 2px;
          }
      }
    }
  }
  }
}
.fliter {
  .fliter_tab {
    height: 0.8rem;
    line-height: 0.8rem;
    display: flex;
    justify-content: space-around;
    border-bottom: 1px solid #f1f1f1;
    .active {
      color: #FF0036;
    }
    .chang_icon {
      width: 1rem;
      text-align: center;
    }
  }
  .tab_box {
    position: relative;
    .tab_item {
      position: absolute;
      background: white;
      width: 100%;
      left: 0;
      top: 0;
      z-index: 2;
      .cell {
        padding: 0 0.3rem;
        height: 0.6rem;
        line-height: 0.6rem;
        display: flex;
        justify-content: space-between;
        i {
          color: #FF0036;
        }
      }
    }
  }
}
.tab_category {
  background: white;

  .category_tab {
    height: 0.8rem;
    line-height: 0.8rem;
    .item {
      display: inline-block;
      width: 100px;
      text-align: center;
      font-size: 0.26rem;
      position: relative;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: pre;
    }
    .cur::before {
      content: "";

      position: absolute;
      bottom: 1px;
      left: 50%;
      width: 1rem;
      height: 1px;
      margin-left: -0.5rem;
      background: #FF0036;
    }
  }
}
.categorybox{display: block;width: 100%;text-align:center;float: left;    border-top: 0.1rem solid #eee;line-height: 0.5rem;padding-top: 0.1rem;}
.canbuy{
  font-size: 0.26rem;
  kbd{
    display: inline-block;width: 0.28rem;height: 0.28rem;border: 0.02rem solid #FF0036;line-height:0.28rem;margin-right:0.1rem;
    i{
      font-size: 0.20rem;color: #FF0036;
    }
  }
}
</style>
