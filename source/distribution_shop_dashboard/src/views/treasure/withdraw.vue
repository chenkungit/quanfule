<template>
  <div>
    <!-- <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item>
          <el-button type="primary" @click="handleAdd">新增</el-button>
        </el-form-item>
      </el-form>
    </el-col> -->
    <!-- 列表 -->
    <el-table :data="tableData" border style="width: 100%" v-loading="tableloading" element-loading-text="拼命加载中">
      <el-table-column prop="user_name" label="用户名">
      </el-table-column>
      <el-table-column prop="type" label="类型">
           <template slot-scope="scope">
                {{scope.row.type==1?'支付宝':'银行卡'}}
            </template>
      </el-table-column>
      <el-table-column prop="alipay_account" label="支付宝号">
      </el-table-column>
      <el-table-column prop="bank_number" label="银行卡号">
      </el-table-column>
      <el-table-column prop="bank_name" label="银行名称">
      </el-table-column>
      <el-table-column prop="status_name" label="状态">
      </el-table-column>
      <el-table-column prop="withdraw_money" label="金额">
      </el-table-column>
      <el-table-column prop="withdraw_service_charge_money" label="手续费">
      </el-table-column>
      <el-table-column prop="created_time" label="创建时间">
      </el-table-column>
      <el-table-column label="操作" width="220">
        <template slot-scope="scope">
          <el-button v-if="scope.row.status==0" @click="handleFinish(scope.row.id)">通过</el-button>
        </template>
      </el-table-column>
    </el-table>
    <!-- 分页 -->
    <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination @current-change="handleCurrentChange" layout="prev, pager, next, jumper" :page-count="pagecount">
      </el-pagination>
    </div>
  </div>
</template>
<script>
import { withdrawLists } from "../../api/api";
import { withdrawfinish } from "../../api/api";
export default {
  data() {
    return {
      tableData: [],
      page: 1,
      pagecount: 1,
      tableloading: true
    };
  },
  methods: {
    getData() {
      //获取数据
      this.tableloading = true;
      withdrawLists({
        page: this.page
      }).then(res => {
        if (res.code == 200) {
          this.pagecount = res.data.pagecount;
          this.tableData = res.data.list;
          this.tableloading = false;
        }
      });
    },
    handleCurrentChange(val) {
      this.page = val;
      this.getData();
    },
    handleFinish(id) {
      var that = this;
      this.$confirm("确认通过吗？", "提示", {}).then(() => {
        withdrawfinish({
          id: id
        }).then(res => {
          if (res.code == 200) {
            that.$message({
              message: res.msg,
              type: "success"
            });
            that.getData();
          } else {
            that.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    }
  },
  mounted() {
    this.getData();
  }
};
</script>