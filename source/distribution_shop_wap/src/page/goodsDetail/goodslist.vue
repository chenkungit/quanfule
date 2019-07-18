<template>
  <div class="wt">
    <!-- 搜索输入框 -->
       <div class="search">
          <div class="back" @click="back"><i class="iconfont">&#xe622;</i></div>
          <div class="sou">
              <input placeholder="输入关键字" v-model="keywords" @focus="show_page"/>
          </div>
          <div class="sou_btn" @click="sou">搜索</div>	
      </div>
      <!-- 筛选条件 -->
      <div class="itemlist">
          <div @click="paixu" :class="{active:pxactive}">{{pxcont}}<i class="iconfont">&#xe614;</i></div>
          <div @click="pxsale" :class="{active:saleshow}">销量优先</div>
          <div @click="showstyle"><i class="iconfont" v-if="isshowstyle==1">&#xe663;</i><i class="iconfont" v-else>&#xe67e;</i></div>
          <div @click="filter">筛选<i class="iconfont">&#xe61f;</i></div>
          <ul v-show="isshow" class="animated" :class="{bounceInDown:isshow}">
            <li v-for="(item,i) in paixulist" :key="i" :class="{active:item.isactive}" @click="pxlist(i,item.title,item.order,item.name)">{{item.name}}<i class="iconfont">&#xe604;</i></li>
          </ul>
      </div>
      <!-- 筛选列表 -->
      <div class="itemlist2 animated" v-show="isfilter" :class="{bounceInRight:isfilter}">
          <div class="cont">
              <p>价格区间</p>
              <div class="inputbox"><input type="number" min="1" v-model="lowprice" placeholder="最低价"/>-<input type="number" min="lowprice" v-model="highprice" placeholder="最高价"/></div>
              <p>分类</p>
              <div class="sortbox">
                <div v-for="item in sortlist" @click="clicksort(item.cat_id)" :key="item.cat_id" :class="item.isactive?'active':''"><span>{{item.cat_name}}</span></div>
              </div>
          </div>
          <div class="btnbox">
            <button type="button" @click="reset()">重置</button>
            <button type="button" @click="makesure()">确定</button>
          </div>
      </div>
      <!-- 列表 -->
       <vscorll :on-refresh="onRefresh" :on-infinite="onInfinite">  
         <div class="goodsbox" :class="isshowstyle!=1?'':'goodstwo'">
            <router-link :to="{name:'goodsDetail'}" v-for="item in goodslist" :key="item.id">
              <div class="img"><img :src="item.img" alt=""></div>
              <div class="txt">
                <h2>{{item.name}}</h2>
                <!-- <p>{{item.price}}<span v-for="(val,i) in item.after_mark" :key="i">{{val}}</span></p> -->
              </div>
            </router-link>
         </div>
      </vscorll>
      <!-- 灰色蒙层背景 -->
      <div class="glass" v-show="glassshow" :style="'z-index:'+zindex" @click="noshow"></div>
  </div>
