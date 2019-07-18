<template>
  <div style="padding-top:30px;font-size:12px;">
    <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item>
          <div style="float:left;padding-right:10px;padding-left:10px;">
            <el-button type="warning" icon="el-icon-back" @click="backrou()">返回</el-button>
          </div>
          <div style="float:left;padding-right:10px;padding-left:10px;">
            退货单操作：查看
          </div>
        </el-form-item>
      </el-form>
    </el-col>

    <div style="width:100%;" class="back_orderinfo">
      <el-col :span="24">
        <div class="bg-orange">订单信息</div>
      </el-col>
      <table style="width:100%">
        <tr>
          <td class="jieshi">订单号：</td>
          <td><router-link :to="{ name: '订单详情', params: {id: infodata.order_id}}">{{infodata.order_sn}}</router-link></td>
          <td class="jieshi">退货原因：</td>
          <td>{{infodata.reason_info}}</td>
        </tr>
        <tr>
          <td class="jieshi">退货前的订单状态：</td>
          <td>{{infodata.status_name}}</td>
          <td class="jieshi">申请退货时间：</td>
          <td>{{infodata.add_time}}</td>
        </tr>
        <tr>
          <td class="jieshi">支付方式：</td>
          <td>积分</td>
          <!-- <td class="jieshi">退货原因：</td>
          <td>{{infodata.reason_info}}</td> -->
        </tr>
        <tr>
          <td class="jieshi">退货单号：</td>
          <td>{{infodata.refund_sn}}</td>
          <td class="jieshi"></td>
          <td></td>
        </tr>
      </table>
    </div>

    <div style="width:100%;" class="back_orderinfo">
      <el-col :span="24">
        <div class="bg-orange">其他信息
        </div>
      </el-col>

      <table style="width:100%" >
        <tr>
          <td class="jieshi">客户备注：</td>
          <td>
            {{infodata.msg_to_shop}}
          </td>
        </tr>
        <!-- <tr>
          <td class="jieshi">商家备注：</td>
          <td>
            {{infodata.msg_to_buyer}}
          </td>
        </tr> -->
        <!-- <tr>
          <td class="jieshi">订单标签：</td>
          <td>{{infodata.label}}</td>
        </tr> -->
        <!-- <tr>
          <td class="jieshi">订单留言：</td>
          <td></td>
        </tr> -->
        <!-- <tr>
          <td class="jieshi">退货快递：</td>
          <td>{{infodata.refund_shipping}}</td>
        </tr> -->
        <tr>
          <td class="jieshi">退货快递单号：</td>
          <td>{{infodata.refund_invoince_no}}</td>
        </tr>
      </table>
    </div>

    <div style="width:100%;" class="back_orderinfo">
      <el-col :span="24">
        <div class="bg-orange">下单人账户信息</div>
      </el-col>

      <table style="width:100%">
        <tr>
          <td class="jieshi">用户id：</td>
          <td>{{infodata.user_id}}</td>
          <td class="jieshi">用户名：</td>
          <td> {{infodata.user_name}}</td>
        </tr>
        <tr>
          <td class="jieshi">昵称：</td>
          <td>{{infodata.nick}}</td>
          <!-- <td class="jieshi">手机号：</td>
          <td>{{infodata.mobile}}</td> -->
        </tr>
      </table>
    </div>

    <div style="width:100%;" class="back_orderinfo">
      <el-col :span="24">
        <div class="bg-orange">收货人信息</div>
      </el-col>

      <table style="width:100%">
        <tr>
          <td class="jieshi">收货人：</td>
          <td>
            {{infodata.consignee}}
          </td>
          <!-- <td class="jieshi">电子邮件：</td>
          <td></td> -->
        </tr>
        <tr>
          <td class="jieshi">地址：</td>
          <td>[{{infodata.region}}]{{infodata.address}}</td>
          <!-- <td class="jieshi">邮编：</td>
          <td>{{infodata.zipcode}}</td> -->
        </tr>
        <tr>
          <td class="jieshi">电话：</td>
          <td>{{infodata.phone}}</td>
          <!-- <td class="jieshi">最佳送货时间：</td>
          <td></td> -->
        </tr>
        <tr>
          <td class="jieshi">客户留言:</td>
          <td></td>
          <!-- <td class="jieshi">退货凭证:</td>
          <td>
            <div style="position: relative" @mouseover="showImg()" @mouseout="hideImg()">
              <img style="width:200px;" :src="reason_img" alt="">
              <div ref="imgDiv" style="width: 300px;height: 300px;position: absolute;display: none;"><img :src="reason_img" alt="" width="600px" height="500px"></div>
            </div>
          </td> -->
        </tr>
        <tr>
          <td class="jieshi">退货凭证:</td>
          <td>
            <div style="position: relative" @mouseover="showImg()" @mouseout="hideImg()">
              <!--<img style="width:200px;" :src="item" alt="" v-for="item in infodata.reason_img">-->
              <img style="width:200px;" :src="reason_img" alt="">
              <div ref="imgDiv" style="width: 300px;height: 300px;position: absolute;display: none;"><img :src="reason_img" alt="" width="600px" height="500px"></div>
            </div>
          </td>
          </tr>
      </table>
    </div>

    <div style="width:100%;" class="back_infogoods">
      <el-col :span="24">
        <div class="bg-orange">商品信息</div>
      </el-col>

      <table style="width:100%">
        <tr>
          <th>商品名称</th>
          <!-- <th>货号</th>
          <th>供应商</th>
          <th>入驻商</th> -->
          <th>属性</th>
          <th>单价</th>
          <th>购买数量</th>
          <!-- <th>优惠券均摊</th> -->
          <th>实际支付金额</th>
          <th>退货数量</th>
        </tr>
        <tr v-for="item in infodata.refund_goods">
          <td>{{item.goods_name}}
            <span v-if="item.predate!=null" style="color:red">【预售】
              <br/>{{item.predate}} </span>
          </td>
          <!-- <td>{{item.goods_sn}}</td>
          <td>{{item.suppliers_name}}</td>
          <td>{{item.shops_name}} </td> -->
          <td>{{item.goods_attr}}</td>
          <td>{{item.goods_price}}</td>
          <td>{{item.goods_number}} </td>
          <!-- <td>{{item.refund_bonus}}</td> -->
          <td>{{item.refund_goods_price - item.refund_bonus - item.refund_discount}}</td>
          <td>{{item.refund_number}}</td>
        </tr>
      </table>
    </div>

    <!-- <div style="width:100%;" class="back_infogoods">
      <el-col :span="24">
        <div class="bg-orange">入驻商建议</div>
      </el-col>

      <table style="width:100%">
        <tr>
          <th>入驻商</th>
          <th>操作者</th>
          <th>操作时间</th>
          <th>退货建议</th>
          <th>备注</th>

        </tr>
        <tr>
          <td></td>
          <td></td>
          <td> </td>
          <td></td>
          <td></td>
        </tr>
      </table>
    </div> -->

    <div style="width:100%;" class="back_infogoods" v-if="infodata.status!=1&&infodata.status!=9">
      <el-col :span="24">
        <div class="bg-orange">操作信息</div>
      </el-col>
      <p style="text-align:center;font-size:16px;font-weight:600;">退款商品金额（{{infodata.refund_goods_price}}）
        <span
          v-if="infodata.r_status==1||infodata.r_status==2"></span>实际退款余额（{{infodata.refund_surplus}}）</p>
      <table style="width:100%">
        <tr>
          <td style="width:20%">备注：</td>
          <td>
            <textarea name="" id="" cols="100" rows="5" v-model="action_note"></textarea>
          </td>
        </tr>
        <tr>
          <td style="width:20%">操作:</td>
          <td>
            <el-button type="warning" @click="backRef()" :loading="btnloading">完成退货</el-button>
            <el-button type="warning" @click="cancelbackRef()">取消退货</el-button>
          </td>
        </tr>
      </table>
    </div>
    <div style="width:100%;" class="back_infogoods" v-if="infodata.status==1">
      <el-col :span="24">
        <div class="bg-orange">退款金额信息</div>
      </el-col>
      <p style="text-align:center;font-size:16px;font-weight:600;">退款商品金额（{{infodata.refund_goods_price}}）
       = 实际退款余额（{{infodata.refund_surplus}}）
      </p>
    </div>
    <!-- <table style="width:100%">
      <tr>
        <th>操作者</th>
        <th>操作时间 </th>
        <th>订单状态</th>
        <th>付款状态</th>
        <th>发货状态</th>
        <th>备注</th>
      </tr>
      <tr v-for="item in infodata.order_action">
        <td>{{item.action_user}}</td>
        <td>{{item.log_time}}</td>
        <td> {{item.order_status}}</td>
        <td>{{item.pay_status}}</td>
        <td>{{item.shipping_status}}</td>
        <td>{{item.action_note}}</td>
      </tr>
    </table> -->
  </div>
