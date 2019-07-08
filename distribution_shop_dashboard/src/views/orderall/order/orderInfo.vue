<template>
  <div style="padding-top:30px;font-size:12px;">
      <el-col :span="24" class="toolbar" style="100%">
					<el-form :inline="true">
              <el-form-item>
                  <el-button type="warning" icon="el-icon-back"  @click="goBack()">返回</el-button>
              </el-form-item>
					</el-form>
      </el-col>

      <div style="width:100%;" class="order_info">
        <el-col :span="24"><div class="bg-orange">基本信息</div></el-col>
        <table style="width:100%" >
          <tr>
            <td class="jieshi">订单号：</td>
            <td>{{infodata.order_sn}}</td>
            <td class="jieshi">订单状态：</td>
            <td>{{infodata.o_status}}</td>
          </tr>
          <tr>
            <td class="jieshi">购货人：</td>
            <td>{{infodata.consignee}}</td>
            <td class="jieshi">下单时间：</td>
            <td>{{infodata.add_time}}</td>
          </tr>
          <tr>
            <td class="jieshi">支付方式：</td>
            <td>{{infodata.pay_name}}</td>
            <td class="jieshi">付款时间：</td>
            <td>{{infodata.pay_time}}</td>
          </tr>
          <tr>
            <td class="jieshi">配送方式：</td>
            <td>{{infodata.shipping_name}}</td>
            <td class="jieshi">发货时间：</td>
            <td>{{infodata.shipping_time}}</td>
          </tr>
          <tr>
            <td class="jieshi">发货单号：</td>
            <td>{{infodata.invoice_no}}</td>
            <td class="jieshi">订单来源：</td>
            <td>{{infodata.from_where}}</td>
          </tr>
        </table>
      </div>

      <div style="width:100%;" class="order_info">
        <el-col :span="24">
          <div class="bg-orange">其他信息
          </div>
        </el-col>

        <table style="width:100%" >
          <tr>
            <td class="jieshi">客户备注：</td>
            <td>{{infodata.msg_to_shop}}</td>
            <td class="jieshi">商家备注：</td>
            <td>{{infodata.msg_to_buyer}}</td>
          </tr>
          <tr>
            <td class="jieshi">订单标签：</td>
            <td>{{infodata.label}}</td>
            <td class="jieshi">订单留言：</td>
            <td></td>
          </tr>
        </table>
      </div>



      <div style="width:100%;" class="order_info">
        <el-col :span="24">
          <div class="bg-orange">收货人信息
          </div>
        </el-col>

        <table style="width:100%" >
          <tr>
            <td class="jieshi">收货人：</td>
            <td>{{infodata.consignee}}</td>
            <td class="jieshi">电子邮件：</td>
            <td>{{infodata.email}}</td>
          </tr>
          <tr>
            <td class="jieshi">地址：</td>
            <td>【{{infodata.provincename}}{{infodata.cityname}}{{infodata.districtname}}】{{infodata.address}}</td>
            <td class="jieshi">邮编：</td>
            <td>{{infodata.zipcode}}</td>
          </tr>
          <tr>
            <td class="jieshi">电话：</td>
            <td>{{infodata.phone}}</td>
            <td class="jieshi">最佳送货时间：</td>
            <td>{{infodata.best_time}}</td>
          </tr>
        </table>
      </div>

      <div style="width:100%;" class="back_infogoods">
        <el-col :span="24">
          <div class="bg-orange">商品信息
          </div>
        </el-col>
        <table style="width:100%" >
        <tr>
          <th>商品名称</th>
          <th>货号</th>
          <th>供应商</th>
          <th>状态</th>
          <th>快递单号</th>
          <th>属性</th>
          <th>价格</th>
          <th>数量</th>
          <th>优惠券平摊</th>
          <th>折扣</th>
          <th>库存</th>
          <th>小计</th>
        </tr>
        <tr v-for="v in infoGood.item">
          <td><span v-if="v.delivery_id" style="color: red">[预售]</span>{{v.goods_name}}</td>
          <td>{{v.goods_sn}}</td>
          <td>{{v.suppliers_name}}</td>
          <td>{{v.o_status}}</td>
          <td>{{v.invoice}}</td>
          <td>{{v.goods_attr}}</td>
          <td>{{v.goods_price}}</td>
          <td>{{v.goods_number}}</td>
          <td>{{v.bonus}}</td>
          <td>{{v.discount}}</td>
          <td>{{v.stock}}</td>
          <td>{{v.subprice}}</td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td><b>商品总重量：</b></td>
          <td><b>{{infoGood.subweight}}kg</b></td>
          <td></td>
          <td><b>合计：</b></td>
          <td><b>{{infoGood.subprice}}</b></td>
        </tr>
      </table>
      </div>

      <div style="width:100%;" class="order_info">
        <el-col :span="24">
          <div class="bg-orange">费用信息
          </div>
        </el-col>
        <table style="width:100%" >
          <tr style="text-align:right;">
              商品总积分： {{infodata.goods_amount}}- 折扣：{{infodata.discount}}
          </tr>
          <tr style="text-align:right;">
             = 订单总积分：{{infodata.total_fee}}
          </tr>
          <tr style="text-align:right;">
             付款积分:{{infodata.surplus}}
          </tr>
          <tr style="text-align:right;">
      
          </tr>
        </table>
      </div>

       <!-- <div style="width:100%;" class="back_infogoods" v-if="infodata.status!=1&&infodata.status!=9" >
          <el-col :span="24">
            <div class="bg-orange">操作信息
            </div>
          </el-col>
      </div> -->

      <!-- <table style="width:100%;text-align: center" >
        <tr>
          <th >操作者</th>
          <th>操作时间	</th>
          <th >订单状态</th>
          <th>付款状态</th>
          <th>发货状态</th>
          <th>备注</th>
        </tr>
          <tr v-for="v in infodata.order_action">
          <td >{{v.action_user}}</td>
          <td>{{v.log_time}}</td>
          <td> {{v.order_status}}</td>
          <td>{{v.pay_status}}</td>
          <td>{{v.shipping_status}}</td>
          <td>{{v.action_note}}</td>
        </tr>
      </table> -->
  </div>
