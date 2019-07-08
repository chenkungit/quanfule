<template>
  <div>
    <el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
      <el-form :inline="true">
        <el-form-item>
          <div style="float:left;margin-right:10px">
            <el-button type="primary" @click="handleAdd">新增</el-button>
          </div>
          <div style="float:left;margin-right:10px">
            <el-input v-model="keywords" @keyup.enter.native="apsearch()"></el-input>
          </div>
          <div style="float:left;margin-right:10px">
            <el-button type="primary" @click="apsearch()">搜索</el-button>
          </div>
        </el-form-item>
      </el-form>
    </el-col>

    <!-- 管理员列表 -->
    <el-table :data="privilegedata" style="width: 100%" v-loading="tableloading">
      <el-table-column prop="user_name" label="管理员名称" width="180" fixed>
      </el-table-column>
      <!-- <el-table-column prop="email" label="管理员邮箱" width="250">
      </el-table-column> -->
      <el-table-column prop="add_time" label="加入时间">
      </el-table-column>
      <el-table-column prop="last_login" label="最后登录时间">
      </el-table-column>
      <el-table-column fixed="right" label="操作" width="300">
        <template slot-scope="scope">
          <el-button @click="getInfo(scope.row)"  v-if="scope.row.can_modify ==1">权限</el-button>
          <el-button @click="handleEdit(scope.row)" type="primary" v-if="scope.row.can_modify ==1">修改密码</el-button>
        </template>
      </el-table-column>
    </el-table>
    <!-- 新增管理员 -->
    <el-dialog title="新增" :visible.sync="addFormVisible">
      <el-form :model="addForm" ref="addForm" label-width="150px">
        <el-form-item label="管理员名称" prop="user_name">
          <el-input v-model="addForm.user_name"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input type="password" v-model="addForm.password"></el-input>
        </el-form-item>
        <el-form-item label="确认密码" prop="repassword">
          <el-input type="password" v-model="addForm.repassword"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="addFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="addsubmit">确 定</el-button>
      </div>
    </el-dialog>
        <!-- 修改 -->
    <el-dialog title="修改密码" :visible.sync="editFormVisible">
      <el-form :model="editForm" ref="editForm" label-width="150px">
        <!-- <el-form-item label="管理员名称" prop="user_name">
          <el-input v-model="editForm.user_name"></el-input>
        </el-form-item> -->
        <el-form-item label="密码" prop="password">
          <el-input type="password" v-model="editForm.password"></el-input>
        </el-form-item>
        <el-form-item label="确认密码" prop="repassword">
          <el-input type="password" v-model="editForm.repassword"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="editFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="editsubmit">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="权限管理" :visible.sync="qxVisible" height="800px">
      <div></div>
      <div v-for="item in formLabeldata" style="margin: 15px 0;overflow:hidden">
        <div style="margin: 15px 0 0 5px;float:left">
          <label>
            <input type="checkbox" :checked="item.connect==1" @change="handleCheckAllChange(item)" :value="item" style="zoom:140%;float:left">
            <h4 style="display:inline-block;float:left;margin:0">{{item.title}}</h4>
          </label>
        </div>
        <br/>
        <div style="margin: 15px 0 0 5px;float:left;width:100%">
          <label v-for="itemson in item.child" style="margin-right:15px;">
            <input type="checkbox" :checked="itemson.connect==1" @change="checkChange(item,itemson)" :value="itemson" style="zoom:120%;">{{itemson.title}}</label>
        </div>
      </div>
      <div slot="footer" class="dialog-footer">
        <el-button @click="qxVisible = false">取 消</el-button>
        <el-button type="primary" @click="privilsubmit">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { privilegeLists } from "../../api/api";
