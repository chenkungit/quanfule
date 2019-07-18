<template>
  <div class="gd">
    <header-view :title="'商品详情'"></header-view>
      <div>  
        <swiper :show-dots='true' dots-position='center' dots-class="sw_dot" :loop='true' :auto='true' :height="'7.5rem'" :class="{'mt9':is_inapp}">
          <swiper-item v-for="(v,i) in dataList.pictures" :key="i"><img :src="v.img_url" width="100%" /></swiper-item>
        </swiper>
        <!--商品标题s -->
        <div class="g_des">
          <div class="title">
            <div class="name"><span class="has_presale" v-show="has_presale">【预售】</span><span class="has_presale" v-show="dataList.marketing_type==1">【{{djname.dj_name}}】</span>{{dataList.name}}</div>
            <div class="sotck"> 
              <div> 库存</div>
              <div class="num">{{dataList.stock}}</div>
            </div>
          </div>
          <!-- <div v-show="has_presale" class="predate">发货时间：{{predate}}</div>
          <div class="d_tips"> {{dataList.tbsm}} </div>
          <div v-show="dataList.marketing_type==1" class="predate">预定开始时间：{{djname.beg_time}}</div>
          <div v-show="dataList.marketing_type==1" class="predate">预定结束时间：{{djname.end_time}}</div> -->
          <div class="price">
            
            <span class="now_price" v-if="is_group!=0"><small>首单价￥</small>{{dataList.new_price}}</span>
            <!--<span class="now_price" v-else-if="isko==0">{{dataList.price}}</span>-->
            <span class="now_price" v-else>{{dataList.price}}</span>
            <span class="market_price">{{dataList.market_price}}</span>
          </div>
        </div>
        <!-- 发货地 属性 配送范围s -->
        <div class="attr">
          <div class="item" @click="dialog_swt=!dialog_swt">
            <div class="left">
              <span class="name">{{is_choose_attr!=''?'已选择':'请选择属性'}}</span><span class="is_choose color7">{{is_choose_attr}}</span>
            </div>
            <div class="right iconfont">&#xe713;</div>
          </div>
          <!-- <div class="cell2" v-if="is_group==0">
            <div v-for="(activity,i) in dataList.activities_list" :key="i">
            <div class="left">{{activity.type}} </div>
            <div class="right">{{activity.name}} </div>
            </div>
          </div> -->
        </div>

        <div class="attr">
           <!-- <div class="cell">
            <div class="left">发货地 </div>
            <div class="right">{{dataList.sendAddress}} </div>
          </div> -->
          <div class="cell">
            <div class="left">配送范围</div>
            <div class="right" v-if="dataList.shipping_desc!=''">{{dataList.shipping_desc}} </div>
            <div class="right" v-else>全国</div>
          </div>
          <div class="cell" >
            <div class="left">温馨提示</div>
            <div class="right" v-if="dataList.tishi_desc!=''">{{dataList.tishi_desc}}</div>
            <div class="right" v-else>如有问题可联系商家</div>
          </div>
        </div>

        <!-- 用户评价 -->
        <div class="comment" v-show="cmmentshow">
            <p>用户评价（{{dataList.comment_count}}）<span v-show="dataList.comment_count>1" @click="gojundge()">查看全部<i class="iconfont">&#xe713;</i></span></p>
            <div class="comcont">
                <div class="comtop">
                  <span class="compic"><img :src="commentData.avatar" alt=""></span>
                  <span class="comname">{{commentData.nick}}</span>
                  <span class="star">
                    <i v-for="(item,i) in starData" :key="i">
                      <img v-if="item.value==false" src="../../assets/image/star_no.png" alt="">
                      <img v-else src="../../assets/image/star_yes.png" alt="">
                    </i>
                  </span>
                  <span class="face">
                    <img v-if="commentData.comment_rank==1" src="../../assets/image/face1.png" alt="">
                    <img v-if="commentData.comment_rank==2" src="../../assets/image/face2.png" alt="">
                    <img v-if="commentData.comment_rank==3" src="../../assets/image/face3.png" alt="">
                    <img v-else src="../../assets/image/face4.png" alt="">
                  </span>
                </div>
                <div class="comtime">{{commentData.add_time}} <span>{{commentData.goods_attr}}</span></div>
                <div class="comtext">{{commentData.content}}</div>
                <div class="comimgbox">
                  <div class="comimg" v-for="(pic,i) in commentData.imgs" :key="i">
                    <img :src="pic" alt="" class="heheimg">
                  </div>
                </div>
            </div>
        </div>
  
        <!-- 发货地 属性 配送范围end -->  
        <!-- 图文详情 规格参数 售后服务s -->
  
        <div class="pic" id="pic">
          <div class="nav" :class="{fix:scrolled_swt}">
            <div v-for="(v,i) in pic_data" @click="tab_pic(i)" :class="{active:tab_id==i?true:false}" :key="i">{{v.pic_name}}</div>
          </div>
  
          <div class="tab">
            <div class="t_item " v-html="dataList.descpt" v-show="tab_id==0?true:false"></div>
            <!-- <div class="t_item " v-show="tab_id==1?true:false" v-html="dataList.asked_question"></div> -->
            <div class="t_item " v-show="tab_id==2?true:false" v-if="shouhouImg!=''" v-html="shouhouImg"> </div>
            <div class="t_item " v-show="tab_id==2?true:false" v-else>
              <div class="cell" v-for="(v,i) in properties.pro" :key="i">
                <span>{{v.attr_name}}</span><span>{{v.attr_value}}</span>
              </div>
            </div>
          </div>
        </div>
        <!-- 图文详情 规格参数 售后服务end -->
      </div>

        <div class="footer2" v-show="noshop">暂无现货，快去看看其他商品吧</div>
        <div class="footer">
            <!-- <div class="item one" @click="one_buy">
              <span>{{dataList.price}}</span>
              <span>单独购买</span>
            </div>
            <div class="item more" @click="check_out">
              <span>{{dataList.price}}</span>
              <span>一键拼团</span>
            </div> -->
            <!-- <div class="block" @click="openchat()"><i class="iconfont">&#xe676;</i><span>客服</span></div> -->
            <router-link to="shopcart" class="block"><i class="iconfont">&#xe801;</i> <span>购物车</span><em>{{cartnum}}</em></router-link>
            <!-- <div :class="{'block2':true,'alread':already}" v-if="already" v-show="noshop">{{yuding}}</div>
            <div :class="{'block2':true,'alread':already}" v-else @click="show=true" v-show="noshop">{{yuding}}</div> -->
            <div :class="{'block2':true,'alread':already}" @click="buynow" v-if="is_group!=0">立即购买</div>
            <div class="blockbox" v-show="!noshop && is_group==0">
              <div class="block" @click="addcart">加入购物车</div>
              <div class="block" @click="buynow">立即购买</div>
            </div>   
        </div>
      
        <!-- 属性多个维度s -->
        <transition :name="transitionName">
          <div class="dialog" v-show="dialog_swt">
            <div class="bg_model" @click="dialog_swt=!dialog_swt"></div>
            <div class="dig_con">
              <div class="header">
                <div class="left">
                  <img :src="Now_src!=''?Now_src:dataList.thumbnail" />
                </div>
                <div class="right">
                  <p class="price">
                    {{price_data.result_danjia}}
                    <span>库存：{{price_data.stock}}</span>
                  </p>
                  <p v-if="properties.spe!=''">{{is_choose_attr}}</p>
                  <p v-else>该商品暂无规格属性</p>
                </div>
              </div>
              <div class="spe">
                <div class="item" v-for="(v,i,index) in properties.spe" :key="i">
                  <p>{{v.name}} </p> 
                  <div class="btn_list" :data-index="index">
                    <x-button v-for="(val,j) in v.values" :key="j" :data-id="val.goods_attr_id" @click.native="change_attr($event,val.goods_attr_id,index,val.label,val.thumb_url)" :mini="true" :disabled="no_allow_attr_arr.indexOf(val.goods_attr_id)!=-1?true:false" :plain="true">{{val.label}}</x-button>
                   </div>
                </div>
              </div>
              <div class="number">数量
                  <div class="btnbox">
                    <span @click="reduce">-</span>
                    <input type="nubmer" v-model="num" readonly>
                    <span @click="add">+</span>
                  </div>
              </div>
            </div>
          </div>
        </transition>
  
        <!-- 属性多个维度end -->
  </div>
