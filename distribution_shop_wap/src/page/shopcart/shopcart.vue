<template>
    <div class="index_page">
         <header>
          <div class="h_center">购物车</div>
          <div class="h_right" @click="deletes()">删除</div>
        </header>
        <div style="margin-top:0.9rem;margin-bottom:2rem;">
           <div class="emptycart" v-if="data.goods_list==''">您的购物车空空如也</div>
          <div class="address" v-for="(item,i) in data.goods_list" :key="i">
            
              <!-- <div class="address-check" @click="chooseaddress(i)">
                <label for=""><input type="radio" :checked="addresscheck[i].ischecked==1?true:false"><i class="iconfont">&#xe604;</i>发货地：{{item.address}}</label>
              </div> -->
              
              <div :class="{'address-cont':true,'active':d.isyellow,'margin':d.isshow,'no':d.isfirst}" v-for="(d,j) in item.goods_detail" @click="choose(d.product_id,d.goods_id,i)"  :key="d.goods_id">
                <div>

                
                      <label for=""><input type="radio" :checked="d.checked==1?true:false"><i class="iconfont">&#xe604;</i></label>
                      <div>
                        <div class="pic"> <img :src="d.goods_thumb" alt=""> </div>
                        <div class="txt">
                          <h2 @click.stop="gogoods(d.goods_id)">{{d.goods_name}}</h2>
                          <h4>{{d.goods_attr?d.goods_attr:''}}</h4>
                          <div class="bottom">
                            <div class="left">
                              <span>{{d.goods_price}}</span>
                              <del>{{d.market_price}}</del>
                            </div>
                            <div class="right">
                              <span @click.stop="reduce(i,j)">-</span>
                                <input type="text" v-model="d.goods_number" readonly>
                                <span @click.stop="add(i,j)">+</span>
                            </div>
                          </div>
                        </div>
                      </div>
                </div>

              </div>
          </div>
        <div class="allcount">
          <label  for="checkall"><input @click.capture="chooseall()" type="radio" id="checkall" :checked="checkall"><i class="iconfont">&#xe604;</i>全选</label>
          <div class="center">
             <p>总计：<span>{{mytotal.checked_total_price_fromat}}</span></p>
          </div>
          <button @click="jies()">去结算({{mytotal.checked_total_number}})</button>
        </div>
        <foot :cartnumnew="newfoot"></foot>
 <div class="kuaidi">
        <dialog-bottom v-show="dialog_apart" @change_swt="hide_dialog_apart">
          <div class="k_title">
            请分开结算以下商品
          </div>
          <div class="k_list">
            <div class="cell2" v-for="(v,i) in data.goods_list" :key="i">
              <div>{{v.address}}</div>
              <div>共{{v.checked_subnumber}}件商品，合计：￥{{v.checked_subtotal}}</div>
              <span @click="gojs(v.sendaddress)">去结算</span>
            </div>

          </div>
        </dialog-bottom>
         <nologin></nologin>
      </div>

                        <!-- 大家都在买 -->
 <!-- <vscorll :on-infinite="onInfinite">   -->
         <!-- <div class="goodsbox goodstwo">
           <p class="title"><span>大家都在买</span></p>
            <router-link :to="{name:'goodsDetail', query:{gshp_id:item.id}}" v-for="item in goodslist" :key="item.id">
              <div class="img"><img :src="item.thumbnail" alt=""></div>
              <div class="txt">
                <h2>{{item.name}}</h2>
                <p>{{item.price}}</p>
              </div>
            </router-link>
         </div> -->
      <!-- </vscorll> -->
        </div>
       

    </div>
</template>
<script>
import API from "../../api/api.js";
import foot from "../../components/footer/Footer";
import { Scroller } from "vux";
import { Dialog } from 'vant';
import vscorll from "../../components/b_scorll/b_scorll"; 
import dialog_bottom from "../../components/dialog_bottom/dialog_bottom.vue";

import nologin from "../../components/nologin/nologin";
import { parse } from 'querystring';

