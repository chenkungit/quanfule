<template>
  <!-- 首页主题-->
  <div>
    <!--工具条-->
    <el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
      <el-form :inline="true">
        <el-form-item>
          <el-button type="primary" @click="handleAdd">新增</el-button>
        </el-form-item>
        <el-form-item>
          <el-button type="danger" @click="clearall">清除首页缓存</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 轮播图列表 -->
    <el-table :data="homethemeLists" border style="width: 100%" v-loading="tableloading">
      <el-table-column prop="theme_name" label="主题名称" width="100">
      </el-table-column>
      <el-table-column label="图片" width="200">
        <template slot-scope="scope">
          <img :src="scope.row.theme_banner" width="150" height="80" />
        </template>
      </el-table-column>
      <el-table-column prop="redirect_type" label="跳转类型">
      </el-table-column>
      <el-table-column prop="redirect_name" label="跳转名称">
      </el-table-column>
      <el-table-column prop="redirect_id" label="跳转ID">
      </el-table-column>
      <el-table-column prop="children_goods_name" label="子类名称" show-overflow-tooltip>
      </el-table-column>
      <el-table-column prop="sort" label="排序">
      </el-table-column>
      <el-table-column prop="enabled" label="启用">
      </el-table-column>
      <el-table-column label="操作" width="150">
        <template slot-scope="scope">
          <el-button @click="handleEdit(scope.row)">编辑</el-button>
          <el-button type="danger" @click="handleDel(scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <!--新增界面-->
    <el-dialog title="新增" :visible.sync="addFormVisible">
      <el-form :model="addForm" label-width="200px" :rules="addFormRules" ref="addForm">
        <el-form-item label="主题名称">
          <el-input v-model="addForm.theme_name"></el-input>
        </el-form-item>
        <el-form-item label="跳转类型">
          <el-select v-model="addForm.redirect_type" placeholder="请选择" @change="addidchange">
            <el-option v-for="item in addselecteOptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item :label="addForm.redirect_type==1?'跳转商品':addForm.redirect_type==2?'跳转分类':'跳转链接'">
          <el-select v-if="addForm.redirect_type==1||addForm.redirect_type==2" v-model="addForm.redirect" filterable remote :placeholder="addForm.redirect_type==1?'请输入商品关键词':'请输入分类关键词'"
            :remote-method="addremoteMethod" :loading="loading">
            <el-option v-for="item in options4" :key="item.value" :label="item.label" :value="item">
            </el-option>
          </el-select>
          <el-input v-model="addForm.redirect_id" v-if="addForm.redirect_type==3"></el-input>
        </el-form-item>
        <el-form-item label="子类名称">
          <el-select v-model="addForm.children_goods_ids_id" filterable remote placeholder="请输入商品关键词" :remote-method="addremoteMethod2"
            :loading="loading">
            <el-option v-for="item in options5" :key="item.value" :label="item.label" :value="item">
            </el-option>
          </el-select>
          <el-button type="primary" @click="addsonAdd">新增</el-button>
        </el-form-item>
        <el-form-item label="子类名称">
          <el-table :data="addForm.children_goods" stripe style="width: 100%">
            <el-table-column prop='value' label="ID">
            </el-table-column>
            <el-table-column prop='label' label="名称">
            </el-table-column>
            <el-table-column label="操作" width="150">
              <template slot-scope="scope">
                <el-button type="danger" @click="addsonDelete(scope.$index, addForm.children_goods)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-form-item>
        <el-form-item label="排序">
          <el-input v-model="addForm.sort"></el-input>
        </el-form-item>
        <el-form-item label="启用">
          <el-switch v-model="addForm.enabled" on-color="#13ce66" off-color="#ff4949">
          </el-switch>
        </el-form-item>
        <el-form-item label="图片">
          <VueImgInputer v-model="addForm.img" theme="light"></VueImgInputer>
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
        <el-form-item label="主题名称">
          <el-input v-model="editForm.theme_name"></el-input>
        </el-form-item>
        <el-form-item label="跳转类型">
          <el-select v-model="editForm.redirect_type" placeholder="请选择" @change="editidchange">
            <el-option v-for="item in editselecteOptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item :label="editForm.redirect_type==1?'跳转商品':editForm.redirect_type==2?'跳转分类':'跳转链接'">
          <el-select v-if="editForm.redirect_type==1||editForm.redirect_type==2" v-model="editForm.redirect" filterable remote :placeholder="editForm.redirect_type==1?'请输入商品关键词':'请输入分类关键词'"
            :remote-method="editremoteMethod" :loading="loading">
            <el-option v-for="item in options6" :key="item.value" :label="item.label" :value="item">
            </el-option>
          </el-select>
          <el-input v-if="editForm.redirect_type==3" v-model="editForm.redirect_id"></el-input>
        </el-form-item>
        <el-form-item label="子类名称">
          <el-select v-model="editForm.children_goods_ids_id" filterable remote placeholder="请输入商品关键词" :remote-method="editremoteMethod2"
            :loading="loading">
            <el-option v-for="item in options5" :key="item.value" :label="item.label" :value="item">
            </el-option>
          </el-select>
          <el-button type="primary" @click="editsonAdd">新增</el-button>
        </el-form-item>
        <el-form-item label="子类名称">
          <el-table :data="editForm.children_goods" stripe style="width: 100%">
            <el-table-column prop='value' label="ID">
            </el-table-column>
            <el-table-column prop='label' label="名称">
            </el-table-column>
            <el-table-column label="操作" width="150">
              <template slot-scope="scope">
                <el-button type="danger" @click="addsonDelete(scope.$index, editForm.children_goods)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-form-item>
        <el-form-item label="排序">
          <el-input v-model="editForm.sort"></el-input>
        </el-form-item>
        <el-form-item label="启用">
          <el-switch v-model="editForm.enabled" on-color="#13ce66" off-color="#ff4949">
          </el-switch>
        </el-form-item>
        <div class='editimages'>
          <el-form-item label="图片">
            <VueImgInputer v-model="editForm.img" theme="light" :imgSrc='editForm.theme_banner'></VueImgInputer>
          </el-form-item>
        </div>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="editFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="editSubmit">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import store from "../../vuex/store";