</template>
<script>
import { Swiper, SwiperItem, XButton, Scroller } from "vux";
import Header from "../../components/header/Header.vue";
import Count_down from "../../components/count_down/count_down.vue";
import dialog_bottom from "../../components/dialog_bottom/dialog_bottom.vue";
import API from "../../api/api.js";
import $ from "jquery";
import { setTimeout } from "timers";
import { Popup } from "vant";
export default {
  data() {
    return {
      dataList: [],
      cartnum: 0,
      dialog_swt: false,
      transitionName: "slide-up",
      properties: {},
      has_presale: false,
      predate: "",
      attr_id: [],
      attr: {},
      is_choose_attr: "",
      gshp_id: 0,
      gsup_id: 0,
      sendaddress: 0,
      pre_id: "",
      no_allow_attr_arr: [],
      price_data: {},
      Now_src: "",
      tab_id: 0,
      scrolled: 0,
      scrolled_swt: false,
      num: 1,
      pic_data: [{ pic_name: "概述" }, { pic_name: "售后服务须知" }],
      shouhouImg: "",
      pin_data: [],
      djname: {},
      isko: 0,
      list: [], //优惠券列表
      coupon_show: false,
      coupon_btn: false,
      dl_coupon: 0,
      noshop: false,
      yuding: "到货提醒",
      show: false,
      phone: "",
      shopname: "",
      phoneReg: /(^1[3|4|5|7|8]\d{9}$)/,
      already: false,
      cmmentshow: false,
      commentData: {},
      is_group: 0,
      starData: [
        { value: false },
        { value: false },
        { value: false },
        { value: false },
        { value: false }
      ],
      is_inapp: false
    };
  },
  created() {
    var _this = this;
    addEventListener("scroll", this.handleScroll);
  },
  mounted() {
    var _this = this;
    let info = JSON.parse(localStorage.getItem("info"));
    var id = this.$route.query.gshp_id || 0;
    if (this.$route.query.is_group) {
      this.is_group = this.$route.query.is_group;
    }

    if (navigator.userAgent.indexOf("HCApp") > -1) {
      //在app内部
      _this.is_inapp = true;
    }

    var uid = "";
    var uname = "";
    if (info) {
      uid = info.user_id;
      uname = info.user_name;
    }
    // 弹窗动画
    // if (this.dialog_swt) {
    //   this.transitionName = "slide-down"
    // } else {
    //   this.transitionName = "slide-up"
    // }
    this.cartnum = localStorage.getItem("cartnum") || 0;

    //加载商品详情

    this.fatchData(id);
    // this.get_nowping(id);
  },
  components: {
    "header-view": Header,
    "count-down": Count_down,
    Swiper,
    SwiperItem,
    XButton,
    Scroller,
    "dialog-bottom": dialog_bottom
  },
  methods: {
    gojundge() {
      var _this = this;
      _this.$router.push({
        name: "jundgeList",
        query: {
          gshp_id: _this.$route.query.gshp_id
        }
      });
    },
    fatchData(id) {
      var _this = this;
      if (_this.is_group != 0) {
        var params = { gshp_id: id, is_group: _this.is_group };
      } else {
        var params = { gshp_id: id };
      }

      this.$http
        .get(API.info, {
          params: params
        })
        .then(function(res) {
          if (res.data.code == 200) {
            _this.dataList = res.data.data;
            // _this.getcoupon(res.data.data.gsup_id)

            if (_this.dataList.comment) {
              _this.commentData = _this.dataList.comment;
              _this.cmmentshow = true;

              _this.starData.forEach(function(v, i) {
                if (i != _this.commentData.comment_rank) {
                  v.value = true;
                }
              });
              setTimeout(function() {
                console.log($(".heheimg").length);
                $(".heheimg").each(function() {
                  if ($(this).width() / $(this).height() > 1) {
                    $(this).removeClass("heheimg");
                    $(this).addClass("toowidth");
                  }
                });
              }, 10);
            }

            _this.properties = res.data.data.properties;
            _this.gshp_id = res.data.data.gshp_id;
            _this.gsup_id = res.data.data.gsup_id;
            _this.sendaddress = res.data.data.sendAddress_id;
            _this.shopname = res.data.data.name;
            if (res.data.data.Nbei != 0) {
              _this.num = res.data.data.Nbei;
            }
            var shouhou = res.data.data.shouhou;
            if (shouhou != "") {
              _this.shouhouImg = shouhou;
            }
            var data = {
              number: 1,
              gsup_id: _this.gsup_id
            };
            if (_this.pre_id != "") {
              data.presale = _this.pre_id;
            }
            if (_this.is_group != 0) {
              data.is_group = _this.is_group;
            }
            _this.get_price(data);
          }
        })
        .catch(function(err) {});
    },
    // 多个维度选择
    change_attr(e, id, index, label, url) {
      var el = e.target.parentNode.childNodes;
      if (e.target.classList == "active") {
        e.target.classList = "";
        delete this.attr[index];
      } else {
        for (var i = 0; i < el.length; i++) {
          el[i].classList = "";
        }
        e.target.classList = "active";
        this.attr[index] = {
          goods_attr_id: id,
          label: label,
          thumb_url: url
        };
      }
      this.Now_src = url;
      var str = "";
      var attr_id = [];
      for (var i in this.attr) {
        str += this.attr[i].label;
        attr_id.push(this.attr[i].goods_attr_id);
      }

      this.is_choose_attr = str;
      var data = {
        number: 1,
        gsup_id: this.gsup_id
      };
      if (this.pre_id != "") {
        data.presale = this.pre_id;
      }
      if (attr_id != "") {
        data.attr_id = attr_id;
      }
      if (this.is_group != 0) {
        data.is_group = this.is_group;
      }
      this.get_price(data);
      this.attr_id = attr_id;
    },

    get_price(data) {
      var _this = this;
      this.$http
        .post(API.price, data)
        .then(function(res) {
          _this.no_allow_attr_arr = res.data.data.no_allow_attr_arr;
          _this.price_data = res.data.data;
          if (_this.num > _this.price_data.stock) {
            _this.num = _this.price_data.stock;
          }
        })
        .catch(function(err) {});
    },
    tab_pic(i) {
      this.tab_id = i;
    },
    handleScroll() {
      this.scrolled = window.scrollY;
      var pic = document.getElementById("pic").offsetTop;
      if (pic < this.scrolled) {
        this.scrolled_swt = true;
      } else {
        this.scrolled_swt = false;
      }
    },
    check_out() {
      var _this = this;
      var data = {
        once: 1,
        gshp_id: this.gshp_id,
        number: 1,
        is_dingjin: 0,
        sendaddress: this.sendaddress,
        is_group: _this.group
      };
      if (this.pre_id != "") {
        data.presale = this.pre_id;
      }
      if (this.attr_id != "") {
        data.attr_id = this.attr_id;
      }
      this.$http
        .post(API.check_out, data)
        .then(function(res) {
          if (res.data.code == 200) {
            _this.$router.push({
              name: "editOrder",
              params: {
                data: res.data.data
              }
            });
            // sessionStorage.setItem("data", JSON.stringify(res.data.data));
            // sessionStorage.setItem("params", JSON.stringify(res.data.data));
          }
          if (res.data.code == 2002) {
            _this.dialog_swt = !_this.dialog_swt;
          }
          if (res.data.code == 400)
            _this.$vux.toast.text(res.data.msg, "middle");
        })
        .catch(function(err) {});
    },

    one_buy() {
      var _this = this;
      JSBridge.openWindow("/product/" + this.gshp_id);
    },
    reduce() {
      var that = this;
      if (this.is_group != 0) {
        this.$vux.toast.text("新人限购一件商品", "middle");
      } else {
        var num = that.dataList.Nbei;
        if (num == 0) {
          num = 1;
        }
        if (that.num > num) {
          that.num -= num;
        }
      }
    },
    add() {
      var that = this;
      if (this.is_group != 0) {
        this.$vux.toast.text("新人限购一件商品", "middle");
      } else {
        var num = that.dataList.Nbei;
        if (num == 0) {
          num = 1;
        }
        if (that.num < that.price_data.stock) {
          that.num += num;
        }
      }
    },
    keyup() {
      var that = this;
      that.num = that.num.replace(/[^1-9]/g, "");
      if (that.num >= that.price_data.stock) {
        that.num = that.price_data.stock;
      }
    },
    //立即购买
    buynow() {
      var _this = this;
      // if(_this.dataList.dj.dj_status>0){
      //   _this.$vux.toast.text("预定商品请前往app购买！", 'middle');
      // }else{
      if (_this.is_choose_attr == "") {
        _this.dialog_swt = !_this.dialog_swt;
        if (!_this.dialog_swt) {
          _this.goorder();
        }
      } else {
        // if(_this.dialog_swt){
        _this.goorder();
        // }
      }
      //  }
    },
    goorder() {
      var _this = this;
      localStorage.setItem("beforeindex", window.location.href);
      // alert('购物车获得key：'+localStorage.getItem("key"))
      var data1 = {
        key: localStorage.getItem("key"),
        sendaddress: _this.dataList.sendAddress_id,
        gshp_id: _this.dataList.gshp_id,
        number: _this.num,
        once: 1,
        is_dingjin: _this.is_dingjin,
        attr_id: _this.attr_id,
        delivery_id: _this.pre_id
      };
      if (_this.is_dingjin == 1) {
        data1.type = "deposit";
      } else if (_this.pre_id != "") {
        data1.type = "preSale";
      } else {
        data1.type = "normal";
      }
      if (_this.is_group != 99) {
        data1.is_group = _this.is_group;
      }
      localStorage.removeItem("order_data");
      this.$http
        .post(API.check_out, data1)
        .then(function(res) {
          if (res.data.code == 2002) {
            _this.$vux.toast.text(res.data.msg, "middle");
            _this.dialog_swt = !_this.dialog_swt;
          } else if (res.data.code == 200) {
            localStorage.setItem("order_data", JSON.stringify(res.data.data));
            sessionStorage.setItem("once", 1);
            _this.$router.push({
              path: "/editOrder",
              query: {
                once: 1,
                is_dingjin: _this.is_dingjin
              }
            });
          } else if (res.data.code == 2001 || res.data.code == 2004) {
            _this.$vux.toast.text(res.data.msg, "middle");
          } else {
            _this.$vux.toast.text(res.data.msg, "middle");
          }
        })
        .catch(function(err) {
          _this.$vux.toast.text(err.data.msg, "middle");
        });
    },
    addcart() {
      // console.log('走到了这里呀')
      var that = this;
      if (that.dataList.marketing_type == 1) {
        that.$vux.toast.text("预定商品请前往app购买！", "middle");
      } else {
        // console.log(that.is_choose_attr,that.dialog_swt)
        // if(that.is_choose_attr==""){
        that.dialog_swt = !that.dialog_swt;
        if (!that.dialog_swt) {
          that.addcartapi();
        }
        // }else{
        //   if(that.dialog_swt){
        //     that.addcartapi()
        //   }
        // }
      }
    },
    addcartapi() {
      var that = this;
      localStorage.setItem("beforeindex", window.location.href);
      // console.log(that.gshp_id,that.num,that.attr_id)
      this.$http
        .post(API.addcart, {
          gshp_id: that.gshp_id,
          number: that.num,
          attr_id: that.attr_id,
          presale: that.pre_id
        })
        .then(function(res) {
          if (res.data.code == 200) {
            localStorage.setItem("cartnum", res.data.data.sum);
            that.cartnum = res.data.data.sum;
            that.$vux.toast.text(res.data.msg, "middle");
          } else if (res.data.code == 2001) {
            that.$vux.toast.text(res.data.msg, "middle");
            setTimeout(function() {
              that.$router.replace({ name: "login" });
            }, 1500);
          } else {
            that.$vux.toast.text(res.data.msg, "middle");
          }
        })
        .catch(function(err) {
          that.$vux.toast.text(err.data.msg, "middle");
        });
    }
  },
  destroyed() {
    window.removeEventListener("scroll", this.handleScroll);
  }
};
</script>

