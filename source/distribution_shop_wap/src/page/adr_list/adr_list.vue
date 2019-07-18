<template lang="html">
  <div>
    <header-view :title="'收货地址'"></header-view>
    <div class="delete_div">
      <div class="line"></div>
      <div v-for="(item,index) in adr_list" >
        <div class="left-delete" :class="{'touch-move-active':item.isTouchMove}" :idnex="item.address_id" @touchstart="_touchStart"  @touchmove="_touchMove($event,item.address_id)" @touchend="_touchEnd()" >
          <div class="move">
            <div class="box_adr">
              <div class="name">
                <span>{{item.consignee}}</span>
                <span v-if="item.is_default==1" class="default">默认</span>
              </div>
              <div class="phone"  @click="chosen_adr(index)">
                <p>{{item.tel}}</p>
                <p>{{item.province_name}}省{{item.city_name}}市{{item.district_name}}{{item.address}}</p>
              </div>
              <div class="edit" @click="edit_adr(item.address_id)">
                <i class="iconfont">&#xe650;</i>
              </div>
            </div>
          </div>
          <div class="deleteIcon" @click="deleteItem(item.address_id,index)">
            <i class="iconfont">&#xe607;</i>
          </div>
        </div>

      </div>
    </div>
    <router-link :to="{ name: 'new_adr', params: {} }"><div class="new_adr">新增地址</div></router-link>
  </div>
</template>

<script>
import Header from "../../components/header/Header.vue";
import { Dialog } from 'vant';
import API from "../../api/api.js";

export default {
  data() {
    return {
      adr_list: [],
      start_Pos: {
        startX: 0,
        startY: 0
      },
      is_dingjin: 0
    };
  },
  components: {
    "header-view": Header
  },
  mounted() {
    this.fetch();
  },
  methods: {
    fetch() {
      var _this = this;
      this.$http
        .get(API.lists, {
          params: {}
        })
        .then(function(res) {
          var item = res.data.data;
          var isflag = false;
          item.forEach(function(v, i) {
            v.isTouchMove = false;
            if(v.is_default==1){
                isflag = true;
            }
          });
          if(!isflag){
            var data = {
              address_id:item[0].address_id,
              consignee:item[0].consignee,
              province:item[0].province,
              city:item[0].city,
              district:item[0].district,
              address:item[0].address,
              tel:item[0].tel,
              is_default:1
            }
            _this.edit(data);
            item[0].is_default = 1;
          }
          _this.adr_list = item;
        })
        .catch(function(res) {});
    },
    edit:function(data){
     this.$http.put(API.adr_edit,data).then(res=>{
      //  _this.$vux.toast.text(res.data.msg, 'top');
      //  _this.$router.push({name:'adr_list'})
     })
    },
    _touchStart: function(ev) {
      ev = ev || event;
      this.start_Pos.startX = ev.changedTouches[0].clientX;
      this.start_Pos.startY = ev.changedTouches[0].clientY;
      var item = this.adr_list;
      item.forEach(function(v, i) {
        if (v.isTouchMove)
          //只操作为true的
          v.isTouchMove = false;
      });
      this.adr_list = item;
    },
    _touchMove: function(ev, index) {
      ev = ev || event;
      var index = index,
        startX = this.start_Pos.startX,
        startY = this.start_Pos.startY,
        touchMoveX = ev.changedTouches[0].clientX,
        touchMoveY = ev.changedTouches[0].clientY,
        angle = this.angle(
          { X: startX, Y: startY },
          { X: touchMoveX, Y: touchMoveY }
        ),
        item = this.adr_list;
      item.forEach(function(v, i) {
        v.isTouchMove = false;
        //滑动超过30度角 return
        if (Math.abs(angle) > 20) return;
        if (v.address_id == index) {
          if (touchMoveX > startX)
            //右滑
            v.isTouchMove = false; //左滑
          else v.isTouchMove = true;
        }
      });
      this.adr_list = item;
    },
    _touchEnd: function(index) {},

    angle: function(start, end) {
      var _X = end.X - start.X,
        _Y = end.Y - start.Y;
      //返回角度 /Math.atan()返回数字的反正切值
      return 360 * Math.atan(_Y / _X) / (2 * Math.PI);
    },
    chosen_adr(index) {
      var _this = this;
      var d = _this.adr_list[index];
      // console.log(d)
      var address = {
        address_id:d.address_id,
        consignee:d.consignee,
        tel:d.tel,
        province_name:d.province_name,
        city_name:d.city_name,
        district_name:d.district_name,
        address:d.address
      }
        // console.log(address)
      if(_this.$route.query.sendaddress){
        this.$router.push({
          name: "editOrder",
          query: {
              choose_r_data:JSON.stringify(address)
          }
        });
      }
    },
    deleteItem: function(index, i) {
      var _this = this;
      Dialog.confirm({
        title: '确认',
        message: '确认删除地址吗？'
      }).then(() => {
      this.$http
        .delete(API.del_adr, {
          params: {
            address_id: index
          }
        })
        .then(res => {
          if (res.data.code == 200) {
            // _this.fetch();
            _this.adr_list.splice(i, 1);
          }
        });
      })
    },
    edit_adr(id) {
      this.$router.push({ name: "new_adr", query: { id: id } });
    }
  }
};
</script>

<style lang="less" scoped>
.line {
  height: 0.12rem;
  background: repeating-linear-gradient(
    -45deg,
    #8fc9f5 0,
    #8fc9f5 50%,
    #f58f8f 50%,
    #f58f8f 100%
  );
}
.touch-move-active .move,
.touch-move-active .deleteIcon {
  transform: translateX(0) !important;
}
.left-delete {
  display: flex;
  background: white;
  border-bottom: 1px solid #f1f1f1;
  .move {
    display: flex;
    min-height: 1.5rem;
    width: 100%;
    transform: translateX(1rem);
    margin-left: -1rem;
    transition: all 0.4s;
  }

  .deleteIcon {
    width: 1rem;
    min-height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: translateX(1rem);
    transition: all 0.4s;
    background: #FF0036;
    i {
      color: white;
    }
  }
}
.delete_div {
  margin-top: 0.9rem;
  overflow: hidden;
  margin-bottom: 1rem;
  .box_adr {
    display: flex;
    width: 100%;
    .name {
      flex: 1.5;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      .default {
        border: 1px solid #FF0036;
        border-radius: 5px;
        color: #FF0036;
        padding: 0 0.1rem;
        font-size: 0.2rem;
      }
    }
    .phone {
      flex: 5;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .edit {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      i {
        font-size: 0.4rem;
      }
    }
  }
}
.new_adr {
  position: fixed;
  bottom: 0;
  width: 100%;
  font-size: 0.3rem;
  height: 1rem;
  line-height: 1rem;
  color: white;
  text-align: center;
  background: #FF0036;
}
</style>
