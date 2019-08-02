<template>
    <div>
      <el-row>
        <el-col :span="24" class="toolbar" style="100%">
          <el-form :inline="true">
            <el-form-item>
              <el-button type="warning" icon="el-icon-back"  @click="goBack()">返回</el-button>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="addFormSubmit">立即提交</el-button>
            </el-form-item>
            <el-form-item>
              <el-button @click="reset">重置</el-button>
            </el-form-item>
          </el-form>
        </el-col>
      </el-row>
      <el-tabs type="border-card" v-model="tabName">
        <el-tab-pane label="通用详情" name="first">
          <div style="width: 640px;">
            <el-form :model="addForm" label-width="200px" ref="addForm" :rules="rules">
            <el-form-item label="商品名称" prop="name">
              <div ref="name">
                <el-input v-model="addForm.name" width="50" style="font-size: 15px"></el-input>
              </div>
            </el-form-item>
            <!-- <el-form-item label="副标题" prop="tbsm">
              <el-input v-model="addForm.tbsm" style="font-size: 15px"></el-input>
            </el-form-item> -->
            <!-- <el-form-item label="货号" prop="code">
              <div ref="code">
                <el-input v-model="addForm.code"></el-input>
              </div>
            </el-form-item> -->
            <el-form-item label="重量(千克)" prop="weight">
              <div ref="weight">
                <el-input v-model="addForm.weight"></el-input>
              </div>
            </el-form-item>
            <el-form-item label="排序" prop="ord">
              <el-input-number v-model="addForm.ord" :min="1"></el-input-number>
            </el-form-item>
            <el-form-item label="商品分类" prop="category">
              <div ref="category">
                <el-cascader :options="categoryOptions" v-model="category" @change="handleSupplier" clearable  :props="props">
                </el-cascader>
              </div>
            </el-form-item>
            <el-form-item label="会员商品" prop="vs_id">
            <div ref="vs_id">
              <el-select v-model="addForm.vs_id" placeholder="请选择供应商">
                <el-option v-for="item in vs_idOptions" :key="item.value" :label="item.label" :value="item.value"></el-option>
              </el-select>
            </div>
          </el-form-item>
            <el-form-item label="售价" prop="price">
              <div ref="price">
                <el-input v-model="addForm.price"></el-input>
              </div>
            </el-form-item>
            <el-form-item label="是否上架" prop="sale">
              <el-checkbox v-model="addForm.sale">打钩表示商品上架</el-checkbox>
            </el-form-item>
            <!-- <el-form-item label="起订量" prop="start_num">
              <el-input v-model="addForm.start_num"></el-input>
            </el-form-item>
            <el-form-item label="以几的倍数拍" prop="Nbei">
              <el-input v-model="addForm.Nbei"></el-input>
            </el-form-item>
            <el-form-item label="优惠信息" prop="youhui_desc">
              <el-input v-model="addForm.youhui_desc"></el-input>
            </el-form-item> -->
            <el-form-item label="配送范围" prop="shipping_desc">
              <el-input v-model="addForm.shipping_desc"></el-input>
            </el-form-item>
            <el-form-item label="温馨提示" prop="tishi_desc">
              <el-input v-model="addForm.tishi_desc"></el-input>
            </el-form-item>
            <el-form-item label="图片(默认第一张为商品主图)" prop="img">
              <el-upload action="" :auto-upload="false" list-type="picture-card" multiple :limit="9" :file-list="fileListImg" :on-change='changeUploadImg' :on-exceed="handleExceedImg">
                <i class="el-icon-plus"></i>
              </el-upload>
            </el-form-item>
          </el-form>
          </div>
        </el-tab-pane>
        <el-tab-pane label="详情" name="second">
          <div style="width:1000px;margin:0 auto;">
            <vue-html5-editor :content="addForm.descpt" id="asked_question" @change="updateData"></vue-html5-editor>
          </div>
        </el-tab-pane>
        <el-tab-pane label="商品属性" name="third">
          <el-form label-width="150px" :model="addThirdForm" ref="addThirdForm" :rules="thirdRules">
            <el-form-item label="商品类型" prop="cat_id">
              <el-select v-model="addThirdForm.cat_id" placeholder="请选择" @change="getAttrLists">
                <el-option v-for="item in typeOptions" :key="item.value" :label="item.label" :value="item.value"></el-option>
              </el-select>
            </el-form-item>

            <div class="mb15" v-for="(item,index) in attrLists" :key="index">
              <tr>
                <td class="label">
                  <i class="el-icon-plus" style="cursor: pointer" v-if="item.attr_type==1" @click="addAttr(item,index)"></i>
                  {{item.attr_name}}
                </td>
                <td class="color-gray">
                  <!--<el-select v-model="item.attrValue" placeholder="请选择" v-if="item.attr_values !=''">-->
                  <el-select filterable allow-create v-model="item.attrValue" clearable placeholder="请选择" v-if="item.attr_values !=''">
                    <el-option v-for="(v,i) in item.values" :key="i" :label="v" :value="v"></el-option>
                  </el-select>
                  <el-input v-model="item.attrValue" placeholder="请输入内容" clearable v-if="item.attr_values ==''"></el-input>
                </td>
                <td v-if="item.attr_type==1">
                  <input :id="'upload'+index" type="file" class="file" name="file" @change="changeImg(index,$event)">
                </td>
              </tr>
              <div  class="mt15" v-for="(its,idx) in item.attrList" :key="idx">
                <tr>
                  <td class="label">
                    <i class="el-icon-minus" style="cursor: pointer" v-if="its.attr_type==1" @click="decreaseAttr(index,idx)"></i>
                    {{its.attr_name}}
                  </td>
                  <td class="color-gray">
                    <!--<el-select v-model="form[index].attrList[idx].attrValues" @change="change(index)" placeholder="请选择" v-if="its.attr_values !=''">-->
                    <el-select filterable allow-create default-first-option v-model="form[index].attrList[idx].attrValues" clearable @change="change(index)" placeholder="请选择" v-if="its.attr_values !=''">
                      <el-option v-for="(m,n) in its.values" :key="n" :label="m" :value="m"></el-option>
                    </el-select>
                    <el-input v-model="form[index].attrList[idx].attrValues" clearable placeholder="请输入内容" v-if="its.attr_values ==''"></el-input>
                  </td>
                  <td v-if="its.attr_type==1">
                    <input :id="index+'upload'+idx" type="file" class="file" @change="changeImg(index,$event,idx)">
                  </td>
                </tr>
              </div>
            </div>
          </el-form>
        </el-tab-pane>
      </el-tabs>
    </div>
