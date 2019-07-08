<template>
  <div>
    <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item>
          <el-button type="primary" @click="handleAdd">新增</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 列表 -->
    <el-table :data="tableData" border style="width: 100%" v-loading="tableloading" element-loading-text="拼命加载中">
      <!-- <el-table-column prop="map_store_id" label="门店ID">
      </el-table-column> -->
      <el-table-column prop="name" label="会员等级名称">
      </el-table-column>
      <el-table-column prop="level" label="会员等级">
      </el-table-column>
      <el-table-column prop="discount" label="会员折扣">
      </el-table-column>
      <el-table-column prop="first_reback_rate" label="向上级返佣比例">
      </el-table-column>
      <el-table-column prop="second_reback_rate" label="向上上级返佣比例">
      </el-table-column>
      <el-table-column prop="third_reback_rate" label="向上上上级返佣比例">
      </el-table-column>
      <el-table-column prop="capping_rate" label="业绩奖比例">
      </el-table-column>
      <el-table-column prop="capping_price" label="业绩封顶金额">
      </el-table-column>
      <el-table-column prop="achievement_award" label="终身成就奖/月">
      </el-table-column>
      <el-table-column prop="recommend_award" label="推荐同级奖励">
      </el-table-column>
      <el-table-column label="操作" width="220">
        <template slot-scope="scope">
          <el-button @click="handleEdit(scope.row)">编辑</el-button>
          <!-- <el-button type="danger" @click="handleDel(scope.row)">删除</el-button> -->
        </template>
      </el-table-column>
    </el-table>
    <!-- 分页 -->
    <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination @current-change="handleCurrentChange" layout="prev, pager, next, jumper" :page-count="pagecount">
      </el-pagination>
    </div>
    <!-- 新增 -->
    <el-dialog title="新增" :visible.sync="addFormVisible">
      <el-form :model="addForm" label-width="150px" ref="addForm">
        <el-form-item label="会员等级名称" prop="name">
          <el-input type="text" v-model="addForm.name"></el-input>
        </el-form-item>
        <el-form-item label="会员等级" prop="level">
          <el-input type="text" v-model="addForm.level"></el-input>
        </el-form-item>
        <el-form-item label="会员折扣" prop="discount">
          <el-input type="text" v-model="addForm.discount"></el-input>
        </el-form-item>
        <el-form-item label="向上级返佣比例" prop="first_reback_rate">
          <el-input type="text" v-model="addForm.first_reback_rate"></el-input>
        </el-form-item>
        <el-form-item label="向上上级返佣比例" prop="second_reback_rate">
          <el-input type="text" v-model="addForm.second_reback_rate"></el-input>
        </el-form-item>
        <el-form-item label="向上上上级返佣比例" prop="third_reback_rate">
          <el-input type="text" v-model="addForm.third_reback_rate"></el-input>
        </el-form-item>
        <el-form-item label="业绩奖比例" prop="capping_rate">
          <el-input type="text" v-model="addForm.capping_rate"></el-input>
        </el-form-item>
        <el-form-item label="业绩封顶金额" prop="capping_price">
          <el-input type="text" v-model="addForm.capping_price"></el-input>
        </el-form-item>
        <el-form-item label="终身成就奖/月" prop="achievement_award">
          <el-input type="text" v-model="addForm.achievement_award"></el-input>
        </el-form-item>
        <el-form-item label="推荐同级奖励" prop="recommend_award">
          <el-input type="text" v-model="addForm.recommend_award"></el-input>
        </el-form-item>
        </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="addFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="addSubmit">提交</el-button>
      </div>
    </el-dialog>
    <!--编辑界面-->
    <el-dialog title="编辑" :visible.sync="editFormVisible">
      <el-form :model="editForm" label-width="150px" ref="editForm">
        <el-form-item label="会员等级名称" prop="name">
          <el-input type="text" v-model="editForm.name"></el-input>
        </el-form-item>
        <el-form-item label="会员等级" prop="level">
          <el-input type="text" v-model="editForm.level"></el-input>
        </el-form-item>
        <el-form-item label="会员折扣" prop="discount">
          <el-input type="text" v-model="editForm.discount"></el-input>
        </el-form-item>
        <el-form-item label="向上级返佣比例" prop="first_reback_rate">
          <el-input type="text" v-model="editForm.first_reback_rate"></el-input>
        </el-form-item>
        <el-form-item label="向上上级返佣比例" prop="second_reback_rate">
          <el-input type="text" v-model="editForm.second_reback_rate"></el-input>
        </el-form-item>
        <el-form-item label="向上上上级返佣比例" prop="third_reback_rate">
          <el-input type="text" v-model="editForm.third_reback_rate"></el-input>
        </el-form-item>
        <el-form-item label="业绩奖比例" prop="capping_rate">
          <el-input type="text" v-model="editForm.capping_rate"></el-input>
        </el-form-item>
        <el-form-item label="业绩封顶金额" prop="capping_price">
          <el-input type="text" v-model="editForm.capping_price"></el-input>
        </el-form-item>
        <el-form-item label="终身成就奖/月" prop="achievement_award">
          <el-input type="text" v-model="editForm.achievement_award"></el-input>
        </el-form-item>
        <el-form-item label="推荐同级奖励" prop="recommend_award">
          <el-input type="text" v-model="editForm.recommend_award"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="editFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="editSubmit">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { vipLists } from "../../api/api";
