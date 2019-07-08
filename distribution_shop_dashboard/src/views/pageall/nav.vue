<template>
  <div>
    <el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
      <el-form :inline="true">
        <el-form-item>
          <el-button type="primary" @click="handleAdd">新增</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- nav列表 -->
    <el-table :data="tableData" style="width: 100%;" v-loading="tableloading">
      <el-table-column prop="n_name" label="导航名称" width="120">
      </el-table-column>
      <el-table-column prop="redirect_type" label="跳转类型" width="150">
        <template slot-scope="scope">
          <p v-if="scope.row.redirect_type==1">商品详情</p>
          <p v-if="scope.row.redirect_type==2">分类详情</p>
          <p v-if="scope.row.redirect_type==3">活动详情</p>
        </template>
      </el-table-column>
      <el-table-column prop="redirect_id" label="跳转地址">
      </el-table-column>
      <el-table-column prop="redirect_name" label="跳转名称" width="100">
      </el-table-column>
      <el-table-column prop="sort" label="排序" width="120" sortable>
      </el-table-column>
      <el-table-column prop="enabled" label="启用" min-width="130">
        <template slot-scope="scope">
          <p v-if="scope.row.enabled==1">是</p>
          <p v-if="scope.row.enabled==0">否</p>
        </template>
      </el-table-column>
      <!-- <el-table-column prop="first" label="默认导航" min-width="130">
        <template slot-scope="scope">
          <p v-if="scope.row.first==1">是</p>
          <p v-if="scope.row.first==0">否</p>
        </template>
      </el-table-column> -->
      <el-table-column label="操作" min-width="150">
        <template slot-scope="scope">
          <el-button @click="handleEdit(scope.row)">编辑</el-button>
          <el-button type="danger" @click="handleDel(scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <!-- 新增界面  -->
    <el-dialog title="新增" :visible.sync="addFormVisible">
      <el-form :model="addForm" label-width="200px" :rules="Rules" ref="addForm">
        <el-form-item label="导航名称" prop="n_name">
          <el-input v-model="addForm.n_name"></el-input>
        </el-form-item>
        <el-form-item label="跳转类型" prop="redirect_type">
          <el-select v-model="addForm.redirect_type" placeholder="请选择">
            <el-option v-for="item in selecteOptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item :label="addForm.redirect_type==1?'跳转商品':addForm.redirect_type==2?'跳转分类':'跳转地址'" prop="redirect_id">
          <el-input v-model="addForm.redirect_id" @blur="validRedirect(addForm.redirect_type,addForm.redirect_id,0)" :placeholder="addForm.redirect_type==1?'跳转商品ID':addForm.redirect_type==2?'跳转分类ID':'跳转地址'"></el-input>
        </el-form-item>
        <el-form-item label="跳转名称" prop="redirect_name">
          <el-input v-model="addForm.redirect_name" disabled></el-input>
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input v-model="addForm.sort"></el-input>
        </el-form-item>
        <el-form-item label="启用" prop="enabled">
          <el-radio-group v-model="addForm.enabled">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
          </el-radio-group>
        </el-form-item>
        <!-- <el-form-item label="默认导航" prop="first">
          <el-radio-group v-model="addForm.first">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
          </el-radio-group>
        </el-form-item> -->
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="addFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="addSubmit">提交</el-button>
      </div>
    </el-dialog>
    <!-- 编辑界面 -->
    <el-dialog title="编辑" :visible.sync="editFormVisible">
      <el-form :model="editForm" label-width="200px" :rules="Rules" ref="editForm">
        <el-form-item label="导航名称" prop="n_name">
          <el-input v-model="editForm.n_name"></el-input>
        </el-form-item>
        <el-form-item label="跳转类型" prop="redirect_type">
          <el-select v-model="editForm.redirect_type" placeholder="请选择">
            <el-option v-for="item in selecteOptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item :label="editForm.redirect_type==1?'跳转商品':editForm.redirect_type==2?'跳转分类':'跳转地址'"  prop="redirect_id">
          <el-input v-model="editForm.redirect_id" @blur="validRedirect(editForm.redirect_type,editForm.redirect_id,1)" :placeholder="editForm.redirect_type==1?'跳转商品ID':editForm.redirect_type==2?'跳转分类ID':'跳转地址'"></el-input>
        </el-form-item>
        <el-form-item label="跳转名称" prop="redirect_name">
          <el-input v-model="editForm.redirect_name" disabled></el-input>
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input v-model="editForm.sort"></el-input>
        </el-form-item>
        <el-form-item label="启用" prop="enabled">
          <el-radio-group v-model="editForm.enabled">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
          </el-radio-group>
        </el-form-item>
        <!-- <el-form-item label="默认导航" prop="first">
          <el-radio-group v-model="editForm.first">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
          </el-radio-group>
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
import { idSearchnew } from "../../api/api";
import { navLists } from "../../api/api";
import { removeNav } from "../../api/api";
import { addNav } from "../../api/api";
import { editNav } from "../../api/api";
export default {
  data() {
    return {
      tableloading: true,
      tableData: [],
      addFormVisible: false, //新增界面是否显示
      editFormVisible: false,
      Rules: {
        redirect_id: [
          {
            required: true,
            message: "请输入跳转",
            trigger: "blur"
          }
        ],
        redirect_name: [
          {
            required: true,
            message: "跳转ID错误",
            trigger: "blur"
          }
        ],
        n_name: [
          {
            required: true,
            message: "输入导航名称",
            trigger: "blur"
          }
        ],
        sort: [
          {
            required: true,
            message: "排序",
            trigger: "blur"
          }
        ],
        enabled: [
          {
            required: true,
            message: "启用",
            trigger: "change"
          }
        ],
        redirect_type: [
          {
            required: true,
            message: "跳转类型",
            trigger: "change"
          }
        ],
        // first: [
        //   {
        //     required: true,
        //     message: "默认导航",
        //     trigger: "change"
        //   }
        // ]
      },
      addForm: {
        n_name: "",
        redirect_type: 1,
        redirect_id: "",
        redirect_name: "",
        sort: 1,
        enabled: 1,
        // first: 0
      },
      editForm: {},
      selecteOptions: [
        {
          value: 1,
          label: "商品详情"
        },
        {
          value: 2,
          label: "分类详情"
        },
        {
          value: 3,
          label: "活动详情"
        }
      ]
    };
  },
  methods: {
    validRedirect(redirect_type, redirect_id, type) {
      if (redirect_type == 1 || redirect_type == 2) {
        idSearchnew({
          type: redirect_type,
          keywords: redirect_id
        }).then(res => {
          if (res.code == 200) {
            if (type) {
              this.editForm.redirect_name = res.data.name;
            } else {
              this.addForm.redirect_name = res.data.name;
            }
          } else {
            if (type) {
              this.editForm.redirect_name = "";
            } else {
              this.addForm.redirect_name = "";
            }
          }
        });
      } else if (redirect_type == 3) {
        if (type) {
          this.editForm.redirect_name = "活动";
        } else {
          this.addForm.redirect_name = "活动";
        }
      } 
    },
    getnavLists() {
      this.tableloading = true;
      navLists().then(res => {
        if (res.code == 200) {
          this.tableData = res.data.item;
          this.tableloading = false;
        }
      });
    },
    handleAdd() {
      this.addFormVisible = true;
    },
    addSubmit() {
      this.$refs.addForm.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            addNav(this.addForm).then(res => {
              if (res.code == 200) {
                this.$message({
                  message: res.msg,
                  type: "success"
                });
                this.$refs["addForm"].resetFields();
                this.addFormVisible = false;
                this.getnavLists();
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
    handleEdit(params) {
      this.editFormVisible = true;
      this.editForm = params;
    },
    editSubmit() {
      var that = this;
      this.$refs.editForm.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            editNav(this.editForm).then(res => {
              if (res.code == 200) {
                that.$message({
                  message: res.msg,
                  type: "success"
                });
                that.editFormVisible = false;
                this.$refs["editForm"].resetFields();
                this.getnavLists();
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
    handleDel(params) {
      this.$confirm("确认删除该记录吗?", "提示", {
        type: "warning"
      }).then(() => {
        removeNav({
          id: params.n_id
        }).then(res => {
          if (res.code == 200) {
            this.$message({
              message: res.msg,
              type: "success"
            });
            this.getnavLists();
          } else {
            this.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    }
  },
  mounted() {
    this.getnavLists();
  }
};
</script>
