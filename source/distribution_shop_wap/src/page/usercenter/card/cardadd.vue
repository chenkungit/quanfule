<template>
  <div class="login">
      <header-view title="新增"></header-view>
      <div class="formbox">
          <div class="item" @click="dialog_type=!dialog_type">
             <label>类型</label>
            <i class="iconfont">&#xe713;</i>
            <input type="text" v-model="mytype" readonly>
          </div>
          <div class="item" v-show="userinfo.type==1">
             <label>支付宝号</label>
            <input type="text" v-model="userinfo.alipay_account">
          </div>
          <div class="item" v-show="userinfo.type==2">
             <label>银行卡号</label>
            <input type="text" v-model="userinfo.bank_number" @blur="ckeckcard">
          </div>
          <div class="item" v-show="userinfo.type==2&&userinfo.bank_name">
             <label>银行卡类型</label>
            <span style="float:right;line-height:1rem">{{userinfo.bank_name}}</span>
          </div>
      </div>
      <div class="kuaidi">
        <dialog-bottom v-show="dialog_type" @change_swt="hide_dialog_type">
          <div class="k_title">
            选择类型
          </div>
          <div class="k_list">
            <div class="cell" v-for="(v,i) in type" @click="checked_type(i)" :key="i">
              <span>{{v.name}}</span>
              <i class="iconfont" v-show="userinfo.type==v.id?true:false">&#xe604;</i>
            </div>

          </div>
        </dialog-bottom>
      </div>
      <button  type="button" class="cardsave" @click="cardsave">保存</button>
  </div>
</template>

<script>
import { XButton } from "vux";
import Header from "../../../components/header/Header.vue";
import dialog_bottom from "../../../components/dialog_bottom/dialog_bottom.vue";
import { Dialog } from "vant";
import API from "../../../api/api.js";
import { setTimeout } from "timers";

export default {
  components: { "header-view": Header, "dialog-bottom": dialog_bottom },

  data() {
    return {
      userinfo: { type: 1, alipay_account: "", bank_name: "", bank_number: "" },
      dialog_type: false,
      type: [{ name: "银行卡", id: 2 }, { name: "支付宝", id: 1 }]
    };
  },
  created() {
    var _this = this;
  },
  methods: {
    ckeckcard() {
      var _this = this;
      _this.$http
        .post(API.cardCheck, {
          bank_number: _this.userinfo.bank_number
        })
        .then(res => {
          if (res.data.code == 200) {
            this.userinfo.bank_name = res.data.data.bankName;
          } else {
            _this.$vux.toast.text(res.data.msg, "middle");
          }
        });
    },
    cardsave() {
      var _this = this;
      _this.$http.post(API.addCard, this.userinfo).then(res => {
        if (res.data.code == 200) {
          _this.$vux.toast.text(res.data.msg, "middle");
          setTimeout(function() {
            _this.$router.push({
              name: "mycard",
              query: { choosecard: _this.$route.query.choosecard ? 1 : 0 }
            });
          }, 1500);
        }
      });
    },
    hide_dialog_type() {
      this.dialog_type = !this.dialog_type;
    },
    checked_type(i) {
      this.userinfo.type = this.type[i].id;
    }
  },
  computed: {
    mytype: function() {
      var type = this.userinfo.type;
      if (type == 2) {
        type = "银行卡";
      } else if (type == 1) {
        type = "支付宝";
      } else {
        type = "未设置";
      }
      return type;
    }
  }
};
</script>

<style lang="less" scoped>
.van-uploader {
  height: 1rem;
  position: fixed;
  top: 1rem;
  right: 0rem;
  width: 2rem;
  opacity: 0;
  z-index: 99999999999999;
  .van-uploader__input {
    width: 100%;
  }
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
.cardsave {
  background: #f60;
  color: #fff;
  width: 90%;
  line-height: 1rem;
  border: 0;
  border-radius: 0.2rem;
  margin: 1rem 5%;
}
.login {
  background: white;
  .formbox {
    margin-top: 0.9rem;
    .avatar {
      width: 0.8rem;
      height: 0.8rem;
      overflow: hidden;
      border-radius: 50%;
      float: right;
      margin-top: 0.1rem;
      background: #e5cc63;
      position: relative;
      img {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        margin: auto;
        width: 100%;
      }
      span {
        display: block;
        text-align: center;
        line-height: 1rem;
        margin: 0;
        font-size: 0.6rem;
        color: #ffefde;
      }
    }
    .item {
      height: 1rem;
      border-bottom: 1px solid #f1f1f1;
      margin: 0 auto;
      padding: 0 0.3rem;
      justify-content: center;
      label {
        display: inline-block;
        color: #121212;
        font-size: 0.28rem;
        line-height: 1rem;
      }
      input {
        width: 50%;
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
  }
  .save {
    border: 0;
    background: transparent;
    color: #6b6b6b;
    padding: 0 0.3rem;
    height: 0.5rem;
    line-height: 0.5rem;
    font-size: 0.3rem;
    position: fixed;
    right: 0.3rem;
    top: 0.2rem;
    z-index: 100;
  }
}
</style>
