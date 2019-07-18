<template>
  <section>
    <el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
      <el-form :inline="true">
        <el-form-item label="用户名">
          <div style="float:left;margin-right:10px" >
            <el-input v-model="user_name" @keyup.enter.native="getMember()" placeholder="输入用户名搜索"></el-input>
          </div>
        </el-form-item>
          <el-form-item label="是否是会员">
            <el-select v-model="is_vip" placeholder="请选择">
              <el-option label="全部" value="-1"></el-option>
              <el-option label="否" value="0"></el-option>
              <el-option label="是" value="1"></el-option>
            </el-select>
          </el-form-item>
          </el-form-item>
          <el-button type="primary" @click="getMember()">搜索</el-button>
          </el-form-item>
      </el-form>
    </el-col>
    <el-table :data="memberData" border style="width: 100%" v-loading="tableloading">
      <el-table-column prop="vip_code" label="会员ID"></el-table-column>
      <el-table-column prop="nick" label="昵称"></el-table-column>
      <el-table-column prop="user_name" label="用户名">
        <template slot-scope="scope">
          <h4>{{scope.row.user_name!=''?scope.row.user_name:'无用户名'}}</h4>
          <p v-if="scope.row.is_vip" style="color:red">{{scope.row.vip_setting_name}}</p>
        </template>
      </el-table-column>
      <!-- <el-table-column prop="mobile" label="账号"></el-table-column> -->
      <el-table-column prop="user_money" label="积分"></el-table-column>
      <el-table-column prop="prize_money" label="奖励金额"></el-table-column>
      <el-table-column prop="reg_time" label="注册时间"></el-table-column>
      <el-table-column label="操作" width="350">
        <template slot-scope="scope">
          <el-button icon="el-icon-view" @click="handlemoneyInfo(scope.row)">积分日志</el-button>
          <el-button  type="primary" @click="handlemoneyEdit(scope.row)">下发积分</el-button>
          <el-button  type="primary" @click="handlethankfulEdit(scope.row)">感恩红包</el-button>
        </template>
      </el-table-column>
    </el-table>
    <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination
        @size-change="handleSizeChange"
        :page-sizes="[15, 50, 100, 200]"
        :page-size="pagesize"
        @current-change="handleCurrentChange"
        layout="total, sizes, prev, pager, next, jumper"
        :total="count"
      ></el-pagination>
    </div>
    <!--编辑界面-->
      <el-dialog title="感恩红包" :visible.sync="thankfulVisible">
      <el-form :model="thankful" label-width="150px" ref="thankful">
        <el-form-item label="金额" prop="money">
          <el-input type="text" v-model="thankful.money"></el-input>
        </el-form-item>
        <el-form-item label="已发放积分" prop="send_money">
          <el-input type="text" v-model="thankful.send_money" disabled></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="thankfulVisible = false">取消</el-button>
        <el-button type="primary" @click.native="editthankfulSubmit">提交</el-button>
      </div>
    </el-dialog>
    <!--编辑界面-->
    <el-dialog title="下发积分" :visible.sync="moneyFormVisible">
      <el-form :model="moneyForm" label-width="150px" ref="moneyForm">
        <el-form-item label="积分数" prop="money">
          <el-input type="text" v-model="moneyForm.money"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="moneyFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="editmoneySubmit">提交</el-button>
      </div>
    </el-dialog>
    <el-dialog title="下发积分详情" :visible.sync="moneylogFormVisible">
      <el-table :data="logData" border v-loading="tablelogloading">
        <el-table-column prop="change_type_name" label="类型"></el-table-column>
        <el-table-column prop="change_desc" label="备注"></el-table-column>
        <el-table-column prop="created_time" label="时间"></el-table-column>
      </el-table>
          <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination
        @size-change="handlelogSizeChange"
        :page-sizes="[100, 200]"
        :page-size="logpagesize"
        @current-change="handlelogCurrentChange"
        layout="total, sizes, prev, pager, next, jumper"
        :total="logcount"
      ></el-pagination>
    </div>
    </el-dialog>
  </section>
