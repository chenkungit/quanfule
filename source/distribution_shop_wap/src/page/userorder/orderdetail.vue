<template>
  <div>
    <!--  -->
    <header-view title="订单详情"></header-view>
    <div class="adr">
      <div class="box">
        <div class="statusbox">
          <p>{{status}}</p>
        </div>
        <div class="is_adr">
          <div class="is_l">
            <i class="iconfont">&#xe620;</i>
          </div>
          <div class="is_c">
            <div class="name">
              <span>收货人:{{data.consignee}}</span>
              <span>{{data.phone}}</span>
            </div>
            <div
              class="addres"
            >收货地址:{{data.province_name}}省{{data.city_name}}市{{data.district_name}}{{data.address}}</div>
          </div>
          <div class="is_r"></div>
        </div>
      </div>
      <div class="line"></div>
    </div>
    <div class="goods">
      <!-- <div class="send_adr">发货地:{{data.sendaddress_name}}</div> -->
      <div class="list">
        <div v-for="(v,i) in goodslist" :key="i" @click="gogoods(v.goods_id)">
          <div class="item">
            <div class="left">
              <img :src="v.img" alt>
            </div>
            <div class="right">
              <p>{{v.goods_name}}</p>
              <p>
                <small v-if="v.goods_attr">{{v.goods_attr}}</small>
                <kbd>x{{v.goods_number}}</kbd>
              </p>
              <p>
                {{v.goods_price_format}}
                <del>{{v.market_price_format}}</del>
              </p>
            </div>
          </div>
          <div class="btnbox" v-if="v.o_status==5">
            <span>正在退货</span>
          </div>
        </div>
      </div>
    </div>
    <div class="money">
      <div>
        商品积分
        <span>{{data.goods_amount}}</span>
      </div>
      <div class="actu">
        实付积分：
        <span>{{data.total_fee_format}}</span>
      </div>
      <div style="clear:both"></div>
    </div>
    <div class="time">
      <div>
        订单号：{{data.order_sn}}
        <span v-clipboard:copy="message" v-clipboard:success="onCopy" v-clipboard:error="onError">复制</span>
      </div>
      <div>创建时间：{{data.add_time}}</div>
      <div>付款时间：{{data.pay_time}}</div>
    </div>
    <div class="fixed">
      <span v-if="data.o_status==0&&data.is_delete!=1" @click="cancelorder(data.order_id)">取消订单</span>
      <span
        @click="showrefund=!showrefund"
        v-if="data.o_status==1||data.o_status==2||data.o_status==3||data.o_status==4||data.o_status==5"
      >申请售后</span>
      <router-link :to="{name:'track',params:{id:data.order_id}}" v-if="data.o_status==4">查看物流</router-link>
      <span v-if="data.o_status==4" @click="sureorder(data.order_id)">确认收货</span>
      <span v-if="data.o_status==5" @click="cancelcancel(data.order_id)">取消退货</span>
    </div>

    <div class="kuaidi">
      <dialog-bottom v-show="showrefund" @change_swt="hide_dialog_refund">
        <div class="k_title">选择申请售后的商品</div>
        <div class="k_list">
          <div class="cell" v-for="(v,i) in refundgood" @click="checked_refund(i)" :key="i">
            <span>{{v.name}}</span>
            <i class="iconfont" v-show="v.isactive">&#xe604;</i>
          </div>
        </div>
        <button type="button" @click="sureid()">确定</button>
      </dialog-bottom>
    </div>
  </div>
</template>

<script>
import Header from "../../components/header/Header.vue";
import dialog_bottom from "../../components/dialog_bottom/dialog_bottom.vue";
import { Dialog } from "vant";
import { XSwitch, Group, Radio } from "vux";
import API from "../../api/api.js";
import { setTimeout } from "timers";
import $ from "jquery";

