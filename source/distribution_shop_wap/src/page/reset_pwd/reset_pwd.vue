<template lang="html">
  <div class="login">
      <header-view :title="'重置密码'"></header-view>
      <div class="logo">
          <img src="../../assets/image/login/logo.png" alt="">
      </div>
      <div class="form">
          <div class="cell">
              <input type="text" v-model="tel" name="" placeholder="请输入手机号">
          </div>

          <div class="cell">
              <input type="text" v-model="pwd" name="" value="" placeholder="请输入验证码">
              <x-button @click.native="get_phonecode($event)" :mini="true" :disabled="btn_swt">{{txt}}</x-button>
          </div>
          <div class="cell">
              <input type="password" v-model="passwad" name="" placeholder="新密码（6-20位）">
          </div>
          <div class="cell">
              <input type="password" v-model="passwad_two" name="" placeholder="确认新密码">
          </div>
          <div class="btn_box">
            <div class="btn" @click="reset_pwd">
              完成
            </div>
          </div>
      </div>

  </div>
</template>

<script>
import { XButton} from 'vux'

import Header from '../../components/header/Header.vue'
import API from "../../api/api.js"

export default {
  data(){
    return{
      see_pwd_swt:false,
      txt:"获取验证码",
      pwd:"",
      tel:"",
      passwad:"",
      passwad_two:"",
      btn_swt:false
    }
  },
  components:{
    'header-view':Header,
    XButton
  },
  methods:{
    can_see_pwd(){
      this.see_pwd_swt=!this.see_pwd_swt;
    },
    get_phonecode(ev){

        var _this=this;
        this.$http.post(API.send,{
          mobile:this.tel,
          type:3
        }).then(res=>{
            _this.$vux.toast.text(res.data.msg, 'top');
            if(res.data.code==200){
              _this.btn_swt=true;
              var n=60;
              var timer=setInterval(function() {
                --n;
                _this.txt=n+"s重新获取";
                if(n<=0){
                  clearInterval(timer);
                  _this.btn_swt=false;
                  _this.txt="获取验证码";
                }
              },1000)
            }
        }).catch(err=>{

        })
    },
    reset_pwd(){
      var _this=this;
      this.$http.post(API.forget,{
        mobile:this.tel,
        password:this.passwad,
        repassword:this.passwad_two,
        sms_code:this.pwd
      }).then(res=>{
        _this.$vux.toast.text(res.data.msg, 'top');
         setTimeout(function(){
            _this.$router.push({name:"login"});
          },1500)
      })
    }

  }
}
</script>

<style lang="less">
.login{
  background: white;
  .logo{
    margin-top: .9rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    img{
      width: 1.2rem;
      height: 1.2rem;
    }
  }
  .form{
    .cell{
      width: 80%;
      height: 1rem;
      border-bottom: 1px solid #f1f1f1;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
      input{
        width: 100%;
        height: 100%;
        border:0;
        outline: none;

      }
      img{
        width: .4rem;
        height: .4rem;
      }
      button{
        display: block;
        width: 2rem;
        height: 100%;
        font-size: .22rem;
        text-align: center;
        padding: 0;
        background: white;
      }
      button::after{
        border: 0;
      }
    }
    .item{
      display: flex;
      width: 80%;
      height: 1rem;
      margin: 0 auto;
      align-items: center;
      justify-content: space-between;
      a{
        font-size: .22rem;
      }
    }
    .btn_box{

      width: 80%;
      height: 1rem;
      line-height: 1rem;
      margin: .5rem auto;
      border-radius: .04rem;

      background: #FF0036;
      color: white;
      .btn{
          text-align: center;
      }
    }
  }
  .other{
    position: fixed;
    bottom: 0;
    height: 3rem;
    width: 100%;
    .o_tit{
      height: 1rem;
      line-height: 1rem;
      text-align: center;
      color: #8A8A8A;
    }
    .o_tit::after{
      content: '';
      position: absolute;
      top: 18%;
      background: #f1f1f1;
      width: 30%;
      height: 1px;
    }
    .o_tit::before{
      content: '';
      position: absolute;
      top: 18%;
      background: #f1f1f1;
      width: 30%;
      height: 1px;
    }
    .o_tit::before{
      left: 10%;
    }
    .o_tit::after{
      right: 10%;
    }
    .o_login{
      width: 80%;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-around;
    }
  }
}
</style>