</template>
<script>
import {
  memberLists,
  sendpoint,
  consumeList,
  thankfulInfo,
  thankfuledit
} from "../../api/api";
export default {
  data() {
    return {
      memberData: [],
      user_name: "",
      is_vip: "-1",
      pagecount: 1,
      count: 1,
      page: 1,
      pagesize: 15,
      tableloading: false,
      moneyForm: {
        money: "",
        user_id: 0
      },
      moneyFormVisible: false,
      logpagecount: 1,
      logcount: 1,
      logpage: 1,
      logpagesize: 100,
      moneylogFormVisible: false,
      logData: [],
      tablelogloading: false,
      logdia: {},
      thankfulVisible: false,
      thankful: {
        user_id: "",
        money: "",
        send_money: ""
      }
    };
  },
  methods: {
    getMember() {
      let params = {};
      params.page = this.page;
      params.limit = this.pagesize;
      if (this.user_name) {
        params.user_name = this.user_name;
      }
      if (this.is_vip != "-1") {
        params.is_vip = this.is_vip;
      }
      this.tableloading = true;
      memberLists(params).then(res => {
        if (res.data.code == 200) {
          this.tableloading = false;
          this.memberData = res.data.data.list;
          this.pagecount = res.data.data.pagecount;
          this.count = res.data.data.count;
        }
      });
    },
    handleCurrentChange(val) {
      this.page = val;
      this.getMember();
    },
    handleSizeChange(val) {
      this.pagesize = val;
      this.getMember();
    },
    handlelogCurrentChange(val) {
      this.logpage = val;
      this.getlogData(this.logdia);
    },
    handlelogSizeChange(val) {
      this.logpagesize = val;
      this.getlogData(this.logdia);
    },
    handlemoneyEdit(params) {
      this.moneyFormVisible = true;
      this.moneyForm.user_id = params.user_id;
    },
    editmoneySubmit() {
      var that = this;
      this.$confirm("确认提交吗？", "提示", {}).then(() => {
        sendpoint(this.moneyForm).then(res => {
          if (res.code == 200) {
            that.$message({
              message: res.msg,
              type: "success"
            });
            that.moneyFormVisible = false;
            that.getMember();
          } else {
            that.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    },
    handlemoneyInfo(params) {
      this.moneylogFormVisible = true;
      this.logData = [];
      this.logdia = params;
      this.getlogData(this.logdia);
    },
    handlethankfulEdit(params) {
      this.thankful = {
        user_id: "",
        money: "",
        send_money: ""
      };
      this.thankful.user_id = params.user_id;
      thankfulInfo(params).then(res => {
        if (res.code == 200) {
          this.thankfulVisible = true;
          if (res.data.info) {
            this.thankful.money = res.data.info.money;
            this.thankful.send_money = res.data.info.send_money;
          }
        }
      });
    },
    editthankfulSubmit() {
      var that = this;
      this.$confirm("确认提交吗？", "提示", {}).then(() => {
        thankfuledit(this.thankful).then(res => {
          if (res.code == 200) {
            that.$message({
              message: res.msg,
              type: "success"
            });
            that.thankfulVisible = false;
          } else {
            that.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    },
    getlogData(params) {
      consumeList({
        user_id: params.user_id,
        limit: this.logpagesize,
        page: this.logpage
      }).then(res => {
        if (res.code == 200) {
          this.tablelogloading = false;
          this.logData = res.data.list;
        }
      });
    }
  },
  mounted() {
    this.getMember();
  },
  watch: {
    moneyFormVisible(curVal, oldVal) {
      if (!curVal) {
        this.$refs.moneyForm.resetFields();
      }
    },
    thankfulVisible(curVal, oldVal) {
      if (!curVal) {
        this.$refs.thankful.resetFields();
      }
    }
  }
};
</script>
