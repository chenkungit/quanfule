<template>
  <div>
    <!--<el-row>-->
      <!--<el-col :span="24" class="toolbar" style="100%">-->
        <!--<el-form :inline="true">-->
          <!--<el-form-item>-->
            <!--<el-button @click="reset">重置</el-button>-->
          <!--</el-form-item>-->
        <!--</el-form>-->
      <!--</el-col>-->
    <!--</el-row>-->
    <el-tabs v-model="activeName" type="card"  @tab-click="handleClick">
    <el-tab-pane label="基本管理" name="first">
      <div style="width:640px;">
        <el-form :model="goodsData" ref="goodsData" label-width="200px" :rules="rules">
          <el-form-item label="商品名称" prop="name">
            <div ref="name">
              <el-input v-model="goodsData.name" placeholder="请输入商品名称" style="font-size: 15px"></el-input>
            </div>
          </el-form-item>
          <!-- <el-form-item label="副标题" prop="tbsm">
            <el-input v-model="goodsData.tbsm" placeholder="请输入副标题" style="font-size: 15px"></el-input>
          </el-form-item> -->
          <el-form-item label="商品图片" prop="img">
            <VueImgInputer v-model="goodsData.img" theme="light" :imgSrc="goodsData.img"></VueImgInputer>
          </el-form-item>
          <!-- <el-form-item label="货号" prop="code">
            <div ref="code">
              <el-input v-model="goodsData.code" placeholder="请输入货号"></el-input>
            </div>
          </el-form-item> -->
          <el-form-item label="重量(千克)" prop="weight">
            <div ref="weight">
              <el-input v-model="goodsData.weight" placeholder="请输入重量"></el-input>
            </div>
          </el-form-item>
          <el-form-item label="排序" prop="ord">
            <div ref="ord">
              <el-input-number v-model="goodsData.ord" :min="1" placeholder="请输入排序"></el-input-number>
            </div>
          </el-form-item>
          <el-form-item label="商品分类" prop="category">
            <el-cascader :options="categoryOptions" v-model="goodsData.category" @change="handleSupplier" :props="props">
            </el-cascader>
          </el-form-item>
          <el-form-item label="会员商品" prop="vs_id">
            <div ref="vs_id">
              <el-select v-model="goodsData.vs_id" placeholder="请选择供应商">
                <el-option v-for="item in vs_idOptions" :key="item.value" :label="item.label" :value="item.value"></el-option>
              </el-select>
            </div>
          </el-form-item>
          <!-- <el-form-item label="供应商" prop="supplier">
            <div ref="supplier">
              <el-select v-model="goodsData.supplier" placeholder="请选择供应商">
                <el-option v-for="item in supplierOptions" :key="item.value" :label="item.label" :value="item.value"></el-option>
              </el-select>
            </div>
          </el-form-item> -->
          <!-- <el-form-item label="品牌" prop="brand">
            <el-select v-model="goodsData.brand" placeholder="请选择品牌">
              <el-option v-for="item in brandOptions" :key="item.value" :label="item.label" :value="item.value"></el-option>
            </el-select>
          </el-form-item> -->
          <el-form-item label="售价" prop="price">
            <div ref="price">
              <el-input v-model="goodsData.price" placeholder="请选择售价"></el-input>
            </div>
          </el-form-item>
          <el-form-item label="商品状态">
            <el-select v-model="goodsData.is_sale" placeholder="请选择">
              <el-option v-for="item in goodsState" :key="item.value" :label="item.label" :value="item.value"></el-option>
            </el-select>
          </el-form-item>
          <!-- <el-form-item label="起订量" prop="start_num">
            <el-input v-model="goodsData.start_num" placeholder="请输入起订量"></el-input>
          </el-form-item>
          <el-form-item label="以几的倍数拍" prop="N_bei">
            <el-input v-model="goodsData.N_bei" placeholder="请输入以几的倍数拍"></el-input>
          </el-form-item>
          <el-form-item label="优惠信息" prop="youhui_type">
            <el-input v-model="goodsData.youhui_type" placeholder="请输入优惠信息"></el-input>
          </el-form-item> -->
          <el-form-item label="配送范围" prop="shipping_desc">
            <el-input v-model="goodsData.shipping_desc" placeholder="请输入配送范围"></el-input>
          </el-form-item>
          <el-form-item label="温馨提示" prop="tishi_desc">
            <el-input v-model="goodsData.tishi_desc" placeholder="请输入温馨提示"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="edit()">确定</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-tab-pane>
    <el-tab-pane label="图文详情管理" name="second">
      <div style="width:375px;margin:0 auto;" class="goods-yulan">
        <vue-html5-editor :content="desc" id="desc" @change="updateData"></vue-html5-editor>
        <div style="text-align:center;padding-top:20px;">
          <el-button type="primary" @click.native="edit()">确定</el-button>
        </div>
      </div>
    </el-tab-pane>
    <!-- <el-tab-pane label="问答管理" name="third">
       <div style="width:375px;margin:0 auto;" class="goods-yulan">
        <vue-html5-editor :content="asked_question" id="asked_question" @change="updateData1"></vue-html5-editor>
        <div style="text-align:center;padding-top:20px;">
          <el-button type="primary" @click.native="edit()">确定</el-button>
        </div>
      </div>
    </el-tab-pane> -->
    <el-tab-pane label="相册管理" name="forth">
      <el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
        <el-form :inline="true">
          <el-form-item>
            <el-button type="primary" @click="handleAddphoto">新增</el-button>
          </el-form-item>
        </el-form>
      </el-col>
      <el-table :data="XcphotoData" style="width: 100%" border>
        <el-table-column prop="img_url" label="图片" width="120">
          <template slot-scope="scope">
            <img :src="scope.row.img_url" alt="" style="height:100px;">
          </template>
        </el-table-column>
        <el-table-column prop="img_desc" label="描述" width="180">
        </el-table-column>
        <el-table-column prop="sort" label="排序" width="180">
        </el-table-column>
        <el-table-column prop="is_show" label="是否显示">
          <template slot-scope="scope">
            <p v-if="scope.row.is_show==0">否</p>
            <p v-if="scope.row.is_show==1">是</p>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="290">
          <template slot-scope="scope">
            <el-button type="primary" @click="handleEditPhoto(scope.row)">编辑</el-button>
            <el-button type="danger" icon="el-icon-delete" @click="edit('delete',scope.row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

       <!--新增相册-->
      <el-dialog title="新增相册" :visible.sync="photoVisible" width="30%">
        <el-form :model="addphotoForm" label-width="80px" ref="addphotoForm">
          <el-form-item label="描述" prop="img_desc">
            <el-input v-model="addphotoForm.img_desc"></el-input>
          </el-form-item>
          <el-form-item label="图片" prop="is_show">
            <el-upload action="" :auto-upload="false" list-type="picture-card" multiple :limit="9" :file-list="fileListImg" :on-change='changeUploadImg'>
              <i class="el-icon-plus"></i>
            </el-upload>
          </el-form-item>
          <el-form-item label="排序" prop="sort">
            <el-input-number v-model="addphotoForm.sort" :min="1"></el-input-number>
          </el-form-item>
          <el-form-item label="显示" prop="is_show">
            <el-radio-group v-model="addphotoForm.is_show">
              <el-radio :label="1">是</el-radio>
              <el-radio :label="0">否</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click.native="photoVisible = false">取消</el-button>
          <el-button type="primary" @click.native="edit('add')">提交</el-button>
        </div>
      </el-dialog>

      <!--编辑相册-->
      <el-dialog title="编辑相册" :visible.sync="photoEditVisible" width="30%">
        <el-form :model="editphotoForm" label-width="80px" ref="editphotoForm">
          <el-form-item label="排序" prop="sort">
            <el-input-number v-model="editphotoForm.sort" :min="1"></el-input-number>
          </el-form-item>
          <el-form-item label="显示" prop="is_show">
            <el-radio-group v-model="editphotoForm.is_show">
              <el-radio :label="1">是</el-radio>
              <el-radio :label="0">否</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click.native="photoEditVisible = false">取消</el-button>
          <el-button type="primary" @click.native="edit('edit')">提交</el-button>
        </div>
      </el-dialog>
    </el-tab-pane>
    <el-tab-pane label="属性管理" name="fifth">
      <div>
        <el-form label-width="150px" :model="editAttrForm" ref="editAttrForm" :rule="formRules">
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
                <el-input v-model="item.attrValues" placeholder="请输入内容" v-if="item.attr_values ==''"></el-input>
              </td>
              <td v-if="item.img_url!=null">
                <img :src="item.img_url" alt="" style="width: 40px;height: 40px;padding: 0 10px;margin-bottom: -10px;">
              </td>
              <td v-if="item.attr_type==1">
                <!--<input :id="index+item.attr_id+'upload'" type="file" class="file" name="name" @change="changeImg(index,$event)">-->
                <input :id="'upload'+index" type="file" @change="changeImg(index,$event)">
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
                  <input :id="index+'upload'+idx" type="file" @change="changeImg(index,$event,idx)">
                </td>
              </tr>
            </div>
          </div>

          <div style="text-align:center;padding-top:20px;">
            <el-button type="primary" @click.native="edit()">确定</el-button>
          </div>
        </el-form>
      </div>
    </el-tab-pane>
  </el-tabs>
  </div>
