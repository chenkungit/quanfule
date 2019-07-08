<template>
    <div>
      <el-form label-width="150px" >
        <el-form-item label="商品类型">
          <el-select v-model="editAttrForm.cat_id" placeholder="请选择"  @change="getAttrLists">
            <el-option v-for="item in typeOptions" :key="item.value" :label="item.label" :value="item.value"></el-option>
          </el-select>
        </el-form-item>

        <div class="mb15" v-for="(item,index) in attrLists" :key="index">
          <tr>
            <td class="label">
              <i class="el-icon-plus" style="cursor: pointer" v-if="item.attr_type==1&&item.isFirst" @click="addAttr(item,index)"></i>
              <i class="el-icon-minus" style="cursor: pointer" v-if="item.attr_type==1&&!item.isFirst" @click="decreaseAttr(1,index)"></i>
              {{item.attr_name}}
            </td>
            <td class="color-gray">
              <el-select filterable allow-create default-first-option v-model="item.attrValues" clearable placeholder="请选择" @change="changeAttrValue(index)" v-if="item.attr_values !=''">
                <el-option v-for="(v,i) in item.values" :key="i" :label="v" :value="v"></el-option>
              </el-select>
              <el-input v-model="item.attr_value" placeholder="请输入内容" v-if="item.attr_values ==''"></el-input>
            </td>
            <td v-if="item.img_url!=null">
              <img :src="item.img_url" alt="" style="width: 40px;height: 40px;padding: 0 10px;margin-bottom: -10px;">
            </td>
            <td v-if="item.attr_type==1">
              <input :id="index+item.attr_id+'upload'" type="file" class="file" name="name" @change="changeImg(index,$event)">
            </td>
          </tr>

          <div  class="mt15" v-for="(its,idx) in item.attrList" :key="idx">
            <tr>
              <td class="label">
                <i class="el-icon-minus" style="cursor: pointer" v-if="its.attr_type==1" @click="decreaseAttr(2,index,idx)"></i>{{its.attr_name}}
              </td>
              <td class="color-gray">
                <el-select filterable allow-create default-first-option v-model="form[index].attrList[idx].attrValue" @change="change(index)" placeholder="请选择" v-if="its.attr_values !=''">
                  <el-option v-for="(m,n) in its.values" :key="n" :label="m" :value="m"></el-option>
                </el-select>
                <el-input v-model="form[index].attrList[idx].attrValue" placeholder="请输入内容" v-if="its.attr_values ==''"></el-input>
              </td>
              <td v-if="its.attr_type==1">
                <!--:id="index+'upload'+idx"-->
                <!--v-model="form[index].attrList[idx].attrImg"-->
                <!--v-model="form[index].attrList[idx].file"-->
                <input type="file" class="file" @change="changeImg(index,$event,idx)">
              </td>
            </tr>
          </div>
        </div>

        <div style="text-align:center;padding-top:20px;">
          <el-button type="primary" @click.native="editSubmit5">确定</el-button>
        </div>
      </el-form>
    </div>
</template>