<style lang="less">
.gd.child-view .vux-slider > .vux-swiper {
  height: 7.5rem !important;
}
.dig_con * {
  box-sizing: content-box;
}
// .appdown{
//   background: #fff;height: 1.1rem;padding: 0.2rem 0.3rem;overflow: hidden;
//   .left{
//     float: left;width: 0.8rem;height: 0.75rem;overflow: hidden;background: url(../../assets/image/appicon.png) no-repeat top center;background-size:auto 100%;
//   }
//   .middle{
//     float: left;width: 3rem;margin-left: 0.2rem;
//     p{font-size: 12px;color: #8A8A8A;line-height:0.4rem;}
//     p:nth-of-type(1){font-size: 0.3rem;color: #121212;}
//   }
//   .right{
//     float: right;width: 1.9rem;background: #f60;color: #fff;height: 0.7rem;border-radius: 0.7rem;font-size: 0.28rem;line-height: 0.7rem;text-align: center;
//   }
// }
.mt9 {
  margin-top: 0.9rem;
}
.has_presale {
  color: #f60;
}
.predate {
  color: #f60;
  padding: 0 0.3rem;
  font-size: 0.3rem;
  margin: 0.15rem 0 0.1rem;
}
.sw_dot .active {
  background-color: #ff0036 !important;
}
.g_des {
  background: white;
  .title {
    height: 2.6rem;
    padding: 0 0.3rem;
    display: flex;
    height: 1rem;
    padding-top: 0.15rem;
    box-sizing: content-box;
    .name {
      flex: 1;
      font-family: PingFang-SC-Regular;
      color: #121212;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      overflow: hidden;
    }

    .sotck {
      width: 1rem;
      font-size: 0.22rem;
      color: #999;
      text-align: center;
      padding: 0.1rem 0;
    }
  }
  .d_tips {
    padding: 0 0.3rem;
    color: #666;
    font-size: 0.26rem;
    margin-top: 0.2rem;
  }
  .price {
    padding: 0 0.3rem;
    .now_price {
      font-size: 0.58rem;
      font-weight: bold;
      color: #ff4c4c;
      display: block;
      line-height: 0.6rem;
      margin: 0.2rem 0 0.1rem;
      small {
        font-size: 0.36rem;
      }
    }

    .market_price {
      text-decoration: line-through;
      font-size: 0.24rem;
      color: #bbb;
      display: block;
      padding-bottom: 0.1rem;
    }
  }
}