import { addPivilege } from "../../api/api";
import { allotPrivilege } from "../../api/api";
import { allotPrivilegeLists } from "../../api/api";
import { resetPwd } from "../../api/api";
export default {
  data() {
    return {
      isIndeterminate: true,
      checkAll: true,
      checkedprivil: [],
      activeName: "first",
      privilegedata: [],
      tableloading: true,
      addFormVisible: false,
      keywords: "",
      addForm: {
        user_name: "",
        password: "",
        repassword: ""
      },
      editFormVisible: false,
      keywords: "",
      editForm: {
        user_id:'',
        user_name: "",
        password: "",
        repassword: ""
      },
      formLabeldata: [],
      editQX: {
        id: ""
      },
      qxVisible: false
    };
  },
  methods: {
    getPrivigeLists() {
      privilegeLists().then(res => {
        if (res.data.code == 200) {
          this.privilegedata = [];
          this.tableloading = false;
          for (var i in res.data.data.item) {
            if (
              res.data.data.item[i].user_name.indexOf(this.keywords) >= 0 || this.keywords == ""
            ) {
              this.privilegedata.push(res.data.data.item[i]);
            }
          }
        }
      });
    },
    apsearch() {
      this.getPrivigeLists();
    },
    handleAdd() {
      this.addFormVisible = true;
    },
    handleEdit(params){
      this.editFormVisible = true;
      this.editForm.user_id=params.user_id
    },
    addsubmit() {
      let params = {
        user_name: this.addForm.user_name,
        password: this.addForm.password,
        repassword: this.addForm.repassword
      };
      addPivilege(params).then(res => {
        if (res.code == 200) {
          this.$message({
            message: res.msg,
            type: "success"
          });
          this.getPrivigeLists();
          this.addFormVisible = false;
        } else {
          this.$message({
            message: res.msg,
            type: "warning"
          });
        }
      });
    },
    editsubmit() {
      resetPwd(this.editForm).then(res => {
        if (res.code == 200) {
          this.$message({
            message: res.msg,
            type: "success"
          });
          this.getPrivigeLists();
          this.editFormVisible = false;
        } else {
          this.$message({
            message: res.msg,
            type: "warning"
          });
        }
      });
    },
    getInfo(params) {
      allotPrivilegeLists({
        user_id: params.user_id
      }).then(res => {
        if (res.data.code == 200) {
          this.formLabeldata = res.data.data;
          for (var i in this.formLabeldata) {
            for (var j in this.formLabeldata[i].child)
              if (this.formLabeldata[i].child[j].connect) {
                this.checkedprivil.push(this.formLabeldata[i].child[j]);
              }
          }
          this.qxVisible = true;
          this.editQX.id = params.user_id;
        }
      });
    },
    handleCheckAllChange(item) {
      for (var i in item.child) {
        if (event.target.checked) {
          item.connect = 1;
          item.child[i].connect = 1;
        } else {
          item.connect = 0;
          item.child[i].connect = 0;
        }
      }
    },
    checkChange(item, itemson) {
      if (event.target.checked) {
        itemson.connect = 1;
      } else {
        itemson.connect = 0;
      }
      for (var i in item.child) {
        if (item.child[i].connect == 0) {
          item.connect = 0;
          return false;
        } else {
          item.connect = 1;
        }
      }
    },
    privilsubmit() {
      var arr = [];
      for (var i in this.formLabeldata) {
        for (var j in this.formLabeldata[i].child) {
          if (this.formLabeldata[i].child[j].connect == 1) {
            arr.push(this.formLabeldata[i].child[j].rule_id);
            arr.push(this.formLabeldata[i].rule_id);
          }
        }
      }

      function dedupe(arr) {
        return Array.from(new Set(arr));
      }
      arr = dedupe(arr);
      allotPrivilege({
        node_list: arr.toString(),
        user_id: this.editQX.id
      }).then(res => {
        if (res.code == 200) {
          this.$message({
            message: res.msg,
            type: "success"
          });
          this.getPrivigeLists();
          this.qxVisible = false;
        } else {
          this.$message({
            message: res.msg,
            type: "warning"
          });
        }
      });
    },
  },

  mounted() {
    this.getPrivigeLists();
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
