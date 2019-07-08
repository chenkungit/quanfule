<template lang="html">
  <div class="bg_white">
    <header-view :title="'新增地址'"></header-view>
    <div class="new_adr">
        <div class="cell">
          <span>姓名:</span>
          <input type="text"  v-model="name"  placeholder="请输入姓名">
        </div>
        <div class="cell">
          <span>手机号:</span>
          <input type="text"  v-model="tel"  placeholder="请输入手机号">
        </div>
        <div class="cell" @click="dialog_swt=!dialog_swt">
            <span>省份城市：</span>
            <span class="color">{{provice_name==''?'省份':provice_name}} | {{city_name==''?'城市':city_name}} | {{area_name==""?"地区":area_name}}</span>
        </div>
        <div class="cell">
          <span>详细地址：</span>
          <input type="text"  v-model="adr_detail"  placeholder="街道、楼牌号等">
        </div>
        <group class="up">
          <x-switch :title="'设置为默认地址'" @on-change="set_def_adr" :value="is_default==1?true:false"></x-switch>
        </group>
        <div class="sub_btn">
          <div class="btn" v-if="$route.query.id" @click="save_edit">
            保存
          </div>
          <div class="btn" @click="save" v-else>
            保存
          </div>

          <div class="btn" @click="reset">
            重置
          </div>
        </div>
    </div>

    <!-- 三级联动s -->
    <transition :name="transitionName">
      <div class="city_dialog" v-show="dialog_swt">
        <div class="city_model" @click="hide_dialog"></div>
        <div class="city_con">
            <div class="city_header">
                <div class="item" @click="tab(1)" :class="{'active':tab_id==1?true:false}">{{provice_name==""?"请选择":provice_name}}</div>
                <div class="item" v-show="provice_name!=''" @click="tab(2)" :class="{'active':tab_id==2?true:false}">{{city_name==""?"请选择":city_name}}</div>
                <div class="item"v-show="city_name!=''" @click="tab(3)" :class="{'active':tab_id==3?true:false}">{{area_name==""?"请选择":area_name}}</div>

                <div class="btn">
                  <x-button :min="true" :disabled="tab_id==3?false:true" @click.native="save_city">保存</x-button>
                </div>

            </div>
            <div class="city_box">
              <transition :name="tr_one">
                <div class="c_box" v-show="tab_id==1?true:false">
                    <div class="cell" v-for="(v,i) in provice" @click="choose_provice(v.region_id,v.region_name)">
                      {{v.region_name}}
                    </div>
                </div>
              </transition>
              <transition :name="tr_one">
                <div class="c_box" v-show="tab_id==2?true:false">
                    <div class="cell" v-for="(v,i) in city" @click="choose_city(v.region_id,v.region_name)">
                      {{v.region_name}}
                    </div>
                </div>
              </transition>
              <transition :name="tr_one">
                <div class="c_box" v-show="tab_id==3?true:false" >
                    <div class="cell" v-for="(v,i) in area" @click="choose_area(v.region_id,v.region_name)">
                      {{v.region_name}}
                    </div>
                </div>
              </transition>
            </div>
        </div>
      </div>
    </transition>

    <!-- 三级联动end -->

    <toast  type="text" width="20em"></toast>
  </div>
</template>

<script>
import Header from '../../components/header/Header.vue'

import {
  XSwitch,
  Group,
  Toast,
  XButton
} from "vux"

import API from "../../api/api.js"