.rule {
  background: white;

  height: 0.88rem;

  line-height: 0.88rem;

  padding: 0 0.3rem;

  font-size: 0.24rem;

  border-top: 1px solid #f1f1f1;

  a {
    display: flex;

    justify-content: space-between;
  }

  .iconfont {
    font-size: 0.3rem;
  }
}

.pin_doing {
  margin-top: 0.16rem;

  .more {
    padding: 0 0.3rem;

    height: 0.72rem;

    display: flex;

    align-items: center;

    color: #323232;

    background: white;

    justify-content: space-between;

    border-bottom: 1px solid #f1f1f1;
  }

  .item {
    height: 0.98rem;

    padding: 0 0.3rem;

    background: white;

    border-bottom: 1px solid #f1f1f1;

    display: flex;

    .left {
      flex: 1;

      display: flex;

      .l {
        height: 100%;

        display: flex;

        align-items: center;

        img {
          width: 0.6rem;

          height: 0.6rem;

          border-radius: 50%;

          background: #f1f1f1;
        }
      }

      .r {
        margin-left: 0.18rem;
      }
    }

    .right {
      display: flex;

      align-items: center;

      a {
        display: block;

        width: 1.2rem;

        height: 0.6rem;

        line-height: 0.6rem;

        border: 1px solid #ff0036;

        border-radius: 0.04rem;

        text-align: center;

        color: #ff0036;
      }
    }
  }
}

