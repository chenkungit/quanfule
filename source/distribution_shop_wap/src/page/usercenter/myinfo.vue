<template>
  <div class="login">
      <header-view title="个人信息"></header-view>
      <button type="button" class="save" @click="save()">保存</button>
      <div class="formbox">
          <div class="item">
             <label>头像</label>
             <van-uploader :after-read="onRead">
             </van-uploader>
            <div class="avatar"><img v-if="userinfo.avatar!=''" :src="userinfo.avatar" alt="">
            <img v-else src="../../assets/image/toux.jpg" alt=""></div>

          </div>
          <div class="item" v-if="vip_info.vip_code">
             <label>会员ID</label>
            <input type="text" v-model="vip_info.vip_code" readonly>
          </div>
          <div class="item" v-if="vip_info.name">
             <label>会员等级</label>
            <input type="text" v-model="vip_info.name" readonly>
          </div>
          <div class="item">
             <label>昵称</label>
            <input type="text" v-model="userinfo.nick">
          </div>
          <div class="item" @click="dialog_sex=!dialog_sex">
             <label>性别</label>
            <i class="iconfont">&#xe713;</i>
            <input type="text" v-model="mysex" readonly>
          </div>
          <div class="item" @click="forget()">
             <label>密码</label>
            <i class="iconfont">&#xe713;</i>
            <input type="text" readonly>
          </div>
      </div>
      <div class="kuaidi">
        <dialog-bottom v-show="dialog_sex" @change_swt="hide_dialog_sex">
          <div class="k_title">
            选择性别
          </div>
          <div class="k_list">
            <div class="cell" v-for="(v,i) in sex" @click="checked_sex(i)" :key="i">
              <span>{{v.name}}</span>
              <i class="iconfont" v-show="userinfo.sex==v.id?true:false">&#xe604;</i>
            </div>

          </div>
        </dialog-bottom>
      </div>
      <button type="button" class="loginout" @click="loginout">退出登录</button>
  </div>
</template>

<script>
import { XButton } from "vux";
import Header from "../../components/header/Header.vue";
import dialog_bottom from "../../components/dialog_bottom/dialog_bottom.vue";
import { Dialog } from "vant";
import API from "../../api/api.js";
import { setTimeout } from "timers";

export default {
  components: { "header-view": Header, "dialog-bottom": dialog_bottom },

  data() {
    return {
      userinfo: [],
      data_swt: true,
      dialog_sex: false,
      sex: [{ name: "女", id: 2 }, { name: "男", id: 1 }],
      vip_info:{},
      img: null
    };
  },
  created() {
    var _this = this;
    _this.getlist();
  },
  methods: {
    onRead(file) {
      this.userinfo.avatar = file.content;
      this.img = file.file;
    },
    getlist() {
      var _this = this;
      _this.$http
        .get(API.userinfo, {
        })
        .then(res => {
          _this.userinfo = res.data.data.info;
          _this.vip_info = res.data.data.vip_info;
        });
    },
    loginout() {
      var _this = this;
      _this.$http
        .delete(API.logout, {
          params: { key: localStorage.getItem("key") }
        })
        .then(res => {
          //  console.log(res.data)
          if (res.data.code == 200) {
            _this.$vux.toast.text(res.data.msg, "middle");
            localStorage.removeItem("key");
            setTimeout(function() {
              _this.$router.replace({ name: "login" });
            }, 1500);
          }
        });
    },
    forget() {
      this.$router.push({ name: "forget_pwd" });
    },
    hide_dialog_sex() {
      this.dialog_sex = !this.dialog_sex;
    },
    checked_sex(i) {
      this.userinfo.sex = this.sex[i].id;
    },
    save() {
      var _this = this;
      var fd = new FormData();
      fd.append("nick", _this.userinfo.nick);
      fd.append("sex", _this.userinfo.sex);
      fd.append("avatar", _this.img);
      fd.append("key", localStorage.getItem("key"));
      _this.$http.post(API.editinfo, fd).then(res => {
        var data = res.data;
        if (data.code == 200) {
          _this.$vux.toast.text(data.msg, "middle");
        } else {
          _this.$vux.toast.text(data.msg, "middle");
        }
      });
    }
  },
  computed: {
    subArry: function() {
      // console.log(this.userinfo.mobile);
      var mobile = this.userinfo.mobile;
      if (mobile) {
        mobile = mobile.substr(0, 3) + "****" + mobile.substr(7, 10);
      }
      return mobile;
    },
    mysex: function() {
      var sex = this.userinfo.sex;
      // console.log(sex)
      if (sex == 2) {
        sex = "女";
      } else if (sex == 1) {
        sex = "男";
      } else {
        sex = "未设置";
      }
      return sex;
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
.loginout {
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