export default {
  components: {
    "header-view": Header,
    "dialog-bottom": dialog_bottom,
    XSwitch,
    Group,
    Radio
  },
  data() {
    return {
      data: {},
      goodslist: [],
      orderid: "",
      message: "",
      status: "",
      showrefund: false,
      refundgood: [],
      pay_swt: false,
      is_choose_card: null,
      url: "",
      configdata: []
    };
  },

  mounted() {
    var _this = this;
    var id = _this.$route.params.id;
    _this.orderid = id;
    _this.url = window.location.href;

    _this.getdetail(id);
  },
  methods: {
    getdetail(id) {
      var _this = this;
      _this.$http
        .get(API.orderdetail, {
          params: { key: localStorage.getItem("key"), order_id: id }
        })
        .then(res => {
          _this.data = res.data.data;
          _this.message = _this.data.order_sn;
          _this.goodslist = res.data.data.exist_real_goods;
          var refundgood = [];

          _this.goodslist.forEach(function(v, i) {
            if (v.o_status == 4 || v.o_status == 2 || v.o_status == 1) {
              refundgood.push({
                name: v.goods_name,
                id: v.goods_id,
                isactive: false
              });
            }
          });
          _this.refundgood = refundgood;

          var s = res.data.data.o_status;
          if (s == 0 && _this.data.is_delete != 1) {
            s = "待付款";
          } else if (s == 0 && _this.data.is_delete == 1) {
            s = "交易关闭";
          } else if (s == 1 || s == 2) {
            s = "待发货";
          } else if (s == 4) {
            s = "待收货";
          } else if (s == 5 && _this.data.status == 0) {
            s = "退货中";
          } else if (s == 6 && _this.data.status != 0) {
            s = "退货完成";
          } else if (s == 7) {
            s = "交易完成";
          }
          _this.status = s;

          let info = JSON.parse(localStorage.getItem("info"));
        });
    },
    gogoods(id) {
      this.$router.push({ name: "goodsDetail", query: { gshp_id: id } });
    },
    // 复制单号
    onCopy: function() {
      this.$vux.toast.text("复制成功", "middle");
    },
    onError: function() {
      this.$vux.toast.text("无法复制", "middle");
    },
    // 隐藏选择退货商品弹框
    hide_dialog_refund() {
      this.showrefund = false;
    },
    checked_refund(i) {
      var _this = this;
      _this.refundgood[i].isactive = !_this.refundgood[i].isactive;
    },
    // 确定申请售后的id
    sureid() {
      var _this = this;
      var id = [];
      _this.refundgood.forEach(function(v, i) {
        if (v.isactive == true) {
          id.push(v.id);
        }
      });
      if (id.length == 0) {
        _this.$vux.toast.text("你未选择需售后商品", "middle");
      } else {
        var refund_goods = [];
        _this.goodslist.forEach(function(v, i) {
          id.forEach(function(a, j) {
            if (v.goods_id == a) {
              refund_goods.push(v);
              return false;
            }
          });
        });
        localStorage.setItem("refundgoods", JSON.stringify(refund_goods));

        this.$router.push({
          name: "afterservice",
          params: {
            orderid: _this.orderid
          }
        });
      }
    },
    // 取消订单
    cancelorder(order) {
      var _this = this;

      Dialog.confirm({
        title: "确认",
        message: "确认取消订单？"
      })
        .then(() => {
          _this.$http
            .put(API.cancelorder, {
              key: localStorage.getItem("key"),
              order_id: order
            })
            .then(res => {
              var d = res.data;
              if (d.code == 200) {
                this.$vux.toast.text(d.msg, "middle");
                setTimeout(function() {
                  window.location.reload();
                }, 1500);
              }
            });
        })
        .catch(() => {
          // on cancel
        });
    },
    // 确定收货
    sureorder(order) {
      var _this = this;
      Dialog.confirm({
        title: "确认",
        message: "确认收货？"
      })
        .then(() => {
          _this.$http
            .put(API.sure_receive, {
              key: localStorage.getItem("key"),
              order_id: order
            })
            .then(res => {
              var d = res.data;
              if (d.code == 200) {
                this.$vux.toast.text(d.msg, "middle");
                setTimeout(function() {
                  window.location.reload();
                }, 1500);
              }
            });
        })
        .catch(() => {
          // on cancel
        });
    },

    //取消退货
    cancelcancel(order) {
      var _this = this;

      Dialog.confirm({
        title: "确认",
        message: "确认取消退货？"
      }).then(() => {
        _this.$http
          .put(API.cancelcancel, {
            key: localStorage.getItem("key"),
            order_id: order
          })
          .then(res => {
            var d = res.data;
            if (d.code == 200) {
              this.$vux.toast.text(d.msg, "middle");
              setTimeout(function() {
                window.location.reload();
              }, 1500);
            }
          });
      });
    },
    hide_dialog_pay() {
      this.pay_swt = !this.pay_swt;
    },
    choose_pay_way(i, FCardNumber) {
      this.is_choose_card = i;
      this.FCardNumber = FCardNumber;
    }
  }
};
</script>