.attr {
  margin-top: 0.16rem;
  background: #fff;
  .item {
    display: flex;

    background: white;

    padding: 0.2rem 0;
    margin: 0 0.3rem;

    justify-content: space-between;

    font-size: 0.3rem;

    border-bottom: 1px solid #f1f1f1;

    .left {
      display: flex;

      .name {
        width: 2rem;
      }

      .is_choose {
        flex: 1;

        margin-left: 20px;

        overflow: hidden;

        white-space: pre-line;

        text-overflow: ellipsis;
        font-size: 0.28rem;
      }
    }
  }

  .cell {
    display: flex;

    background: white;

    padding: 0.2rem 0;
    margin: 0 0.3rem;

    font-size: 0.3rem;

    border-bottom: 1px solid #f1f1f1;

    .left {
      width: 1.5rem;
      color: #777;
      font-size: 0.26rem;
    }

    .right {
      flex: 1;
      font-size: 0.22rem;
      margin-left: 20px;
    }
  }
  .cell2 {
    padding: 0.25rem 0;
    margin: 0 0.3rem;
    // border-top: 1px solid #f1f1f1;
    .left {
      border: 1px solid #ff0036;
      border-radius: 0.05rem;
      width: 0.7rem;
      font-size: 0.22rem;
      text-align: center;
      color: #ff0036;
      display: inline-block;
      margin-bottom: 0.07rem;
    }
    .right {
      display: inline-block;
      width: 80%;
      margin-left: 0.4rem;
    }
  }
}