import { vipadd } from "../../api/api";
import { vipedit } from "../../api/api";
// import { removeIcon } from "../../api/api";
export default {
  data() {
    return {
      tableData: [],
      page: 1,
      pagecount: 1,
      tableloading: true,
      addFormVisible: false,
      addForm: {
        name: "",
        level: "",
        discount: "",
        first_reback_rate: "",
        second_reback_rate: "",
        third_reback_rate: "",
        capping_rate:'',
        capping_price: "",
        achievement_award:'',
        recommend_award:''
      },
      editForm: {
        name: "",
        level: "",
        discount: "",
        first_reback_rate: "",
        second_reback_rate: "",
        third_reback_rate: "",
        capping_rate:'',
        capping_price: "",
        achievement_award:'',
        recommend_award:''
      },
      editFormVisible: false
    };
  },
  methods: {
    getData() {
      //获取数据
      this.tableloading = true;
      vipLists({
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
    handleAdd() {
      this.addFormVisible = true;
    },
    addSubmit() {
      var that = this;
      this.$refs.addForm.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            vipadd(this.addForm).then(res => {
              if (res.code == 200) {
                that.$message({
                  message: res.msg,
                  type: "success"
                });
                that.addFormVisible = false;
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
      });
    },
    handleEdit(params) {
      this.editForm = params;
      this.editFormVisible = true;
    },
    editSubmit() {
      var that = this;
      this.$confirm("确认提交吗？", "提示", {}).then(() => {
        vipedit(this.editForm).then(res => {
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
    },
    // handleDel(params) {
    //   var that = this;
    //   this.$confirm("确认提交吗？", "提示", {}).then(() => {
    //     removeIcon({
    //       id: params.id
    //     }).then(res => {
    //       if (res.data.code == 200) {
    //         that.$message({
    //           message: res.data.msg,
    //           type: "success"
    //         });
    //         that.getData();
    //       } else {
    //         that.$message({
    //           message: res.data.msg,
    //           type: "warning"
    //         });
    //       }
    //     });
    //   });
    // }
  },
  mounted() {
    this.getData();
  },
  watch: {
    addFormVisible(curVal, oldVal) {
      if (!curVal) {
        this.$refs.addForm.resetFields();
      }
    },
    editFormVisible(curVal, oldVal) {
      if (!curVal) {
        this.$refs.editForm.resetFields();
      }
    }
  }
};
</script>
