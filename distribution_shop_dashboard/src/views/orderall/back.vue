<template>
  <div>
    <el-col :span="24" class="toolbar" style="width:100%">
      <el-form :inline="true">
        <el-form-item>
          <!-- <div style="float:left;padding-right:10px;padding-left:10px;">
            <el-button icon="el-icon-printer" type="warning" @click="exportCsv">导出</el-button>
          </div> -->
          <div style="float:left;">退货单号：</div>
          <div style="float:left;">
            <el-input style="width:150px;" v-model="refund_sn" placeholder="请输入退货单号"></el-input>
          </div>
          <div style="float:left;">订单号：</div>
          <div style="float:left;">
            <el-input style="width:150px;" v-model="order_sn" placeholder="请输入订单号"></el-input>
          </div>
          <div style="float:left;">状态：</div>
          <div style="float:left;">
            <el-select v-model="status" placeholder="请选择" style="width:120px;" @change="handlefind">
              <el-option v-for="item in stateoptions" :key="item.value" :label="item.label" :value="item.value">
              </el-option>
            </el-select>
          </div>
          <div style="float:left;">退货前状态：</div>
          <div style="float:left;">
            <el-select v-model="r_status" placeholder="请选择" style="width:120px;" @change="handlefind">
              <el-option v-for="item in rStateOption" :key="item.value" :label="item.label" :value="item.value">
              </el-option>
            </el-select>
          </div>
          <div style="float:left;">退货原因：</div>
          <div style="float:left;">
            <el-select v-model="reason_type" placeholder="请选择" style="width:120px;" @change="handlefind">
              <el-option v-for="item in reasonOption" :key="item.value" :label="item.label" :value="item.value">
              </el-option>
            </el-select>
          </div>
          <div style="float:left;">退款说明：</div>
          <div style="float:left;">
            <el-input style="width:150px;" v-model="reason_info" placeholder="请输入退款说明"></el-input>
          </div>
          <div style="float:left;">下单用户名：</div>
          <div style="float:left;">
            <el-input style="width:150px;" v-model="user_name" placeholder="请输入下单用户名"></el-input>
          </div>
          <div style="float:left;padding-right:10px;padding-left:10px;">
            <el-button type="primary" icon="el-icon-search" @click="handlefind">搜索</el-button>
          </div>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 列表 -->
    <el-table :data="backData" ref="table" border style="width: 100%" v-loading="tableloading" element-loading-text="拼命加载中" @row-dblclick="handleInfo" @selection-change="handleslect">
      <!-- <el-table-column type="selection" width="55">
      </el-table-column> -->
      <el-table-column prop="refund_sn" label="退货单号" width="150">
      </el-table-column>
      <el-table-column prop="order_sn" label="订单号" width="150">
        <template slot-scope="scope">
          <p @click="gotodingdan(scope.row.order_id)" style="color:blue;cursor:pointer">{{scope.row.order_sn}}</p>
        </template>
      </el-table-column>
      <el-table-column prop="msg_to_buyer" label="订单留言" width="150">
      </el-table-column>
      <!-- <el-table-column prop="label" label="订单标签" width="150">
      </el-table-column> -->
      <el-table-column prop="reason_type" label="退货原因" width="150">
      </el-table-column>
      <el-table-column prop="reason_info" label="退款说明" width="150">
      </el-table-column>
      <el-table-column prop="add_time" label="申请时间" width="150">
      </el-table-column>
      <el-table-column prop="type_name" label="退货类型" width="110">
      </el-table-column>
      <el-table-column prop="status_name" label="状态"  width="110">
        <template slot-scope="scope">
          <p v-if="scope.row.status_name=='等待处理'" style="color:red">等待处理</p>
        </template>
      </el-table-column>
      <el-table-column label="操作">
        <template slot-scope="scope">
          <!-- <el-button  @click="handleEdit(scope.row)">编辑</el-button> -->
          <el-button type="primry" icon="el-icon-view" @click="handleInfo(scope.row)">查看</el-button>
        </template>
      </el-table-column>
    </el-table>
    <!-- 分页 -->
    <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination @current-change="handleCurrentChange" layout="prev, pager, next, jumper" :page-count="pagecount"></el-pagination>
      <p style="font-size:13px;color: #48576a;">每页条数：<input type="text" id="tiaoshu" v-model="pagetiao" @blur="changetiao"></p>
    </div>
    <!-- </el-tab-pane> -->
    <!-- <el-tab-pane label="打印" name="second">
	        <printpage :message="all" v-if="activeName=='second'"></printpage>
	      </el-tab-pane> -->
    <!-- </el-tabs> -->
  </div>