.pic {
  margin-top: 10px;

  margin-bottom: 1rem;

  .nav {
    display: flex;

    align-items: center;

    justify-content: center;

    background: white;

    height: 0.88rem;

    div {
      flex: 1;

      height: 100%;

      line-height: 0.88rem;

      text-align: center;
    }

    .active {
      box-sizing: border-box;

      color: #ff0036;

      border-bottom: 1px solid #ff0036;
    }
  }

  .fix {
    position: fixed;

    width: 100%;

    top: 0.9rem;

    z-index: 2;
  }

  .tab {
    .t_item {
      margin-top: 0.1rem;

      img {
        width: 100%;
      }

      .cell {
        background: white;

        padding: 0.2rem 0.3rem;

        display: flex;

        border-bottom: 1px solid #f1f1f1;

        span {
          flex: 1;
        }
      }
    }
  }
}
.footer2 {
  height: 0.48rem;
  background: #e2e2e2;
  line-height: 0.48rem;
  text-align: center;
  font-size: 0.24rem;
  color: #8a8a8a;
  position: fixed;
  bottom: 1rem;
  width: 100%;
  z-index: 2;
  left: 0;
}
.footer {
  height: 1rem;
  display: flex;
  position: fixed;
  bottom: 0;
  width: 100%;
  z-index: 2;
  background: #fff;
  border-top: 1px solid #dedede;
  font-size: 0;
  .block {
    display: inline-block;
    font-size: 0.28rem;
    text-align: center;
  }
  .block:nth-of-type(1),
  .block:nth-of-type(2) {
    border-right: 1px solid #dedede;
    padding: 0.07rem 0;
    width: 1rem;
    position: relative;
    span {
      display: block;
      font-size: 0.22rem;
      line-height: 0.3rem;
    }
    i {
      font-size: 0.4rem;
      line-height: 0.5rem;
    }
    em {
      font-style: normal;
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
      right: 0rem;
      top: -0.1rem;
    }
  }
  .blockbox {
    .block:nth-of-type(1) {
      border-right: 1px solid #dedede;
      position: relative;
      width: 3.25rem;
      line-height: 1rem;
      padding: 0;
    }
    .block:nth-of-type(2) {
      background: #ff0036;
      color: #fff;
      width: 3.25rem;
      line-height: 1rem;
      padding: 0;
    }
  }
  .block2 {
    display: inline-block;
    background: #ff0036;
    color: #fff;
    width: 6rem;
    font-size: 0.3rem;
    text-align: center;
    line-height: 1rem;
  }
  .block:nth-of-type(2),
  .block:nth-of-type(3) {
    width: 3rem;
    line-height: 1rem;
    padding: 0;
  }
  .block:nth-of-type(3) {
    background: #ff0036;
    color: #fff;
  }

  // .item {
  //   flex: 1;

  //   display: flex;

  //   flex-direction: column;

  //   align-items: center;

  //   justify-content: center;

  //   color: white;
  // }

  // .one {
  //   background: #c00;
  // }

  // .more {
  //   background: #FF0036;
  // }
}

