<template>
  <div>
    <el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
      <el-form :inline="true">
        <el-form-item>
          <el-button type="primary" @click="handleAddfather">新增父级</el-button>
          <el-button type="danger" icon="el-icon-delete" @click="clearCat">清除分类缓存</el-button>
          <el-button type="danger" icon="el-icon-delete" @click="clearGood">清除商品缓存</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <el-table :data="storeLists" row-key="cat_id" style="width: 100%" border :expand-row-keys="expands">
      <el-table-column type="expand">
        <template slot-scope="props">
          <el-table :data="props.row.child" style="width: 100%">
            <el-table-column prop="cat_id" label="子级分类ID">
            </el-table-column>
            <el-table-column prop="cat_name" label="子级分类名称">
            </el-table-column>
            <el-table-column prop="sort_order" label="排序" sortable>
              <template slot-scope="scope">
                <el-input-number v-model="scope.row.sort_order" :min="1" :max="999" label="" size="mini" controls-position="right"></el-input-number>
                <el-button icon="el-icon-check" @click="sureSort(scope.row)">确定</el-button>
              </template>
            </el-table-column>
            <el-table-column prop="is_show" label="启用" :formatter="is_show_formatter">
            </el-table-column>
            <el-table-column label="操作" width="190">
              <template slot-scope="scope">
                <el-button icon="el-icon-edit" @click="handleEditson(scope.row)">编辑</el-button>
                <el-button icon="el-icon-delete" type="danger" @click="handleDel(scope.row)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>
      <el-table-column label="父级分类ID" prop="cat_id">
      </el-table-column>
      <el-table-column label="父级分类名称" prop="cat_name">
      </el-table-column>
      <el-table-column prop="sort_order" label="排序" sortable width="250">
      </el-table-column>
      <el-table-column prop="is_show" label="启用" :formatter="is_show_formatter" width="100">
      </el-table-column>
      <el-table-column label="操作" >
        <template slot-scope="scope">
          <el-button type="primary"  @click="handleAddson(scope.row)" >新增子类</el-button>
          <el-button icon="el-icon-edit" @click="handleEditfather(scope.row)">编辑</el-button>
          <el-button type="danger"  @click="handleDel(scope.row)" >删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <!--新增界面-->
    <!-- father -->
    <el-dialog title="新增" :visible.sync="addFormVisiblefather">
			<el-form :model="addFormfather" label-width="200px" :rules="addFormRules" ref="addFormfather">
				<el-form-item label="父级名称">
					<el-input v-model="addFormfather.cat_name"></el-input>
				</el-form-item>
				<el-form-item label="排序">
					<el-input v-model="addFormfather.sort_order"></el-input>
				</el-form-item>
        <el-form-item label="启用" prop="is_show">
          <el-radio-group v-model="addFormfather.is_show">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="图片">
					<VueImgInputer v-model="addFormfather.mobile_icon" theme="light" ></VueImgInputer>
				</el-form-item>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button @click.native="addFormVisiblefather = false">取消</el-button>
				<el-button type="primary" @click.native="addSubmitfather">提交</el-button>
			</div>
		</el-dialog>
    <!-- son -->
    <el-dialog title="新增son" :visible.sync="addFormVisibleson">
			<el-form :model="addFormson" label-width="200px" :rules="addFormRules" ref="addFormson">
				<el-form-item label="子级名称">
					<el-input v-model="addFormson.cat_name"></el-input>
				</el-form-item>
				<el-form-item label="排序">
					<el-input v-model="addFormson.sort_order"></el-input>
				</el-form-item>
          <el-form-item label="启用" prop="is_show">
          <el-radio-group v-model="addFormson.is_show">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
          </el-radio-group>
        </el-form-item>
				<el-form-item label="图片">
					<VueImgInputer v-model="addFormson.mobile_icon" theme="light" ></VueImgInputer>
				</el-form-item>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button @click.native="addFormVisibleson = false">取消</el-button>
				<el-button type="primary" @click.native="addSubmitson">提交</el-button>
			</div>
		</el-dialog>
    <!-- 编辑 -->
    <!-- father -->
    <el-dialog title="编辑" :visible.sync="editFormVisiblefather">
      <el-form :model="editFormfather" label-width="200px" ref="editFormfather">
       <el-form-item label="广告名称">
								<el-input v-model="editFormfather.cat_name"></el-input>
							</el-form-item>
							<el-form-item label="排序">
								<el-input v-model="editFormfather.sort_order"></el-input>
							</el-form-item>
              <el-form-item label="启用" prop="is_show">
                <el-radio-group v-model="editFormfather.is_show">
                  <el-radio :label="1">是</el-radio>
                  <el-radio :label="0">否</el-radio>
                </el-radio-group>
              </el-form-item>
        <el-form-item label="手机端小标图">
          <VueImgInputer v-model="editFormfather.mobile_icon" theme="light" :imgSrc='editFormfather.mobile_icon'></VueImgInputer>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="editFormVisiblefather = false">取消</el-button>
        <el-button type="primary" @click.native="editSubmitfather">提交</el-button>
      </div>
    </el-dialog>
    <!-- 编辑son -->
    <el-dialog title="编辑son" :visible.sync="editFormVisibleson">
      <el-form :model="editFormson" label-width="200px" ref="editFormson">
        <el-form-item label="广告名称">
          <el-input v-model="editFormson.cat_name"></el-input>
        </el-form-item>
        <el-form-item label="排序">
          <el-input v-model="editFormson.sort_order"></el-input>
        </el-form-item>
        <el-form-item label="启用" prop="is_show">
                <el-radio-group v-model="editFormson.is_show">
                  <el-radio :label="1">是</el-radio>
                  <el-radio :label="0">否</el-radio>
                </el-radio-group>
        </el-form-item>
        <el-form-item label="手机端小标图">
          <VueImgInputer v-model="editFormson.mobile_icon" theme="light" :imgSrc='editFormson.mobile_icon'></VueImgInputer>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="editFormVisibleson = false">取消</el-button>
        <el-button type="primary" @click.native="editSubmitson">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { storecategoryLists } from "../../api/api";