</template>
<script>
import { Scroller } from "vux";
import utils from "../../utils/utils.js";
import API from "../../api/api.js";
import vscorll from "../../components/b_scorll/b_scorll";
export default {
  data() {
    return {
      isshow: false, //排序显示
      glassshow: false, //蒙层显示
      isfilter: false, //筛选显示
      saleshow:true,//销量显示
      pxactive:false,
      pxcont:'综合排序',
      lowprice: "",
      highprice: "",
      zindex: 1,
      keywords:'',
      sort:'sales',
      order:'DESC',
      page:2,
      havedata:true,
      isshowstyle:-1,
      sortlist: [],
      goodslist:[],
      paixulist: [
        { name: "按上新排序", isactive: false ,title:'add_time'},
        { name: "价格由高到低", isactive: false ,title:'sort_price'},
        { name: "价格由低到高", isactive: false ,title:'sort_price',order:'ASC'}
      ]
    };
  },
  components: { Scroller,vscorll },
  methods: {
    sou() {
      if (this.keywords != "") {
        this.hc_sou_history.unshift(this.keywords);
      } else {
        this.$vux.toast.text("输入不能为空", "middle");
      }
      var arr = utils.unique(this.hc_sou_history);
      this.hc_sou_history = arr;
      this.save_storage("hc_sou_history", arr);
    },
    // 展示列表样式
    showstyle(){
        this.isshowstyle = -this.isshowstyle;
        // console.log(this.isshowstyle)
    },
    pxlist(index,item,order,name) {
      var that = this;
      that.sort = item;
      if(order !=undefined){
        that.order = 'asc'
      }
      for (var i = 0; i < that.paixulist.length; i++) {
        that.paixulist[i].isactive = false;
      }
      that.paixulist[index].isactive = true;
      that.isshow = false;
      that.glassshow = false;
       that.saleshow = false;
      that.pxactive = true;
      that.pxcont = name;
      that.goodslist = [];
      that.getlist(1);
    },
    // 点击销量排序
    pxsale(){
      var that = this;
      that.sort = 'sales';
      that.order = 'desc';
      that.saleshow = true;
       that.pxcont = '综合排序';
      for (var i = 0; i < that.paixulist.length; i++) {
        that.paixulist[i].isactive = false;
      }
      that.pxactive = false;
      that.isshow = false;
      that.glassshow = false;
      that.goodslist = [];
      that.getlist(1);

    },
    show_page() {
      this.prompt();
    },
    prompt(keywords) {
      var _this = this;
      this.$http
        .get(API.prompt, {
          params: {
            keywords: keywords
          }
        })
        .then(res => {
          _this.page_swt = true;
          _this.prompt_data = res.data.data;
        });
    },
    // 点击综合排序
    paixu() {
      var _this = this;
      _this.isshow = true;
      _this.glassshow = true;
      _this.zindex = 1;
    },
    // 点击灰色蒙层
    noshow() {
      var _this = this;
      _this.isshow = false;
      _this.glassshow = false;
      _this.isfilter = false;
    },
    back() {
      this.$router.back();
    },
    // 获得筛选的分类
    getsort() {
      var _this = this;
      _this.$http.get(API.parent_list, {}).then(res => {
        res.data.data.unshift({ cat_name: "全部", cat_id: 0 });
        var sortlist = res.data.data;
        for(var i in sortlist){
          sortlist[i].isactive=false;
        }
      _this.sortlist = sortlist;
        
      });

    },
    // 点击筛选的分类
    clicksort(catid) {
      var _this = this;
      var sortData = _this.sortlist;
      for (var i = 0; i < sortData.length; i++) {
        if (sortData[i].cat_id == catid) {
          sortData[i].isactive = true;
        } else {
          sortData[i].isactive = false;
        }
      }
      _this.sortlist = sortData;
      // console.log(_this.sortlist);
    },
    //点击筛选按钮
    filter() {
      var that = this;
      that.isfilter = true;
      that.glassshow = true;
      that.zindex = 15;
    },
    // 重置筛选信息
    reset() {
      this.lowprice = "";
      this.highprice = "";
      for (var i = 0; i < this.sortlist.length; i++) {
        this.sortlist[i].isactive = false;
      }
    },
    // 确定按钮
    makesure(){
      that.isfilter = false;
      that.glassshow = false;
    },
    // 获取商品列表
    getlist(page) {
      var _this = this;
       _this.havedata = false;
       _this.$http.get(API.searchlist, {
         params: {keywords:_this.keywords,sort:_this.sort,order:_this.order, page:page, limit:10}
       }).then(res => {
         var data = res.data.data;
         if(data!=""){
           _this.havedata = true;
         }
         data.forEach(function(val,i){
           _this.goodslist.push(val)
         })
      });
    },
    onRefresh(){
      // console.log('下拉刷新了哦')
      // this.goodslist = [];
      // console.log(this.goodslist)
      // this.getlist(1);
    },
    //上拉加载 
    onInfinite(){
      
      if(this.havedata){
        this.getlist(this.page);
        this.page++;
      }
      
    }
  },
  mounted() {
    this.getlist(1);
    this.getsort();

  }
};
</script>
<style lang="less" scoped>
*{margin: 0;padding: 0;}
.search {
  height: 0.9rem;
  display: flex;
  background: #f5f5f5;
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 2;
  .back {
    width: 0.8rem;
    text-align: center;
    line-height: 0.9rem;
  }

  .sou {
    flex: 1;
    display: flex;
    align-items: center;
    input {
      width: 100%;
      height: 0.7rem;
      border: 0;
      outline: none;
      text-indent: 0.5rem;
      border-radius: 0.1rem;
      background: white url(../../assets/image/search.png) no-repeat left center;
      background-size: 0.32rem;
    }
  }
  .sou_btn {
    width: 1rem;
    line-height: 0.9rem;
    text-align: center;
  }
}
.itemlist {
  height: 0.84rem;
  line-height: 0.84rem;
  background: #fff;
  color: #121212;
  font-size: 0px;
  margin-top: 0.9rem;
  position: fixed;
  width: 100%;
  z-index: 10;

  background: #fff;
  div {
    width: 25%;
    display: inline-block;
    font-size: 0.24rem;
    i {
      margin: 0 0.1rem;
    }
  }
  div:nth-of-type(1) {
    margin-left: 0.8rem;
  }
  div:nth-of-type(2) {
    margin-left: 0.5rem;
  }
  div:nth-of-type(3),
  div:nth-of-type(4) {
    width: auto;
    position: relative;
    padding: 0 0.2rem;
  }
  div:nth-of-type(3):after {
    content: "";
    width: 0.01rem;
    height: 0.3rem;
    background: #bdbdbd;
    top: 0.27rem;
    position: absolute;
    right: 0;
  }
  div.active {
    color: #FF0036;
  }
  ul {
    background: #fff;
    position: absolute;
    top: 0.84rem;
    width: 100%;
    padding: 0.15rem 0;
    li {
      font-size: 0.24rem;
      color: #9f9e9e;
      line-height: 0.7rem;
      padding: 0 0.48rem 0 0.8rem;
    }
    i {
      display: none;
      color: #FF0036;
      float: right;
    }
    .active {
      color: #FF0036;
      i {
        display: inline-block;
      }
    }
  }
  .active {
    color: #FF0036;
  }
}
.itemlist2 {
  position: fixed;
  background: #fff;
  height: 100%;
  right: 0;
  top: 0;
  z-index: 20;
  width: 85%;
  padding: 0.2rem;
  .inputbox {
    display: flex;
    justify-content: space-around;
  }
  p {
    font-size: 0.26rem;
    color: #323232;
    line-height: 0.3rem;
    margin: 0.2rem 0;
  }
  input {
    width: 2.7rem;
    line-height: 0.6rem;
    height: 0.6rem;
    border: 1px solid #bbb;
    border-radius: 0.06rem;
    text-align: center;
  }
  .sortbox {
    display: inline-flex;
    justify-content: space-around;
    flex-wrap: wrap;
    div {
      display: inline-block;
      width: 1.84rem;
      height: 0.95rem;
      line-height: 0.95rem;
      border: 1px solid #b1b1b1;
      text-align: center;
      margin-bottom: 0.3rem;
      font-size: 0.28rem;
      color: #323232;
      padding: 0 0.2rem;
      box-sizing: border-box;
      span {
        line-height: 0.35rem;
        display: inline-block;
        vertical-align: middle;
      }
    }
    .active {
      color: #FF0036;
      border-color: #FF0036;
    }
  }
}
.itemlist:after {
  content: "";
  background: rgba(229, 229, 229, 1);
  width: 100%;
  height: 0.01rem;
  position: absolute;
  top: 0.84rem;
  left: 0;
}
.glass {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.14);
  z-index: 1;
}
.btnbox {
  position: absolute;
  bottom: 0.35rem;
  width: 100%;
  left: 0;
  font-size: 0;
  button {
    width: 50%;
    height: 0.98rem;
    font-size: 0.3rem;
    color: #1d1d26;
    border: 0;
    border-top: 1px solid rgba(235, 235, 235, 1);
    line-height: 0.98rem;
    text-align: center;
    background: #fff;
  }
  button:nth-of-type(2) {
    background: #FF0036;
    color: #fff;
  }
}
.goodsbox{
 
  a{
    display: inline-block;width: 100%;margin-bottom: 0.2rem;height:2.4rem;overflow: hidden;
    .img{
      width: 2.4rem;overflow: hidden;float: left;text-align:center;height:100%;
      img{height: 100%;}
    }
    .txt{
      width: 4.6rem;float: right;border-bottom: 1px solid rgba(235,235,235,1);height:100%;position: relative;box-sizing:border-box;
      h2{font-size: 0.28rem;color: #4F4F4F;overflow: hidden;line-height: 0.4rem;font-weight: normal;height:0.8rem;}
      p{
        font-size: 0.3rem;color: #FF4C4C;position: absolute;left: 0;bottom: 0.1rem;
        span{display: inline-block;color: #fff;background: #FF0036;padding:0.03rem 0.15rem;line-height: 0.3rem;margin: 0 0.2rem;font-size: 0.24rem;border-radius: 0.1rem;}
      }
    }
  }
}
.goodstwo{
   font-size: 0px;display:inline-flex;flex-wrap:wrap;justify-content: space-around;
  a{
    width: 49%;font-size: 0.28rem;color: #4F4F4F;height:5.2rem;
    .img{
      width: 3.72rem;float: none;height:3.72rem;
      img{height: 100%;}
    }
    .txt{
      float: none;border-bottom:0;box-sizing:border-box;width:auto;padding:0.2rem;height:1.6rem;
      h2{line-height: 0.35rem;height: 0.7rem;}
      p{
        left: 0.2rem;bottom: 0.1rem;
      }
    }
  }
}
</style>