export default {
  components: {foot,Scroller,vscorll,"dialog-bottom": dialog_bottom,nologin},
  data() {
    return {
      data: [],
      activity:[],
      checked_ids:[],
      addresscheck:[],
      newfoot:0,
      mytotal:{},
      page:2,
      goodslist:[],
      havedata:true,
      dialog_apart:false,
      checkall:true,
      key:localStorage.getItem("key"),
      nowactivity:0,
    };
  },
  mounted() {
    var _this = this;
    // _this.getlist();
    localStorage.setItem("beforeindex",window.location.href)
    _this.fetch().then(res => {
      if(res){
          _this.data = res;
          _this.mytotal = res.total;
          _this.newfoot = res.total.total_number;
          localStorage.setItem("cartnum",res.total.total_number)
          // console.log(res.goods_list);
          // console.log(res.code);
          if(res.code==2001){
              _this.$vux.toast.text(res.msg, "middle");
              
                // setTimeout(function(){
                //     _this.$router.push({ name: "login"})
                // },2000)             
            }
        
          res.goods_list.forEach(function(val,index){
            var addcheck_i = true;
            var activity =[];
            var new_goodsdetail = []; //选中数组并且有包邮活动
            var hehe = []; //选中数组没有包邮活动
            var nochecked_data = []; //未选中的数组
            var d = val.goods_detail;
            for(var i=0;i<d.length;i++){
              if(d[i].checked==0){
                  addcheck_i = false;
                  nochecked_data.push(d[i])
              }else{
                // 根据包邮活动组合数据
                  if(d[i].freeshipping_activity_id){
                    if(d[i].freeshipping_activity.length!=1){
                      d[i].more_activity = true;
                    }else{
                      d[i].more_activity = false;
                    }
                    new_goodsdetail.push(d[i])
                  }else{
                    hehe.push(d[i])
                  }
                  _this.checked_ids.push(d[i].rec_id)
              }
            }

            // 排序
            for(var j=0;j<new_goodsdetail.length-1;j++){
              for(var k=0;k<new_goodsdetail.length-1-j;k++){
                if(new_goodsdetail[k].freeshipping_activity_id>new_goodsdetail[k+1].freeshipping_activity_id){
                  var temp = new_goodsdetail[k];
                  new_goodsdetail[k] = new_goodsdetail[k+1];
                  new_goodsdetail[k+1] = temp;
                }
              }
            }

            for(var p=0;p<new_goodsdetail.length;p++){
              var v = new_goodsdetail[p];
              // v.isyellow = true;
                if(p==0){

                  if(v.freeshipping_activity_id){
                    _this.nowactivity = v.freeshipping_activity_id;
                    v.isshow = true;

                  }

                }else if(v.freeshipping_activity_id != _this.nowactivity){
                    _this.nowactivity = v.freeshipping_activity_id;
                    v.isshow = true;
                }else{
                  v.isshow = false;
                }
            }


          var goodnum = 0;
          var goodprice = 0;
          var now_active = 0;
          var now_id = 0;
          var myd = new_goodsdetail;
          for(var q=0;q<myd.length;q++){
              if(myd[q].isshow == true){

                  if(myd[q].freeshipping_activity_type==1){
                      goodnum = parseFloat(myd[q].goods_number);
                  }else{
                      goodnum = parseFloat(myd[q].subtotal);
                      // goodnum = parseFloat(myd[q].goods_number)*parseFloat(myd[q].subtotal);
                  }

                  if(q==myd.length-1){//选中的最后一个
                    _this.getblance(goodnum,myd[q],myd[q])
                  }else if(myd[q+1].isshow ==true){ //此包邮活动只有一个商品
                      _this.getblance(goodnum,myd[q],myd[q])
                  }
                  now_id = q;
              }else{
                if(myd[q].freeshipping_activity_type==1){
                    goodnum += parseFloat(myd[q].goods_number);
                }else{
                    goodnum += parseFloat(myd[q].goods_number)*parseFloat(myd[q].subtotal);
                }
                if(q==myd.length-1){//选中的最后一个
                  _this.getblance(goodnum,myd[now_id],myd[q])
                  
                }else if(myd[q+1].isshow !=false){ 
                  _this.getblance(goodnum,myd[now_id],myd[q])
                }
                
              }
          }
            if(nochecked_data.length!=0){
              nochecked_data[0].isfirst = true;
            }
            if(hehe.length!=0){
              hehe[0].isfirst = true;
            }

            var isfla = false;
            for(var o=0;o<myd.length;o++){
                 if(myd[o].isshow == true){
                   if(myd[o].isyellow==true){
                     isfla = true
                   }else{
                     isfla = false;
                   }
                    
                 }else{
                  //  isfla = false;
                   if(isfla){
                      myd[o].isyellow=true
                    }else{
                      myd[o].isyellow=false
                    }
                 }
                
            }


            var c = new_goodsdetail.concat(hehe).concat(nochecked_data);
            res.goods_list[index].goods_detail = c;



            _this.addresscheck.push({id:index,ischecked:addcheck_i})
            if(!addcheck_i){
              _this.checkall = false;
            }
          })
       }

    });
    // _this.editcart(_this.editdata);
  },
  methods: {
    // 获取商品
    fetch() {
      return new Promise((resolve, reject) => {
        this.$http
          .get(API.cartlist, {
            params: {key: localStorage.getItem("key")}
          })
          .then(res => {
            resolve(res.data.data);
          })
          .catch(res => {
            reject(res);
          });
      });
    },
    getblance(goodnum,firste,nowe){
        if(nowe.freeshipping_activity_type==1){//根据数量包邮
            if(goodnum>=parseFloat(nowe.freeshipping_activity_number)){
                firste.freeshipping_activity_name += ',已满足';
                firste.isyellow = true;
            }else{
                firste.freeshipping_activity_name += ',还差'+(parseFloat(nowe.freeshipping_activity_number)-goodnum).toFixed(2)+'件';
                firste.isyellow = false;
            }
        
        }else{ //根据价格包邮
            if(goodnum>=parseFloat(nowe.freeshipping_activity_amount)){
                firste.freeshipping_activity_name += ',已满足';
                firste.isyellow = true;
            }else{
                firste.freeshipping_activity_name += ',还差'+(parseFloat(nowe.freeshipping_activity_amount)-goodnum).toFixed(2)+'元';
                firste.isyellow = false;
            }
        }
    },
    gogoods(id){
       this.$router.push({ name: "goodsDetail", query:{gshp_id:id}});
    },
    //上拉加载 
    onInfinite(){
      // console.log("加载下一页呀")
      if(this.havedata){
        // this.getlist(this.page);
        this.page++;
      }
      
    },
    hide_dialog_apart(){
      this.dialog_apart = !this.dialog_apart;
    },
    getindex(oarray,name,b){
				for(var i=0;i<oarray.length;i++){
					if(b==oarray[i][name]){
						return i;
					}
				}
				return -1;
		},
    editcart(data,i,j,d){
       var _this = this;
      _this.$http.put(API.editcart, data)
        .then(function(res) {
          if(res.data.code!=200){
             _this.$vux.toast.text(res.data.msg, 'middle');
          }
          if(res.data.code==2002){
             _this.data.goods_list[i].goods_detail[j].goods_number = d;
          }
          
          if(res.data.code==200){
            _this.newfoot = res.data.data.total.total_number;
            localStorage.setItem("cartnum",res.data.data.total.total_number)
            _this.data = res.data.data;
             _this.mytotal = res.data.data.total;

            var res = res.data.data;
           res.goods_list.forEach(function(val,index){
            var addcheck_i = true;
            var activity =[];
            var new_goodsdetail = []; //选中数组并且有包邮活动
            var hehe = []; //选中数组没有包邮活动
            var nochecked_data = []; //未选中的数组
            var d = val.goods_detail;
            for(var i=0;i<d.length;i++){
              if(d[i].checked==0){

                  addcheck_i = false;
                  nochecked_data.push(d[i])
              }else{
                // 根据包邮活动组合数据
                  if(d[i].freeshipping_activity_id){
                    new_goodsdetail.push(d[i])
                  }else{
                    hehe.push(d[i])
                  }
                  _this.checked_ids.push(d[i].rec_id)
              }
            }

             _this.addresscheck.push({id:index,ischecked:addcheck_i})
            if(!addcheck_i){
              _this.checkall = false;
            }

            // 排序
            for(var j=0;j<new_goodsdetail.length-1;j++){
              for(var k=0;k<new_goodsdetail.length-1-j;k++){
                if(new_goodsdetail[k].freeshipping_activity_id>new_goodsdetail[k+1].freeshipping_activity_id){
                  var temp = new_goodsdetail[k];
                  new_goodsdetail[k] = new_goodsdetail[k+1];
                  new_goodsdetail[k+1] = temp;
                }
              }
            }

              for(var p=0;p<new_goodsdetail.length;p++){
                var v = new_goodsdetail[p];
                 v.isyellow = true;
                  if(p==0){

                    if(v.freeshipping_activity_id){
                      _this.nowactivity = v.freeshipping_activity_id;
                      v.isshow = true;

                    }

                  }else if(v.freeshipping_activity_id != _this.nowactivity){
                      _this.nowactivity = v.freeshipping_activity_id;
                      v.isshow = true;
                  }else{
                    v.isshow = false;
                  }
            }


          var goodnum = 0;
          var goodprice = 0;
          var now_active = 0;
          var now_id = 0;
          var myd = new_goodsdetail;
          for(var q=0;q<myd.length;q++){
              if(myd[q].isshow == true){

                  if(myd[q].freeshipping_activity_type==1){
                      goodnum = parseFloat(myd[q].goods_number);
                  }else{
                      goodnum = parseFloat(myd[q].subtotal);
                      // goodnum = parseFloat(myd[q].goods_number)*parseFloat(myd[q].subtotal);
                  }

                  if(q==myd.length-1){
                    _this.getblance(goodnum,myd[q],myd[q])
                  }else if(myd[q+1].isshow ==true){ 
                      _this.getblance(goodnum,myd[q],myd[q])
                  }
                  now_id = q;
              }else{
                if(myd[q].freeshipping_activity_type==1){
                    goodnum += parseFloat(myd[q].goods_number);
                }else{
                    goodnum += parseFloat(myd[q].goods_number)*parseFloat(myd[q].subtotal);
                }
                if(q==myd.length-1){
                  _this.getblance(goodnum,myd[now_id],myd[q])
                  
                }else if(myd[q+1].isshow !=false){ 
                  _this.getblance(goodnum,myd[now_id],myd[q])
                }
                
              }
          }
            if(nochecked_data.length!=0){
              nochecked_data[0].isfirst = true;
            }
            if(hehe.length!=0){ 
              hehe[0].isfirst = true;
            }
            
             var isfla = false;
            for(var o=0;o<myd.length;o++){
                 if(myd[o].isshow == true){

                      if(myd[o].isyellow == true){
                        isfla = true;
                      }else{
                        isfla = false;
                      }
                    
                 }else{

                      if(isfla){
                        myd[o].isyellow = true;
                      }else{
                        myd[o].isyellow = false;
                      }

                 }
                
            }

            var c = new_goodsdetail.concat(hehe).concat(nochecked_data);
            res.goods_list[index].goods_detail = c;






           
          })


          }
        })
        .catch(function(err) {});
    },
    // 删除商品
    deletes(){
      var _this = this;
      if(_this.checked_ids.length==0){
        _this.$vux.toast.text("请选择要删除的商品", 'middle');
      }else{

    
      Dialog.confirm({
        title: '确认',
        message: '确认删除这'+_this.checked_ids.length+'个商品吗？'
      }).then(() => {
         _this.$http.delete(API.delete, {params:{key:_this.key,rec_id:_this.checked_ids}})
        .then(function(res) {

             _this.$vux.toast.text(res.data.msg, 'middle');
             setTimeout(function(){
        location.reload()
             },1500)
          
        })
        .catch(function(err) {});
      }).catch(() => {
        // on cancel
      });
        }
     

    },
    // 点击商品选中
    choose(spec_id,goodsid,i){
        var _this = this;
        var d = _this.data.goods_list[i].goods_detail;
        var nowid=0,useid=0;
        var flag = true;
        d.forEach(function(val,index){
          // 点击当前设置是否选中（根据规格id或者商品id）
          if(spec_id != 0){
            nowid = val.product_id;
            useid = spec_id;
          }else{
            nowid = val.goods_id;
            useid = goodsid;
          }
          if(nowid == useid){
              if(d[index].checked == 0){
                d[index].checked = 1;
              }else{
                d[index].checked = 0;
              }
          }
          // 当前发货地底下的商品都选中改变发货地按钮状态
          if(val.checked==0){
              flag = false;
          }
        })
        _this.addresscheck[i].ischecked = flag;
        _this.getcheckids();
    },
    getcheckids(){
      var _this = this;
      var d = _this.data.goods_list;
      _this.checked_ids = [];
      var flag = true;
      d.forEach(function(val,index){
        val.goods_detail.forEach(function(v,i){
          if(v.checked==1){
            _this.checked_ids.push(v.rec_id)
          }else{
            flag = false;
          }
        })
      })
      _this.checkall = flag;
      
       _this.editcart({key:_this.key,checked_ids:_this.checked_ids});
    },
    // 点击发货地全选
    chooseaddress(i){
      var _this = this;
      var addresscheck = _this.addresscheck;
      addresscheck[i].ischecked = !addresscheck[i].ischecked;
      var d = _this.data.goods_list[i].goods_detail;
      var flag = 0;
      if(addresscheck[i].ischecked){
        flag = 1;
      }
      d.forEach(function(val,index){
        val.checked = flag;
      })
      _this.getcheckids();
    },
    chooseall(){
        var _this = this;
        _this.checkall = !_this.checkall;
        var addresscheck = _this.addresscheck;
        var d =  _this.data.goods_list;
        
        addresscheck.forEach(function(v,i){
          v.ischecked = _this.checkall;
          
        })
        // alert(JSON.stringify(addresscheck))
        d.forEach(function(val,index){
          val.goods_detail.forEach(function(v,i){
            v.checked=_this.checkall;
          })
        })


        _this.getcheckids();

       
    },
    reduce(i,j){
      var _this = this;
      var d = _this.data.goods_list[i].goods_detail[j].goods_number;
      var num = _this.data.goods_list[i].goods_detail[j].Nbei;
      if(num != 0){
        if(d>num){
            d = d - num;
        }
      }else{
        if(d>1){
          d--;
        }
      }
      
      _this.data.goods_list[i].goods_detail[j].goods_number = d;
      var rec_id = _this.data.goods_list[i].goods_detail[j].rec_id;
      _this.editcart({key:_this.key,checked_ids:_this.checked_ids,number:d,rec_id:_this.data.goods_list[i].goods_detail[j].rec_id});
    },
    add(i,j){
      var _this = this;
      var d = _this.data.goods_list[i].goods_detail[j].goods_number;
      var oldd = d;
      var num = _this.data.goods_list[i].goods_detail[j].Nbei;
      
      if(num != 0){
        d = d + num;
      }else{
        d++;
      }
      _this.data.goods_list[i].goods_detail[j].goods_number = d;
      _this.editcart({key:_this.key,checked_ids:_this.checked_ids,number:d,rec_id:_this.data.goods_list[i].goods_detail[j].rec_id},i,j,oldd);
      
    },
    // 结算
    jies(){
      var _this = this;
      var data = _this.data.goods_list;
      var addressid = [];
      data.forEach(function(v,i){
          if(v.checked_subnumber !=0){
              addressid.push(v.sendaddress)
          }
      })
      // console.log(addressid.length);
      if(addressid.length==1){
          _this.gojs(addressid[0])
      }else if(addressid.length==0){
         _this.$vux.toast.text("请选中要结算的商品哦", 'middle');
      }else{
        _this.dialog_apart = true;
      }
      
    },
    gojs(addressid){
      var _this = this;
      localStorage.removeItem('order_data');
        this.$http.post(API.check_out,{key:localStorage.getItem("key"),sendaddress:addressid}).then(function(res) {
            if (res.data.code == 2002) {

            }else if(res.data.code == 200){
              sessionStorage.setItem("data", JSON.stringify({key:localStorage.getItem("key"),sendaddress:addressid}));
              sessionStorage.setItem("once", 0);
              localStorage.setItem("order_data", JSON.stringify(res.data.data));
              _this.$router.push({
                name: "editOrder",
                params: {
                }
              });
            }else{
              _this.$vux.toast.text(res.data.msg, 'middle');
            }
          })
          .catch(function(err) {
            _this.$vux.toast.text(res.data.msg, 'middle');
          });
    }

         
          
  }
};
</script>
<style lang="less" scoped>
.index_page {
  background: #fff;
}
kbd{font-family: "微软雅黑"}
.emptycart{text-align: center;margin: 2rem 0;}
 header {
    display: flex;
    height: 0.9rem;
    background: #f2f2f2;
    border-bottom: 0.01rem solid #f1f1f1;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 2;
    .h_left {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      i {
        font-size: 0.4rem;
      }
    }
  
    .h_center {
      flex: 8.5;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #666;
      font-size: 0.3rem;
  
    }
  
    .h_right {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
      font-size:0.3rem;
      background: #cc0000;
      padding: 0 0.2rem;
      i {
        font-size: 0.4rem;
      }
    }
  }