.dialog {
  width: 100%;

  height: 100%;

  position: fixed;

  bottom: 0;

  background: rgba(0, 0, 0, 0.6);

  transition: all 0.5s cubic-bezier(0.55, 0, 0.1, 1);

  display: flex;

  flex-direction: column;

  margin-bottom: 1rem;

  .bg_model {
    flex: 1;
  }

  .dig_con {
    min-height: 3rem;
    padding: 0.3rem 0;
    background: white;
    .header {
      display: flex;
      height: 1.4rem;
      padding-bottom: 0.3rem;
      border-bottom: 1px solid #f1f1f1;
      .left {
        position: relative;
        width: 1.4rem;
        height: 1.4rem;
        overflow: hidden;
        text-align: center;
        margin: 0 0.3rem;
        img {
          width: auto;
          height: 100%;
          vertical-align: bottom;
        }
      }

      .right {
        padding-top: 0.35rem;
        p {
          font-size: 0.26rem;
        }
        .price {
          color: #ff4c4c;
          font-size: 0.45rem;
          font-weight: bold;
          span {
            color: #666;
            font-size: 0.24rem;
            font-weight: normal;
          }
        }
      }
    }

    .spe {
      padding: 0 0.3rem;
      margin-bottom: 0.5rem;
      .item {
        p {
          color: #121212;
          font-size: 0.28rem;
          margin: 0.15rem 0;
        }
        .btn_list {
          display: flex;

          flex-flow: wrap;

          button {
            padding: 0 0.2rem;
            height: 0.8rem;
            background: white;
            border: 0;
            // border: 1px solid rgba(0,0,0,0.2);
            // color: rgba(0,0,0,0.2);
            color: #353535;
            border: 1px solid #ccc;
            border-radius: 0px;
            margin: 0.1rem 0.15rem 0.1rem 0;
            font-size: 12px;
            box-sizing: border-box;
          }
          button:disabled {
            border: 1px solid rgba(0, 0, 0, 0.2);
            color: rgba(0, 0, 0, 0.2);
          }
          .weui-btn_plain-default {
            color: #353535;
            border: 1px solid #ccc;
          }
          .weui-btn_disabled {
            color: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(0, 0, 0, 0.2);
          }
          .active {
            background: #ff0036;

            color: white;
          }
        }
      }
    }
    .number {
      height: 0.88rem;
      line-height: 0.88rem;
      padding: 0 0.3rem;
    }
    .btnbox {
      float: right;
      span,
      input {
        width: 0.88rem;
        height: 0.88rem;
        border: 1px solid #ebebeb;
        color: #999;
        line-height: 0.88rem;
        text-align: center;
        display: block;
        margin: 0;
        float: left;
        font-size: 0.5rem;
      }
      input {
        border-left: 0;
        border-right: 0;
        color: #333;
        font-size: 0.3rem;
      }
    }
  }
}

.slide-down-leave-active,
.slide-up-enter {
  opacity: 0;

  -webkit-transform: translate(0, 100%);

  transform: translate(0, 100%);
}

.slide-down-enter,
.slide-up-leave-active {
  opacity: 0;

  -webkit-transform: translate(0, 100%);

  transform: translate(0, 100%);
}

// 领取优惠券
.kd1_coup {
  .coupon-no {
    padding: 0.2rem 0.2rem;
    .list {
      margin-bottom: 0.3rem;
      border: 1px dashed #666;
      .left {
        border-radius: 0 0 0 0.2rem;
        float: left;
        width: 1.8rem;
        border-right: 0.04rem dashed #f4f4f4;
        text-align: center;
        line-height: 0.5rem;
        padding: 0.3rem 0;
        font-size: 0.26rem;
        background: #fff;
        color: #121212;
        p {
          color: #ff0036;
          font-size: 0.26rem;
          span {
            font-size: 0.6rem;
            font-weight: bold;
          }
        }
      }
      .right {
        width: 5.2rem;
        padding: 0.24rem;
        background: #fff;
        border-radius: 0 0 0.2rem 0;
        float: right;
        p {
          color: #323232;
          font-size: 0.26rem;
          line-height: 0.36rem;
          height: 0.73rem;
          overflow: hidden;
        }
        kbd {
          font-family: "微软雅黑";
          font-size: 12px;
          i {
            border-radius: 50%;
            margin-left: 0.1rem;
            width: 0.25rem;
            height: 0.25rem;
            display: inline-block;
            background: url(../../assets/image/coupon_down.png) no-repeat top
              center;
            background-size: auto 0.25rem;
          }
        }
        a {
          width: 1rem;
          float: right;
          height: 0.4rem;
          line-height: 0.4rem;
          margin-top: 0.1rem;
          text-align: center;
          color: #fff;
          font-size: 0.22rem;
          display: block;
          background: #ff0036;
          border-radius: 0.1rem;
        }
        span {
          display: inline-block;
          padding: 0 0.1rem;
          color: #fff;
          background: #ff0036;
          border-radius: 0.1rem;
          height: 0.37rem;
          line-height: 0.37rem;
          font-size: 12px;
          margin-right: 0.1rem;
        }
        .receive {
          float: right;
          margin-top: 0.15rem;
        }
      }
      .bottom {
        border-top: 0.04rem dashed #f4f4f4;
        line-height: 0.7rem;
        height: 0.7rem;
        padding: 0 0.1rem;
        border-radius: 0.2rem 0.2rem 0 0;
        background: #fff;
        color: #8a8a8a;
        font-size: 12px;
      }
    }
    .yunf {
      .left {
        p {
          color: #2bbc69;
        }
      }
      .right {
        p {
          span {
            background: #2bbc69;
          }
        }
        a {
          background: #2bbc69;
        }
      }
    }
    .list-yes {
      height: 1.6rem;
      .left {
        background: transparent;
        width: 1.6rem;
        border-right: 0;
        font-size: 0.22rem;
        color: #7f7f7f;
        p {
          color: #7f7f7f;
          span {
            font-size: 0.4rem;
          }
        }
      }
      .right {
        background: transparent;
        padding: 0.3rem;
        p {
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
          line-height: 0.45rem;
          height: 0.45rem;
          color: #7f7f7f;
          span {
            background: #ccc;
          }
        }
        a {
          background: #ccc;
          color: #999;
        }
        span {
          background: #ccc;
          color: #999;
        }
      }
      // .bottom{background: transparent;}
    }
  }
  .list-yes {
    background: url(../../assets/image/coupon.png) no-repeat top center;
    background-size: 100% 1.6rem;
  }
}

