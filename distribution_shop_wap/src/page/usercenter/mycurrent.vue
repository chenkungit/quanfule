<template>
  <div class="share">
      <header-view :title="'收支流水'"></header-view>
      <!-- <button type="button" class="add" @click="addcard()">新增</button> -->
      <div class="select">       
         <div :class="money_type==0?'selectactive':''" @click="getmoney(0)"><i class="iconfont">&#xe638;</i>&nbsp积分</div>
         <div :class="money_type==1?'selectactive':''" @click="getmoney(1)"><i class="iconfont">&#xe62a;</i>&nbsp余额</div>
      </div>
     <scroller lock-x :height="Scroller_hight"  ref="scrollerEvent" @on-scroll-bottom="onScroll_buttom"  style="margin-top:0.9rem">
        <div class="mainc">
                <x-table :cell-bordered="false" :content-bordered="false" style="background-color:#fff;">
        <thead>
          <tr style="background-color: #F7F7F7">
            <th>日期</th>
            
            <th>类型</th>
            <th>{{money_type==1?'余额':'积分'}}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item,i) in flowLists" :key="i">
            <td>{{item.created_time}}</td>
           
            <td>{{item.change_type_name}}</td>
             <td>{{money_type==1?item.prize_money:item.user_money}}</td>
          </tr>
        </tbody>
      </x-table>
        </div>
        </scroller>
  </div>
</template>
<style lang="less">
.select {
  bottom: 0rem;
  position: fixed;
  width: 100%;
  border-top: 1px solid #e0e0e0;
  z-index: 999999;
  div {
    float: left;
    width: 50%;
    text-align: center;
    line-height: 0.9rem;
    font-size: 0.38rem;
  }
}
.selectactive {
  color: green;
}
.van-cell__value span {
  word-break: break-all;
  word-wrap: break-word;
}
.mainc {
  margin-top: 0;
  text-align: center;
  .flowLists {
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
import { Scroller, XTable } from "vux";
import { Dialog } from "vant";
export default {
  data() {
    return {
      flowLists: [],
      currentPage: 1,
      pagecount: 1,
      change_type: 0,
      loading: false,
      money_type: 0,
      Scroller_hight: "calc(100% - 1.8rem)",
      change_typeoption: [
        { text: "全部类型", value: 0 },
        { text: "系统转账", value: 1 },
        { text: "用户消费", value: 2 },
        { text: "返佣奖励", value: 3 },
        { text: "申请提现", value: 4 },
        { text: "提现成功", value: 5 },
        { text: "领导奖奖励", value: 6 },
        { text: "业绩奖奖励", value: 7 },
        { text: "积分转出", value: 8 },
        { text: "积分转入", value: 9 },
        { text: "用户退款", value: 10 },
        { text: "奖励金转换积分", value: 11 },
        { text: "每月终生成就奖奖励", value: 12 }
      ],
      change_type2: 0,
      change_type2option: [
        { text: "余额", value: 0 },
        { text: "积分", value: 1 }
      ]
    };
  },
  components: {
    "header-view": Header,
    Scroller,
    XTable
  },
  mounted() {
    this.getflowLists();
  },
  methods: {
    onScroll_buttom() {
      if (this.loading) {
        this.currentPage++;
        if (this.currentPage > this.pagecount) {
          this.$vux.toast.text("没有更多了", "middle");
        }
      }
      if (this.currentPage <= this.pagecount && this.loading) {
        this.getflowLists();
      }
    },
    getmoney(type) {
      if (this.money_type != type) {
        this.loading = true;
        this.flowLists = [];
        this.pagecount = 1;
        this.currentPage=1
      }
      this.money_type = type;
      this.getflowLists(type);
    },
    getflowLists(type) {
      var _this = this;
      this.loading = false;

      var params = {
        limit: 15,
        page: _this.currentPage
      };
      if (_this.change_type) params.change_type = _this.change_type;
      params.money_type = _this.money_type;
      _this.$http
        .get(API.treasureflow, {
          params: params
        })
        .then(res => {
          if (res.data.code == 200) {
            _this.loading = true;
            if(_this.currentPage==1){
              _this.flowLists=res.data.data.list;
            }else{
            _this.flowLists = _this.flowLists.concat(res.data.data.list);
            }
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
