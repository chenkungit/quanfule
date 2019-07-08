<template>
  <div class="order">
    <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item>
          <el-input v-model="order_id" size="mini"><template slot="prepend">订单ID</template></el-input>
        </el-form-item>
        <el-form-item>
          <el-input v-model="order_sn" size="mini"><template slot="prepend">订单号</template></el-input>
        </el-form-item>
        <el-form-item>
          <el-input v-model="consignee" size="mini"><template slot="prepend">收货人</template></el-input>
        </el-form-item>
        <el-form-item>
          <el-input v-model="user_name" size="mini"><template slot="prepend">下单人</template></el-input>
        </el-form-item>
        <el-form-item>
          <el-input v-model="label" size="mini"><template slot="prepend">标签</template></el-input>
        </el-form-item>
        <!-- <el-form-item>
          <el-input v-model="code" size="mini"><template slot="prepend">货号</template></el-input>
        </el-form-item> -->
        <el-form-item label="订单状态">
          <el-select v-model="o_status" placeholder="请选择" size="mini" @change="getOrder">
            <el-option v-for="item in o_statusoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <!-- <el-form-item label="发货地">
          <el-select v-model="sendaddress" placeholder="请选择" @change="getOrder">
            <el-option v-for="item in sendoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item> -->
        <!-- <el-form-item label="商品状态">
          <el-select v-model="g_status" placeholder="请选择" @change="getOrder">
            <el-option v-for="item in goodoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item> -->
        <el-form-item>
          <el-input v-model="goods_name" size="mini"><template slot="prepend">商品名称</template></el-input>
        </el-form-item>
        <el-form-item>
          <el-date-picker v-model="create_time" type="datetime" placeholder="选择下单开始日期时间" value-format="yyyy-MM-dd HH:mm:ss">
          </el-date-picker>
        </el-form-item>
        <el-form-item>
          <el-date-picker v-model="end_time" type="datetime" placeholder="选择下单结束日期时间" value-format="yyyy-MM-dd HH:mm:ss">
          </el-date-picker>
        </el-form-item>
        <!-- <el-form-item>
          <el-checkbox v-model="is_dingjin">是否订金时间</el-checkbox>
        </el-form-item> -->
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" size="mini" @click="getOrder">搜索</el-button>
        </el-form-item>
        <el-form-item>
          <el-button icon="el-icon-printer" type="warning" @click="exportCsv">导出</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 列表 -->
    <div style="display: table-cell;">
    <el-table :data="orderData" ref="table" border style="width: 100%" v-loading="tableloading" element-loading-text="拼命加载中"
              @row-dblclick="handleInfo" @selection-change="handleSelect" >
      <el-table-column type="selection" width="40"></el-table-column>
      <el-table-column prop="order_id" label="订单ID" width="65"></el-table-column>
      <el-table-column prop="order_sn" label="订单号" width="155" >
         <template slot-scope="scope">
          <el-popover placement="bottom" trigger="hover" @show="showgoodInfo(scope.row)" @hide='hidegoodInfo' :open-delay=1500>
            <el-table :data="infoGood" v-loading="infoloading" element-loading-text="拼命加载中">
              <el-table-column width="400" prop="goods_name" label="商品名称"></el-table-column>
              <el-table-column width="150" prop="goods_sn" label="商品货号"></el-table-column>
              <el-table-column width="100" prop="goods_price" label="商品价格(￥)"></el-table-column>
              <el-table-column width="100" prop="goods_number" label="购买数量"></el-table-column>
              <el-table-column width="150" prop="goods_attr" label="商品属性"></el-table-column>
              <el-table-column width="100" prop="stock" label="商品库存"></el-table-column>
              <el-table-column width="100" prop="goods_price" label="积分小计(￥)">
                <template slot-scope="scope">
                <p>{{(scope.row.goods_number*scope.row.goods_price).toFixed(2)}}</p>
              </template>
              </el-table-column>
            </el-table>
            <div slot="reference" >
               <p>{{scope.row.order_sn}} </p>
                <p style="color:red">数据来源：{{scope.row.referer}} </p>
                <p>{{scope.row.print_info}} </p>
            </div>
           
           </el-popover>
        </template>
       

      </el-table-column>
      <!-- <el-table-column prop="biaoqian" label="订单标签" width="100">
        <template slot-scope="scope">
          <p style="color:red">{{scope.row.label}} </p>
        </template>
      </el-table-column> -->
      <el-table-column prop="sendaddress_name" label="发货地" width="130">
      </el-table-column>
      <el-table-column prop="add_time" label="下单时间" width="140">
        <template slot-scope="scope">
          <p>{{scope.row.nick}} </p>
          <p>{{scope.row.add_time}} </p>
          <p style="color:red;font-weight:600;">{{scope.row.msg_to_shop}} </p>
        </template>
      </el-table-column>
      <el-table-column prop="pay_time" label="付款时间" width="135">
      </el-table-column>
      <el-table-column prop="phone" label="手机号" width="100" v-show="isShow">
      </el-table-column>
      <el-table-column prop="shouhuoren" label="收货人">
        <template slot-scope="scope">
          <p>{{scope.row.consignee}}[TEL: {{scope.row.phone}}] </p>
          <p><span style="color:red">{{scope.row.provincename}}</span> {{scope.row.cityname}}{{scope.row.districtname}}{{scope.row.address}}</p>
        </template>
      </el-table-column>
      <el-table-column prop="order_amount" label="总积分" width="60">
      </el-table-column>
      <el-table-column prop="money_paid" label="应付积分" width="80">
      </el-table-column>
      <!-- <el-table-column prop="pay_name" label="支付方式" width="120">
      </el-table-column>
      <el-table-column prop="shipping_name" label="配送方式" width="80">
      </el-table-column> -->
      <el-table-column prop="o_status" label="订单状态" width="100">
      </el-table-column>
      <!-- <el-table-column label="是否推送" width="70">
        <template slot-scope="scope">
          <p v-if="scope.row.is_push==0">未推送</p>
          <p v-if="scope.row.is_push==1">已推送</p>
        </template>
      </el-table-column> -->
      <el-table-column label="操作" width="290">
        <template slot-scope="scope">
          <el-button v-if="scope.row.o_status=='等待卖家发货'" icon="el-icon-printer" @click="handleFahuo(scope.row)">发货</el-button>
          <el-button icon="el-icon-view" @click="handleInfo(scope.row)">详情</el-button>
          <el-button v-if="scope.row.o_status=='发货完成'||scope.row.o_status=='申请退货'||scope.row.o_status=='退货完成'||scope.row.o_status=='交易成功'"
            icon="el-icon-view" @click="handleDelivery(scope.row)">发货单</el-button>
        </template>
      </el-table-column>
    </el-table>
    </div>
    <!-- 分页 -->
    <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination @size-change="handleSizeChange" :page-sizes="[15, 50, 100, 200]" :page-size="pagesize" @current-change="handleCurrentChange"
        layout="total, sizes, prev, pager, next, jumper" :total="count">
      </el-pagination>
    </div>
    <el-dialog title="发货单信息" :visible.sync="DeliveryVisible">
      <el-table :data="DeliveryData" border>
        <el-table-column prop="invoice_no" label="快递单号" width="110">
        </el-table-column>
        <el-table-column prop="consign_time" label="发货时间	">
        </el-table-column>
        <el-table-column prop="logistics_name" label="快递公司">
        </el-table-column>
      </el-table>
    </el-dialog>
        <!-- 发货操作 -->
    <el-dialog title="发货" :visible.sync="fahuoFormVisible">
			<el-form :model="fahuoForm" label-width="200px" :rules="fahuoFormRules" ref="fahuoForm">
        <el-form-item label="快递公司" prop="logistics_type">
          <el-select v-model="fahuoForm.logistics_type" placeholder="请选择">
            <el-option v-for="item in logistics_typeOptions" :key="item.id" :label="item.name" :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="快递单号" prop="invoice_no">
					<el-input v-model="fahuoForm.invoice_no"></el-input>
				</el-form-item>
        <el-form-item label="发出时间" prop="consign_time">
          <el-date-picker v-model="fahuoForm.consign_time" type="datetime" placeholder="选择发出时间" value-format="yyyy-MM-dd HH:mm:ss">
            </el-date-picker>
        </el-form-item>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button @click.native="fahuoFormVisible = false">取消</el-button>
				<el-button type="primary" @click.native="fahuoSubmit">提交</el-button>
			</div>
		</el-dialog>
  </div>