</template>
<script>
import { uploadImg,regionAll,goodsCateList } from "../../../api/api";
import {editGoods,goodsSuppliers,goodsEditLists,removeGoods,seletedGoodsattr,goodsAttrLists} from "../../../api/api";
import selectgoods from "../../pubcom/selectgoods";
import editAttr from "./goodsEdit/editAttr.vue"
export default {
  components: {
    selectgoods,
    editAttr
  },
  data() {
    return {
      type:1,
      activeName: "first",
      editData: [],
      album: "",
      img: "",
      goodDesc:"",
      desc: "",
      goodsData: {
        id:"",
        name:"",
        // tbsm:"",
        // code:"",
        weight:"",
        ord:"",
        category:"",
        vs_id:'',
        // supplier:"",
        // brand:"",
        price:"",
        pro_price:"",
        sale:true,
        is_sale:"",
        album:"",
        is_fei5zhe:"",
        // start_num:"",
        N_bei:0,
        youhui_desc:"",
        shipping_desc:"",
        tishi_desc:"",
        img:"",
        descpt:"",
        // asked_question:"",
        cat_id:"",
        attr:[],
        attr_img:[],
        rel_goods_id: "",
        regions:"",
      },
      isReady:false,
      descpt:"",
      // asked_question:"",
      goodsState:[
        {
          value:0,
          label:'上架'
        },
        {
          value:1,
          label:'下架'
        },
        // {
        //   value:2,
        //   label:'即将上架'
        // },
        // {
        //   value:3,
        //   label:'暂无现货'
        // }
        ],
      fileListImg: [],//图片文件列表
      // brandOptions:[],
      vs_idOptions:[],
      categoryOptions:[],
      category:[],
      props: {
        label:'cat_name',
        value: 'cat_id',
        children: 'child'
      },
      XcphotoData: [],
      photoVisible: false,
      addphotoForm: {
        img_desc: "",
        is_show: 1,
        img_original: null,
        sort: 2
      },
      photoEditVisible:false,
      descptConfig: {
        initialFrameHeight: 500,
        scaleEnabled: true,
        zIndex: "0"
      },
      askedConfig: {
        initialFrameHeight: 500,
        scaleEnabled: true,
        zIndex: "0"
      },
      editphotoForm:{
        img_id:'',
        is_show:'',
        // sort:'',
      },
      editAttrForm:{
        cat_id:"",
        original_cat_id:"",
      },
      attrLists:[],
      goods_attr:[],
      form:[],
      sontabel: [],
      rules:{
        name:[{ required: true, message: '请输入商品名称', trigger: 'blur' },],
        // code:[{ required: true, message: '请输入商品货号', trigger: 'blur' },],
        weight:[{ required: true, message: '请输入商品重量', trigger: 'blur' },],
        ord:[{ required: true, message: '请选择排序', trigger: 'blur' },],
        category:[{ required: true, message: '请选择商品分类', trigger: 'blur' },],
        price:[{ required: true, message: '请输入售价', trigger: 'blur' },],
      },
      formRules:{
        cat_id:[{ required: true, message: '请选择商品属性', trigger: 'blur' },],
      },
      leafOnly:true,
      typeOptions:[
        // {
        //   label: "请选择",
        //   value: 0
        // }
      ],
      originalCatId:"",
      fileIdArr:[],
      isReset:false,
    };
  },
  methods: {
    gettabledata(msg) {
      msg.forEach(element => {
        let obj = {
          name: element.name,
          smallid: element.id
        };
        this.sontabel.push(obj);
      });
      this.sontabel = this.uniqeByKeys(this.sontabel, ["smallid"]);
    },
    addSonDelete(index, rows) {
      rows.splice(index, 1);
    },
    handleClick(tab){
      this.type = tab.index-0+1;
    },
    getGoodEditList(){
      this.isReady = false;
      goodsEditLists({
        id:this.$route.query.id
      }).then(res=>{
        if(res.data.code == 200){
          this.goodsData.attr = JSON.stringify(res.data.data.goods_attr);
          this.editData = res.data.data.goods_info;
          this.goodsData.name = this.editData.name;
          // this.goodsData.tbsm = this.editData.tbsm;
          this.goodsData.price = this.editData.price;
          // this.goodsData.code = this.editData.code;
          this.goodsData.ord = this.editData.ord;
          this.goodsData.weight = this.editData.weight;
          this.goodsData.category = this.editData.category;
          this.goodsData.vs_id = this.editData.vs_id;
          // this.goodsData.supplier = this.editData.supplier;
          // this.editData.brand!=0?this.goodsData.brand = this.editData.brand:this.$set(this.goodsData,"brand","");
          if(this.editData.img!="") this.goodsData.img =this.editData.img;
          this.goodsData.is_sale = this.editData.is_sale;
          // this.goodsData.start_num = this.editData.start_num;
          // this.goodsData.N_bei = this.editData.Nbei;
          // this.goodsData.youhui_type = this.editData.youhui_type;
          this.goodsData.tishi_desc = this.editData.tishi_desc;
          this.goodsData.shipping_desc = this.editData.shipping_desc;
          this.goodsData.cat_id = this.editData.cat_id;
          this.desc = this.editData.descpt;
          // this.asked_question = this.editData.asked_question;
          this.XcphotoData = res.data.data.goods_img;
          this.editAttrForm.cat_id = res.data.data.goods_info.cat_id;
          this.originalCatId = res.data.data.goods_info.cat_id;
          this.editAttrForm.original_cat_id = res.data.data.goods_info.original_cat_id;
          this.editData = res.data.data.goods_info;
          this.goods_attr = res.data.data.goods_attr;
          this.getAttrLists();
          // this.goodDesc = this.editData.descpt;
          // if(this.editData.asked_question!=null)this.desc = this.editData.asked_question;
          this.goods_attr = res.data.data.goods_attr;
          this.isReady = true;
          this.goodsData.descpt = this.editData.descpt;
          // this.goodsData.asked_question = this.editData.asked_question;
        }
      })
    },
    getOptions(){
      var data = JSON.parse(sessionStorage.getItem("data"));
      // this.brandOptions = data.data.argument.brand;
      this.categoryOptions = data.data.argument.category;
      this.vs_idOptions = data.data.argument.vip_setting;
      this.vs_idOptions.unshift({label:'普通商品',value:0})
    },
    getAttrLists(){
      if(this.editAttrForm.cat_id == 0){
        this.attrLists = [];
      }else{
        this.fileIdArr = [];
        goodsAttrLists({
          cat_id:this.editAttrForm.cat_id
        }).then(res=>{
          if(res.data.code == 200){
            if(res.data.data.length>0){
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
              arr.forEach((item,index)=>{
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
            }else{
              this.attrLists = res.data.data;
            }

            //重置按钮
            if(this.isReset){
              this.isReset = !this.isReset;
              for(var x=0;x<this.attrLists.length;x++){
                if(document.getElementById('upload'+x)!=null){
                  var obj = document.getElementById('upload'+x);
                  obj.outerHTML=obj.outerHTML;
                }
              }
            }
          }
        })
      }
    },
    // getSupplier(){
    //   goodsSuppliers().then(res=>{
    //     if(res.data.code == 200){
    //       this.supplierOptions = res.data.data;
    //     }
    //   })
    // },
    handleSupplier(val){
      // let params = {};
      // if(val.length == 1) {
      //   params.cat_id = val[val.length-1];
      //   goodsSuppliers(params).then(res=>{
      //     if(res.data.code == 200){
      //       this.supplierOptions =  [];
      //       for(var i in  res.data.data){
      //         this.supplierOptions.push(res.data.data[i])
      //       }
      //     }
      //   })
      // }else
       if(val.length == 2){
        this.goodsData.category = val[val.length-1];
      }
    },
    edit(flag,imgId){
      let isValid1 = false;
      let isValid2 = false;
      this.$refs.goodsData.validate((valid) => {
        if (valid) {
          isValid1 = true;
        }
      });
      this.$refs.editAttrForm.validate((valid)=>{
        if(valid){
          isValid2 = true;
        }
      });
      if(isValid1&&isValid2){//两个验证都通过
        console.log("通过")
        this.editSubmit(flag,imgId);
      }else {//验证不通过
        if (this.activeName == "first"||this.activeName == "second"||this.activeName == "third"||this.activeName == "forth") {
          if(!isValid1){
            if(this.activeName == "second"||this.activeName == "third"){
              this.activeName = "first";
            }
            if(this.activeName == "forth"){
              this.photoVisible = false;
              this.photoEditVisible = false;
              this.activeName = "first";
            }
            switch(true){
              case this.goodsData.name == "":this.$refs.name.scrollIntoView(false);break;
              // case this.goodsData.code == "":this.$refs.code.scrollIntoView(false);break;
              case this.goodsData.weight == "":this.$refs.weight.scrollIntoView(false);break;
              case this.goodsData.ord == "":this.$refs.ord.scrollIntoView(false);break;
              case this.goodsData.category == "":this.$refs.category.scrollIntoView(false);break;
              // case this.goodsData.supplier == "":this.$refs.supplier.scrollIntoView(false);break;
              case this.goodsData.price == "":this.$refs.price.scrollIntoView(false);break;
            }
          }else if(!isValid2){
            this.activeName = "third";
          }
        }else{
          if(!isValid2){

          }else if(!isValid1){
            this.activeName = "first";
            switch(true){
              case this.goodsData.name == "":this.$refs.name.scrollIntoView(false);break;
              // case this.goodsData.code == "":this.$refs.code.scrollIntoView(false);break;
              case this.goodsData.weight == "":this.$refs.weight.scrollIntoView(false);break;
              case this.goodsData.ord == "":this.$refs.ord.scrollIntoView(false);break;
              case this.goodsData.category == "":this.$refs.category.scrollIntoView(false);break;
              // case this.goodsData.supplier == "":this.$refs.supplier.scrollIntoView(false);break;
              case this.goodsData.price == "":this.$refs.price.scrollIntoView(false);break;
            }
          }
        }
      }    },
    editSubmit(flag,imgId){
        let params = {};
        params.id = this.$route.query.id;
        //基本管理
        // params.tbsm = this.goodsData.tbsm;
        params.price = this.goodsData.price;
        // params.code = this.goodsData.code;
        params.ord = this.goodsData.ord;
        params.weight = this.goodsData.weight ;
        params.category = this.goodsData.category ;
        params.vs_id = this.goodsData.vs_id;
        // params.supplier = this.goodsData.supplier;
        // params.brand = this.goodsData.brand;
        params.is_sale = this.goodsData.is_sale;
        params.is_fei5zhe = this.goodsData.is_fei5zhe;
        params.is_limit = 0;
        // params.start_num = this.goodsData.start_num;
        // params.N_bei = this.goodsData.N_bei;
        // params.youhui_type = this.goodsData.youhui_type;
        params.tishi_desc = this.goodsData.tishi_desc;
        params.shipping_desc = this.goodsData.shipping_desc;
        params.name = this.goodsData.name;
        params.regions = this.goodsData.regions;
        //图文管理
        params.descpt = this.desc;
        //问答管理
        // params.asked_question = this.asked_question;
        //属性管理
        params.cat_id = this.editAttrForm.cat_id;
        params.original_cat_id = this.originalCatId;
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
        params.attr = JSON.stringify(attr);
        //相册管理
        if(flag){
          params.flag = flag;
          if(flag == "add"){
            params.is_show = this.addphotoForm.is_show;
            params.sort = this.addphotoForm.sort;
            params.img_desc = this.addphotoForm.img_desc;
            let fd = new FormData();
            for(let i in params){
              if(i !="album")
              fd.append(i,params[i]);
            }
            for(var j in this.fileListImg){
              fd.append("album[]",this.fileListImg[j].raw)
            }
            this.editApi(fd,"add");
          }else if(flag == "edit"){
            params.is_show = this.editphotoForm.is_show;
            params.img_id = this.editphotoForm.img_id;
            params.sort = this.editphotoForm.sort;
            this.editApi(params,"edit");
          }else{
            params.img_id = imgId.img_id;
            this.editApi(params,"delete");
          }
        }else{
          this.editApi(params);
        }
    },
    editApi(params,flag){
      if(typeof(this.goodsData.img)=="object"){
        var formData = new FormData();
        formData.append("img", this.goodsData.img);
        uploadImg(formData).then(res=>{
          if(res.data.code == 200) {
            if(flag == "add"){
              params.append("img", res.data.data.img_url);
            }else{
              params.img = res.data.data.img_url;
            }
            editGoods(params).then(res => {
              if (res.code == 200) {
                this.getGoodEditList();
                this.$router.go(-1);
                this.$message({
                  message: res.msg,
                  type: "success"
                });
              } else {
                this.$message({
                  message: res.msg,
                  type: "warning"
                });
              }
            });
          }
        })
      }else{
        if(flag == "add"){
          params.append("img", this.goodsData.img);
        }else{
          params.img = this.goodsData.img;
        }
        editGoods(params).then(res => {
          if (res.code == 200) {
            this.getGoodEditList();
            if(!flag){
              this.$router.go(-1);
            }
            if(flag == "add"){
              this.photoVisible = false;
              this.$refs.addphotoForm.resetFields();
              this.fileListImg = [];
            }else if(flag == "edit"){
              this.photoEditVisible = false;
            }
            this.$message({
              message: res.msg,
              type: "success"
            });
          } else {
            this.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      }
    },
    updateData(val) {
      this.desc = val;
      console.log(val)
    },
    // updateData1(val) {
    //   this.asked_question = val;
    // },
    handleAddphoto() {
      this.photoVisible = true;
    },
    //图片选择
    changeUploadImg(file, fileList){
      this.fileListImg = fileList;
    },
    handleEditPhoto(params){
      this.photoEditVisible = true;
      this.editphotoForm.img_id = params.img_id;
      this.editphotoForm.is_show = params.is_show;
      this.$set(this.editphotoForm,"sort",params.sort);
    },
    changeAttrValue(index){
      this.$set(this.attrLists,index,this.attrLists[index])
    },
    //获取商品类型
    getGoodType(){
      seletedGoodsattr().then(res=>{
        if(res.data.code == 200){
          // this.typeOptions = this.typeOptions.concat(res.data.data.list);
          this.typeOptions = res.data.data.list;
        }
      })
    },
    addAttr(item,index){
      let {attrList,img_url,img_id,attr_value,goods_attr_id,...obj} = item;
      this.attrLists[index].attrList.push(obj);
      this.$set(this.attrLists, index, this.attrLists[index]);
    },
    decreaseAttr(flag,index,idx){
      if(flag == 1){
        this.attrLists.splice(index,1);
        // this.$set(this.attrLists,index,this.attrLists[index]);
      }else{
        this.attrLists[index].attrList.splice(idx,1);
        this.$set(this.attrLists, index, this.attrLists[index]);
      }
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
            this.attrLists[index].showFile = e.target.files[0].name;
          }
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
    reset(){
      this.getOptions();
      this.getGoodEditList();
      this.getSupplier();
      this.isReset = true;
    },
  },
  mounted() {
    this.getOptions();
    this.getGoodEditList();
    // this.getSupplier();
    this.getGoodType();
  },
  watch:{
    attrLists:{
      handler(newValue,oldValue) {
        this.form = newValue;
      },
    }
  },
};
</script>
<style scoped>
.goods-yulan img {
  width: 100%;
}
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