export default {
  data() {
    return {
      name: "",
      tel: "",
      adr_detail: "",
      is_default:0,
      provice: [],
      city:[],
      area:[],
      dialog_swt:false,
      transitionName:'slide-up',
      tr_one:"slide-left",
      provice_id:"",
      provice_name:"",
      city_id:"",
      city_name:"",
      area_id:"",
      area_name:"",
      tab_id:1,
      phoneReg:/(^1[3|4|5|7|8]\d{9}$)/,
    }
  },
  components: {
    'header-view': Header,
    XSwitch,
    Group,
    Toast,
    XButton
  },
  mounted() {
    var _this = this;
    if(this.dialog_swt){
      this.transitionName="slide-down"
    }else {
      this.transitionName="slide-up"
    }
    this.get_city().then(res => {
      _this.provice = res;
    });
    if(this.$route.query.id){
      this.eidt_info(this.$route.query.id)
    }
  },
  methods: {
    get_city( t, p_id) {
      return new Promise((resolve, reject) => {
          this.$http.post(API.regions, {
            type: t,
            parent_id: p_id
          }).then((res) => {
            resolve(res.data.data)
          }).catch((err) => {
            reject(err)
          })

        }
      )

    },
    tab(index){
      this.tab_id=index
      if(index==1){
        this.tr_one="slide-right";
        this.provice_id="";
        this.provice_name="";
        this.city_id="";
        this.city_name="";
        this.area_id="";
        this.area_name="";
      }else {
        this.tr_one="slide-left"
      }

      if(index==2){
        this.city_id="";
        this.city_name="";
        this.area_id="";
        this.area_name="";
      }
    },
    choose_provice(id,name){
     var _this=this;
     this.provice_id=id;
     this.provice_name=name;
     this.tab_id=2;
     this.get_city(2,id).then(res=>{
       _this.city=res;
     })
   },
   choose_city(id,name){
     var _this=this;
     this.tab_id=3;
     this.city_id=id;
     this.city_name=name;
     this.get_city(3,id).then(res=>{
       _this.area=res;
     })
   },
   choose_area(id,name){
     this.area_id=id;
     this.area_name=name;
   },
   set_def_adr(e){
    e?this.is_default=1:this.is_default=0;
   },
   save(){
     var _this=this;
     if(this.name==""){
       _this.$vux.toast.text("请输入姓名！", 'middle');
     }else if(!_this.phoneReg.test(_this.tel)){
       _this.$vux.toast.text("请输入正确的手机号！", 'middle');
     }else if(this.provice_id=="" || this.city_id=="" ||this.area_id==""){
       _this.$vux.toast.text("请将省市区填写完整！", 'middle');
     }else if(this.adr_detail ==""){
        _this.$vux.toast.text("请输入详细地址！", 'middle');  
     }else{
        this.$http.post(API.add,{
          consignee:this.name,
          province:this.provice_id,
          city:this.city_id,
          district:this.area_id,
          address:this.adr_detail,
          tel:this.tel,
          is_default:this.is_default
        }).then(res=>{
          _this.$vux.toast.text(res.data.msg, 'top');
          _this.$router.back();
        })
      }
   },
   reset(){
     this.name="";
     this.tel="";
     this.is_default=0;
     this.adr_detail="";
     this.area_id="";
     this.area_name="";
     this.city_id="";
     this.city_name="";
     this.provice_id="";
     this.provice_name="";
     this.tab_id=1;
   },
   hide_dialog(){
     this.dialog_swt=!this.dialog_swt;
     this.area_id="";
     this.area_name="";
     this.city_id="";
     this.city_name="";
     this.provice_id="";
     this.provice_name="";
     this.tab_id=1;
   },
   save_city(){
     this.dialog_swt=!this.dialog_swt;
   },
  //  编辑地址进入调用
   eidt_info(id){
     var _this=this;

     this.$http.get(API.adr_info,{
       params:{
         address_id:id
       }
     }).then(res=>{
       var data=res.data.data;
       _this.area_id=data.district;
       _this.area_name=data.district_name;
       _this.city_id=data.city;
       _this.city_name=data.city_name;
       _this.provice_id=data.province;
       _this.provice_name=data.province_name;
       _this.tel=data.tel;
       _this.name=data.consignee;
       _this.adr_detail=data.address;
       _this.is_default=data.is_default;
     })
   },
   save_edit(){
     var _this=this;
     if(this.name==""){
       _this.$vux.toast.text("请输入姓名！", 'middle');
     }else if(!_this.phoneReg.test(_this.tel)){
       _this.$vux.toast.text("请输入正确的手机号！", 'middle');
     }else if(this.provice_id=="" || this.city_id=="" ||this.area_id==""){
       _this.$vux.toast.text("请将省市区填写完整！", 'middle');
     }else if(this.adr_detail ==""){
        _this.$vux.toast.text("请输入详细地址！", 'middle');  
     }else{
     this.$http.put(API.adr_edit,{

       address_id:this.$route.query.id,
       consignee:this.name,
       province:this.provice_id,
       city:this.city_id,
       district:this.area_id,
       address:this.adr_detail,
       tel:this.tel,
       is_default:this.is_default
     }).then(res=>{
       _this.$vux.toast.text(res.data.msg, 'top');
       _this.$router.back();
     })
     }
   }

  },



}
</script>

<style lang="less">
.bg_white {
    background: white;
}
.new_adr {
    margin-top: 0.9rem;
    .cell {
        height: 1rem;
        border-bottom: 1px solid #f1f1f1;
        display: flex;
        padding: 0 0.3rem;
        span {
            display: flex;
            align-items: center;
            font-size: 0.3rem;
        }
        .color {
            font-size: 0.28rem;
            color: #999;
        }
        input {
            border: 0;
            flex: 1;
            outline: none;
        }
    }
    .sub_btn {
        display: flex;
        justify-content: space-around;
        margin-top: 0.7rem;
        .btn {
            width: 3.3rem;
            height: 0.88rem;
            line-height: 0.88rem;
            background: #FF0036;
            color: white;
            text-align: center;
            border-radius: 0.04rem;
        }
    }
}
.city_dialog{
  width: 100%;
  height: 100%;
  position: fixed;
  bottom: 0;
  background: rgba(0, 0, 0,.6);
  transition: all .5s cubic-bezier(.55,0,.1,1);
  display: flex;
  flex-direction: column;

  .city_model{
    flex: 1
  }
  .city_con{
    flex: 1;
    background:white;
    display: flex;
    flex-direction: column;
    .city_header{
      height: .8rem;
      border-bottom: 1px solid #f1f1f1;
      display: flex;
      padding: 0 .3rem;
      position: relative;
      .item{
        display: flex;
        padding: 0 .1rem;
        margin-right: .2rem;
        align-items: center;
        justify-content: center;
      }
      .btn{
        position: absolute;
        right: 0;
        button{
          border:0;
          background: white;
        }
      }
      .active{
        color: #FF0036;
        border-bottom: 1px solid #FF0036;
      }
    }
    .city_box{
      flex: 1;
      overflow-y: scroll;
      transition: all .3s cubic-bezier(.55,0,.1,1);
      .c_box{
        overflow: hidden;
        transition: all .3s cubic-bezier(.55,0,.1,1);
        .cell{
          height: .8rem;
          line-height: .8rem;
          padding-left: .4rem;
        }
      }

    }
  }
}
.slide-up-enter, .slide-down-leave-active {
  opacity: 0;

  -webkit-transform: translate(0,100%);
  transform: translate(0,100%);
}
.slide-up-leave-active, .slide-down-enter {
  opacity: 0;
  -webkit-transform: translate( 0,100%);
  transform: translate(0,100%);
}

.slide-left-enter, .slide-right-leave-active {
  opacity: 0;
  -webkit-transform: translate(100%, 0);
  transform: translate(100%, 0);
}
.slide-left-leave-active, .slide-right-enter {
  opacity: 0;
  -webkit-transform: translate(-100%, 0);
  transform: translate(-100%, 0);
}

</style>
