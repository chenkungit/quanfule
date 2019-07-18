<template>
  <div>
    <header-view :title="'404'"></header-view>
    <div class="img404">
        <img src="http://ou3n3c59p.bkt.clouddn.com/404.png" @click="reload()"/>
    </div>

  </div>
</template>
<script>
    import Header from "../../components/header/Header.vue";

export default {
   components: {
    
            "header-view": Header,
   
        },
    methods:{
        reload(){
            window.location.reload()
        }
    }    
}
</script>

<style lang="less">
    .img404{
        margin-top: 1rem;
        height: calc(100% - 10rem);
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        img{
            width: 2.6rem;
            height: 2rem;
        }
    }
</style>