</template>

<script>
import { backInfo,backRefund,cancelBackrefund,deliveryLists,editRefundLabel} from "../../../api/api";
export default {
  data() {
    return {
      infodata: Object,
      reason_img: [],
      loading: false,
      action_note: "",
      btnloading: false,
    };
  },
  methods: {
    getdata() {
      backInfo({
        refund_id: this.$route.params.id
      }).then(res => {
        this.infodata = res.data.data;
        this.loading = true;
        this.reason_img = this.infodata.reason_img[0];
      });
    },
    backrou() {
      this.$router.push({
        path: "/back/lists/"
      });
    },
    cancelbackRef() {
      cancelBackrefund({
        refund_id: this.$route.params.id,
        action_note: this.action_note
      }).then(res => {
        if (res.code == 200) {
          this.$message({
            message: res.msg,
            type: "success"
          });
          this.$router.push({
            path: "/back/lists/"
          });
        } else {
          this.$message({
            message: res.msg,
            type: "warning"
          });
        }
      });
    },
    backRef() {
      this.btnloading = true;
      backRefund({
        refund_id: this.$route.params.id,
        action_note: this.action_note
      }).then(res => {
        this.btnloading = false;
        this.$message({
          message: res.msg,
          type: "success"
        });
        if (res.code == 200) {
          this.$router.push({path: "/back/lists/"});
        } else {
          this.$message({
            message: res.msg,
            type: "warning"
          });
        }
      });
    },

    closeDialog(){},

    showImg(){
      this.$refs.imgDiv.style.display="block";
    },

    hideImg(){
      this.$refs.imgDiv.style.display="none";
    },

    //跳转订单详情
    handleInfo() {
      let id = this.infodata.order_id;
      this.$router.push({
        path: "/order/info/" + id
      });
    },
  },
  mounted() {
    this.getdata();
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

  .back_orderinfo tr {
    height: 30px;
  }

  .back_orderinfo .jieshi {
    text-align: right;
    width: 13%;
  }

  .back_orderinfo td {
    width: 37%;
  }

  .back_infogoods td {
    text-align: center;
  }
</style>
