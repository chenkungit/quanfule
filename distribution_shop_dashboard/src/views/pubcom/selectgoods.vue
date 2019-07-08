<template>
  <div>
    <el-cascader :options="options" :value="casclass" @change="handleItemChange"></el-cascader>
    <div class="goods">
      <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
      <div style="margin: 15px 0;"></div>
      <el-checkbox-group v-model="goods" v-loading="loading">
        <el-checkbox v-for="good in goodslists" :label="good.sup_id" :key="good.sup_id"><span style="color: red;" v-if="good.presell==1">[预售]</span>{{good.name}}{{good.is_sale==0?'':good.is_sale==1?'（已下架）':'（即将上架）'}}</el-checkbox>
      </el-checkbox-group>
    </div>
    <el-button type="primary" icon="el-icon-plus" @click="addall">新增</el-button>
  </div>
</template>
<style>
</style>
<script>
import { productslistid } from "../../api/api";
export default {
  data() {
    return {
      options: [],
      casclass: [],
      goodslists: [],
      goods: [],
      checkAll: true,
      isIndeterminate: true,
      alldata: [],
      loading: false
    };
  },
  methods: {
    getoptions() {
      this.options = [];
      var a = JSON.parse(sessionStorage.getItem("data"));
      for (var i in a.data.argument.category) {
        let c = {
          label: a.data.argument.category[i].cat_name,
          value: a.data.argument.category[i].cat_id,
          children: []
        };
        for (var j in a.data.argument.category[i].child) {
          let d = {
            label: a.data.argument.category[i].child[j].cat_name,
            value: a.data.argument.category[i].child[j].cat_id
          };
          c.children.push(d);
        }
        this.options.push(c);
      }
    },
    handleItemChange(params) {
      this.loading = true;
      productslistid(params[params.length - 1]).then(res => {
        if (res.data.code == 200) {
          this.goodslists = res.data.data;
          this.loading = false;
        }
      });
    },
    handleCheckAllChange(val) {
      var all = [];
      for (var i in this.goodslists) {
        all.push(this.goodslists[i].sup_id);
      }
      this.goods = val ? all : [];
      this.isIndeterminate = false;
    },
    addall() {
      this.alldata = [];
      for (var i in this.goodslists) {
        for (var j in this.goods) {
          if (this.goods[j] == this.goodslists[i].sup_id) {
            let a = {
              id: this.goodslists[i].sup_id,
              name: this.goodslists[i].name,
              bigid: this.goodslists[i].shp_id
            };
            this.alldata.push(a);
          }
        }
      }
      this.alldata = this.uniqeByKeys(this.alldata, ["id"]);
      this.$emit("faff", this.alldata);
    }
  },
  mounted() {
    this.getoptions();
  }
};
</script>