<script>
    import {goodsEditLists,goodsAttrLists,seletedGoodsattr,editGoods} from "../../../../api/api";
    import {uploadImg} from "../../../../api/api";
    export default {
        name: "edit-attr",
        data(){
          return{
            typeOptions:[
              {
                label: "请选择",
                value: 0
              }
            ],
            editAttrForm:{
              cat_id:"",
              original_cat_id:"",
            },
            attrLists:[],
            goods_attr:[],
            form:[],
            sontabel: [],
            is_table: false,
            originalCatId:"",
            fileIdArr:[],
          }
        },
        methods:{
          getGoodEditList(){
            goodsEditLists({
              id:this.$route.query.id
            }).then(res=>{
              if(res.data.code == 200){
                this.editAttrForm.cat_id = res.data.data.goods_info.cat_id;
                this.originalCatId = res.data.data.goods_info.cat_id;
                this.editAttrForm.original_cat_id = res.data.data.goods_info.original_cat_id;
                this.editData = res.data.data.goods_info;
                this.goods_attr = res.data.data.goods_attr;
                this.getAttrLists();
              }
            })
          },
          getAttrLists(){
            if(this.editAttrForm.cat_id == 0){

            }else{
              this.fileIdArr = [];
              goodsAttrLists({
                cat_id:this.editAttrForm.cat_id
              }).then(res=>{
                if(res.data.code == 200){
                  let attrs = res.data.data;
                  let arr = [];
                  let isExit = false;
                  for(var i in attrs){
                    for(var j in this.goods_attr){
                      if(attrs[i].attr_id == this.goods_attr[j].attr_id){
                        arr.push({...attrs[i],...this.goods_attr[j]});
                        isExit = true;
                      }
                    }
                    if(!isExit){
                      arr.push(attrs[i])
                    }
                    isExit = false;
                  }
                  let id = arr[0].attr_id;
                  // let defaultValue = ["请选择"];
                  arr.forEach((item,index)=>{
                    // item.values = defaultValue.concat(item.attr_values.split("\n"));
                    item.values = item.attr_values.split("\n");
                    if(item.img_url!=null)item.img_url = item.img_url;
                    item.attrValues = item.attr_value;
                    item.showFile = "";
                    if(index == 0){
                      item.isFirst = true;
                      item.attrList = [];
                    }else{
                      if(item.attr_id == id){
                        item.isFirst = false;
                      }else{
                        item.isFirst = true;
                        item.attrList = [];
                      }
                      id = item.attr_id;
                    }
                  });
                  this.attrLists = arr;
                  // console.log( this.attrLists)
                }
              })
            }
          },
          editSubmit5(){
            this.$confirm("确认提交吗？", "提示", {}).then(() => {
              var attr = [];
              //数据处理，没有默认值的，赋值空字符串
              for(var i in this.attrLists){
                let data1 = {};
                this.attrLists[i].attr_id?(data1.attr_id=this.attrLists[i].attr_id):(data1.attr_id="");
                this.attrLists[i].attrValues?(data1.attr_value=this.attrLists[i].attrValues):(data1.attr_value="");
                this.attrLists[i].goods_attr_id?(data1.goods_attr_id=this.attrLists[i].goods_attr_id):(data1.goods_attr_id="");
                this.attrLists[i].img_id?(data1.img_id=this.attrLists[i].img_id):(data1.img_id="");
                this.attrLists[i].attr_img?(data1.img_url=this.attrLists[i].attr_img):
                  (this.attrLists[i].img_url!=null?data1.img_url=this.attrLists[i].img_url:data1.img_url="");
                if(this.attrLists[i].attrValues!=undefined){
                  attr.push(data1);
                }
                if(this.attrLists[i].attrList&&this.attrLists[i].attrList.length!=0){
                  for(var j in this.attrLists[i].attrList){
                    let data2 = {};
                    this.attrLists[i].attrList[j].attr_id?(data2.attr_id=this.attrLists[i].attrList[j].attr_id):(data2.attr_id="");
                    this.attrLists[i].attrList[j].attrValue?(data2.attr_value=this.attrLists[i].attrList[j].attrValue):(data2.attr_value="");
                    this.attrLists[i].attrList[j].goods_attr_id?(data2.goods_attr_id=this.attrLists[i].attrList[j].goods_attr_id):(data2.goods_attr_id="");
                    this.attrLists[i].attrList[j].img_id?(data2.img_id=this.attrLists[i].attrList[j].img_id):(data2.img_id="");
                    this.attrLists[i].attrList[j].attr_img?(data2.img_url=this.attrLists[i].attrList[j].attr_img):
                      (this.attrLists[i].attrList[j].img_url!=null?data2.img_url=this.attrLists[i].attrList[j].img_url:data2.img_url="");
                    if(this.attrLists[i].attrList[j].attrValue!=""){
                      attr.push(data2);
                    }
                  }
                }
              }
              // console.log(attr)
              let params = {
                type:5,
                id: this.$route.query.id,
                cat_id: this.editAttrForm.cat_id,
                original_cat_id: this.originalCatId,
                attr:JSON.stringify(attr)
              };
              editGoods(params).then(res=>{
                if(res.code == 200){
                  this.$message({
                    message: res.msg,
                    type: "success"
                  });
                  this.getGoodEditList();
                }else{
                  this.$message({
                    message: res.msg,
                    type: "warning"
                  });
                }
              })
            })
          },
          changeAttrValue(index){
            this.$set(this.attrLists,index,this.attrLists[index])
          },
          //获取商品类型
          getGoodType(){
            seletedGoodsattr().then(res=>{
              if(res.data.code == 200){
                this.typeOptions = this.typeOptions.concat(res.data.data.list);
              }
            })
          },
          addAttr(item,index){
            let {attrList,...obj} = item;
            this.attrLists[index].attrList.push(obj);
            this.$set(this.attrLists, index, this.attrLists[index]);
          },
          decreaseAttr(flag,index,idx){
            if(flag == 1){
              console.log(2)
              // var self = document.getElementById(index+this.attrLists[index].attr_id+"upload");
              // var parent = self.parentElement;
              // console.log(parent)
              // parent.removeChild(self);
              this.attrLists.splice(index,1);
              this.$set(this.attrLists,index,this.attrLists[index]);
            }else{
              this.attrLists[index].attrList.splice(idx,1);
              this.$set(this.attrLists, index, this.attrLists[index]);
            }
            console.log(this.attrLists)
          },
          changeImg(index,e,idx){
            var img = e.target.files[0];
            console.log(img)
            var formdata = new FormData();
            formdata.append("img",img);
            uploadImg(formdata).then(res=>{
              if(res.data.code == 200){
                if(idx !=null){
                  this.attrLists[index].attrList[idx].attr_img = res.data.data.img_url;
                }else{
                  this.attrLists[index].attr_img = res.data.data.img_url;
                  this.attrLists[index].showFile = e.target.files[0].name;
                }
                console.log(this.attrLists)
              }else{
                this.$message({
                  message: res.data.msg,
                  type: "warning"
                });
              }
            })
          },
          change(index){
            this.$set(this.form,index,this.form[index])
          },
        },
        mounted(){
          this.getGoodEditList();
          this.getGoodType();
        },
        watch:{
          attrLists:{
            handler(newValue,oldValue) {
              this.form = newValue;
            },
          }
      },
    }
</script>

<style scoped>
  .label{
    width: 135px;text-align: right;color: #606266;padding-right: 15px
  }
  .color-gray{
    color: #606266;
  }
  .mb15{
    margin-bottom: 15px;
  }
  .mt15{
    margin-top: 15px;
  }
</style>
