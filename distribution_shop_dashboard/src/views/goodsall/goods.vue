<template>
  <div>
    <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item>
          <el-button type="primary" @click="handleAdd">新增</el-button>
        </el-form-item>
        <el-form-item>
          <el-input v-model="keywords" placeholder="输入关键字" clearable></el-input>
        </el-form-item>
        <!-- <el-form-item>
          <el-input v-model="code" placeholder="输入k3码" clearable></el-input>
        </el-form-item> -->
        <el-form-item>
          <el-cascader :options="categoryOptions" v-model="category" :props="props"   @change="getGoods"  placeholder="请选择商品分类">
          </el-cascader>
        </el-form-item>
        <!-- <el-form-item>
          <el-select v-model="brand" placeholder="请选择" @change="getGoods">
            <el-option v-for="item in brandoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item> -->
        <!-- <el-form-item>
          <el-select v-model="suppliers" placeholder="请选择" @change="getGoods">
            <el-option v-for="item in suppliersoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item> -->
        <el-form-item>
          <el-select v-model="is_sale" placeholder="请选择" @change="getGoods">
            <el-option v-for="item in is_saleoptions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="getGoods">搜索</el-button>
        </el-form-item>
        <el-form-item>
          <el-button icon="el-icon-printer" @click="allEditStatus">批量修改状态</el-button>
        </el-form-item>
        <!-- <el-form-item>
          <el-button class="el-icon-delete" type="danger" @click="handleClear">清除商品缓存</el-button>
        </el-form-item> -->
        <el-form-item>
          <el-button class="el-icon-delete" type="danger" @click="handleClearList">清除列表缓存</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <el-table :data="goodData" border style="width: 100%" v-loading="tableloading" element-loading-text="拼命加载中" @cell-dblclick="handleSort" @selection-change="handleslecttable">
      <el-table-column type="selection" width="55"></el-table-column>
      <el-table-column prop="id" label="ID" width="100"></el-table-column>
      <!-- <el-table-column prop="code" label="货号" width="110"></el-table-column> -->
      <el-table-column prop="name" label="名称">
      </el-table-column>
      <el-table-column prop="cat_name" label="类别" width="70"></el-table-column>
      <!-- <el-table-column prop="brand_name" label="品牌" width="70"></el-table-column>
      <el-table-column prop="suppliers_name" label="供应商" width="80"></el-table-column> -->
      <el-table-column prop="price" label="售价" width="65"></el-table-column>
      <el-table-column prop="stock" label="现货库存" width="95" sortable></el-table-column>
      <el-table-column label="排序" prop="ord" width="80" sortable>
        <template slot-scope="scope">
          <el-input v-if="scope.row.isSort" size="mini" placeholder="请输入排序" v-model="scope.row.ord" v-focus @blur="inputBlur(scope.row)"></el-input>
          <p v-if="!scope.row.isSort">{{scope.row.ord}}</p>
        </template>
      </el-table-column>
      <el-table-column prop="is_sale" label="上架" width="65">
        <template slot-scope="scope">
          <span :style="scope.row.is_sale==0?'color:green':scope.row.is_sale==1?'color:red':'color:#ff7e00'">{{scope.row.is_sale==0?'上架中':scope.row.is_sale==1?'已下架':scope.row.is_sale==2?'即将上架':'暂无现货'}}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="300">
        <template slot-scope="scope">
          <el-button icon="el-icon-edit" @click="handleEdit(scope.row)" style="margin-top: 6px;margin-left: 0">编辑</el-button>
          <el-button @click="handleSpot(scope.row)" style="margin-top: 6px;">现货管理</el-button>
          <!-- <el-button icon="el-icon-delete" type="danger" @click="handleDel(scope.row)" style="margin-top: 6px;">删除</el-button> -->
        </template>
      </el-table-column>
    </el-table>
    <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="page" :page-sizes="[50, 100, 200, 400]"
        :page-size="50" layout="total, sizes, prev, pager, next, jumper" :total="count">
      </el-pagination>
    </div>
    <el-dialog title="修改商品状态" :visible.sync="editStatusVisible">
      <el-select v-model="is_sale_edit" placeholder="请选择">
      <el-option v-for="item in is_saleEditoptions" :key="item.value" :label="item.label" :value="item.value">
      </el-option>
      </el-select>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="editStatusVisible = false">取消</el-button>
        <el-button type="primary" @click.native="editStatusSubmit">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<style>
