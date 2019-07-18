<template>
  <div class="share">
      <header-view :title="'提现申请记录'"></header-view>
      <!-- <button type="button" class="add" @click="addcard()">新增</button> -->
        <div class="main">
            <div class="withdrawLists" v-for="(item,i) in withdrawLists" :key="i">
              <van-swipe-cell :right-width="60" :left-width="60">
                <van-cell
                  :border="false"
                  title="类型"
                  :value="item.type==1?'支付宝':'银行卡'"
                />
                <van-cell
                  :border="false"
                  title="银行名称"
                  :value='item.bank_name'
                  v-show="item.type==2"
                />
                <van-cell
                  :border="false"
                  :title="item.type==1?'支付宝号':'银行卡号'"
                  :value='item.type==1?item.alipay_account:item.bank_number'
                />
                <van-cell
                  :border="false"
                  title="提现金额"
                  :value='item.withdraw_money'
                />
                <van-cell
                  :border="false"
                  title="提现手续费"
                  :value='item.withdraw_service_charge_money'
                />
                <van-cell
                  :border="false"
                  title="状态"
                  :value='item.status_name'
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
            @change="getwithdrawLists"
        />
  </div>
</template>
<style lang="less">
.main {
  margin-top: 1rem;
  text-align: center;
  .withdrawLists {
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
import Header from "../../components/header/Header.vue";
import API from "../../api/api.js";
import { Dialog } from "vant";
export default {
  data() {
    return {
      withdrawLists: [],
      currentPage: 1,
      pagecount: 1
    };
  },
  components: {
    "header-view": Header
  },
  mounted() {
    this.getwithdrawLists();
  },
  methods: {
    getwithdrawLists() {
      var _this = this;
      _this.$http
        .get(API.withdraw, {
          params: {
            limit: 2,
            page: _this.currentPage
          }
        })
        .then(res => {
          if (res.data.code == 200) {
            _this.withdrawLists = res.data.data.list;
            _this.pagecount = res.data.data.pagecount;
          }
        });
    },
    addcard() {
      this.$router.push({
        name: "cardadd",
        query: { choosecard: this.$route.query.choosecard ? 1 : 0 }
      });
    },
    cardInfo(id) {
      this.$router.push({
        name: "cardedit",
        query: { id: id, choosecard: this.$route.query.choosecard ? 1 : 0 }
      });
    }
  }
};
</script>
