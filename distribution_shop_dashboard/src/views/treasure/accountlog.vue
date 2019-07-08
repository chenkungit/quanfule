<template>
  <div>
    <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item label="用户名">
          <el-input v-model="user_name" @keyup.enter.native="getData()" placeholder="输入用户名搜索"></el-input>
        </el-form-item>
        <el-form-item label="积分/余额">
          <el-select v-model="money_type" placeholder="请选择" @change="getData">
            <el-option
              v-for="item in money_typeoption"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="类型">
          <el-select v-model="change_type" placeholder="请选择" @change="getData">
            <el-option
              v-for="item in change_typeoption"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="开始时间">
          <el-date-picker
            v-model="beg_time"
            type="datetime"
            placeholder="选择开始时间"
            value-format="yyyy-MM-dd HH:mm:ss"
          ></el-date-picker>
        </el-form-item>
        <el-form-item label="结束时间">
          <el-date-picker
            v-model="end_time"
            type="datetime"
            placeholder="选择结束时间"
            value-format="yyyy-MM-dd HH:mm:ss"
          ></el-date-picker>
        </el-form-item>
        <el-button type="primary" @click="getData()">搜索</el-button>
      </el-form>
    </el-col>
    <!-- 列表 -->
    <el-table
      :data="tableData"
      border
      style="width: 100%"
      v-loading="tableloading"
      element-loading-text="拼命加载中"
    >
    
     <el-table-column prop="vip_code" label="会员ID"></el-table-column>
      <el-table-column prop="user_name" label="用户名"></el-table-column>
      <el-table-column prop="money_type_name" label="积分/余额"></el-table-column>
      <el-table-column prop="change_type_name" label="类型"></el-table-column>
      <el-table-column prop="change_desc" label="描述"></el-table-column>
      <el-table-column prop="prize_money" label="奖励金流水"></el-table-column>
      <el-table-column prop="user_money" label="积分流水"></el-table-column>
      <el-table-column prop="created_time" label="时间"></el-table-column>
    </el-table>
    <!-- 分页 -->
    <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination
        @current-change="handleCurrentChange"
        layout="prev, pager, next, jumper"
        :page-count="pagecount"
      ></el-pagination>
    </div>
  </div>
</template>
<script>
import { accountlogLists } from "../../api/api";
export default {
  data() {
    return {
      tableData: [],
      page: 1,
      pagecount: 1,
      tableloading: true,
      user_name: "",
      money_type: 0,
      change_type: 0,
      beg_time: "",
      end_time: "",
      money_typeoption: [
        { label: "全部类型", value: 0 },
        { label: "积分", value: 1 },
        { label: "余额", value: 2 },
      ],
      change_typeoption: [
        { label: "全部类型", value: 0 },
        { label: "系统转账", value: 1 },
        { label: "用户消费", value: 2 },
        { label: "返佣奖励", value: 3 },
        { label: "申请提现", value: 4 },
        { label: "提现成功", value: 5 },
        { label: "领导奖奖励", value: 6 },
        { label: "业绩奖奖励", value: 7 },
        { label: "积分转出", value: 8 },
        { label: "积分转入", value: 9 },
        { label: "用户退款", value: 10 },
        { label: "奖励金转换积分", value: 11 },
        { label: "每月终生成就奖奖励", value: 12 }
      ]
    };
  },
  methods: {
    getData() {
      //获取数据
      this.tableloading = true;
      var params={
        page: this.page
      }
      if(this.user_name)params.user_name=this.user_name
      if(this.money_type)params.money_type=this.money_type
      if(this.change_type)params.change_type=this.change_type
      if(this.beg_time)params.beg_time=this.beg_time
      if(this.end_time)params.end_time=this.end_time
      accountlogLists(params).then(res => {
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
    }
  },
  mounted() {
    this.getData();
  }
};
</script>