<template>

</template>
<script>
import { setTimeout } from 'timers';
  export default {
    data() {
  
      return {};
    },
    methods: {},
    mounted() {
      var _this = this;
      localStorage.setItem("beforeindex",window.location.href)
      var key = localStorage.getItem("key");
      var mobile = "";
      if(localStorage.getItem("info")){
        mobile = JSON.parse(localStorage.getItem("info")).mobile;
      }
      
      if(key == ""||!key||!localStorage.getItem("info")){
        _this.$vux.toast.text("登陆后才可查看哦！", "middle");
        // setTimeout(function(){
        //      _this.$router.push({ name: "login"})
        // },2000)
      }
    }
  };
</script>
<style lang='less' scoped>
  
</style>
