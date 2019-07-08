<template>
  <div>
    <!--工具条-->
    <el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
      <el-form :inline="true">
        <el-form-item>
          <el-button type="primary" @click="handleAdd">新增</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 轮播图列表 -->
    <el-table :data="serchLists" border style="width: 100%" v-loading="tableloading">
      <el-table-column prop="s_name" label="热搜名称" width="100">
      </el-table-column>
      <el-table-column prop="s_level" label="热搜等级" width="100">
      </el-table-column>
      <el-table-column prop="sort" label="排序" width="100" sortable>
      </el-table-column>
      <el-table-column prop="enabled" label="启用" width="80">
      </el-table-column>
      <el-table-column label="操作">
        <template slot-scope="scope">
          <el-button @click="handleEdit(scope.row)">编辑</el-button>
          <el-button type="danger" @click="handleDel(scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <!--新增界面-->
    <el-dialog title="新增" :visible.sync="addFormVisible">
      <el-form :model="addForm" label-width="200px" ref="addForm">
        <el-form-item label="热搜名称">
          <el-input v-model="addForm.s_name"></el-input>
        </el-form-item>
        <el-form-item label="热搜等级">
          <el-input v-model="addForm.s_level"></el-input>
        </el-form-item>
        <el-form-item label="排序">
          <el-input v-model="addForm.sort"></el-input>
        </el-form-item>
        <el-form-item label="启用">
          <el-switch v-model="addForm.enabled" on-color="#13ce66" off-color="#ff4949">
          </el-switch>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="addFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="addSubmit">提交</el-button>
      </div>
    </el-dialog>
    <!--编辑界面-->
    <el-dialog title="编辑" :visible.sync="editFormVisible">
      <el-form :model="editForm" label-width="200px" ref="editForm">
        <el-form-item label="热搜名称">
          <el-input v-model="editForm.s_name"></el-input>
        </el-form-item>
        <el-form-item label="热搜等级">
          <el-input v-model="editForm.s_level"></el-input>
        </el-form-item>
        <el-form-item label="排序">
          <el-input v-model="editForm.sort"></el-input>
        </el-form-item>
        <el-form-item label="启用">
          <el-switch v-model="editForm.enabled" on-color="#13ce66" off-color="#ff4949">
          </el-switch>
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
import store from "../../../vuex/store";
import { hotserchLists } from "../../../api/api";
import { addhotserch } from "../../../api/api";
import { edithotserch } from "../../../api/api";
import { removehotserchLists } from "../../../api/api";
import { mapGetters } from "vuex";
import { mapActions } from "vuex";
// const format = require('date-fns/format');
import format from "date-fns/format";
export default {
  data() {
    return {
      tableloading: true,
      serchLists: [], //列表获取的数据
      //新增界面
      addFormVisible: false, //新增界面是否显示
      addFormRules: {
        name: [
          {
            required: true,
            message: "请输入",
            trigger: "blur"
          }
        ]
      }, //新增界面数据
      addForm: {
        s_name: "",
        s_level: "",
        sort: "",
        enabled: false
      },
      editFormVisible: false, //编辑
      editForm: {
        s_name: "",
        s_level: "",
        sort: "",
        enabled: ""
      },
      addselecteOptions: [
        {
          value: 1,
          label: "商城"
        },
        {
          value: 3,
          label: "门店"
        }
      ],
      editselecteOptions: [
        {
          value: 1,
          label: "商城"
        },
        {
          value: 3,
          label: "门店"
        }
      ]
    };
  },
  computed: {
    ...mapGetters(["getToken"])
  },
  methods: {
    getserchLists() {
      this.tableloading = true;
      let newdata = [];
      // let a=JSON.parse(sessionStorage.getItem('data'));
      const param = {
        // position:2,
        // accessToken:a.data.accessToken
      };
      hotserchLists(param).then(res => {
        if (res.data.code == 200) {
          for (var i in res.data.data.item) {
            // console.log(res.data.data.item)
            // if(res.data.data.item[i].carousel_position==2){
            newdata.push(res.data.data.item[i]);
            // this.carouselLists=newdata
            // }
          }
          for (var i in newdata) {
            if (newdata[i].enabled == 1) {
              newdata[i].enabled = "是";
            } else {
              newdata[i].enabled = "否";
            }
          }
          this.serchLists = newdata;
          this.tableloading = false;
        }
      });
    },
    handleAdd() {
      this.addFormVisible = true;
    },
    addSubmit() {
      //新建提交
      var that = this;
      this.$refs.addForm.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            //NProgress.start();
            var formData = new FormData();
            if (this.addForm.enabled == true) {
              this.addForm.enabled = 1;
            } else {
              this.addForm.enabled = 0;
            }
            formData.append("enabled", this.addForm.enabled);
            // formData.append('accessToken',this.getToken.accessToken)
            formData.append("s_name", this.addForm.s_name);
            formData.append("s_type", 1);
            formData.append("s_level", this.addForm.s_level);
            formData.append("sort", this.addForm.sort);

            addhotserch(formData).then(res => {
              if (res.data.code == 200) {
                //NProgress.done();
                that.$message({
                  message: res.data.msg,
                  type: "success"
                });
                that.$refs["addForm"].resetFields();
                that.addFormVisible = false;
                that.getserchLists();
              } else {
                that.$message({
                  message: res.data.msg,
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
      if (params.enabled == "是") {
        params.enabled = true;
      } else {
        params.enabled = false;
      }
      this.editForm = params;
    },
    editSubmit() {
      this.$refs.editForm.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            //NProgress.start();
            var that = this;
            var formData = new FormData();
            if (this.editForm.enabled == true) {
              this.editForm.enabled = 1;
            } else {
              this.editForm.enabled = 0;
            }
            formData.append("enabled", this.editForm.enabled);
            formData.append("id", this.editForm.s_id);
            formData.append("s_name", this.editForm.s_name);
            formData.append("s_type", 1);
            formData.append("s_level", this.editForm.s_level);
            formData.append("sort", this.editForm.sort);
            edithotserch(formData).then(res => {
              if (res.data.code == 200) {
                that.editFormVisible = false;
                //NProgress.done();
                that.$message({
                  message: res.data.msg,
                  type: "success"
                });
                // that.getCarouselLists();
              } else {
                that.$message({
                  message: res.data.msg,
                  type: "warning"
                });
              }
            });
          });
        }
      });
    },
    handleDel(params) {
      // console.log(params.carousel_id)
      this.$confirm("确认删除该记录吗?", "提示", {
        type: "warning"
      })
        .then(() => {
          //NProgress.start();
          let para = {
            // accessToken:this.getToken.accessToken,
            id: params.s_id
          };
          removehotserchLists(para).then(res => {
            if (res.data.code == 200) {
              //NProgress.done();
              this.$message({
                message: res.data.msg,
                type: "success"
              });
              this.getserchLists();
            } else {
              this.$message({
                message: res.data.msg,
                type: "warning"
              });
            }
          });
        })
        .catch(() => {});
    }
  },
  watch: {
    editFormVisible(curVal, oldVal) {
      if (!curVal) {
        this.getserchLists();
      }
    },
    addFormVisible(curVal, oldVal) {
      if (!curVal) {
        this.addForm = {
          s_name: "",
          s_level: "",
          sort: "",
          enabled: false
        };
      }
    }
  },
  mounted() {
    this.getserchLists();
  }
};
</script>
<style>
.editimg {
  width: 300px;
  height: 200px;
}
</style>
