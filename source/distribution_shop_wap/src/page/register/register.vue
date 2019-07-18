<template lang="html">
  <div class="login">
  
    <header-view :title="'欢迎注册'"></header-view>
  
    <div class="logo">
  
      <img src="../../assets/image/logo.png" alt="">
  
    </div>
  
    <div class="form">
  
      <div class="cell">
  
        <input type="text" v-model="tel" name="" placeholder="请输入手机号">
  
      </div>
  
  			<!--<div class="cell">
            <input type="text" v-model="captcha_txt" placeholder="请输入图形验证码">
            <img :src="captcha"  alt="" @click="get_captche">
        </div>-->
  
      <div class="cell">
  
        <input type="text" v-model="pwd" name="" value="" placeholder="请输入验证码">
  
        <x-button @click.native="get_phonecode($event)" :mini="true" :disabled="btn_swt">{{txt}}</x-button>
  
      </div>
  
      <div class="cell">
  
        <input type="password" v-model="passwad" name="" placeholder="设置密码（6-20位）">
  
      </div>
<!--   
      <div class="cells">
  
        <p>*注册代表同意
  
          <router-link :to="{ name: 'serve_txt', params: {} }">服务使用协议</router-link>
  
        </p>
   -->
      </div>
  
      <div class="btn_box">
  
        <x-button class="btn" @click.native="register()" :disabled="isdisabled==0?true:false">确定</x-button>
  
      </div>
  
  
  
    </div>
  
  
  
  </div>
</template>

<script>
import { XButton } from "vux";

import Header from "../../components/header/Header.vue";

import API from "../../api/api.js";

import { setTimeout } from "timers";

export default {
  data() {
    return {
      see_pwd_swt: false,

      txt: "获取验证码",

      pwd: "",

      tel: "",

      passwad: "",

      btn_swt: false,
      device_id: "",
      captcha: "",
      captcha_txt: "",
      isdisabled: 1
    };
  },

  components: {
    "header-view": Header,

    XButton
  },
  mounted() {
    this.device_id = this.randomWord(true, 6, 32);
    // this.get_captche();
  },

  methods: {
    randomWord(randomFlag, min, max) {
      var str = "",
        range = min,
        arr = [
          "0",
          "1",
          "2",
          "3",
          "4",
          "5",
          "6",
          "7",
          "8",
          "9",
          "a",
          "b",
          "c",
          "d",
          "e",
          "f",
          "g",
          "h",
          "i",
          "j",
          "k",
          "l",
          "m",
          "n",
          "o",
          "p",
          "q",
          "r",
          "s",
          "t",
          "u",
          "v",
          "w",
          "x",
          "y",
          "z",
          "A",
          "B",
          "C",
          "D",
          "E",
          "F",
          "G",
          "H",
          "I",
          "J",
          "K",
          "L",
          "M",
          "N",
          "O",
          "P",
          "Q",
          "R",
          "S",
          "T",
          "U",
          "V",
          "W",
          "X",
          "Y",
          "Z"
        ];

      // 随机产生
      if (randomFlag) {
        range = Math.round(Math.random() * (max - min)) + min;
      }
      for (var i = 0; i < range; i++) {
        var pos = Math.round(Math.random() * (arr.length - 1));
        str += arr[pos];
      }
      return str;
    },
    // get_captche() {
    //   this.$http
    //     .post(API.captcha, {
    //       device_id: this.device_id
    //     })
    //     .then(res => {
    //       this.captcha = res.data.data.captcha;
    //       // console.log(res.data.data.captcha);
    //     });
    // },
    can_see_pwd() {
      this.see_pwd_swt = !this.see_pwd_swt;
    },

    get_phonecode(ev) {
      var _this = this;

      this.$http
        .post(API.send, {
          mobile: this.tel,
          type: 2,
          // device_id: this.device_id,
          // captcha: this.captcha_txt
        })
        .then(res => {
          _this.$vux.toast.text(res.data.msg, "top");

          if (res.data.code == 200) {
            _this.btn_swt = true;

            var n = 60;

            var timer = setInterval(function() {
              --n;

              _this.txt = n + "s重新获取";

              if (n <= 0) {
                clearInterval(timer);

                _this.btn_swt = false;

                _this.txt = "获取验证码";
              }
            }, 1000);
          }
        })
        .catch(err => {});
    },

    register() {
      var _this = this;
      _this.isdisabled = 0;

      this.$http
        .post(API.signup, {
          mobile: this.tel,

          password: this.passwad,

          sms_code: this.pwd,
          account: "huacai_h5"
        })
        .then(res => {
          _this.$vux.toast.text(res.data.msg, "top");
          if (res.data.code == 200) {
            localStorage.setItem("key", res.data.data.key);
            _this.getAddress();
            localStorage.setItem("info", JSON.stringify(res.data.data.info));
            var index = localStorage.getItem("beforeindex");
            if (index) {
              window.location.replace(index);
            } else {
              setTimeout(function() {
                _this.$router.push({ name: "usercenter" });
              }, 1500);
            }
          }
        });
    },
     getAddress(){
      this.$http
        .get(API.lists, {
          params: {}
        })
        .then(function(res) {
          var item = res.data.data;
          item.forEach(function(v, i) {
            if(v.is_default==1){
                isflag = true;
                localStorage.setItem("defaultcity",v.city+','+v.city_name);
            }
          });
          
        }).catch(function(reason) {});
    },
  }
};
</script>