<style lang="less" scoped>
.adr {
  margin-top: 0.9rem;
  background: white;
  .box {
    .is_adr {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 1.5rem;
      .is_l {
        flex: 1;
        height: 100%;
        text-align: center;
      }
      .is_c {
        flex: 8;

        .name {
          display: flex;
          justify-content: space-between;
        }
      }
      .is_r {
        flex: 1;
        text-align: center;
      }
    }
  }
}
.statusbox {
  height: 2rem;
  background: url(../../assets/image/order_header.png) no-repeat top center;
  background-size: 100%;
  p {
    color: #fff;
    font-size: 0.3rem;
    padding-left: 0.3rem;
    position: relative;
    top: 50%;
    transform: translateY(-50%);
  }
}
.line {
  height: 0.12rem;
  background: repeating-linear-gradient(
    -45deg,
    #8fc9f5 0,
    #8fc9f5 50%,
    #f58f8f 50%,
    #f58f8f 100%
  );
}
.goods {
  padding: 0 0.3rem;
  background: white;
  margin-top: 0.1rem;
  .send_adr {
    height: 0.73rem;
    line-height: 0.73rem;
    border-bottom: 1px solid #f1f1f1;
  }
  .list {
    .item {
      display: flex;
      padding: 0.2rem 0;
      border-bottom: 1px solid #f1f1f1;
      .left {
        width: 1.72rem;
        height: 1.72rem;
        border: 1px solid #f1f1f1;
        display: flex;
        align-items: center;
        justify-content: center;
        img {
          width: 1.48rem;
          height: 1.48rem;
          border-radius: 0.05rem;
        }
      }
      .right {
        flex: 1;
        margin-left: 0.2rem;
        p {
          text-align: justify;
          font-size: 0.28rem;
          min-height: 0.3rem;
        }
        p:nth-of-type(1) {
          color: #333;
          line-height: 0.35rem;
          height: 0.7rem;
          overflow: hidden;
        }
        p:nth-of-type(2) {
          color: #999;
          font-size: 0.24rem;
          margin-bottom: 0.15rem;
          kbd {
            float: right;
          }
        }
        p:nth-of-type(3) {
          color: #dd2727;
          font-size: 0.24rem;
          del {
            color: #999;
            margin: 0 0.2rem;
          }
        }
      }
    }
  }
}
.btnbox {
  padding: 0.2rem;
  text-align: right;
  span {
    color: #555;
    font-size: 0.26rem;
    display: inline-block;
    height: 0.55rem;
    line-height: 0.55rem;
    padding: 0 0.2rem;
    border: 1px solid #eee;
  }
}
.money {
  background: #fff;
  color: #323232;
  font-size: 0.28rem;
  line-height: 0.55rem;
  padding: 0.2rem 0;
  margin-top: 0.15rem;
  div {
    padding: 0 0.3rem;
  }
  .actu {
    text-align: right;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    padding: 0.15rem 0.3rem;
    margin-top: 0.2rem;
  }
  span {
    color: #dd2727;
    font-size: 0.26rem;
    float: right;
  }
  a {
    display: block;
    width: 3.45rem;
    height: 0.72rem;
    line-height: 0.72rem;
    text-align: center;
    border: 1px solid #dadada;
    border-radius: 0.1rem;
    margin-top: 0.2rem;
    margin: 0.2rem 0.12rem 0;
    float: left;
    i {
      margin-right: 0.1rem;
      color: #26b6f6;
      font-size: 0.35rem;
    }
  }
}
.time {
  background: #fff;
  color: #323232;
  font-size: 0.28rem;
  line-height: 0.8rem;
  padding: 0 0;
  margin-top: 0.15rem;
  color: #222;
  font-size: 0.28rem;
  margin-bottom: 1.2rem;
  div {
    border-bottom: 1px solid #ddd;
    padding: 0 0.3rem;
  }
  span {
    border: 0.02px solid #ebebeb;
    padding: 0 0.15rem;
    font-size: 0.24rem;
    color: #666;
    height: 0.4rem;
    line-height: 0.36rem;
    float: right;
    margin-top: 0.2rem;
  }
}
.fixed {
  position: fixed;
  background: #fff;
  width: 100%;
  z-index: 3;
  bottom: 0;
  left: 0;
  text-align: right;
  span,
  a {
    padding: 0 0.15rem;
    height: 0.56rem;
    line-height: 0.5rem;
    border: 0.02rem solid #ddd;
    display: inline-block;
    color: #555;
    font-size: 0.28rem;
    border-radius: 0.1rem;
    margin: 0.2rem 0.1rem;
  }
  span:nth-last-child(1) {
    color: #ff0036;
    border-color: #ff0036;
  }
}
.kuaidi {
  line-height: 0.5rem;
  .k_title {
    text-align: center;
    padding: 0.2rem 0;
    font-size: 0.35rem;
  }
  .k_list {
    padding: 0 0.2rem;
    .cell {
      border-bottom: 1px solid #eee;
      padding: 0.1rem 0;
    }
    span {
      display: inline-block;
      width: 90%;
      height: 0.5rem;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    i {
      color: #f60;
      float: right;
    }
  }
  button {
    background: #f60;
    color: #fff;
    display: block;
    width: 100%;
    border: 0;
    line-height: 0.7rem;
    position: fixed;
    bottom: 0;
    z-index: 10;
  }
}
.pay {
  .k_title {
    height: 1rem;
    line-height: 1rem;
    border-bottom: 1px solid #f1f1f1;
    font-size: 0.3rem;
    text-align: center;
  }
  .k_list {
    .cell {
      height: 0.8rem;
      line-height: 0.8rem;
      padding: 0 0.3rem;
      display: flex;
      justify-content: space-between;
      border-bottom: 1px solid #f1f1f1;

      .icon-z {
        margin-right: 0.2rem;
        color: #1296db;
      }
      .icon-w {
        margin-right: 0.2rem;
        color: #1afa29;
      }
      .icon-c {
        margin-right: 0.2rem;
        color: #ff0036;
      }
      i {
        margin-right: 0.2rem;
        color: #ff0036;
      }
    }
  }
  .pay_s {
    position: fixed;
    bottom: 0;
    height: 1rem;
    line-height: 1rem;
    width: 100%;
    background: #ff0036;
    color: white;
    text-align: center;
  }
}
</style>