</template>
<script>
import { backLists } from "../../api/api";
import backInfo from "./back/backInfo";
import CsvExport from "../../CsvExport";
export default {
  components: {
    // printpage
  },
  data() {
    return {
      activeName: "first",
      backData: [],
      pagetiao: 20,
      pagecount: 1,
      page: 1,
      allid: [],
      tableloading: true,
      shouldpdata: [],
      all: {
        data: ""
      },
      stateoptions: [
        {
          value: 0,
          label: "等待处理"
        },
        {
          value: 1,
          label: "处理完成"
        },
        {
          value: 9,
          label: "取消"
        }
      ],
      rStateOption: [
        {
          value: 0,
          label: "请选择订单状态"
        },
        {
          value: 1,
          label: "等待卖家发货"
        },
        {
          value: 4,
          label: "发货完成"
        }
      ],
      reasonOption: [
        {
          value: 0,
          label: "请选择退货原因"
        },
        {
          value: 1,
          label: "卖家缺货"
        },
        {
          value: 2,
          label: "质量问题/破损"
        },
        {
          value: 3,
          label: "尺寸拍错"
        },
        {
          value: 4,
          label: "颜色/规格/大小尺寸与描述不符"
        },
        {
          value: 5,
          label: "卖家发错货"
        },
        {
          value: 6,
          label: "卖家少发/漏发"
        },
        {
          value: 7,
          label: "其他问题"
        }
      ],
      status: 0,
      r_status:0,
      reason_type:0,
      reason_info:'',
      user_name:'',
      refund_sn: "",
      order_sn: ""
    };
  },
  methods: {
    gotodingdan(params) {
      this.$router.push({
        path: "/order/info/" + params
      });
    },
    // exportCsv() {
    //   let columns = this.$refs.table.$children.filter(t => t.prop != null);
    //   const fields = columns.map(t => t.prop);
    //   const fieldNames = columns.map(t => t.label);
    //   CsvExport(this.backData, fields, fieldNames, "列表");
    // },
    getBack() {
      backLists({
        page: this.page,
        limit: this.pagetiao,
        refund_sn: this.refund_sn,
        order_sn: this.order_sn,
        status: this.status,
        r_status:this.r_status,
        reason_type:this.reason_type,
        reason_info:this.reason_info,
        user_name:this.user_name
      }).then(res => {
        if (res.data.code == 200) {
          this.tableloading = false;
          this.backData = res.data.data.item;
          this.pagecount = res.data.data.page_count;
        }
      });
    },
    changetiao() {
      this.getBack();
    },
    handleCurrentChange(val) {
      this.page = val;
      this.getBack();
    },
    handleslect(val) {
      this.shouldpdata = [];
      this.shouldpdata = val;
    },
    handleInfo(params) {
      let id = params.id;
      this.$router.push({
        path: "/back/info/" + id
      });
    },
    handlefind() {
      this.getBack();
    }
  },
  mounted() {
    this.getBack();
  },
  activated(){
    this.getBack();
  }
};
</script>
<style>
#tiaoshu {
  display: inline-block;
  border: 1px solid #d1dbe5;
  border-radius: 2px;
  line-height: 18px;
  padding: 4px 2px;
  width: 50px;
  text-align: center;
  margin: 0 6px;
  box-sizing: border-box;
  transition: border 0.3s;
}
</style>
