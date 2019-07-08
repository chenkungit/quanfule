<template>
  <div>
    <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item>
          <!-- <el-button type="primary" @click="handleAdd">新增</el-button> -->
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 列表 -->
    <el-table :data="tableData" border style="width: 100%" v-loading="tableloading" element-loading-text="拼命加载中" row-key="user_id" lazy :load="load"
    :tree-props="{children: 'children', hasChildren: 'user_id'}">
      <!-- <el-table-column prop="map_store_id" label="门店ID">
      </el-table-column> -->
      <el-table-column prop="vip_code" label="会员ID">
      </el-table-column>
      <el-table-column prop="user_name" label="用户名">
      </el-table-column>
      <el-table-column prop="vip_setting_name" label="会员等级名称">
      </el-table-column>
    </el-table>
    <!-- 分页 -->

  </div>
</template>
<script>
import { topuserLists, downuserLists } from "../../api/api";
export default {
  data() {
    return {
      tableData: [],
      tableloading: true
    };
  },
  methods: {
    getData() {
      //获取数据
      this.tableloading = true;
      topuserLists().then(res => {
        if (res.code == 200) {
          this.tableData = res.data.collection;
          this.tableloading = false;
        }
      });
    },
    load(tree, treeNode, resolve) {
      console.log(treeNode);
      downuserLists({ down_user_id: tree.user_id }).then(res => {
        if (res.code == 200) {
          resolve(res.data.collection);
        }
      });
    }
  },
  mounted() {
    this.getData();
  }
};
</script>