import { homeLists } from "../../api/api";
import { removehomeLists } from "../../api/api";
import { addhome } from "../../api/api";
import { edithome } from "../../api/api";
import { idSearch } from "../../api/api";
import { idSearchbk } from "../../api/api";
import { clearCache } from "../../api/api";
import { mapGetters } from "vuex";
import { mapActions } from "vuex";

export default {
  data() {
    return {
      homethemeLists: [], //列表获取的数据
      //新增界面
      addFormVisible: false, //新增界面是否显示
      addactrangeid: [],
      options4: [],
      options5: [],
      options6: [],
      options7: [],
      redirect: [],
      loading: false,
      tableloading: false,
      addFormRules: {
        name: [
          {
            required: true,
            message: "请输入",
            trigger: "blur"
          }
        ]
      },
      //新增界面数据
      addForm: {
        theme_name: "",
        theme_banner: "",
        redirect_type: "",
        redirect_id: "",
        redirect: {},
        children_goods_ids: [],
        children_goods_name: [],
        children_goods: [],
        sort: "",
        enabled: false,
        children_goods_ids_id: ""
      },
      editFormVisible: false, //编辑
      editForm: {
        theme_name: "",
        theme_banner: "",
        redirect_type: "",
        redirect: {},
        redirect_id: "",
        redirect_name: "",
        children_goods_ids: [],
        children_goods: [],
        sort: "",
        enabled: "",
        children_goods_ids_id: ""
      },
      addselecteOptions: [
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
      addselectelabel: "商品详情",
      editselecteOptions: [
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
      editselectetype: [
        {
          value: 1,
          label: "商城"
        },
        {
          value: 3,
          label: "门店"
        }
      ],
      addselectetype: [
        {
          value: 1,
          label: "商城"
        },
        {
          value: 3,
          label: "门店"
        }
      ],
      editselectelabel: "商品详情"
    };
  },
  computed: {
    ...mapGetters(["getToken"])
  },
  methods: {
    clearall() {
      this.$confirm("确认清除吗?", "提示", {
        type: "warning"
      }).then(() => {
        clearCache({
          arg: "cache_index:1:*"
        }).then(res => {
          if (res.code == 200) {
            this.$message({
              message: res.msg,
              type: "success"
            });
            this.gethomeLists();
          } else {
            this.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    },
    addsonAdd() {
      this.addForm.children_goods.push({
        value: this.addForm.children_goods_ids_id.value,
        label: this.addForm.children_goods_ids_id.label
      });
      this.addForm.children_goods = this.uniqeByKeys(
        this.addForm.children_goods,
        ["value"]
      );
    },
    addsonDelete(index, rows) {
      rows.splice(index, 1);
      //  console.log(rows)
    },
    editsonAdd() {
      this.editForm.children_goods.push({
        value: this.editForm.children_goods_ids_id.value,
        label: this.editForm.children_goods_ids_id.label
      });
      this.editForm.children_goods = this.uniqeByKeys(
        this.editForm.children_goods,
        ["value"]
      );
    },
    addremoteMethod(query) {
      //增加远程搜索
      this.options4 = [];
      if (query !== "") {
        this.loading = true;
        setTimeout(() => {
          if (this.addForm.redirect_type == 1) {
            let param = {
              presale: 1,
              type: 3,
              keywords: query
            };
            idSearch(param).then(res => {
              if (res.data.code == 200) {
                for (var i in res.data.data) {
                  let a = {
                    value: res.data.data[i].id,
                    label: res.data.data[i].name
                  };
                  this.options4.push(a);
                  this.loading = false;
                }
              }
            });
          } else if (this.addForm.redirect_type == 2) {
            let param = {
              type: 1,
              keywords: query
            };
            idSearch(param).then(res => {
              this.options4 = [];
              if (res.data.code == 200) {
                for (var i in res.data.data) {
                  let a = {
                    value: res.data.data[i].cat_id,
                    label: res.data.data[i].cat_name
                  };
                  this.options4.push(a);
                  this.loading = false;
                }
              }
            });
          }
        }, 200);
      } else {
        this.options4 = [];
      }
    },
    editremoteMethod2(query) {
      //增加远程搜索
      this.options5 = [];
      if (query !== "") {
        this.loading = true;
        setTimeout(() => {
          let param = {
            presale: 1,
            type: 3,
            keywords: query
          };
          idSearch(param).then(res => {
            if (res.data.code == 200) {
              this.options5 = [];
              for (var i in res.data.data) {
                let a = {
                  value: res.data.data[i].id,
                  label: res.data.data[i].name
                };
                this.options5.push(a);
                this.loading = false;
              }
            }
          });
        }, 200);
      } else {
        this.options4 = [];
      }
    },
    addremoteMethod2(query) {
      //增加远程搜索
      this.options5 = [];
      if (query !== "") {
        this.loading = true;
        setTimeout(() => {
          let param = {
            presale: 1,
            type: 3,
            keywords: query
          };
          idSearch(param).then(res => {
            if (res.data.code == 200) {
              this.options5 = [];
              for (var i in res.data.data) {
                let a = {
                  value: res.data.data[i].id,
                  label: res.data.data[i].name
                };
                this.options5.push(a);
                this.loading = false;
              }
            }
          });
        }, 200);
      } else {
        this.options4 = [];
      }
    },
    editremoteMethod(query) {
      //增加远程搜索
      this.options6 = [];
      if (query !== "") {
        this.loading = true;
        setTimeout(() => {
          console.log(query);
          console.log(this.editForm.redirect_type);
          if (
            this.editForm.redirect_type == 1 ||
            this.editForm.redirect_type == "商品详情"
          ) {
            let param = {
              type: 3,
              keywords: query
            };
            idSearch(param).then(res => {
              if (res.data.code == 200) {
                for (var i in res.data.data) {
                  let a = {
                    value: res.data.data[i].shp_id,
                    label: res.data.data[i].name
                  };
                  this.options6.push(a);
                  this.loading = false;
                }
              }
            });
          } else if (
            this.editForm.redirect_type == 2 ||
            this.editForm.redirect_type == "分类详情"
          ) {
            let param = {
              type: 1,
              keywords: query
            };
            idSearch(param).then(res => {
              this.options6 = [];
              if (res.data.code == 200) {
                for (var i in res.data.data) {
                  let a = {
                    value: res.data.data[i].cat_id,
                    label: res.data.data[i].cat_name
                  };
                  this.options6.push(a);
                  this.loading = false;
                }
              }
            });
          }
        }, 200);
      } else {
        this.options6 = [];
      }
    },
    editclear() {
      console.log(1);
    },
    gethomeLists() {
      let newdata = [];
      this.tableloading = true;
      homeLists().then(res => {
        if (res.data.code == 200) {
          this.tableloading = false;
          for (var i in res.data.data.item) {
            newdata.push(res.data.data.item[i]);
            // console.log(this.homethemeLists)
          }
          for (var i in newdata) {
            if (newdata[i].redirect_type == 1) {
              newdata[i].redirect_type = "商品详情";
            } else if (newdata[i].redirect_type == 2) {
              newdata[i].redirect_type = "分类详情";
            } else {
              newdata[i].redirect_type = "活动详情";
            }
            if (newdata[i].enabled == 1) {
              newdata[i].enabled = "是";
            } else {
              newdata[i].enabled = "否";
            }
          }
          this.homethemeLists = newdata;
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
            if (this.addForm.redirect_type == 3) {
              formData.append("redirect_id", this.addForm.redirect_id);
              formData.append("redirect_name", "");
            } else {
              formData.append("redirect_id", this.addForm.redirect.value);
              formData.append("redirect_name", this.addForm.redirect.label);
            }
            this.addForm.children_goods_ids = [];
            this.addForm.children_goods_name = [];
            for (var i in this.addForm.children_goods) {
              this.addForm.children_goods_ids.push(
                this.addForm.children_goods[i].value
              );
              this.addForm.children_goods_name.push(
                this.addForm.children_goods[i].label
              );
            }
            formData.append("enabled", this.addForm.enabled);
            // formData.append('accessToken',this.getToken.accessToken)
            formData.append("img", this.addForm.img);
            formData.append("theme_name", this.addForm.theme_name);
            formData.append("redirect_type", this.addForm.redirect_type);
            formData.append("carousel_type", 1);
            formData.append(
              "children_goods_id",
              this.addForm.children_goods_ids
            );
            formData.append(
              "children_goods_name",
              this.addForm.children_goods_name
            );
            formData.append("sort", this.addForm.sort);
            addhome(formData).then(res => {
              if (res.data.code == 200) {
                //NProgress.done();
                that.$message({
                  message: res.data.msg,
                  type: "success"
                });
                that.$refs["addForm"].resetFields();
                that.addFormVisible = false;
                that.gethomeLists();
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

      if (params.redirect_type == "商品详情") {
        params.redirect_type = 1;
      } else if (params.redirect_type == "分类详情") {
        params.redirect_type = 2;
      } else {
        params.redirect_type = 3;
      }
      console.log(params.children_goods_name.split(","));
      if (params.children_goods_name) {
        params.children_goods_name = params.children_goods_name.split(",");
        params.children_goods_id = params.children_goods_id.split(",");
      }

      let children_goods = [];
      for (var i in params.children_goods_name) {
        children_goods.push({
          value: params.children_goods_id[i],
          label: params.children_goods_name[i]
        });
      }

      this.editForm = {
        id: params.t_id,
        theme_name: params.theme_name,
        theme_banner: params.theme_banner,
        redirect_type: params.redirect_type,
        redirect: {
          value: params.redirect_id,
          label: params.redirect_name
        },
        redirect_id: params.redirect_id,
        children_goods_ids: params.children_goods_id,
        children_goods: children_goods,
        sort: params.sort,
        enabled: params.enabled,
        children_goods_ids_id: ""
      };
      if (this.editForm.redirect_type == 3) {
        this.editForm.redirect = {};
      } else {
        this.editForm.redirect = {
          value: params.redirect_id,
          label: params.redirect_name
        };
        this.options6[0] = this.editForm.redirect;
      }
    },
    editSubmit() {
      this.$refs.editForm.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            var that = this;
            var formData = new FormData();
            if (this.editForm.enabled == true) {
              this.editForm.enabled = 1;
            } else {
              this.editForm.enabled = 0;
            }
            formData.append("t_id", this.editForm.id);
            formData.append("enabled", this.editForm.enabled);
            // formData.append('accessToken',this.getToken.accessToken)
            if (this.editForm.img) {
              formData.append("img", this.editForm.img);
            }

            formData.append("theme_name", this.editForm.theme_name);
            formData.append("redirect_type", this.editForm.redirect_type);
            if (this.editForm.redirect_type == 3) {
              formData.append("redirect_id", this.editForm.redirect_id);
              formData.append("redirect_name", "");
            } else {
              formData.append("redirect_id", this.editForm.redirect.value);
              formData.append("redirect_name", this.editForm.redirect.label);
            }
            console.log(this.editForm.children_goods);
            this.editForm.children_goods_ids = [];
            this.editForm.children_goods_name = [];
            for (var i in this.editForm.children_goods) {
              this.editForm.children_goods_ids.push(
                this.editForm.children_goods[i].value
              );
              this.editForm.children_goods_name.push(
                this.editForm.children_goods[i].label
              );
            }
            console.log(this.editForm.children_goods_ids);
            formData.append(
              "children_goods_id",
              this.editForm.children_goods_ids
            );
            formData.append(
              "children_goods_name",
              this.editForm.children_goods_name
            );
            formData.append("carousel_type", 1);
            formData.append("sort", this.editForm.sort);
            if (this.editForm.img) {
              formData.append("img", this.editForm.img);
            }
            edithome(formData).then(res => {
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
            id: params.t_id
          };
          removehomeLists(para).then(res => {
            if (res.data.code == 200) {
              this.$message({
                message: res.data.msg,
                type: "success"
              });
              this.gethomeLists();
            } else {
              this.$message({
                message: res.data.msg,
                type: "warning"
              });
            }
          });
        })
        .catch(() => {});
    },
    addidchange() {
      this.addForm.redirect = {};
    },
    editidchange() {
      this.editForm.redirect = {};
    }
  },
  watch: {
    editFormVisible(curVal, oldVal) {
      if (!curVal) {
        this.gethomeLists();
      }
    },
    addFormVisible(curVal, oldVal) {
      if (!curVal) {
        this.options4 = [];
        this.options5 = [];
        this.addForm = {
          theme_name: "",
          theme_banner: "",
          redirect_type: "",
          redirect_id: "",
          children_goods_ids: [],
          children_goods: [],
          sort: "",
          enabled: false,
          children_goods_ids_id: ""
        };
        // this.$refs['addForm'].resetFields();
      }
    }
  },
  mounted() {
    this.gethomeLists();
  }
};
</script>
<style>
.editimg {
  width: 300px;
  height: 200px;
}
</style>
