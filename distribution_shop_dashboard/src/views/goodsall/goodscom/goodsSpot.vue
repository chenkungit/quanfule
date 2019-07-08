<template>
    <div>
      <el-row>
        <el-col :span="24" class="toolbar" style="100%">
          <el-form :inline="true">
            <el-form-item>
              <el-button type="warning" icon="el-icon-back"  @click="goBack()">返回</el-button>
            </el-form-item>
          </el-form>
        </el-col>
      </el-row>

      <table style="width: 100%;" id="parent">
        <!--标题显示-->
        <tr>
          <td v-for="item in attrLists" v-if="isMultiple">{{item.attr_name}}</td>
          <!-- <td>货号</td> -->
          <td>库存</td>
          <td v-show="isMultiple">价格</td>
          <td v-show="isMultiple">重量</td>
          <td style="width: 110px;">操作</td>
        </tr>
        <!--显示已成功添加的-->
        <tr v-for="item in tableData">
          <td v-for="i in item.attr" v-if="isMultiple">{{i}}</td>
          <!-- <td>{{item.product_sn}}</td> -->
          <td>{{item.stock}}</td>
          <td v-show="isMultiple">{{item.price}}</td>
          <td v-show="isMultiple">{{item.weight}}</td>
          <td>
            <el-button @click="handleDel(item)" v-show="isMultiple"  style="float: left">-</el-button>
            <el-button @click="handleEdit(item)"  style="float: left">编辑</el-button>
          </td>
        </tr>
        <!--减-->
        <tr  v-for="(item,index) in attrTable" :key="index">
          <td v-for="(itm,idx) in item.attrList" :key="idx">
            <el-select v-model="form[index].attrList[idx].value" value-key="id" placeholder="请选择">
              <el-option v-for="v in itm.attr_values" :key="Math.random()" :label="v.value" :value="v.id"></el-option>
            </el-select>
          </td>
          <!-- <td>
            <input type="text" v-model="form[index].product_sn">
          </td> -->
          <td>
            <input type="text" v-model="form[index].stock">
          </td>
          <td>
            <input type="text" v-model="form[index].price">
          </td>
          <td>
            <input type="text" v-model="form[index].weight">
          </td>
          <td>
            <el-button @click="delAttr(index)"  style="float: left">-</el-button>
          </td>
        </tr>
        <!--加-->
        <tr v-show="isMultiple">
          <td v-for="(item,i) in attrLists" v-if="item.attr_type==1" :key="i">
            <el-select v-model="attr[i]" placeholder="请选择">
              <el-option v-for="v in item.attr_values" :key="v.id" :label="v.value" :value="v.id">
              </el-option>
            </el-select>
          </td>
          <!-- <td>
            <input type="text" v-model="products.product_sn">
          </td> -->
          <td>
            <input type="text" v-model="products.stock">
          </td>
          <td>
            <input type="text" v-model="products.price">
          </td>
          <td>
            <input type="text" v-model="products.weight">
          </td>
          <td>
            <el-button @click="addAttr" style="float: left">+</el-button>
          </td>
        </tr>
      </table>
      <div style="text-align:center;padding-top:20px;">
        <el-button type="primary" @click="handleAdd">确 定</el-button>
      </div>

      <el-dialog title="编辑" :visible.sync="editFormVisible" :close-on-click-modal="false">
        <el-form :model="editForm" label-width="150px" ref="editForm">
          <!-- <el-form-item label="货号" prop="product_sn" v-show="isMultiple">
            <el-input type="text" v-model="editForm.product_sn"></el-input>
          </el-form-item> -->
          <el-form-item label="库存" prop="stock">
            <el-input type="text" v-model="editForm.stock"></el-input>
          </el-form-item>
          <el-form-item label="价格" prop="price" v-if="isMultiple">
            <el-input type="text" v-model="editForm.price"></el-input>
          </el-form-item>
          <el-form-item label="重量" prop="weight" v-if="isMultiple">
            <el-input type="text" v-model="editForm.weight"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="editFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="editSubmit">确 定</el-button>
        </div>
      </el-dialog>
    </div>
</template>