</template>

<script>
    import {goodsSuppliers,goodsInsert,goodsAttrLists,seletedGoodsattr,uploadImg,regionAll,goodsCateList} from "../../../api/api.js"
    export default {
        name: "goods-add",
        data(){
          return{
            tabName:"first",
            addForm:{
              name:"",
              // tbsm:"",
              // code:1,
              weight:'',
              ord:"",
              category:"",
              vs_id:0,
              // supplier:1,
              // brand:"",
              price:"",
              sale:true,
              is_sale:"",
              album:"",
              mprice:false,
              // is_fei5zhe:"",
              // hei5ka:false,
              // is_hei5ka:"",
              // is_limit:0,
              // start_num:"",
              // Nbei:"",
              // youhui_desc:"",
              shipping_desc:"",
              tishi_desc:"",
              img:"",
              descpt:"",
              cat_id:"",
              attr:[],
              attr_img:[],
              // regions:"",
            },
            addThirdForm:{
              cat_id:"",
            },
            typeOptions:[
              // {
              //   label: "请选择",
              //   value: 0
              // }
            ],
            form:[],
            attrLists:[],
            fileListImg: [],//图片文件列表
            categoryOptions:[],
            vs_idOptions:[],
            category:[],
            props: {
              label:'cat_name',
              value: "cat_id",
              children: 'child'
            },
            leafOnly:true,
            rules:{
              name:[{ required: true, message: '请输入商品名称', trigger: 'blur' },],
              // code:[{ required: true, message: '请输入商品货号', trigger: 'blur' },],
              weight:[{ required: true, message: '请输入商品重量', trigger: 'blur' },],
              ord:[{ required: true, message: '请输入排序', trigger: 'blur' },],
              category:[{ required: true, message: '请选择商品分类', trigger: 'blur' },],
              price:[{ required: true, message: '请输入售价', trigger: 'blur' },],
            },
            thirdRules:{
              cat_id:[{ required: true, message: '请选择商品属性', trigger: 'blur' },],
            },
            config: {
              initialFrameHeight: 500,
              scaleEnabled: true,
              zIndex: "0"
            },
          }
        },
        methods:{
          getOptions(){
            var data = JSON.parse(sessionStorage.getItem("data"));
            this.categoryOptions = data.data.argument.category;
            this.vs_idOptions = data.data.argument.vip_setting;
            this.vs_idOptions.unshift({label:'普通商品',value:0})
            this.vs_idOptions.unshift({label:'平价商品',value:-1})
          },
          handleSupplier(val){
            if(this.category.length>0){
              this.addForm.category = this.category[this.category.length-1];
            }
          },
          //图片选择
          changeUploadImg(file, fileList){
            this.fileListImg = fileList;
            this.addForm.img = fileList[0];
          },
          handleExceedImg(files, fileList) {
            this.$message.warning(`当前限制选择 9 个文件，本次选择了 ${files.length} 个文件，共选择了 ${files.length + fileList.length} 个文件`);
          },
          changeImg(index,e,idx){
            var img = e.target.files[0];
            var formdata = new FormData();
            formdata.append("img",img);
            uploadImg(formdata).then(res=>{
              if(res.data.code == 200){
                if(idx !=null){
                  this.attrLists[index].attrList[idx].attr_img = res.data.data.img_url;
                }else{
                  this.attrLists[index].attr_img = res.data.data.img_url;
                }
              }else{
                this.$message({
                  message: res.data.msg,
                  type: "warning"
                });
              }
            })
          },
          addFormSubmit(){
            let isValid1 = false;
            let isValid2 = false;
            this.$refs.addForm.validate((valid) => {
              if (valid) {
                isValid1 = true;
              }
            })
            this.$refs.addThirdForm.validate((valid)=>{
              if(valid){
                isValid2 = true;
              }
            });

            if(isValid1&&isValid2){//两个验证都通过
              this.$confirm("确认提交吗?", "提示", {}).then(() => {
                this.addForm.sale ? this.addForm.is_sale = 0:this.addForm.is_sale = 1;
                // this.addForm.mprice ? this.addForm.is_fei5zhe = 1:this.addForm.is_fei5zhe = 0;
                // this.addForm.hei5ka ? this.addForm.is_hei5ka = 1:this.addForm.is_hei5ka = 0;
                this.addForm.cat_id = this.addThirdForm.cat_id;
                var attr = [];
                for(var i in this.attrLists){
                  if(this.attrLists[i].attrValue){
                    attr.push({
                      attr_type:this.attrLists[i].attr_type,
                      attr_id:this.attrLists[i].attr_id,
                      attr_value:this.attrLists[i].attrValue,
                      attr_img:this.attrLists[i].attr_img,
                    });
                  }else{
                    attr.push({
                      attr_type:this.attrLists[i].attr_type,
                      attr_id:this.attrLists[i].attr_id,
                      attr_value:"",
                      attr_img:this.attrLists[i].attr_img,
                    });
                  }
                  for(var j in this.attrLists[i].attrList){
                    if(this.attrLists[i].attrList[j]){
                      attr.push({
                        attr_type:this.attrLists[i].attrList[j].attr_type,
                        attr_id:this.attrLists[i].attrList[j].attr_id,
                        attr_value:this.attrLists[i].attrList[j].attrValues,
                        attr_img:this.attrLists[i].attrList[j].attr_img,
                      });
                    }else{
                      attr.push({
                        attr_type:this.attrLists[i].attrList[j].attr_type,
                        attr_id:this.attrLists[i].attrList[j].attr_id,
                        attr_value:"",
                        attr_img:this.attrLists[i].attrList[j].attr_img,
                      });
                    }
                  }
                }
                this.addForm.attr = JSON.stringify(attr);
                var fd = new FormData();
                for(let o in this.addForm){
                  if(o == "img"|| o == "album"||o == "descpt"){
                  }else{
                    fd.append(o,this.addForm[o]);
                  }
                }
                fd.append("descpt",this.addForm.descpt);
                if(this.fileListImg&&this.fileListImg.length>0){
                  for(var i in this.fileListImg){
                    if(i != 0){
                      fd.append("album[]", this.fileListImg[i].raw);
                    }
                  }
                  fd.append("img", this.fileListImg[0].raw);
                }else{
                  fd.append("img","");
                }
                for(var x=0;x<this.attrLists.length;x++){
                  if(document.getElementById('upload'+x)){
                    fd.append("attr_img[]",document.getElementById('upload'+x).files[0]);
                  }
                  for(var y=0;y<this.attrLists[x].attrList.length;y++){
                    if(document.getElementById(x+'upload'+y).files[0]){
                      fd.append("attr_img[]",document.getElementById(x+'upload'+y).files[0]);
                    }
                  }
                }
                goodsInsert(fd).then(res=>{
                  if(res.code == 200){
                    this.$message({
                      message: res.msg,
                      type: "success"
                    });
                    this.$router.go(-1);
                  }else{
                    this.$message({
                      message: res.msg,
                      type: "warning"
                    });
                  }
                })
              })
            }else {//验证不通过
              if (this.tabName == "first"||this.tabName == "second") {
                if(!isValid1){
                  if(this.tabName == "second"){
                    this.tabName = "first";
                  }
                  switch(true){
                    case this.addForm.name == "":this.$refs.name.scrollIntoView(false);break;
                    // case this.addForm.code == "":this.$refs.code.scrollIntoView(false);break;
                    case this.addForm.weight == "":this.$refs.weight.scrollIntoView(false);break;
                    case this.addForm.ord == "":this.$refs.ord.scrollIntoView(false);break;
                    case this.addForm.category == "":this.$refs.category.scrollIntoView(false);break;
                    case this.addForm.price == "":this.$refs.price.scrollIntoView(false);break;
                  }
                }else if(!isValid2){
                  this.tabName = "third";
                }
              }else{
                if(!isValid2){

                }else if(!isValid1){
                  this.tabName = "first";
                  switch(true){
                    case this.addForm.name == "":this.$refs.name.scrollIntoView(false);break;
                    // case this.addForm.code == "":this.$refs.code.scrollIntoView(false);break;
                    case this.addForm.weight == "":this.$refs.weight.scrollIntoView(false);break;
                    case this.addForm.ord == "":this.$refs.ord.scrollIntoView(false);break;
                    case this.addForm.category == "":this.$refs.category.scrollIntoView(false);break;
                    case this.addForm.price == "":this.$refs.price.scrollIntoView(false);break;
                  }
                }
              }
            }
          },
          updateData(val) {
            this.addForm.descpt = val;
          },
          getAttrLists(){
            if(this.addThirdForm.cat_id == 0){
              this.attrLists = [];
            }else{
              goodsAttrLists({
                cat_id:this.addThirdForm.cat_id
              }).then(res=>{
                if(res.data.code == 200){
                  this.attrLists = res.data.data;
                  this.attrLists.forEach((item,index)=>{
                    this.attrLists[index].values = item.attr_values.split("\n");
                    this.$set(this.attrLists[index],"attrValue","");
                    this.attrLists[index].attrList = [];
                  })
                }
              })
            }
          },
          getGoodType(){
            seletedGoodsattr().then(res=>{
              if(res.data.code == 200){
                // this.typeOptions = this.typeOptions.concat(res.data.data.list);
                this.typeOptions = res.data.data.list;
              }
            })
          },
          addAttr(item,index){
            let {attrList,...obj} = item;
            obj.attrValues = "";
            this.attrLists[index].attrList.push(obj);
            this.$set(this.attrLists, index, this.attrLists[index]);
          },
          decreaseAttr(index,idx){
            this.attrLists[index].attrList.splice(idx,1);
            this.$set(this.attrLists, index, this.attrLists[index]);
          },
          change(index){
            this.$set(this.form,index,this.form[index])
          },
          goBack() {
            this.$router.go(-1);
          },
          reset(){
            this.$refs.addForm.resetFields();
            this.category = [];
            this.fileListImg = [];
            this.attrLists = [];
            this.addThirdForm.cat_id = "";
          }
        },
        mounted(){
          this.getOptions();
          this.getGoodType();
          // this.getSupplier();
        },
        watch:{
          attrLists:{
            handler(newValue,oldValue) {
              this.form = newValue;
            },
          }
        }
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
