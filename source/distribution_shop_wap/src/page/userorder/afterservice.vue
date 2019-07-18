<template>
  <div>
    <!--  -->
    <header-view title="申请售后"></header-view>

    <div class="goods">
      <div class="send_adr">发货地:{{data.sendaddress_name}}</div>
      <div class="list">
        <div v-for="(v,i) in goodslist" :key="i">
          <div class="item">
            <div class="left">
              <img :src="v.img" alt>
            </div>
            <div class="right">
              <p>{{v.goods_name}}</p>
              <p>
                <small v-if="v.goods_attr">{{v.goods_attr}}</small>
              </p>
              <!-- <kbd>x{{v.goods_number}}</kbd> -->
              <div class="numberbox">
                <span @click.stop="reduce(i)">-</span>
                <input type="number" v-model="v.goods_number" readonly>
                <span @click.stop="add(i)">+</span>
              </div>

              <p>
                {{v.goods_price_format}}
                <del>{{v.market_price_format}}</del>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="listbox">
      <div class="lists" @click="dialog_type=true">
        <label>售后类型</label>
        <i class="iconfont">&#xe713;</i>
        <input type="text" v-model="typename" readonly>
      </div>

      <div v-show="myshow==1" class="lists" @click="dialog_status=true">
        <label>货物状态</label>
        <i class="iconfont">&#xe713;</i>
        <input type="text" v-model="statusname" readonly>
      </div>
    </div>
    <div class="listbox">
      <div class="lists" @click="dialog_reason=true">
        <label>退款原因</label>
        <i class="iconfont">&#xe713;</i>
        <input type="text" v-model="reasonname" readonly>
      </div>
      <div class="lists left">
        <label>退款说明：</label>
        <input type="text" v-model="refund_info">
      </div>
      <div v-show="myshow==2" class="lists" @click="dialog_express=true">
        <label>快递公司</label>
        <i class="iconfont">&#xe713;</i>
        <input type="text" v-model="expressname" readonly>
      </div>
      <div v-show="myshow==2" class="lists left">
        <label>快递单号：</label>
        <input type="text" v-model="refund_invoince_no">
      </div>
    </div>

    <div class="listbox uploadbox">
      <p>上传凭证</p>
      <van-uploader :after-read="onRead">
        <van-icon name="photograph"/>
        <i class="iconfont">&#xe601;</i>
      </van-uploader>

      <div v-if="img.length!=0" class="pic" v-for="(v,i) in img" :key="i">
        <span @click="deletepic(i)">点击删除</span>
        <img :src="v" alt>
      </div>
    </div>

    <!-- 售后类型 -->
    <div class="kuaidi">
      <dialog-bottom v-show="dialog_type" @change_swt="hide_dialog_type">
        <div class="k_title">选择售后类型</div>
        <div class="k_list">
          <div class="cell" v-for="(v,i) in type" @click="checked_type(i)" :key="i">
            <span>{{v.name}}</span>
            <i class="iconfont" v-show="v.isactive">&#xe662;</i>
          </div>
        </div>
      </dialog-bottom>
    </div>
    <!-- // 退款原因 -->
    <div class="kuaidi">
      <dialog-bottom v-show="dialog_reason" @change_swt="hide_dialog_reason">
        <div class="k_title">选择退款原因</div>
        <div class="k_list">
          <div class="cell" v-for="(v,i) in reason" @click="checked_reason(i)" :key="i">
            <span>{{v.name}}</span>
            <i class="iconfont" v-show="v.isactive">&#xe662;</i>
          </div>
        </div>
      </dialog-bottom>
    </div>

    <!-- 货物状态 -->

    <div class="kuaidi">
      <dialog-bottom v-show="dialog_status" @change_swt="hide_dialog_status">
        <div class="k_title">选择货物状态</div>
        <div class="k_list">
          <div class="cell" v-for="(v,i) in status" @click="checked_status(i)" :key="i">
            <span>{{v.name}}</span>
            <i class="iconfont" v-show="v.isactive">&#xe662;</i>
          </div>
        </div>
      </dialog-bottom>
    </div>

    <!-- 选择快递 -->
    <div class="kuaidi">
      <dialog-bottom v-show="dialog_express" @change_swt="hide_dialog_express">
        <div class="k_title">选择快递公司</div>
        <div class="k_list">
          <div class="cell" v-for="(v,i) in express" @click="checked_express(i)" :key="i">
            <span>{{v.name}}</span>
            <i class="iconfont" v-show="v.isactive">&#xe662;</i>
          </div>
        </div>
      </dialog-bottom>
    </div>

    <button class="sure" @click="sure()">确定</button>
  </div>
</template>