</template>

<script>
import { orderInfo,regionList } from "../../../api/api";

export default {
  data() {
    return {
      infodata: Object,
      infoGood:{},
      HYaddress:[],
      shouldPay:'',
      editForm:{
        order_id:'',
        type:0,
        o_status:'',
        msg_to_buyer:'',
        label:'',
        // country:'',
        province:'',
        city:'',
        district:'',
        address:'',
        phone:'',
        email:'',
        zipcode:'',
        best_time:'',
        consignee:'',
        shipping_fee:'',
        // discount:'',
        action_note:'',
        list:[],
      },
      pOption:[],
      cOption:[],
      dOption:[],
      o_statusoptions: JSON.parse(sessionStorage.getItem("data")).data.argument.o_status,
      editFormVisible:false,
    };
  },
  methods: {
    getData() {
      this.HYaddress = JSON.parse(localStorage.getItem("HYaddress"));
      orderInfo({ order_id: this.$route.params.id }).then(res => {
        for (var j in this.HYaddress) {
          if (res.data.data.province == j) {
            res.data.data.provincename = this.HYaddress[j];
          }
          if (res.data.data.city == j) {
            res.data.data.cityname = this.HYaddress[j];
          }
          if (res.data.data.district == j) {
            res.data.data.districtname = this.HYaddress[j];
          }
        }
        this.infodata = res.data.data;
        this.infodata1 = res.data.data;
        this.infoGood = res.data.data.order_goods;
        this.shouldPay = (this.infodata.total_fee- this.infodata.money_paid-this.infodata.surplus-this.infodata.bonus_ship-this.infodata.bonus).toFixed(2);
        this.getRegion();
        this.getRegion(2,res.data.data.province);
        this.getRegion(3,res.data.data.city);
      });
    },

    getRegion(type,pId){
      regionList({
        type:type,
        parent_id:pId
      }).then(res=>{
        if(res.code == 200){
          if(type == 2){
            if(pId != this.editForm.city){
              this.editForm.city = "";
              this.editForm.district = "";
            }
            this.cOption = res.data;
          }else if(type == 3){
            if(pId != this.editForm.district){
              this.editForm.district = "";
            }
            this.dOption = res.data;
          }else if(type != 4){
            this.pOption = res.data;
          }
        }
      })
    },
    goBack() {
      this.$router.go(-1);
    },
  },
  mounted() {
    this.getData();
  }
};
</script>

<style>
  .el-col {
    border-radius: 4px;
  }
  .bg-orange {
    background: #bbdde5;
    text-align: center;
    line-height: 40px;
    border-radius: 5px;
  }
  .order_info tr {
    height: 30px;
    border: 1px solid #333;
  }
  .order_info .jieshi {
    text-align: right;
    width: 13%;
  }
  .order_info td {
    width: 37%;
  }
  .back_infogoods td {
    text-align: center;
  }
</style>
