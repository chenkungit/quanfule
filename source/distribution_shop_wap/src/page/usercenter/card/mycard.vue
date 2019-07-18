<template>
  <div class="share">
      <header-view :title="'我的卡号'"></header-view>
      <button type="button" class="add" @click="addcard()">新增</button>
        <div class="main">
            <div class="cardlists" v-for="(item,i) in cardLists" :key="i" @click="choosecard(item)">
              <van-swipe-cell :right-width="60" :left-width="60">
                  <van-button
                    square
                    slot="left"
                    type="info"
                    text="编辑"
                    @click="cardInfo(item.id)"
                  />
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
                <van-button
                  square
                  slot="right"
                  type="danger"
                  text="删除"
                  @click="carddel(item.id)"
                />
              </van-swipe-cell>
              
              <!-- <div><span>类型:   </span><span v-show="item.type==1">支付宝</span><span v-show="item.type==2">银行卡</span></div>
              <div v-show="item.type==2"><span>银行名称:   </span>{{item.bank_name}}</div>
              <div><span  v-show="item.type==1">支付宝号:</span><span v-show="item.type==2">银行卡号:</span>{{item.type==1?item.alipay_account:item.bank_number}}</div> -->
            </div>
        </div>
  </div>
</template>
<style lang="less">
.main {
  margin-top: 1rem;
  text-align: center;
  .cardlists {
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
      cardLists: []
    };
  },
  components: {
    "header-view": Header
  },
  mounted() {
    this.getcard();
  },
  methods: {
    choosecard(item) {
      if (this.$route.query.choosecard) {
        this.$router.push({
          name: "withdraw",
          query: {
            choose_card_data: JSON.stringify(item)
          }
        });
      }
    },
    carddel(id) {
      var _this = this;
      Dialog.confirm({
        title: "确认",
        message: "确认删除？"
      }).then(() => {
        _this.$http.delete(API.cardDelete + id).then(res => {
          var d = res.data;
          if (d.code == 200) {
            this.$vux.toast.text(d.msg, "middle");
            _this.getcard();
          }
        });
      });
    },
    getcard() {
      var _this = this;
      _this.$http
        .get(API.cardLists, {
          params: {
            key: localStorage.getItem("key"),
            limit: 100
          }
        })
        .then(res => {
          if (res.data.code == 200) {
            _this.cardLists = res.data.data.list;
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