<script>
import Header from "../../components/header/Header.vue";
import dialog_bottom from "../../components/dialog_bottom/dialog_bottom.vue";
import { Dialog } from "vant";
import { XSwitch, Group, Radio } from "vux";
import API from "../../api/api.js";
import { setTimeout } from "timers";

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
      img: [],
      imgfile: [],
      myshow: 1,
      dialog_reason: false,
      dialog_express: false,
      dialog_type: false,
      dialog_status: false,
      status: "",
      reason: [
        { id: 1, name: "卖家缺货", isactive: false },
        { id: 2, name: "质量问题/破损", isactive: false },
        { id: 3, name: "尺寸拍错", isactive: false },
        { id: 4, name: "颜色/规格/大小尺寸与描述不符", isactive: false },
        { id: 5, name: "卖家发错货", isactive: false },
        { id: 6, name: "卖家少发/漏发", isactive: false },
        { id: 7, name: "其他问题", isactive: false }
      ],
      express: [
        { id: 6, name: "圆通", isactive: false },
        { id: 22, name: "申通", isactive: false }
      ],
      type: [
        { id: 1, name: "仅退款", isactive: true },
        { id: 2, name: "退货退款", isactive: false }
      ],
      status: [
        { id: 1, name: "已收到货", isactive: false },
        { id: 0, name: "未收到货", isactive: false }
      ],
      reasonname: "",
      reasonid: 0,
      expressname: "",
      expressid: 0,
      typename: "仅退款",
      statusname: "",
      goods_status: "",
      oldnum: [],
      orderid: 0,
      refund_invoince_no: "",
      refund_info: ""
    };
  },

  mounted() {
    var _this = this;
    this.getexpress();
    _this.orderid = _this.$route.params.orderid;
    _this.goodslist = JSON.parse(localStorage.getItem("refundgoods"));
    _this.goodslist.forEach(function(v, i) {
      _this.oldnum.push(v.goods_number);
    });
  },
  methods: {
    getexpress() {
      var _this= this;
      _this.$http
        .get(API.logistics_type, {
          params: {
            key: localStorage.getItem("key"),
          }
        })
        .then(res => {
          var data = res.data.data;
          data.forEach(function(val, i) {
            data[i]={id:val.id,name:val.name,isactive:false}
          });
          _this.express=data
        });
    },
    onRead(file) {
      var _this = this;
      _this.img.push(file.content);
      _this.imgfile.push(file.file);
    },
    deletepic(i) {
      var _this = this;
      Dialog.confirm({
        title: "确认",
        message: "确认删除这此图片吗？"
      }).then(() => {
        _this.img.splice(i, 1);
        _this.imgfile.splice(i, 1);
      });
    },
    sure() {
      var _this = this;
      var goods_json = [];
      _this.goodslist.forEach(function(v, i) {
        goods_json.push({ rec_id: v.rec_id, number: v.goods_number });
      });
      var _flag = true;
      if (_this.myshow == 2) {
        if (_this.expressid == 0) {
          _this.$vux.toast.text("请选择快递公司！", "middle");
          _flag = false;
        } else if (_this.refund_invoince_no == 0) {
          _this.$vux.toast.text("请填写快递单号！", "middle");
          _flag = false;
        }
      }
      var data = {
        key: localStorage.getItem("key"),
        order_id: _this.orderid,
        goods_json: JSON.stringify(goods_json),
        refund_type: _this.myshow,
        reason_type: _this.reasonid,
        shipping_id: _this.expressid,
        refund_invoince_no: _this.refund_invoince_no,
        refund_info: _this.refund_info,
        img: _this.imgfile,
        goods_status: _this.goods_status
      };
      var fd = new FormData();
      for (let o in data) {
        if(o=='img'){
          for(var i in data.img){
            fd.append('img[]', data.img[i]);
          }
        }else{
        fd.append(o, data[o]);
        }
      }
      if (_flag) {
        _this.$http
          .post(API.refund, fd)
          .then(function(res) {
            if (res.data.code == 200) {
              _this.$vux.toast.text(res.data.msg, "middle");
              setTimeout(function() {
                _this.$router.replace({
                  name: "userorder",
                  params: {
                    status: -1
                  }
                });
              }, 2000);
            } else {
              _this.$vux.toast.text(res.data.msg, "middle");
            }
          })
          .catch(function(err) {
            _this.$vux.toast.text(res.data.msg, "middle");
          });
      }
    },
    hide_dialog_reason() {
      this.dialog_reason = !this.dialog_reason;
    },
    hide_dialog_express() {
      this.dialog_express = !this.dialog_express;
    },
    hide_dialog_type() {
      this.dialog_type = !this.dialog_type;
    },
    hide_dialog_status() {
      this.dialog_status = !this.dialog_status;
    },
    checked_reason(i) {
      var _this = this;
      _this.reason.forEach(function(v, i) {
        v.isactive = false;
      });
      _this.reason[i].isactive = true;
      _this.reasonname = _this.reason[i].name;
      _this.reasonid = _this.reason[i].id;
      _this.dialog_reason = false;
    },
    checked_express(i) {
      var _this = this;
      _this.express.forEach(function(v, i) {
        v.isactive = false;
      });
      _this.express[i].isactive = true;
      _this.expressname = _this.express[i].name;
      _this.expressid = _this.express[i].id;
      _this.dialog_express = false;
    },
    checked_type(i) {
      var _this = this;
      _this.type.forEach(function(v, i) {
        v.isactive = false;
      });
      _this.type[i].isactive = true;
      _this.typename = _this.type[i].name;
      _this.myshow = _this.type[i].id;
      _this.dialog_type = false;
    },
    checked_status(i) {
      var _this = this;
      _this.status.forEach(function(v, i) {
        v.isactive = false;
      });
      _this.status[i].isactive = true;
      _this.statusname = _this.status[i].name;
      _this.goods_status = _this.status[i].id;
      _this.dialog_status = false;
    },
    reduce(i) {
      var _this = this;
      var d = _this.goodslist[i].goods_number;
      if (d > 1) {
        d--;
      } else {
        _this.$vux.toast.text("数量不能再减少啦！", "middle");
      }
      _this.goodslist[i].goods_number = d;
    },
    add(i) {
      var _this = this;
      var d = _this.goodslist[i].goods_number;
      if (d < _this.oldnum[i]) {
        d++;
      } else {
        _this.$vux.toast.text("超过购买数量！", "middle");
      }
      _this.goodslist[i].goods_number = d;
    }
  }
};
</script>