</style>
<script>
import { goodsLists,goodsSort } from "../../api/api";
import {clearCache,clearCacheList } from "../../api/api";
import {goodsUpdate,goodsDelRest } from "../../api/api";
export default {
  data() {
    return {
      grade:"",
      goodData: [],
      tableloading: true,
      page: 1,
      code: "",
      pagecount: 0,
      limit: 50,
      count: 0,
      keywords: "",
      categoryOptions: [],
      props: {
        label:'cat_name',
        value: 'cat_id',
        children: 'child'
      },
      category: [],
      // brandoptions: [],
      // brand: 0,
      // suppliersoptions: [],
      // suppliers: 0,
      is_saleoptions: [
        {
          label: "所有",
          value: 99
        },
        {
          label: "上架",
          value: 0
        },
        {
          label: "下架",
          value: 1
        },
        // {
        //   label: "即将上架",
        //   value: 2
        // },
        // {
        //   label: "暂无现货",
        //   value: 3
        // }
      ],
      is_sale: 99,
      is_sale_edit:'',
      sortForm:{},
      push_ids: [],
      is_saleEditoptions: [
        {
          label: "上架",
          value: 0
        },
        {
          label: "下架",
          value: 1
        },
        // {
        //   label: "即将上架",
        //   value: 2
        // },
        // {
        //   label: "暂无现货",
        //   value: 3
        // }
      ],
      editStatusVisible:false,
    };
  },
  methods: {
    getoptions() {
      var a = JSON.parse(sessionStorage.getItem("data"));
      this.categoryOptions = a.data.argument.category;
      // this.brandoptions = [
      //   {
      //     label: "所有品牌",
      //     value: 0
      //   }
      // ];
      // a.data.argument.brand.map(item => {
      //   this.brandoptions.push(item);
      // });
      // this.suppliersoptions = [
      //   {
      //     label: "所有供应商",
      //     value: 0
      //   }
      // ];
      // a.data.argument.suppliers.map(item => {
      //   this.suppliersoptions.push(item);
      // });
    },
    getGoods() {
      this.tableloading = true;
      let params = {};
      if (this.keywords != "") params.keywords = this.keywords;
      params.page = this.page;
      params.limit = this.limit;
      if (this.code != "") params.code = this.code;
      if (this.category.length == 2) params.category = this.category[this.category.length - 1];
      if (this.brand != 0) params.brand = this.brand;
      if (this.suppliers != 0) params.supplier = this.suppliers;
      if (this.is_sale != 99) params.is_sale = this.is_sale;
      goodsLists(params).then(res => {
        if (res.data.code == 200) {
          this.goodData = res.data.data.item;
          this.pagecount = res.data.data.pagecount;
          this.count = res.data.data.count;
          this.tableloading = false;
        }
      });
    },
    handleslecttable(params) {
      this.push_ids = params.map(item => {
        return item.id;
      });
    },
    handleSort(row, column){
      if(column.label=="排序"){
        this.$set(row,"isSort",true);
        this.sortForm.id = row.id;
      }
    },
    inputBlur(row){
      this.$set(row,"isSort",false);
      this.sortForm.ord = Number(row.ord);
      goodsSort(this.sortForm).then(res => {
        if (res.data.code == 200) {
          this.$message({
            message: res.data.msg,
            type: "success"
          });
        } else {
          this.$message({
            message: res.data.msg,
            type: "warning"
          });
        }
      });
    },
    handleCurrentChange(val) {
      this.page = val;
      this.getGoods();
    },
    handleSizeChange(size) {
      this.limit = size;
      this.getGoods();
    },
    handleAdd(){
      this.$router.push({path:'/goods/add'});
    },
    handleEdit(params) {
      this.$router.push({
        name: "商品编辑",
        params: params,
        query: {id: params.id}
      });
    },
    allEditStatus(){
      this.editStatusVisible = true;
    },
    editStatusSubmit(){
      this.$confirm("确认修改选中商品的状态?", "提示", {
        type: "warning"
      }).then(() => {
        goodsUpdate({
          is_sale:this.is_sale_edit,
          gsup_id_arr:this.push_ids
        }).then(res=>{
          if(res.code == 200){
            this.$message({
              message: res.msg,
              type: "success"
            });
            this.editStatusVisible = false;
            this.is_sale_edit = '';
            this.getGoods();
          }else {
            this.$message({
              message: res.msg,
              type: "warning"
            });
          }
        })
      });
    },
    handleSpot(params){
      this.$router.push({path:'/goods/spot/' + params.id});
    },
    handleDel(params){
      this.$confirm("确认删除该商品吗?", "提示", {
        type: "warning"
      }).then(() => {
        goodsDelRest({
          id:params.id
        }).then(res=>{
          if(res.data.code == 200){
            this.$message({
              message: res.data.msg,
              type: "success"
            });
            this.getGoods();
          }else{
            this.$message({
              message: res.data.msg,
              type: "warning"
            });
          }
        })
      })
    },
    handleClear(){
      this.$confirm("确认清除吗?", "提示", {
        type: "warning"
      }).then(() => {
        let para = {
          arg: "Goods_id:*"
        };
        clearCacheList(para).then(res => {
          if (res.code == 200) {
            this.$message({
              message: res.msg,
              type: "success"
            });
            this.getGoods();
          } else {
            this.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    },
    handleClearList(){
      this.$confirm("确认清除吗?", "提示", {
        type: "warning"
      }).then(() => {
        let para = {
          arg: "product_list_index:*"
        };
        clearCache(para).then(res => {
          if (res.code == 200) {
            this.$message({
              message: res.msg,
              type: "success"
            });
            this.getGoods();
          } else {
            this.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    },
  },
  mounted() {
    this.getGoods();
    this.getoptions();
  },
  activated() {
    this.getGoods();
    this.getoptions();
  },
};
</script>