.kd1_coup {
  .k_list {
    height: 6rem;
    overflow-y: scroll;
    margin-bottom: 2.2rem;
  }
  .k_title {
    border: 0;
    line-height: 0.6rem;
    height: 0.8rem;
    padding: 0.5rem 0.6rem 0;
    text-align: center;
    font-size: 0.36rem;
    font-weight: bold;
    box-sizing: content-box;
    i {
      float: right;
      font-weight: normal;
      font-size: 0.4rem;
    }
  }
  .coupon-no {
    .list {
      .right {
        i {
          display: block;
          border-radius: 50%;
          width: 0.35rem;
          height: 0.35rem;
          line-height: 0.35rem;
          font-size: 12px;
          text-align: center;
          border: 0.02rem solid #ccc;
          color: #ccc;
          float: right;
          margin: 0.15rem 0.3rem 0 0;
        }
        i.active {
          color: #fff;
          background: #ff4c4c;
          border-color: #ff4c4c;
        }
      }
    }
  }
  button {
    background: #f60;
    color: #fff;
    border: 0;
    width: 100%;
    height: 1rem;
    position: absolute;
    bottom: 1.3rem;
    left: 0.2rem;
    width: 7.1rem;
  }
}
.van-popup {
  border-radius: 0.2rem;
  overflow: hidden;
}
.reveice-alarm {
  padding: 0.3rem 0 0;
  text-align: center;
  color: #323232;
  width: 6rem;
  background: #fff;
  overflow: hidden;
  h2 {
    color: #323232;
    font-size: 0.36rem;
  }
  h5 {
    font-weight: normal;
    font-size: 0.28rem;
    padding: 0.2rem 0.5rem 0.26rem;
  }
  .box1 {
    border: 0.02rem solid #dedede;
    margin: 0 0.5rem;
    line-height: 0.7rem;
    height: 0.7rem;
    position: relative;
    input {
      border: 0;
      background: transparent;
      width: 100%;
      text-indent: 0.2rem;
    }
    .iconfont {
      position: absolute;
      background: #bbbbbb;
      border-radius: 50%;
      width: 0.3rem;
      height: 0.3rem;
      line-height: 0.27rem;
      text-align: center;
      font-size: 0.24rem;
      color: #fff;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      right: 0.2rem;
    }
  }
  .box2 {
    border-top: 0.02rem solid #dedede;
    margin: 0.3rem 0 0;
    overflow: hidden;
    button {
      width: 50%;
      display: block;
      background: transparent;
      border: 0;
      float: left;
      line-height: 1rem;
      font-size: 0.3rem;
    }
    button:nth-of-type(2) {
      border-left: 0.02rem solid #dedede;
      color: #f60;
    }
  }
}
.alread {
  position: relative;
}
.alread:before {
  content: "";
  display: inline-block;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.5);
  position: absolute;
  left: 0;
  top: 0;
}
.comment {
  padding: 0 0.4rem 0.2rem;
  margin-top: 0.16rem;
  background: #fff;
  > p {
    line-height: 0.88rem;
    height: 0.88rem;
    border-bottom: 0.02rem solid #ebebeb;
    span {
      float: right;
    }
  }
  .comcont {
    margin-top: 0.3rem;
    .comtop {
      line-height: 0.6rem;
      span {
        display: inline-block;
        vertical-align: middle;
      }
      .compic {
        width: 0.6rem;
        height: 0.6rem;
        border-radius: 50%;
        overflow: hidden;
        img {
          width: 100%;
          height: 100%;
        }
      }
      .comname {
        font-size: 0.3rem;
        color: #333;
        margin: 0 0.08rem 0 0.15rem;
      }
      .star {
        i {
          display: inline-block;
          width: 0.3rem;
          height: 0.3rem;
          margin: 0 0.08rem;
        }
      }
      .face {
        height: 0.35rem;
        width: 0.35rem;
        float: right;
        margin: 0.1rem;
      }
    }
    .comtime {
      font-size: 0.24rem;
      color: #444;
      margin: 0.2rem 0 0.1rem;
      span {
        color: #646464;
        margin-left: 0.1rem;
      }
    }
    .comtext {
      color: #333;
      font-size: 0.3rem;
      line-height: 0.4rem;
      margin-bottom: 0.2rem;
    }
    .comimgbox {
      .comimg {
        width: 2.2rem;
        height: 2.2rem;
        overflow: hidden;
        text-align: center;
        line-height: 2.2rem;
        display: inline-block;
        margin: 0 0.05rem;
        position: relative;
        img {
          width: 100%;
          height: auto;
          display: inline-block;
          position: absolute;
          left: 0;
          right: 0;
          top: 0;
          bottom: 0;
          margin: auto;
        }
        img.toowidth {
          width: auto;
          height: 100%;
          left: 50%;
          transform: translateX(-50%);
        }
      }
    }
  }
}

// .btnbox{
//   input,button{width: 0.5rem;border: 1px solid #f1f1f1;background: #fff;margin: 0 0.03rem;color: #999;}
// }
</style>
