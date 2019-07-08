<template>
  <div>
    <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item>
          <el-button type="primary" icon="el-icon-plus" @click="handleAdd">新增</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 列表 -->
    <el-table :data="goodstypeData" border style="width: 100%" v-loading="tableloading" element-loading-text="拼命加载中">
      <el-table-column prop="cat_name" label="商品类型名称">
      </el-table-column>
      <!-- <el-table-column prop="attr_group" label="属性分组">
      </el-table-column> -->
      <el-table-column prop="attr_count" label="属性数">
      </el-table-column>
      <!-- <el-table-column prop="enabledname" label="状态">
      </el-table-column> -->
      <el-table-column label="操作" width="280">
        <template slot-scope="scope">
          <el-button icon="el-icon-view" @click="lookattr(scope.row)">查看</el-button>
          <el-button icon="el-icon-edit" @click="handleEdit(scope.row)">编辑</el-button>
          <el-button icon="el-icon-delete" type="danger" @click="handleDel(scope.row)">删除</el-button>
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
        <el-form-item label="商品类型名称" prop="cat_name">
          <el-input type="text" v-model="addForm.cat_name"></el-input>
        </el-form-item>
        <!-- <el-form-item label="属性分组" prop="attr_group">
          <el-input type="textarea" :rows="2" v-model="addForm.attr_group"></el-input>
        </el-form-item> -->
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="addFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="addSubmit">提交</el-button>
      </div>

    </el-dialog>
    <!--编辑界面-->
    <el-dialog title="编辑" :visible.sync="editFormVisible">
      <el-form :model="editForm" label-width="150px" ref="editForm">
        <el-form-item label="商品类型名称" prop="cat_name">
          <el-input type="text" v-model="editForm.cat_name"></el-input>
        </el-form-item>
        <!-- <el-form-item label="属性分组" prop="attr_group">
          <el-input type="textarea" :rows="2" v-model="editForm.attr_group"></el-input>
        </el-form-item> -->
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="editFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="editSubmit">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { goodstypeLists } from "../../api/api";
import { addGoodstype } from "../../api/api";
import { editGoodstype } from "../../api/api";
import { removeGoodstype } from "../../api/api";
export default {
  data() {
    return {
      goodstypeData: [],
      page: 1,
      pagecount: 1,
      tableloading: true,
      addFormVisible: false,
      addForm: {
        cat_name: "",
        attr_group: ""
      },
      editForm: {
        cat_name: "",
        attr_group: ""
      },
      editFormVisible: false
    };
  },
  methods: {
    getgoodstype() {
      //获取数据
      this.tableloading = true;
      goodstypeLists({
        page: this.page,
        type: this.type
      }).then(res => {
        if (res.data.code == 200) {
          this.pagecount = res.data.data.pagecount;
          this.goodstypeData = res.data.data.item;
          for (var i in res.data.data.item) {
            if (res.data.data.item[i].enabled == 1) {
              res.data.data.item[i].enabledname = "√";
            } else {
              res.data.data.item[i].enabledname = "×";
            }
          }
          this.tableloading = false;
        }
      });
    },
    getGusp(params) {
      goodsInfo({
        gshp_id: params
      }).then(res => {
        this.addForm.gsup_name = res.data.data.name;
        this.addForm.gsup_id = res.data.data.gsup_id;
        this.editForm.gsup_name = res.data.data.name;
        this.editForm.gsup_id = res.data.data.gsup_id;
      });
    },
    handleCurrentChange(val) {
      this.page = val;
      this.getgoodstype();
    },
    handleAdd() {
      this.addFormVisible = true;
    },
    addSubmit() {
      var that = this;
      this.$refs.addForm.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            let par = {
              cat_name: this.addForm.cat_name,
              attr_group: this.addForm.attr_group
            };
            addGoodstype(par).then(res => {
              if (res.code == 200) {
                that.$message({
                  message: res.msg,
                  type: "success"
                });
                that.addFormVisible = false;
                that.getgoodstype();
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
        let params = {
          cat_id: this.editForm.cat_id,
          cat_name: this.editForm.cat_name,
          attr_group: this.editForm.attr_group
        };
        editGoodstype(params).then(res => {
          if (res.code == 200) {
            that.$message({
              message: res.msg,
              type: "success"
            });
            that.editFormVisible = false;
            that.getgoodstype();
          } else {
            that.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    },
    handleDel(params) {
      var that = this;
      this.$confirm("确认提交吗？", "提示", {}).then(() => {
        removeGoodstype({
          cat_id: params.cat_id
        }).then(res => {
          if (res.data.code == 200) {
            that.$message({
              message: res.data.msg,
              type: "success"
            });
            that.getgoodstype();
          } else {
            that.$message({
              message: res.data.msg,
              type: "warning"
            });
          }
        });
      });
    },
    lookattr(params) {
      let id = params.cat_id;
      this.$router.push({
        name: "商品属性",
        params: {
          id: id
        }
      });
    }
  },
  mounted() {
    this.getgoodstype();
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
