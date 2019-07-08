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
      <el-table-column prop="name" label="名称">
      </el-table-column>
      <el-table-column prop="type" label="状态/比例">
           <template slot-scope="scope">
               <span v-if="scope.row.id==1">{{scope.row.value==1?'启用':'关闭'}}</span> 
               <span v-else>{{scope.row.value}}</span> 
            </template>
      </el-table-column>
      <el-table-column prop="updated_time" label="更新时间">
      </el-table-column>
      <el-table-column label="操作" width="220">
        <template slot-scope="scope">
          <el-button  @click="handleEdit(scope.row)" type="primary">编辑</el-button>
        </template>
      </el-table-column>
    </el-table>
        <!--编辑界面-->
    <el-dialog title="编辑" :visible.sync="editFormVisible">
      <el-form :model="editForm" label-width="150px" ref="editForm">
        <el-form-item label="启用" prop="value" v-if="editForm.id==1">
          <el-radio-group v-model="editForm.value">
            <el-radio :label="'1'">是</el-radio>
            <el-radio :label="'0'">否</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="比例" prop="value" v-else>
          <el-input type="text" v-model="editForm.value"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="editFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="editSubmit">提交</el-button>
      </div>
    </el-dialog>
    <!-- 分页 -->
    <!-- <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination @current-change="handleCurrentChange" layout="prev, pager, next, jumper" :page-count="pagecount">
      </el-pagination>
    </div> -->
  </div>
</template>
<script>
import { systemLists } from "../../api/api";
import { systemput } from "../../api/api";
export default {
  data() {
    return {
      tableData: [],
      page: 1,
      pagecount: 1,
      tableloading: true,
      editForm: {},
      editFormVisible: false
    };
  },
  methods: {
    getData() {
      //获取数据
      this.tableloading = true;
      systemLists({
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
    handleEdit(params) {
      this.editFormVisible = true;
      this.editForm = params;
    },
    editSubmit(params) {
      var that = this;
      this.$confirm("确认切换吗？", "提示", {}).then(() => {
        systemput(this.editForm).then(res => {
          if (res.code == 200) {
            that.$message({
              message: res.msg,
              type: "success"
            });
            that.editFormVisible = false;
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
  },
  watch: {
    editFormVisible(curVal, oldVal) {
      if (!curVal) {
        this.getData();
      }
    }
  }
};
</script>