<style lang="less" scoped>
.login {
  background: white;

  .logo {
    margin-top: 0.9rem;

    height: 2.5rem;

    display: flex;

    align-items: center;

    justify-content: center;

    img {
      width: auto;

      height: 1.2rem;
    }
  }

  .form {
    .cell {
      width: 80%;

      height: 1rem;

      border-bottom: 1px solid #f1f1f1;

      margin: 0 auto;

      display: flex;

      align-items: center;

      justify-content: center;

      input {
        width: 100%;

        height: 100%;

        border: 0;

        outline: none;
      }

      img {
        width: 1.4rem;

        height: 0.9rem;
      }

      button {
        display: block;

        width: 2rem;

        height: 100%;

        font-size: 0.22rem;

        text-align: center;

        padding: 0;

        background: white;
      }

      button::after {
        border: 0;
      }
    }

    .item {
      display: flex;

      width: 80%;

      height: 1rem;

      margin: 0 auto;

      align-items: center;

      justify-content: space-between;

      a {
        font-size: 0.22rem;
      }
    }

    .cells {
      width: 80%;

      height: 1rem;

      line-height: 1rem;

      margin: 0 auto;

      p {
        font-size: 0.22rem;

        a {
          color: #03a9f4;
        }
      }
    }


  }
    .btn_box {
      width: 80%;

      height: 1rem;

      line-height: 1rem;

      margin: 0.5rem auto;

      border-radius: 0.04rem;

      background: #ff0036;

      color: white;

      .btn {
        text-align: center;
        background: #ff0036;
        color: white;
        height: 1rem;
        line-height: 1rem;
        border-radius: 0.04rem;
      }
      .btn:after {
        border: 0;
      }
      .weui-btn_disabled {
        background: #ccc;
      }
    }
  .other {
    position: fixed;

    bottom: 0;

    height: 3rem;

    width: 100%;

    .o_tit {
      height: 1rem;

      line-height: 1rem;

      text-align: center;

      color: #8a8a8a;
    }

    .o_tit::after {
      content: "";

      position: absolute;

      top: 18%;

      background: #f1f1f1;

      width: 30%;

      height: 1px;
    }

    .o_tit::before {
      content: "";

      position: absolute;

      top: 18%;

      background: #f1f1f1;

      width: 30%;

      height: 1px;
    }

    .o_tit::before {
      left: 10%;
    }

    .o_tit::after {
      right: 10%;
    }

    .o_login {
      width: 80%;

      margin: 0 auto;

      display: flex;

      align-items: center;

      justify-content: space-around;
    }
  }
}
</style>