import { addcategory } from "../../api/api";
import { editcategory } from "../../api/api";
import { categoryDelete } from "../../api/api";
import { clearCache } from "../../api/api";
import { idSearch } from "../../api/api";
import { uploadImg } from "../../api/api";
export default {
  data() {
    return {
      storeLists: [], //列表获取的数据
      //新增界面
      addFormVisiblefather: false,
      addFormVisibleson: false, //新增界面是否显示
      addLoading: false,
      addFormRules: {
        name: [
          {
            required: true,
            message: "请输入",
            trigger: "blur"
          }
        ]
      }, //新增界面数据
      addFormfather: {
        cat_name: "",
        sort_order: "",
        is_show: 1
      },
      parent_id: "",
      addFormson: {
        cat_name: "",
        sort_order: "",
        is_show: 1
      },
      editFormVisible: false, //编辑
      editFormfather: {
        redirect_type: "",
        redirect_id: "",
        redirect_name: ""
      },
      editFormson: {
        cat_name: "",
        sort_order: "",
        is_show: 1
      },
      lunbo: {
        img: "",
        redirect_id: "",
        redirect_name: "",
        redirect_type: "",
        redirect: {}
      },
      lunbodata: [],
      editFormVisiblefather: false,
      editFormVisibleson: false,
      editselecteOptions: [
        {
          value: 0,
          label: "无操作"
        },
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
      ],
      options6: [],
      loading: true,
      options8: [],
      expands:[]
    };
  },
  methods: {
    sureSort(params){
      var formData = new FormData();
      formData.append("cat_id", params.cat_id);
      formData.append("parent_id", params.parent_id);
      formData.append("sort_order", params.sort_order);
            editcategory(formData).then(res => {
              if (res.data.code == 200) {
                this.$message({
                  message: res.data.msg,
                  type: "success"
                });
                this.getstoreLists();
                // setTimeout(function(){
                //   this.expands = [];
                //   this.expands.push(last.cat);
                // },500)

              } else {
                this.$message({
                  message: res.data.msg,
                  type: "warning"
                });
              }
            });
    },
    clearCat() {
      this.$confirm("确认清除吗?", "提示", {
        type: "warning"
      }).then(() => {
        this.listLoading = true;
        let para = {
          arg: "parent_categories_tree:*"
        };
        clearCache(para).then(res => {
          if (res.code == 200) {
            this.listLoading = false;
            this.$message({
              message: res.msg,
              type: "success"
            });
            this.getstoreLists();
          } else {
            this.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    },

    clearGood(){
      this.$confirm("确认清除吗?", "提示", {
        type: "warning"
      }).then(() => {
        this.listLoading = true;
        let para = {
          arg: "product_list_index:*"
        };
        clearCache(para).then(res => {
          if (res.code == 200) {
            this.listLoading = false;
            this.$message({
              message: res.msg,
              type: "success"
            });
            this.getstoreLists();
          } else {
            this.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    },
    getstoreLists() {
      storecategoryLists().then(res => {
        if (res.data.code == 200) {
          this.storeLists = res.data.data.item;
        }
      });
    },
    // redirect_type_formatter(row) {
    //   if (row.redirect_type == 1) {
    //     return "商品详情";
    //   } else if (row.redirect_type == 2) {
    //     return "分类详情";
    //   } else if (row.redirect_type == 3) {
    //     return "活动详情";
    //   } else {
    //     return "无操作";
    //   }
    // },
    is_show_formatter(row) {
      if (row.is_show == 1) {
        return "是";
      } else {
        return "否";
      }
    },
    handleAddfather() {
      this.addFormVisiblefather = true;
    },
    addSubmitfather() {
      //新建提交
      var that = this;
      this.$refs.addFormfather.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            this.addLoading = true;
            //NProgress.start();
            var formData = new FormData();
            formData.append("parent_id", 0);
            formData.append("cat_name", this.addFormfather.cat_name);
            formData.append("sort_order", this.addFormfather.sort_order);
            formData.append("is_show", this.addFormfather.is_show);
            formData.append("mobile_icon", this.addFormfather.mobile_icon);
            addcategory(formData).then(res => {
              if (res.data.code == 200) {
                // this.listLoading = false;
                that.addFormVisiblefather = false;
                //NProgress.done();
                that.$message({
                  message: res.data.msg,
                  type: "success"
                });
                that.getstoreLists();
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
    handleAddson(params) {
      this.parent_id = params.cat_id;
      this.addFormVisibleson = true;
    },
    addSubmitson() {
      //新建提交
      var that = this;
      this.$refs.addFormson.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            this.addLoading = true;
            //NProgress.start();
            var formData = new FormData();
            formData.append("parent_id", this.parent_id);
            formData.append("cat_name", this.addFormson.cat_name);
            formData.append("sort_order", this.addFormson.sort_order);
            formData.append("is_show", this.addFormson.is_show);
            formData.append("type", 1);
            formData.append("mobile_icon", this.addFormson.mobile_icon);
            addcategory(formData).then(res => {
              if (res.data.code == 200) {
                // this.listLoading = false;
                that.addFormVisibleson = false;
                //NProgress.done();
                that.$message({
                  message: res.data.msg,
                  type: "success"
                });
                that.getstoreLists();
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
    handleEditfather(params) {
      this.editFormVisiblefather = true;
      this.options6 = [
        {
          value: params.redirect_id,
          label: params.redirect_name
        }
      ];
      // this.editremoteMethod(params.redirect_name)
      this.editFormfather = params;
      if (params.swiper != null) {
        this.lunbodata = JSON.parse(params.swiper);
      } else {
        this.lunbodata = [];
      }
    },
    editSubmitfather() {
      this.$refs.editFormfather.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            //NProgress.start();
            var that = this;
            var formData = new FormData();
            formData.append("cat_id", this.editFormfather.cat_id);
            formData.append("is_show", this.editFormfather.is_show);
            formData.append("cat_name", this.editFormfather.cat_name);
            formData.append("parent_id", 0);
            formData.append("sort_order", this.editFormfather.sort_order);
            if (this.editFormfather.mobile_icon != null) {
              formData.append("mobile_icon", this.editFormfather.mobile_icon);
            }
            editcategory(formData).then(res => {
              if (res.data.code == 200) {
                that.editFormVisiblefather = false;
                that.$message({
                  message: res.data.msg,
                  type: "success"
                });
                //    that.getstoreLists();
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
    handleEditson(params) {
      this.editFormVisibleson = true;
      this.editFormson = params;
    },
    editSubmitson() {
      this.$refs.editFormson.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            //NProgress.start();
            var that = this;
            var formData = new FormData();
            // formData.append('accessToken',this.getToken.accessToken)
            formData.append("is_show", this.editFormson.is_show);
            formData.append("cat_name", this.editFormson.cat_name);
            formData.append("sort_order", this.editFormson.sort_order);
            formData.append("type", 1);
            formData.append("cat_id", this.editFormson.cat_id);
            formData.append("parent_id", this.editFormson.parent_id);
            formData.append("mobile_icon", this.editFormson.mobile_icon);
            editcategory(formData).then(res => {
              if (res.data.code == 200) {
                // this.listLoading = false;
                that.editFormVisibleson = false;
                //NProgress.done();
                that.$message({
                  message: res.data.msg,
                  type: "success"
                });
                //    that.getstoreLists();
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
      this.$confirm("确认删除该记录吗?", "提示", {
        type: "warning"
      }).then(() => {
          this.listLoading = true;
          //NProgress.start();
          let para = {
            // accessToken:this.getToken.accessToken,
            id: params.cat_id,
            type: 3
          };
          categoryDelete(para).then(res => {
            if (res.data.code == 200) {
              this.listLoading = false;
              //NProgress.done();
              this.$message({
                message: res.data.msg,
                type: "success"
              });
              this.getstoreLists();
            } else {
              this.$message({
                message: res.data.msg,
                type: "warning"
              });
            }
          });
        }).catch(() => {});
    },
    edit_option_change() {
      this.editFormfather.redirect_id = "";
    }
  },
  watch: {
    editFormVisiblefather(curVal) {
      if (!curVal) {
        this.getstoreLists();
      }
    },
    editFormVisibleson(curVal) {
      if (!curVal) {
        this.getstoreLists();
      }
    },
    addFormVisibleson(curVal) {
      if (!curVal) {
        this.addFormson = {
          cat_name: "",
          sort_order: "",
          is_show: 1
        };
      }
    }
  },
  mounted() {
    this.getstoreLists();
  }
};
</script>
<style scoped>
  .editimg {
    width: 300px;
    height: 200px;
  }
</style>