label{
    position: relative;margin:0 0.3rem;
    i{position: absolute;left: 0rem;color: #FF4C4C;font-size: 0.3rem;top:0rem;height: 0.3rem;line-height: 0.34rem;display: none;}
    input[type="radio"]{width: 0.3rem;height: 0.3rem;border-radius: 50%;border: 1px solid #999;margin-right: 0.1rem;    vertical-align: middle;}
    input[type="radio"]:checked{background: #FF4C4C;border-color: #FF4C4C;}
    input[type="radio"]:checked + i{display: inline-block;}
  }

.address{
  margin-bottom: 0.2rem;padding-bottom: 0.1rem;
  .activity{
      line-height: 0.35rem;height: 0.35rem;padding-bottom: 0.15rem;font-size: 0.26rem;color: #666;border-bottom:1px solid #eee;margin:0.15rem;box-sizing:content-box;
      span{display: block;padding: 0 0.1rem;background: #f60;color: #fff;border-radius: 0.1rem;font-size: 0.22rem;margin-right: 0.1rem;float: left;}
      kbd:nth-of-type(1){display: inline-block;width: 67%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;font-size: 12px;}
      kbd:nth-of-type(2){color: #f60;float:right;}
      }
  .address-check{
    border-bottom: 1px solid rgb(221,221,221);background: #fff;
    label{height: 0.7rem;line-height: 0.7rem;}
  }
  .address-cont{
    position: relative;padding-left:0.8rem;overflow: hidden;background: #fff;    padding-top: 0.1rem;
    label{position: absolute;left: 0;    top: 50%;transform: translateY(-50%);height: 0.4rem;line-height: 0.4rem;}
    .pic{width: 1.75rem;height: 1.75rem;overflow: hidden;border: 0.02rem solid #ddd;float: left;margin-top: 0;margin-bottom: 0.15rem;}
    // .activity{
    //   line-height: 0.35rem;height: 0.35rem;padding-bottom: 0.15rem;font-size: 0.26rem;color: #666;border-bottom:1px solid #eee;margin:0.15rem 0;margin-left:-0.5rem;
    //   span{display: inline-block;padding: 0 0.1rem;background: #f60;color: #fff;border-radius: 0.1rem;font-size: 0.22rem;margin-right: 0.1rem;}
    //   }
    .txt{
      width: 4.7rem;float: right;height: 1.72rem;margin-top: 0;padding:0 0.2rem 0 0;
      h2{color: #333;font-size: 0.26rem;line-height: 0.35rem;font-weight: normal;height: 0.66rem;overflow: hidden;text-overflow: ellipsis;text-align: justify;
display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;}
      h4{color: #999;font-size: 0.24rem;font-weight: normal;margin-top: 0.1rem;height: 0.3rem;    line-height: 0.3rem;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;}
      .bottom{
        height: 0.44rem;margin-top:0.17rem;
        .left{
          float: left;width:57%;
          span{color: #FF4C4C;font-size: 0.28rem;}
          del{color: #999;font-size: 0.22rem;display: inline-block;}
        }
        .right{
          line-height: 0.44rem;float: right;
          span{display: block;width: 0.5rem;text-align: center;font-size: 0.4rem;color: #999;border: 1px solid #ddd;float: left;}
          input{width: 0.7rem;border: 0.01rem solid #ddd;border-left: 0;border-right: 0;height: 0.48rem;font-size: 0.3rem;text-align: center;float: left;}
        }
      }
    }
  }
  .address-cont.active{
    background: #FFFCE0;
    .txt .bottom .right input{background: #FFFCE0}
  }
  .address-cont.no{
    margin-top: 0.2rem;
  }
  .address-cont.margin{
    margin-top: 0.2rem;
  }
  .address-cont:nth-of-type(2){
    margin-top: 0 !important; 
  }

  

}
.allcount{
  background: #fff;height: 0.88rem;position: fixed;left: 0;bottom: 0.98rem;width: 100%;z-index:10;
  label{width: 1.1rem;float: left;margin-top: 0.24rem;line-height: 1.4}
  .center{width: 3rem;float: left;padding: 0.1rem 0;}
  button{width: 2.1rem;background: #FF0036;color: #fff;text-align: center;line-height: 0.88rem;border: 0;float: right;}
  p{font-size: 0.28rem;line-height: 0.68rem;
    span{color: #DD2727;font-weight: bold;}
  }
}
// .yo-scroll .pull-refresh{height: 1rem}

.goodsbox{
 
  a{
    display: inline-block;width: 100%;margin-bottom: 0.2rem;height:2.4rem;overflow: hidden;
    .img{
      width: 2.4rem;overflow: hidden;float: left;text-align:center;height:100%;
      img{height: 100%;}
    }
    .txt{
      width: 4.6rem;float: right;border-bottom: 1px solid rgba(235,235,235,1);height:100%;position: relative;box-sizing:border-box;    margin-top: 0;
      h2{font-size: 0.28rem;color: #4F4F4F;overflow: hidden;line-height: 0.4rem;font-weight: normal;height:0.8rem;}
      p{
        font-size: 0.3rem;color: #FF4C4C;position: absolute;left: 0;bottom: 0.1rem;
        span{display: inline-block;color: #fff;background: #FF0036;padding:0.03rem 0.15rem;line-height: 0.3rem;margin: 0 0.2rem;font-size: 0.24rem;border-radius: 0.1rem;}
      }
    }
  }
}
.goodstwo{
   font-size: 0px;display:inline-flex;flex-wrap:wrap;justify-content: space-around;margin-bottom:2rem;background:#fff;
   .title{
      color: #666;height: 1rem;font-size: 0.3rem;width: 100%;text-align: center;line-height:1rem;
      span{display: inline-block;position: relative;padding: 0 0.2rem;}
      span:before,span:after{content: "";width:1.2rem;height: 0.02rem;background: #999;position: absolute;top: 50%;}
      span:before{left: -1.2rem;}span:after{right: -1.2rem;}
   }
  a{
    width: 49%;font-size: 0.28rem;color: #4F4F4F;height:5.2rem;
    .img{
      width: 3.72rem;float: none;height:3.72rem;
      img{height: 100%;}
    }
    .txt{
      float: none;border-bottom:0;box-sizing:border-box;width:auto;padding:0.2rem;height:1.6rem;
      h2{    line-height: 0.4rem;height: 0.8rem;text-align: justify;}
      p{
        left: 0.2rem;bottom: 0.1rem;
      }
    }
  }
}
.k_title{text-align: center;margin: 0.2rem 0;font-size: 0.3rem;}
.k_list .cell2{
  text-align: justify;border-bottom:1px solid #eee;padding:0.15rem;position: relative;
  div{width: 75%;display: inline-block;font-size:0.24rem;color: #646464;line-height: 0.3rem;height: 0.3rem;text-overflow: ellipsis;white-space: nowrap;}
  div:nth-of-type(2){color: #323232;font-size: 0.26rem;}
  span{display: inline-block;width:1.5rem;text-align: center;border: 1px solid #f60;color: #f60;border-radius: 0.05rem;line-height: 0.6rem;font-size: 0.3rem;
  position: absolute;right: 0.3rem;top: 50%;transform: translateY(-50%)}
}
.yo-scroll{position: static;
.inner{position: static;}
}

</style>
