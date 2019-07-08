<template>
  <div class="share">
      <header-view :title="'下发积分记录'"></header-view>
        <div class="main">
            <div class="transferLists" v-for="(item,i) in transferLists" :key="i">
              <van-swipe-cell :right-width="60" :left-width="60">
                <van-cell
                  :border="false"
                  title="类型"
                  :value="item.change_type_name"
                />
                <van-cell
                  :border="false"
                  title="详情"
                  :value='item.change_desc'
                />
                <van-cell
                  :border="false"
                  title="创建时间"
                  :value='item.created_time'
                />
              </van-swipe-cell>
            </div>
        </div>
        <van-pagination 
            v-model="currentPage" 
            :page-count="pagecount"
            mode="simple" 
            @change="gettransferLists"
        />
  </div>
</template>
<style lang="less">
.main {
  margin-top: 1rem;
  text-align: center;
  .transferLists {
    border-bottom: 1px solid #ccc;
    text-align: left;
    .van-swipe-cell__right button {
      height: 100%;
    }
    .van-swipe-cell__left button {
      height: 100%;
    }
  }
}
.add {
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
</style>
<script>
import Header from "../../../components/header/Header.vue";
import API from "../../../api/api.js";
import { Dialog } from "vant";
export default {
  data() {
    return {
      transferLists: [],
      currentPage: 1,
      pagecount: 1
    };
  },
  components: {
    "header-view": Header
  },
  mounted() {
    this.gettransferLists();
  },
  methods: {
    gettransferLists() {
      var _this = this;
      _this.$http
        .get(API.transfer, {
          params: {
            limit: 5,
            page: _this.currentPage
          }
        })
        .then(res => {
          if (res.data.code == 200) {
            _this.transferLists = res.data.data.list;
            _this.pagecount = res.data.data.pagecount;
          }
        });
    }
  }
};
</script>
