<template>
  <div class="gd">
    <header-view :title="'评价列表'"></header-view>
      <div>  
        <!-- 用户评价 -->
        


       <vscorll :on-infinite="onInfinite" :on-refresh="onRefresh">  
         <div class="commentbox">
         <div class="comment" v-for="(item,i) in list" :key="i">
            <div class="comcont">
                <div class="comtop">
                  <span class="compic" >
                  <img v-if="item.avatar" :src="item.avatar" alt="">
                  <img v-else src="http://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83erJ86598ylSvQ3xW2tLyoWhp0BybpkOoOp0ez0DIfuUasjEWWcsoVc1XuVPd5AyzsUynm9DBbFapw/132" alt=""></span>
                  <span class="comname">{{item.nick}}</span>
                  <span class="star">
                    <i v-for="(it,j) in starData" :key="j">
                      <img v-if="j>=item.comment_rank" src="../../assets/image/star_no.png" alt="">
                      <img v-else src="../../assets/image/star_yes.png" alt="">
                    </i>
                  </span>
                  <span class="face">
                    <img v-if="item.comment_rank==1" src="../../assets/image/face1.png" alt="">
                    <img v-else-if="item.comment_rank==2" src="../../assets/image/face2.png" alt="">
                    <img v-else-if="item.comment_rank==3" src="../../assets/image/face3.png" alt="">
                    <img v-else src="../../assets/image/face4.png" alt="">
                  </span>
                </div>
                <div class="comtime">{{item.add_time}} <span>{{item.goods_attr}}</span></div>
                <div class="comtext">{{item.content}}</div>
                <div class="comimgbox">
                  <div class="comimg" v-for="(pic,i) in item.imgs" :key="i">
                    <img :src="pic" :alt="pic.Width" class="heheimg">
                  </div>
                </div>
            </div>

             
        </div>
        <div class="nomore" v-show="!havedata && list.length!=0">暂无更多评论</div>
        </div>
      </vscorll>



      </div>
  </div>
</template>
<script>
import Header from "../../components/header/Header.vue";
import API from "../../api/api.js";
import vscorll from "../../components/b_scorll/b_scorll";
import $ from 'jquery'
import { setTimeout } from 'timers';
export default {

  data() {
    return {
      list:[],
      page:1,
      cmmentshow:false,
      havedata:false,
      starData:[{value:false},{value:false},{value:false},{value:false},{value:false}],
      id:0,
    };
  }, 
  created() {
    var _this = this;
    addEventListener("scroll", this.handleScroll);
    
  },
  mounted() {
    var _this = this;
    var id = this.$route.query.gshp_id || 0;
    _this.id = id;
    _this.getlist()
  },
  components: {"header-view": Header,vscorll},
  methods: {
    getlist() {
          var _this = this;
          _this.$http.get(API.comments, {
            params: {key:localStorage.getItem("key"),page:_this.page,limit:10,gshp_id:_this.id}
          }).then(res => {
            var data = res.data.data;
              // console.log(data.length);
                  if(data.length>=10){
                      _this.havedata = true;
                  }else{
                    _this.havedata = false;
                  }
                  if(_this.page==1 && data.length==0){
                      // _this.isshow = true;
                    }
                  data.forEach(function(val,i){
                    
                      _this.list.push(val)
                  })
                  setTimeout(function(){
                    console.log($('.heheimg').length)
                    $('.heheimg').each(function(){
                      if($(this).width()/$(this).height()>1){
                        $(this).removeClass('heheimg')
                        $(this).addClass('toowidth')
                      }
                    })
                  },10)
                  
                  
          });
        },
         onRefresh(){},
    onInfinite(){
      var _this = this;
      // if(e==0){
        if(_this.havedata){
          _this.page++;
          _this.getlist();
        }
        _this.havedata = false;
    }


  },

};
</script>

<style lang="less" scoped>
.commentbox{    margin-top: -0.8rem;position: relative;}
.comment{
  padding: 0 0.4rem 0.2rem;margin-top:0.16rem;background:#fff;
  >p{line-height: 0.88rem;height:0.88rem;border-bottom:0.02rem solid #EBEBEB;span{float: right;}}
  .comcont{
    padding-top: 0.3rem;
    .comtop{
      line-height: 0.6rem;
      span{display: inline-block;vertical-align: middle;}
      .compic{width: 0.6rem;height: 0.6rem;border-radius: 50%;overflow: hidden;img{width: 100%;height:100%;}}
      .comname{font-size: 0.3rem;color: #333;margin: 0 0.08rem 0 0.15rem;}
      .star{
        i{display: inline-block;width:0.3rem;height:0.3rem;margin: 0 0.08rem}
      }
      .face{height: 0.35rem;width: 0.35rem;float: right;    margin: 0.1rem;}
    }
    .comtime{font-size: 0.24rem;color: #444;margin:0.2rem 0 0.1rem;span{color: #646464;margin-left:0.1rem;}}
    .comtext{color: #333;font-size: 0.3rem;line-height: 0.4rem;margin-bottom: 0.2rem;}
    .comimgbox{
      .comimg{width: 2.2rem;height:2.2rem;overflow:hidden;text-align:center;line-height:2.2rem;display:inline-block;margin:0 0.05rem;position: relative;
      img{width:100%;height:auto;display: inline-block;position: absolute;left: 0;right: 0;top: 0;bottom: 0;margin: auto;}
      img.toowidth{width:auto;height:100%;left: 50%;    transform: translateX(-50%);}
      }
    }
  }
}


// .btnbox{
//   input,button{width: 0.5rem;border: 1px solid #f1f1f1;background: #fff;margin: 0 0.03rem;color: #999;}
// }
</style>
