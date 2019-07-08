<template lang="html">
  <div class="ser_con">
    <header-view :title="'服务协议'"></header-view>
    <div class="txt">
      <h2>一、接受条款</h2>
      <p class="t_indent">
       
      </p>
      <p class="t_indent">
    

      </p>
      <h2>
        二、服务说明
      </h2>
      <p class="t_indent">
        

      </p>
      <p class="mt10">
        

      </p>
      <p class="mt10">
        

      </p>
      <p class="mt10">

      </p>
    </div>
  </div>
</template>

<script>
import Header from '../../components/header/Header.vue'

export default {
  components:{
    "header-view":Header
  }
}
</script>

<style lang="less">
.ser_con{
  background: white;
}
.txt{
  margin-top: .9rem;
  padding: 0 .2rem;
  h2{
    font-size: .3rem;

  }
  p{
    font-size: .24rem;
    text-align: justify;
  }
  .t_indent{
    text-indent: .2rem;
    margin-top: .2rem;
  }
  .mt10{
    margin-top: .1rem;
  }
}
</style>
