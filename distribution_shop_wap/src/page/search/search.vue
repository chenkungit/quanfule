<template>
  <div class="wt">
      <div class="search">
          <div class="back" @click="back"><i class="iconfont">&#xe622;</i></div>
          <div class="sou">
              <input placeholder="输入关键字" @keyup.enter="sou" v-model="sou_value" @focus="show_page"/>
          </div>
          <div class="sou_btn" @click="sou">搜索</div>	
      </div>
      <div class="page1">
        <div class="history" v-if="hc_sou_history!=''">
            <div class="history_header">
                <span>搜索历史</span>
                <i class="iconfont" @click="clear_history">&#xe60d;</i>
            </div>
            <scroller lock-x :bounce="false" :scrollbarY="true" height="200px">
                <div class="history_list">
                    <div class="cell" v-for="(v,i) in hc_sou_history" :key="i" @click="gohref(v)" v-if="hc_sou_history!=''">
                        <span>{{v}}</span>
                        <i class="iconfont" @click.stop="removeItem_history(i)">&#xe725;</i>    
                    </div>
                </div>             
            </scroller>
        </div>
        <div class="hot_search">
            <div class="hot_header">
                <span>热门搜索</span>
            </div>
            <div class="hot_list">
                <span v-for="(v,i) in hot_search_data" @click="gohref(v.s_name)" :class="{active:v.s_level>1}" :key="i">
                    {{v.s_name}}
                </span>
            </div>
        </div>  
      </div>
      <div class="page2 animated" v-show="page_swt" :class="{bounceInUp:page_swt}">

          <div class="cell" v-for="(v,i) in prompt_data" :key="i" @click="gohref(v.keyword)">
              {{v.keyword}}
          </div>
          <div class="close">
              <i class="iconfont" @click="hide_page">&#xe60d;</i>
          </div>
      </div>
  </div>
</template>
<script>
import { Scroller } from "vux";
import utils from "../../utils/utils.js";
import API from "../../api/api.js";

export default {
  data() {
    return {
      sou_value: "", //搜索值
      hc_sou_history: [],
      hot_search_data: [],
      page_swt: false,
      prompt_data:[]
    };
  },
  components: {
    Scroller
  },
  mounted() {
    this.hc_sou_history = this.get_storage("hc_sou_history");
    //  获取热搜
    this.hot_search();
  },
  methods: {
    //搜索
    sou() {
      if (this.sou_value.replace(/(^\s*)|(\s*$)/g, "") != "") {
        this.hc_sou_history.unshift(this.sou_value);
        // console.log(this.sou_value)
        this.$router.push({name:'searchlist',params:{keywords:this.sou_value}})
      } else {
        this.$vux.toast.text("输入不能为空", "middle");
      }
      var arr = utils.unique(this.hc_sou_history);
      this.hc_sou_history = arr;
      this.save_storage("hc_sou_history", arr);
      
    },
    gohref(v){
        var _this = this;
        this.$router.push({name:'searchlist',params:{keywords:v}})
    },
    clear_history() {
      this.hc_sou_history = [];
      localStorage.removeItem("hc_sou_history");
    },
    removeItem_history(i) {
      this.hc_sou_history.splice(i, 1);
      this.save_storage("hc_sou_history", this.hc_sou_history);
    },
    save_storage(key, val) {
      localStorage.setItem(key, val);
    },
    get_storage(key) {
      var val = localStorage.getItem(key);
      if (val) {
        return val.split(",");
      } else {
        return [];
      }
    },
    hot_search() {
      var _this = this;
      this.$http.get(API.hot).then(res => {
        _this.hot_search_data = res.data.data;
      });
    },
    show_page() {
      this.prompt();
    },
    hide_page(){
      this.page_swt = false;
        
    },
    prompt(keywords) {
      var _this=this;
      this.$http.get(API.prompt, {
        params: {
          keywords: keywords
        }
      }).then(res=>{
         _this.page_swt = true;  
         _this.prompt_data=res.data.data;
      });
    },
    back() {
      this.$router.back();
    }
  }
};
</script>
<style lang="less" scoped>
.wt {
  background: white;
}
.page1 {
  margin-top: 0.9rem;
}
.page2 {
  position: fixed;
  top: 0.9rem;
  background: #f2f2f2;
  width: 100%;
  height: 100%;
  z-index: 2;
  .cell{
      height: 1rem;
      line-height: 1rem;
      padding: 0 .2rem;
      border-bottom: 1px solid #fafafa;
  }
  .close{
      height: 1rem;
      line-height: 1rem;
      text-align: center;
      font-size: .5rem;
  }
}
.hot_search {
  border-top: 0.1rem solid #f5f5f5;
  padding: 0 0.2rem;

  .hot_header {
    height: 0.8rem;
    line-height: 0.8rem;
  }
  .hot_list {
    span {
      display: inline-block;
      padding: 0.1rem 0.2rem;
      margin-right: 0.2rem;
      margin-bottom: 0.2rem;
      border: 1px solid #f1f1f1;
      border-radius: 0.1rem;
    }
    .active {
      border: 1px solid #FF0036;
      color: #FF0036;
    }
  }
}
.history {
  .history_header {
    height: 0.8rem;
    line-height: 0.8rem;
    border-bottom: 1px solid #f1f1f1;
    padding: 0 0.2rem;
    display: flex;
    justify-content: space-between;
    i {
      display: block;
      width: 0.8rem;
      text-align: center;
    }
  }
  .history_list {
    padding: 0 0.2rem;
    .cell {
      height: 0.8rem;
      line-height: 0.8rem;
      display: flex;
      border-bottom: 1px dashed #f1f1f1;
      span {
        flex: 1;
      }
      i {
        width: 0.8rem;
        text-align: center;
      }
    }
  }
}
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
</style>