<script>
    import {goodsSpotLists,spotAttrLists,goodsSpotAdd,goodsSpotEdit,goodsSpotDel} from "../../../api/api.js"
    export default {
        name: "goods-spot",
        data(){
          return{
            id:"",
            list:[],
            attrLists:[],
            tableData:[],
            editFormVisible:false,
            editForm:{},
            isMultiple:false,
            form:[],
            attrTable:[],
            products:{
              product_sn:1,
              stock:"",
              price:"",
              weight:"",
            },
            attr:[],
          }
        },
        methods:{
          getData(){
            spotAttrLists({
              goods_id:this.id
            }).then(res=>{
              if(res.data.code == 200){
                if(this.isMultiple){
                  //数据处理，后添加的属性没有值，则添加空值
                  let list = res.data.data.product_list;
                  list.forEach((item,index)=>{
                    let attr = [];
                    for(var i in this.attrLists){
                      for(var j in item.goods_attr){
                        if(this.attrLists[i].attr_name == item.goods_attr[j].attr){
                          attr.push(item.goods_attr[j].value);
                          break;
                        }
                        if(j == item.goods_attr.length-1){
                          attr.push("")
                        }
                      }
                    }
                    item.attr = attr;
                  })
                  this.tableData = list;
                }else{
                  this.tableData = res.data.data.goods;
                }
              }
            })
          },
          getAttr(){
            goodsSpotLists({
              goods_id:this.id
            }).then(res=>{
              if(res.data.code == 200){
                this.attrLists = [];
                for(var i in res.data.data){
                  if(res.data.data[i].attr_type == 1){
                    this.attrLists.push({
                      attr_id: res.data.data[i].attr_id,
                      attr_name: res.data.data[i].attr_name,
                      attr_values: res.data.data[i].attr_values,
                      attr_type: res.data.data[i].attr_type,
                      value:"",
                    });
                  }
                }
                this.list = this.attrLists;
                this.attrLists.forEach((item)=>{
                  if(item.attr_type == 1){
                    this.isMultiple = true;
                  }
                });
                this.getData();
              }
            })
          },
          addAttr(){
            this.attrTable.push({
              product_sn:1,
              stock:"",
              price:"",
              weight:"",
              attrList:this.list
            });
            this.form = JSON.parse(JSON.stringify(this.attrTable));//vue爬坑，不改变原对象
          },
          handleAdd(){
            this.$confirm("确认提交吗？", "提示", {}).then(() => {
              let params={goods_id:this.id,products:[] };
              this.products.attr_id = this.attr;
              if(this.attr.length<this.attrLists.length){
                this.attr.push("");
              }
              for(var i = 0; i < this.attr.length; i++) {
                if(typeof(this.attr[i]) == "undefined"){
                  this.attr[i] = "";
                }
              }
              params.products.push(this.products);
              this.form.forEach((item,index)=>{
                let attr = [];
                item.attrList.forEach((v,i)=>{
                  attr.push(v.value);
                });
                params.products.push({
                  attr_id:attr,
                  product_sn:item.product_sn,
                  stock:item.stock,
                  price:item.price,
                  weight:item.weight
                })
              });
              goodsSpotAdd(params).then(res=>{
                if(res.data.code == 200){
                  this.$message({
                    message: res.data.msg,
                    type: "success"
                  });
                  this.getData();
                  this.$router.go(-1);
                }else{
                  this.$message({
                    message: res.data.msg,
                    type: "warning"
                  });
                }
              })
            })
          },
          handleEdit(params){
            this.editFormVisible = true;
            this.editForm = params;
          },
          editSubmit(){
            this.$confirm("确认提交吗？", "提示", {}).then(() => {
              this.editForm.number = this.editForm.stock;
              goodsSpotEdit(this.editForm).then(res=>{
                if(res.data.code == 200){
                  this.$message({
                    message: res.data.msg,
                    type: "success"
                  });
                  this.getData();
                  this.editFormVisible = false;
                }else{
                  this.$message({
                    message: res.data.msg,
                    type: "warning"
                  });
                }
              })
            })
          },
          delAttr(index){
            this.$confirm("确认删除吗？", "提示", {}).then(() => {
              this.attrTable.splice(index,1);
              this.form = JSON.parse(JSON.stringify(this.attrTable));
            });
          },
          handleDel(params){
            this.$confirm("确认删除吗？", "提示", {}).then(() => {
              goodsSpotDel({
                product_id:params.product_id
              }).then(res=>{
                if(res.data.code == 200){
                  this.$message({
                    message: res.data.msg,
                    type: "success"
                  });
                  this.getData();
                }else{
                  this.$message({
                    message: res.data.msg,
                    type: "warning"
                  });
                }
              })
            });
          },
          goBack() {
            this.$router.go(-1);
          },
        },
        created(){
          this.id = this.$route.params.id;
          this.getAttr();
          // this.getData();
        },
        watch:{
          form:{
            deep:true,
            handler(curval,newval){
              this.attrTable = JSON.parse(JSON.stringify(this.form));
            }
          }
        }
    }
</script>

<style scoped>
  table{
    color: #606266;
    border-collapse: collapse;
  }
  table,table tr th, table tr td {
    border:1px solid #ebeef5;
    text-align: center;
  }
</style>
