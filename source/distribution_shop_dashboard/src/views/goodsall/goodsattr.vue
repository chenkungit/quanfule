<template>
  <div>
    <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item>
          <div style="float:left;margin-left:20px;">
            <el-button type="primary" icon="el-icon-plus" @click="handleAdd">新增</el-button>
          </div>
          <div style="float:left;margin-left:20px;">
            按商品类型显示：
            <el-select v-model="goods_type" placeholder="请选择" @change="handleselect">
              <el-option v-for="item in fatheroptions" :key="item.value" :label="item.label" :value="item.value">
              </el-option>
            </el-select>
          </div>
          <div style="float:left;margin-left:20px;">
            <el-button type="danger" icon="el-icon-delete" @click="batchDelete">批量删除</el-button>
          </div>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 列表 -->
    <el-table :data="goodsattrdata" border style="width: 100%" v-loading="tableloading" element-loading-text="拼命加载中" @selection-change="handleslecttable">
      <el-table-column type="selection" width="50">
      </el-table-column>
      <!-- <el-table-column prop="id" label="ID" sortable></el-table-column> -->
      <el-table-column prop="attr_id" label="编号" width="60">
      </el-table-column>
      <el-table-column prop="attr_name" label="属性名称" width="100">
      </el-table-column>
      <el-table-column prop="cat_name" label="商品类型" width="120">
      </el-table-column>
      <el-table-column prop="attr_input_type" label="属性值的录入方式" width="140" :formatter="attr_input_typeformatter">
      </el-table-column>
      <el-table-column prop="attr_values" label="可选值列表">
      </el-table-column>
      <!-- <el-table-column prop="sort_order" label="	推荐排序" width="100" sortable>
      </el-table-column> -->
      <el-table-column label="操作" width="190">
        <template slot-scope="scope">
          <el-button icon="el-icon-edit" @click="handleEdit(scope.row)">编辑</el-button>
          <el-button type="danger" icon="el-icon-delete" @click="handleDel(scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination @current-change="handleCurrentChange" layout="prev, pager, next, jumper" :page-count="pagecount">
      </el-pagination>
    </div>

    <!-- 新增 -->
    <el-dialog title="新增" :visible.sync="addFormVisible">
      <el-form :model="addForm" label-width="200px" ref="addForm">
        <el-form-item label="属性名称" prop="attr_name">
          <el-input type="text" v-model="addForm.attr_name"></el-input>
        </el-form-item>
        <el-form-item label="所属商品类型" prop="goodstype_id">
          <el-select v-model="addForm.goodstype_id" placeholder="请选择" @change="dialogselect(addForm.goodstype_id)">
            <el-option v-for="item in dialogoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <!-- <el-form-item label="属性分组" prop="attr_group" v-if="dialogsonoptions!=''">
          <el-select v-model="addForm.attr_group" placeholder="请选择">
            <el-option v-for="item in dialogsonoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item> -->
        <el-form-item label="该属性值的录入方式" prop="attr_input_type">
          <el-radio-group v-model="addForm.attr_input_type" @change="inputdisabled(addForm.attr_input_type)">
            <el-radio :label="0">手工录入</el-radio>
            <el-radio :label="1">从下面的列表中选择（一行代表一个可选值）</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="可选值列表" prop="attr_values">
          <el-input type="textarea" :rows="3" v-model="addForm.attr_values" :disabled="inpdisabled"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="addFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="addSubmit">提交</el-button>
      </div>
    </el-dialog>
    <!-- 编辑 -->
    <el-dialog title="编辑" :visible.sync="editFormVisible">
      <el-form :model="editForm" label-width="200px" ref="editForm">
        <el-form-item label="属性名称" prop="attr_name">
          <el-input type="text" v-model="editForm.attr_name"></el-input>
        </el-form-item>
        <el-form-item label="所属商品类型" prop="goodstype_id">
          <el-select v-model="editForm.goodstype_id" placeholder="请选择" @change="dialogselect(editForm.goodstype_id)">
            <el-option v-for="item in dialogoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <!-- <el-form-item label="属性分组" prop="attr_group" v-if="dialogsonoptions!=''">
          <el-select v-model="editForm.attr_group" placeholder="请选择">
            <el-option v-for="item in dialogsonoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item> -->
        <el-form-item label="该属性值的录入方式" prop="attr_input_type">
          <el-radio-group v-model="editForm.attr_input_type" @change="inputdisabled(editForm.attr_input_type)">
            <el-radio :label="0">手工录入</el-radio>
            <el-radio :label="1">从下面的列表中选择（一行代表一个可选值）</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="可选值列表" prop="attr_values">
          <el-input type="textarea" :rows="3" v-model="editForm.attr_values" :disabled="inpdisabled"></el-input>
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
import { seletedGoodsattr } from "../../api/api";
import { seletedGoodsattrson } from "../../api/api";
import { goodsattrLists } from "../../api/api";
import { addGoodsattr } from "../../api/api";
import { editGoodsattr } from "../../api/api";
import { removeGoodsattr } from "../../api/api";
export default {
  data() {
    return {
      goodsattrdata: [],
      tableloading: true,
      page: 1,
      pagecount: 1,
      goods_type: "",
      fatheroptions: [
        {
          value: 0,
          label: "------------请选择-----------"
        }
      ],
      addFormVisible: false,
      addForm: {
        attr_name: "",
        goodstype_id: "",
        attr_group: "",
        attr_values: "",
        attr_index: "",
        is_linked: 0,
        attr_type: 1,
        attr_input_type: 0
      },
      editFormVisible: false,
      editForm: {
        attr_id: "",
        attr_name: "",
        goodstype_id: "",
        attr_group: "",
        attr_values: "",
        attr_index: "",
        is_linked: 0,
        attr_type: 1,
        attr_input_type: 0
      },
      inpdisabled: true,
      dialogoptions: [],
      dialogsonoptions: [],
      batcharr: []
    };
  },
  methods: {
    getgoodsattr() {
      this.tableloading = true;
      goodsattrLists({
        goods_type: this.goods_type,
        page: this.page
      }).then(res => {
        this.tableloading = false;
        this.pagecount = res.data.data.pagecount;
        this.goodsattrdata = res.data.data.item;
      });
    },
    handleselect() {
      this.getgoodsattr();
    },
    handleslecttable(val) {
      this.batcharr = [];
      val.forEach(element => {
        this.batcharr.push(element.attr_id);
      });
    },
    handleCurrentChange(val) {
      this.page = val;
      this.getgoodsattr();
    },
    dialogselect(params, params2) {
      this.dialogsonoptions = [];
      seletedGoodsattrson({
        cat_id: params
      }).then(res => {
        for (var i in res.data.data.grouplist) {
          let obj = {
            label: res.data.data.grouplist[i],
            value: i
          };
          this.dialogsonoptions.push(obj);
        }
        this.addForm.attr_group = "0";
        // this.editForm.attr_group = this.dialogsonoptions[0].value;
        if (params2 != null || params2 != undefined) {
          console.log(params2);
          this.editForm.attr_group = params2 + "";
        }
      });
    },
    attr_input_typeformatter(row) {
      if (row.attr_input_type == 0) {
        return "手工录入";
      } else if (row.attr_input_type == 1) {
        return "从下面的列表中选择";
      } else if (row.attr_input_type == 2) {
        return "多行文本框";
      }
    },
    inputdisabled(param1) {
      if(param1 == 1){
        this.inpdisabled = false;
      } else {
        this.inpdisabled = true;
      }
    },
    handleAdd() {
      this.addFormVisible = true;
      this.dialogoptions = this.fatheroptions;
    },
    addSubmit() {
      var that = this;
      this.$confirm("确认提交吗？", "提示", {}).then(() => {
        addGoodsattr(this.addForm).then(res => {
          if (res.code == 200) {
            that.$message({
              message: res.msg,
              type: "success"
            });
            that.addFormVisible = false;
            that.getgoodsattr();
          } else {
            that.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    },
    handleEdit(params) {
      this.editFormVisible = true;
      this.dialogoptions = this.fatheroptions;
      this.editForm.attr_name = params.attr_name;
      this.editForm.goodstype_id = params.cat_id;
      this.editForm.attr_id = params.attr_id;
      this.editForm.attr_values = params.attr_values;
      this.editForm.attr_index = params.attr_index;
      this.editForm.is_linked = params.is_linked;
      this.editForm.attr_type = params.attr_type;
      this.editForm.attr_input_type = params.attr_input_type;
      this.dialogselect(this.editForm.goodstype_id, params.attr_group);
      this.inputdisabled(this.editForm.attr_input_type);
    },
    editSubmit() {
      var that = this;
      this.$confirm("确认提交吗？", "提示", {}).then(() => {
        editGoodsattr(this.editForm).then(res => {
          if (res.code == 200) {
            that.$message({
              message: res.msg,
              type: "success"
            });
            that.editFormVisible = false;
            that.getgoodsattr();
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
        removeGoodsattr({
          attr_id: params.attr_id
        }).then(res => {
          if (res.data.code == 200) {
            that.$message({
              message: res.data.msg,
              type: "success"
            });
            that.getgoodsattr();
          } else {
            that.$message({
              message: res.data.msg,
              type: "warning"
            });
          }
        });
      });
    },
    batchDelete() {
      var that = this;
      this.$confirm(
        "确认删除" + this.batcharr.length + "项吗？",
        "提示",
        {}
      ).then(() => {
        removeGoodsattr({
          attr_id: this.batcharr
        }).then(res => {
          if (res.data.code == 200) {
            that.$message({
              message: res.data.msg,
              type: "success"
            });
            that.getgoodsattr();
          } else {
            that.$message({
              message: res.data.msg,
              type: "warning"
            });
          }
        });
      });
    }
  },
  mounted() {
    var that = this;
    seletedGoodsattr().then(res => {
      for (var i in res.data.data.list) {
        this.fatheroptions.push(res.data.data.list[i]);
        if (this.$route.params.id != undefined) {
          this.goods_type = this.$route.params.id;
        } else {
          this.goods_type = 0;
        }
      }
      that.getgoodsattr();
    });
  },
  watch: {
    addFormVisible(curVal) {
      if (!curVal) {
        this.$refs.addForm.resetFields();
      }
    },
    editFormVisible(curVal) {
      if (!curVal) {
        this.$refs.editForm.resetFields();
      }
    }
  }
};
</script>
