<template>
  <div>
    <el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
      <el-form :inline="true">
        <el-form-item>
          <el-button type="primary" @click="handleAdd">新增</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 轮播图列表 -->
    <el-table :data="tableData" border style="width: 100%" v-loading="tableloading">
      <el-table-column prop="carousel_name" label="广告名称">
      </el-table-column>
      <el-table-column label="图片" width="195" v-if="!((carouselPosition==2&&carouselType==1)||carouselPosition==10||carouselPosition==14)">
        <template slot-scope="scope">
          <img :src="scope.row.img_url" height="80" />
        </template>
      </el-table-column>
      <el-table-column prop="img_url" label="园艺视频" v-if="carouselPosition==10">
      </el-table-column>
      <el-table-column prop="redirect_type" label="跳转类型" width="85">
        <template slot-scope="scope">
          <p v-if="scope.row.redirect_type==0">无操作</p>
          <p v-if="scope.row.redirect_type==1">商品详情</p>
          <p v-if="scope.row.redirect_type==2">分类详情</p>
          <p v-if="scope.row.redirect_type==3">活动详情</p>
        </template>
      </el-table-column>
      <el-table-column prop="redirect_id" label="跳转id">
      </el-table-column>
      <el-table-column prop="redirect_name" label="跳转名称" show-overflow-tooltip>
      </el-table-column>
      <el-table-column prop="add_time" label="开始时间" width="140">
      </el-table-column>
      <el-table-column prop="end_time" label="结束时间" width="140">
      </el-table-column>
      <el-table-column prop="sort" label="排序" width="45">
      </el-table-column>
      <el-table-column prop="enabled" label="启用" width="45">
        <template slot-scope="scope">
          <p v-if="scope.row.enabled==0">否</p>
          <p v-if="scope.row.enabled==1">是</p>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="150">
        <template slot-scope="scope">
          <el-button @click="handleEdit(scope.row)">编辑</el-button>
          <el-button type="danger" @click="handleDel(scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog title="新增" :visible.sync="addFormVisible">
      <el-form :model="addForm" label-width="200px" ref="addForm" :rules="Rules">
        <el-form-item label="广告名称" prop="carousel_name">
          <el-input v-model="addForm.carousel_name" maxlength="15" placeholder="不超过15个字符"></el-input>
        </el-form-item>
        <el-form-item label="跳转类型" prop="redirect_type">
          <el-select v-model="addForm.redirect_type" placeholder="请选择" @change="validZero(addForm.redirect_type,0)">
            <el-option v-for="item in selecteOptions" :key="item.value" :label="item.label" :value="item.value" v-if="carouselPosition != 15"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item :label="addForm.redirect_type==1?'跳转商品':addForm.redirect_type==2?'跳转分类':'跳转地址'" prop="redirect_id">
          <el-input v-model="addForm.redirect_id" @blur="validRedirect(addForm.redirect_type,addForm.redirect_id,0)" :placeholder="addForm.redirect_type==1?'跳转商品ID':addForm.redirect_type==2?'跳转分类ID':'跳转地址'"></el-input>
        </el-form-item>
        <el-form-item label="跳转名称" prop="redirect_name">
          <el-input v-model="addForm.redirect_name" disabled></el-input>
        </el-form-item>
        <el-form-item label="开始时间" prop="add_time">
          <el-date-picker v-model="addForm.add_time" type="datetime" placeholder="选择日期时间" value-format="yyyy-MM-dd HH:mm:ss">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="结束时间" prop="end_time">
          <el-date-picker v-model="addForm.end_time" type="datetime" placeholder="选择日期时间" value-format="yyyy-MM-dd HH:mm:ss">
          </el-date-picker>
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
        <el-form-item label="图片" prop="img" v-if="!((carouselPosition==2&&carouselType==1)||carouselPosition==10||carouselPosition==14)">
          <VueImgInputer v-model="addForm.img" theme="light"></VueImgInputer><br>{{desc}}
        </el-form-item>
        <!-- <el-form-item label="视频图片" prop="video_img" v-if="((carouselPosition==2&&carouselType==1)||carouselPosition==10)">
          <VueImgInputer v-model="addForm.video_img" theme="light"></VueImgInputer>
        </el-form-item>
        <el-form-item label="视频地址" prop="img_url" v-if="carouselPosition==10&&carouselPosition!=14">
          <el-input v-model="addForm.img_url"></el-input>
        </el-form-item> -->
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="addFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="addSubmit">提交</el-button>
      </div>
    </el-dialog>
    <!--编辑界面-->
    <el-dialog title="编辑" :visible.sync="editFormVisible">
      <el-form :model="editForm" label-width="200px" ref="editForm" :rules="Rules">
        <el-form-item label="广告名称" prop="carousel_name">
          <el-input v-model="editForm.carousel_name" maxlength="15" placeholder="不超过15个字符"></el-input>
        </el-form-item>
        <el-form-item label="跳转类型" prop="redirect_type">
          <el-select v-model="editForm.redirect_type" placeholder="请选择" @change="validZero(editForm.redirect_type,1)">
            <el-option v-for="item in selecteOptions" :key="item.value" :label="item.label" :value="item.value" v-if="carouselPosition != 15"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item :label="editForm.redirect_type==1?'跳转商品':editForm.redirect_type==2?'跳转分类':'跳转地址'" prop="redirect_id">
          <el-input v-model="editForm.redirect_id" @blur="validRedirect(editForm.redirect_type,editForm.redirect_id,1)" :placeholder="editForm.redirect_type==1?'跳转商品ID':editForm.redirect_type==2?'跳转分类ID':'跳转地址'"></el-input>
        </el-form-item>
        <el-form-item label="跳转名称" prop="redirect_name">
          <el-input v-model="editForm.redirect_name" disabled></el-input>
        </el-form-item>
        <el-form-item label="开始时间" prop="add_time">
          <el-date-picker v-model="editForm.add_time" type="datetime" placeholder="选择日期时间" value-format="yyyy-MM-dd HH:mm:ss">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="结束时间" prop="end_time">
          <el-date-picker v-model="editForm.end_time" type="datetime" placeholder="选择日期时间" value-format="yyyy-MM-dd HH:mm:ss">
          </el-date-picker>
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
        <el-form-item label="图片" v-if="!((carouselPosition==2&&carouselType==1)||carouselPosition==10||carouselPosition==14)">
          <VueImgInputer v-model="editForm.img" theme="light" :imgSrc='editForm.img_url'></VueImgInputer><br>{{desc}}
        </el-form-item>
        <!-- <el-form-item label="视频图片" prop="video_img" v-if="((carouselPosition==2&&carouselType==1)||carouselPosition==10)">
          <VueImgInputer v-model="editForm.video_img" theme="light" :imgSrc='editForm.video_img'></VueImgInputer>
        </el-form-item>
         <el-form-item label="视频地址" prop="img_url" v-if="carouselPosition==10&&carouselPosition!=14">
          <el-input v-model="editForm.img_url"></el-input>
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
import { carouselList } from "../../../api/api";
import { carouselAdd } from "../../../api/api";
import { removecarousel } from "../../../api/api";
import { idSearchnew } from "../../../api/api";
import { editcarousel } from "../../../api/api";
export default {
  data() {
    return {
      tableData: [],
      tableloading: false,
      addFormVisible: false,
      addForm: {
        carousel_name: "",
        redirect_type: 1,
        redirect_id: "",
        redirect_name: "",
        add_time: "",
        end_time: "",
        sort: "",
        enabled: 1,
        carousel_position: this.carouselPosition,
        carousel_type: this.carouselType,
        img: "",
        img_url: "",
        video_img: ""
      },
      editFormVisible: false,
      editForm: {},
      selecteOptions: [
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
      fileListVideo: [], //视频图片列表
      Rules: {
        carousel_name: [
          {
            required: true,
            message: "广告名称",
            trigger: "blur"
          },
          { min: 0, max: 15, message: "长度在 0 到 15 个字符", trigger: "blur" }
        ],
        redirect_type: [
          {
            required: true,
            message: "跳转类型",
            trigger: "change"
          }
        ],
        redirect_id: [
          {
            required: true,
            message: "跳转地址",
            trigger: "blur"
          }
        ],
        redirect_name: [
          {
            required: true,
            message: "跳转地址错误",
            trigger: "blur"
          }
        ],
        add_time: [
          {
            required: true,
            message: "开始时间",
            trigger: "change"
          }
        ],
        end_time: [
          {
            required: true,
            message: "结束时间",
            trigger: "change"
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
        ]
      }
    };
  },
  props: ["carouselPosition", "carouselType", "desc"],
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

    validZero(redirect_type, type) {
      this.addForm.redirect_name = "";
      this.addForm.redirect_id = "";
      this.editForm.redirect_name = "";
      this.editForm.redirect_id = "";
      if (redirect_type == 0) {
        if (type) {
          this.editForm.redirect_name = "无操作";
          this.editForm.redirect_id = "无操作";
        } else {
          this.addForm.redirect_name = "无操作";
          this.addForm.redirect_id = "无操作";
        }
      }
    },

    getData() {
      this.tableloading = true;
      carouselList({
        position: this.carouselPosition,
        carousel_type: this.carouselType
      }).then(res => {
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
            var fd = new FormData();
            for (let o in this.addForm) {
              if (
                o == "img" &&
                this.carouselPosition == 2 &&
                this.carouselType == 1
              ) {
              } else {
                fd.append(o, this.addForm[o]);
              }
            }
            carouselAdd(fd).then(res => {
              if (res.code == 200) {
                this.$message({
                  message: res.msg,
                  type: "success"
                });
                this.$refs["addForm"].resetFields();
                this.addFormVisible = false;
                this.getData();
              } else {
                this.$message({
                  message: res.msg,
                  type: "warning"
                });
              }
            });
            // }
          });
        }
      });
    },

    handleEdit(params) {
      this.editFormVisible = true;
      this.editForm = params;
      this.editForm.img = "";
    },

    editSubmit() {
      var _this = this;
      this.$refs.editForm.validate(valid => {
        if (valid) {
          this.$confirm("确认提交吗？", "提示", {}).then(() => {
            var formData = new FormData();
            for (let o in this.editForm) {
              if (_this.carouselPosition == 10) {
                if (o != "img") {
                  formData.append(o, this.editForm[o]);
                }
              } else {
                if (o == "img" || o == "img_url") {
                } else {
                  formData.append(o, this.editForm[o]);
                }
              }
            }
            if (this.editForm.img.type) {
              formData.append("img", this.editForm.img);
            }
            editcarousel(formData).then(res => {
              if (res.code == 200) {
                this.editFormVisible = false;
                this.$message({
                  message: res.msg,
                  type: "success"
                });
                this.$refs["editForm"].resetFields();
                this.getData();
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

    changeUploadVideo(file, fileList) {
      this.fileListVideo = fileList;
    },

    handleExceedVideo(files, fileList) {
      this.$message.warning(
        `当前限制选择 1 个文件，本次选择了 ${
          files.length
        } 个文件，共选择了 ${files.length + fileList.length} 个文件`
      );
    },

    handleDel(params) {
      this.$confirm("确认删除该记录吗?", "提示", {
        type: "warning"
      })
        .then(() => {
          removecarousel({
            id: params.carousel_id
          }).then(res => {
            if (res.code == 200) {
              this.$message({
                message: res.msg,
                type: "success"
              });
              this.getData();
            } else {
              this.$message({
                message: res.msg,
                type: "warning"
              });
            }
          });
        })
        .catch(() => {});
    }
  },
  mounted() {
    this.getData();
  }
};
</script>
