<template lang="html">
  <div class="login">
      <header-view :title="'密码登录'"></header-view>
      <div class="logo">
          <img src="../../assets/image/logo.png" alt="">
      </div>
      <div class="form">
          <div class="cell">
              <input type="text" v-model="tel" name="" placeholder="手机号">
          </div>
          <div class="cell">
              <input type="password" v-model="pwd" name="" value="" placeholder="密码"  v-if="see_pwd_swt" >
              <input type="text"  v-model="pwd" name="" value="" placeholder="密码" v-if="!see_pwd_swt">
              <img src="../../assets/image/login/close_eye.png" @click="can_see_pwd()" v-if="see_pwd_swt" />
              <img src="../../assets/image/login/eye.png" @click="can_see_pwd()" v-if="!see_pwd_swt"/>
          </div>
          <div class="item">
            <router-link :to="{ name: 'register', params: {} }">点我注册</router-link>
            <router-link :to="{ name: 'reset_pwd', params: {} }">找回密码</router-link>
          </div>
          <div class="btn_box">
            <div class="btn" @click="login">登录</div>
          </div>
      </div>
  </div>
</template>
<script>
import Header from '../../components/header/Header.vue'
import API from "../../api/api.js"
import { setTimeout } from 'timers';
export default {
  data(){
    return{see_pwd_swt:true,pwd:"",tel:""}
  },
  components:{'header-view':Header},
  mounted(){

  },
  methods:{
    can_see_pwd(){
      this.see_pwd_swt=!this.see_pwd_swt;
    },
    login(){
      var _this=this;
      this.$http.post(API.signin,{
        mobile:this.tel,
        login_type:1,
        password:this.pwd
      }).then(res=>{
        if(res.data.code==200){
          localStorage.setItem("key",res.data.data.key);
          localStorage.setItem("info",JSON.stringify(res.data.data.info));
            var index = localStorage.getItem("beforeindex")
            if(index){
                window.location.href = index;
            }else{
                _this.$router.push({name:"usercenter"});
            }
            _this.$vux.toast.text(res.data.msg, 'top');
          // }
        }else{
           _this.$vux.toast.text(res.data.msg, 'middle');
        }
      })
    }
  }                    
}
</script>

<style lang="less" scoped>
.login{
  background: white;
  .logo{
    margin-top: .9rem; height: 2.5rem;display: flex;align-items: center;justify-content: center;
    img{ width: 1.2rem; height: 1.2rem;}
  }
  .form{
    .cell{
      width: 80%; height: 1rem; border-bottom: 1px solid #f1f1f1; margin: 0 auto; display: flex; align-items: center;justify-content: center;
      input{ width: 100%; height: 100%; border:0; outline: none;}
      img{ width: .4rem; height: .4rem; }
      span{display: block;width: 2rem;  font-size: .22rem; text-align: center;}
    }
    .item{display: flex;width: 80%;height: 1rem;margin: 0 auto;align-items: center; justify-content: space-between;
      a{ font-size: .22rem;}
    }
    .btn_box{width: 80%;height: 1rem;line-height: 1rem;margin: .1rem auto; border-radius: .04rem;background: #FF0036; color: white;
      .btn{text-align: center;}
    }
  }
  .other{
    position: fixed;bottom: 0;height: 3rem; width: 100%;
    .o_tit{height: 1rem;line-height: 1rem;text-align: center; color: #8A8A8A;}
    .o_tit::after{content: ''; position: absolute; top: 18%; background: #f1f1f1;width: 30%;height: 1px; }
    .o_tit::before{content: '';position: absolute;top: 18%;background: #f1f1f1;width: 30%;height: 1px;}
    .o_tit::before{left: 10%;}
    .o_tit::after{right: 10%;}
    .o_login{width: 80%; margin: 0 auto;display: flex;align-items: center;justify-content: space-around;}
  }
}
</style>
