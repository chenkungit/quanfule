<template>
  <div id='background'>
    <div style="position:fixed;z-index:888;left:50%;margin-left:-211px;">
    <el-form :model="ruleForm2" :rules="rules2" ref="ruleForm2" label-position="left" label-width="0px" class="demo-ruleForm login-container">
      <h3 class="title">后台系统登录</h3>
      <el-form-item prop="account">
        <el-input type="text" v-model="ruleForm2.account" auto-complete="off" placeholder="账号" size="medium">
          <template slot="prepend">账号</template>
        </el-input>
      </el-form-item>
      <el-form-item prop="checkPass">
        <el-input type="password" v-model="ruleForm2.checkPass" auto-complete="off" placeholder="密码" size="medium" @keyup.enter.native="handleSubmit2">
          <template slot="prepend">密码</template>
        </el-input>
      </el-form-item>
      <!-- <el-checkbox v-model="checked" checked class="remember">下次自动登录</el-checkbox> -->
      <el-form-item style="width:100%; text-aligin:center">
        <el-button type="primary" style="width:100%;" @click.native.prevent="handleSubmit2" :loading="logining" size="medium">登录</el-button>
        <!-- <el-button type="warning" style="width:40%; float:left" @click.native.prevent="handleReset2" size="medium">重置</el-button> -->
      </el-form-item>
    </el-form>
    </div>
  </div>
</template>
<style>
#background {
  overflow: hidden;
  height: 100%;
  background-size: 100% 100%;
  background: #93defe;
  /* opacity:0.5 */
  /* background-color:#ff7e00 !important; */
}
#cas {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  right: 0;
  margin: auto;
  width: 100%;
  height: 100%;
  background-color: #409eff;
  opacity: 0.5;
  z-index: 1;
}
</style>

<script>
import { requestLogin } from "../api/api";
import { addrou } from "../main";
import { mapGetters } from "vuex";
import { mapActions } from "vuex";
//import NProgress from 'nprogress'
export default {
  data() {
    return {
      logining: false,
      ruleForm2: {
        account: "admin",
        checkPass: ""
      },
      rules2: {
        account: [
          { required: true, message: "请输入账号", trigger: "blur" }
          //{ validator: validaePass }
        ],
        checkPass: [
          { required: true, message: "请输入密码", trigger: "blur" }
          //{ validator: validaePass2 }
        ]
      },
      checked: true
    };
  },
  created() {},
  methods: {
    ...mapActions([
      "settoken" // 映射 this.increment() 为 this.$store.dispatch('increment')
    ]),
    handleSubmit2(ev) {
      var _this = this;
      this.$refs.ruleForm2.validate(valid => {
        if (valid) {
          //  console.log(1)
          //_this.$router.replace('/table');

          //NProgress.start();
          var loginParams = {
            account: this.ruleForm2.account,
            password: this.ruleForm2.checkPass
          };

          requestLogin(loginParams).then(data => {
            if (data.code !== 200) {
              alert(data.msg);
            } else {
              this.logining = true;
              sessionStorage.setItem("data", "");
              sessionStorage.setItem("data", JSON.stringify(data));
              if (this.checked) {
                this.setcookie("name999", this.ruleForm2.account, 7);
                this.setcookie("password999", this.ruleForm2.checkPass, 7);
              } else {
                this.setcookie("name999", "", -1);
                this.setcookie("password999", "", -1);
              }
              this.$router.push({ path: "/" });
              this.settoken();
            }
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    }
  },
  mounted() {
    if (this.getcookie("name999")) {
      this.ruleForm2.account = this.getcookie("name999");
      this.ruleForm2.checkPass = this.getcookie("password999");
      this.handleSubmit2();
    }
  }
};
</script>

<style lang="scss" scoped>
.login-container {
  /*box-shadow: 0 0px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);*/
  z-index: 2;
  -webkit-border-radius: 5px;
  border-radius: 5px;
  -moz-border-radius: 5px;
  background-clip: padding-box;
  margin: 230px auto;
  width: 350px;
  padding: 35px 35px 15px 35px;
  background: #fff;
  border: 1px solid #eaeaea;
  box-shadow: 0 0 25px #cac6c6;
  .title {
    margin: 0px auto 40px auto;
    text-align: center;
    color: #505458;
  }
  .remember {
    margin: 0px 0px 35px 0px;
  }
}
</style>