</template>
<script>
import { orderLists } from "../../api/api";
import { deliveryLists } from "../../api/api";
import { regionAddresss } from "../../api/api";
import { orderfahuo, orderlogistics_type } from "../../api/api";
import { orderInfo } from "../../api/api";
import { trackingInfo } from "../../api/api";
import CsvExport from "../../CsvExport";
export default {
  components: {
    // printpage
  },
  data() {
    return {
      orderData: [],
      pagecount: 1,
      count: 1,
      page: 1,
      pagesize: 15,
      tableloading: true,
      infoloading: true,
      HYaddress: [],
      order_sn: "",
      order_id: "",
      consignee: "",
      o_status: "",
      goods_name: "",
      logistics_typeOptions: [],
      fahuoFormVisible: false,
      fahuoFormRules: {
        invoice_no: [
          { required: true, message: "请输入快递单号", trigger: "blur" }
        ],
        logistics_type: [
          { required: true, message: "请选择快递公司", trigger: "change" }
        ]
      },
      fahuoForm: {
        order_id: 0,
        invoice_no: "",
        logistics_type: "",
        consign_time: this.changetime(new Date())
      },
      g_status: 99,
      o_statusoptions: JSON.parse(sessionStorage.getItem("data")).data.argument
        .o_status,
      label: "",
      user_name: "",
      create_time: this.changetime(
        new Date().setMonth(new Date().getMonth() - 6)
      ),
      end_time: this.changetime(new Date()),
      is_fapiao: 0,
      is_dingjin: false,
      code: "",
      infoGood: [],
      sendoptions: [],
      goodoptions: [],
      sendaddress: 0,
      push_ids: [],
      DeliveryVisible: false,
      DeliveryData: [],
      isShow: false,
      dataSwt: true
    };
  },
  created() {
    orderlogistics_type().then(res => {
      this.logistics_typeOptions = res.data.data;
    });
  },
  methods: {
    getOrder() {
      let params = {};
      params.page = this.page;
      params.limit = this.pagesize;
      params.create_time = this.create_time;
      params.end_time = this.end_time;
      params.is_dingjin = this.changeswitch(this.is_dingjin);
      this.tableloading = true;
      if (this.order_id != "") params.order_id = this.order_id;
      if (this.goods_name != "") params.goods_name = this.goods_name;
      if (this.order_sn != "") params.order_sn = this.order_sn;
      if (this.consignee != "") params.consignee = this.consignee;
      if (this.label != "") params.label = this.label;
      if (this.user_name != "") params.user_name = this.user_name;
      if (this.o_status != "99") params.o_status = this.o_status;
      if (this.is_fapiao) params.is_fapiao = this.is_fapiao;
      if (this.sendaddress) params.sendaddress = this.sendaddress;
      if (this.g_status != 99) params.g_status = this.g_status;
      if (this.code) params.code = this.code;

      if (this.dataSwt) {
        this.dataSwt = false;
        orderLists(params).then(res => {
          if (res.data.code == 200) {
            this.tableloading = false;
            this.dataSwt = true;
            for (var i in res.data.data.item) {
              for (var j in this.HYaddress) {
                if (res.data.data.item[i].province == j) {
                  res.data.data.item[i].provincename = this.HYaddress[j];
                }
                if (res.data.data.item[i].city == j) {
                  res.data.data.item[i].cityname = this.HYaddress[j];
                }
                if (res.data.data.item[i].district == j) {
                  res.data.data.item[i].districtname = this.HYaddress[j];
                }
              }
            }
            this.orderData = res.data.data.item;
            res.data.data.item.forEach((item, index) => {
              this.orderData[index].shouhuoren =
                item.consignee +
                "[地址:]" +
                item.provincename +
                item.cityname +
                item.districtname +
                item.address;
              this.orderData[index].biaoqian = item.label;
            });
            this.pagecount = res.data.data.pagecount;
            this.count = res.data.data.count;
          } else {
            this.dataSwt = true;
          }
        });
      }
    },
    showgoodInfo(row) {
      var id = row.order_id;
      orderInfo({ order_id: id }).then(res => {
        this.infoGood = res.data.data.order_goods.item;
        this.infoloading = false;
      });
    },
    hidegoodInfo() {
      this.infoGood = [];
      this.infoloading = true;
    },
    handleCurrentChange(val) {
      this.page = val;
      this.getOrder();
    },
    handleSizeChange(val) {
      this.pagesize = val;
      this.getOrder();
    },
    exportCsv() {
      let columns = this.$refs.table.$children.filter(t => t.prop != null);
      const fields = columns.map(t => t.prop);
      const fieldNames = columns.map(t => t.label);
      CsvExport(this.orderData, fields, fieldNames, "列表");
    },
    handleFahuo(params) {
      this.fahuoFormVisible = true;
      this.fahuoForm.order_id = params.order_id;
    },
    fahuoSubmit() {
      this.$refs.fahuoForm.validate(valid => {
        if (valid) {
          this.$confirm("确认发货?", "提示", {
            type: "warning"
          }).then(() => {
            orderfahuo(this.fahuoForm).then(res => {
              if (res.code == 200) {
                this.$message({
                  message: res.msg,
                  type: "success"
                });
                this.getOrder();
                this.fahuoFormVisible = false;
                this.$refs["fahuoForm"].resetFields();
              } else {
                this.$message({
                  message: res.msg,
                  type: "warning"
                });
              }
            });
          });
        }
      });
    },
    handleSelect(params) {
      this.push_ids = params.map(item => {
        return item.order_id;
      });
    },
    handleInfo(params) {
      let id = params.order_id;
      this.$router.push({
        path: "/order/info/" + id
      });
    },
    handleDelivery(params) {
      deliveryLists({
        order_id: params.order_id
      }).then(res => {
        if (res.data.code == 200) {
          this.DeliveryVisible = true;
          this.DeliveryData = res.data.data;
        }
      });
    }
  },
  mounted() {
    this.HYaddress = JSON.parse(localStorage.getItem("HYaddress"));
    if (this.HYaddress == null) {
      regionAddresss().then(res => {
        localStorage.setItem("HYaddress", JSON.stringify(res.data.data));
        this.HYaddress = JSON.parse(localStorage.getItem("HYaddress"));
        this.getOrder();
      });
    } else {
      this.getOrder();
    }
    var a = JSON.parse(sessionStorage.getItem("data"));
    this.sendoptions = a.data.argument.sendaddress;
    this.goodoptions = a.data.argument.g_status;
    this.sendoptions.unshift({
      value: 0,
      label: "--------------请选择--------------"
    });
  },
  activated() {
    this.HYaddress = JSON.parse(localStorage.getItem("HYaddress"));
    if (this.HYaddress == null) {
      regionAddresss().then(res => {
        localStorage.setItem("HYaddress", JSON.stringify(res.data.data));
        this.HYaddress = JSON.parse(localStorage.getItem("HYaddress"));
        this.getOrder();
      });
    } else {
      this.getOrder();
    }
    var a = JSON.parse(sessionStorage.getItem("data"));
    this.sendoptions = a.data.argument.sendaddress;
    this.goodoptions = a.data.argument.g_status;
    this.sendoptions.unshift({
      value: 0,
      label: "--------------请选择--------------"
    });
  }
};
</script>