<style lang="less" scoped>
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
        .numberbox {
          line-height: 0.44rem;
          float: right;
          span {
            display: block;
            width: 0.5rem;
            text-align: center;
            font-size: 0.4rem;
            color: #999;
            border: 1px solid #ddd;
            float: left;
          }
          input[type="number"] {
            width: 0.7rem;
            border: 1px solid #ddd;
            border-left: 0;
            border-right: 0;
            height: 0.47rem;
            font-size: 0.3rem;
            text-align: center;
            float: left;
          }
        }
      }
    }
  }
}
.listbox {
  background: #fff;
  margin-top: 0.2rem;
}
.lists {
  height: 1rem;
  border-bottom: 1px solid #f1f1f1;
  margin: 0 auto;
  padding: 0 0.3rem;
  justify-content: center;
  overflow: hidden;
  label {
    display: inline-block;
    color: #121212;
    font-size: 0.28rem;
    line-height: 1rem;
  }
  input {
    width: 62%;
    height: 100%;
    border: 0;
    outline: none;
    text-align: right;
    float: right;
  }
  i {
    float: right;
    margin: 0.25rem 0 0.25rem 0.25rem;
  }
}
.left input {
  text-align: left;
  width: 75%;
  border-bottom: 1px solid #eee;
  height: 70%;
  margin-top: 0.2rem;
}

.kuaidi {
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
      i {
        color: #ff0036;
      }
    }
    .cell1 {
      line-height: 0.4rem;
      padding: 0.2rem;
      border-bottom: 1px solid #f1f1f1;
      i {
        color: #ff0036;
        float: right;
      }
      span {
        float: left;
      }
      small {
        display: block;
        font-size: 12px;
        color: #999;
      }
    }
  }
}

.van-uploader {
  width: 1.8rem;
  height: 1.8rem;
  line-height: 1.65rem;
  text-align: center;
  font-size: 0.5rem;
  border: 2px dashed #ddd;
  overflow: hidden;
  float: left;
  margin: 0 0.2rem 0.3rem;
  .van-icon {
    display: none;
  }
  img {
    width: 1.65rem;
  }
}
.uploadbox {
  padding: 0 0.3rem 0.3rem;
  overflow: hidden;
  > p {
    height: 1rem;
    line-height: 1rem;
  }
}
.listbox .pic {
  width: 1.8rem;
  height: 1.8rem;
  overflow: hidden;
  text-align: center;
  float: left;
  margin: 0 0.2rem 0.3rem;
  position: relative;
  img {
    height: 100%;
  }
  span {
    position: absolute;
    display: inline-block;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: auto;
    color: #fff;
    font-size: 0.26rem;
    height: 0.5rem;
    line-height: 0.5rem;
    background: rgba(255, 255, 255, 0.6);
    width: 75%;
    border-radius: 0.1rem;
  }
}
.sure {
  background: #f60;
  color: #fff;
  border: 0;
  width: 100%;
  line-height: 1rem;
  margin-top: 0.2rem;
  font-size: 0.36rem;
}
</style>
