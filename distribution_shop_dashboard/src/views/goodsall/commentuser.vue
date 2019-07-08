<template>
  <div>
    <el-col :span="24" class="toolbar" style="100%">
      <el-form :inline="true">
        <el-form-item>
          <div style="float:left;">输入评论内容 :</div>
          <div style="float:left;margin-left:20px;">
            <el-input type="text" v-model="content"></el-input>
          </div>
          <div style="float:left;">商品id:</div>
          <div style="float:left;margin-left:20px;">
            <el-input type="text" v-model="id_value"></el-input>
          </div>
          <div style="float:left;">评价等级:</div>
          <div style="float:left;margin-left:20px;">
            <el-select v-model="comment_rank">
              <el-option v-for="item in comment_rankoptions" :key="item.value" :label="item.label" :value="item.value">
              </el-option>
            </el-select>
          </div>
          <div style="float:left;margin-left:20px;">
            <el-checkbox v-model="is_content">是否显示无内容评论</el-checkbox>
          </div>
          <div style="float:left;margin-left:20px;">
            <el-button type="primary" icon="el-icon-search" @click="getcommentuser">搜索</el-button>
          </div>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- 列表 -->
    <el-table :data="commentuserData" border style="width: 100%" v-loading="tableloading" element-loading-text="拼命加载中">
      <el-table-column type="selection" width="55">
      </el-table-column>
      <el-table-column prop="comment_id" label=" 编号" width="70">
      </el-table-column>
      <el-table-column prop="user_name" label="用户名" width="100">
      </el-table-column>
      <el-table-column label="评论对象">
        <template slot-scope="scope">
          <p>{{scope.row.title}}
            <span style="color:#ff7e00 ">{{scope.row.goods_attr}}</span>
            <span style="color:red;font-weight:700" v-if="scope.row.official_reply==0">未回复</span>
            <span style="color:green;font-weight:700" v-else>已回复</span>
          </p>
          <p style="font-weight:600">评论内容：{{scope.row.content}}</p>
        </template>
      </el-table-column>
      <el-table-column prop="add_time" label="评论时间" width="140">
      </el-table-column>
      <el-table-column label="	状态" width="60">
        <template slot-scope="scope">
          <p v-if="scope.row.status==1">显示</p>
          <p v-if="scope.row.status==0">不显示</p>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="200">
        <template slot-scope="scope">
          <el-button icon="el-icon-view" @click="handleInfo(scope.row)" id="one">详情</el-button>
          <el-button icon="el-icon-edit" @click="handleEdit(scope.row)">编辑</el-button>
          <!-- <el-button type="danger"  @click="handleDel(scope.row)">删除</el-button> -->
        </template>
      </el-table-column>
    </el-table>
    <!-- 分页 -->
    <div class="block" style="margin-top:20px;text-align:center;">
      <el-pagination @current-change="handleCurrentChange" layout="prev, pager, next, jumper" :page-count="pagecount">
      </el-pagination>
    </div>
    <el-dialog title="提示" :visible.sync="replyVisible" width="60%">
      <p>
        <span style="font-weight:600">{{reply.user_name}}</span>&nbsp于&nbsp
        <span style="font-weight:600">{{reply.add_time}}</span>&nbsp对&nbsp
        <span style="color:#E4A825">{{reply.title}}</span>
        <span style="color:#C12F12">{{reply.goods_attr}}</span>&nbsp&nbsp发表评论</p>
      <hr color="#dadada" size="1">
      <br/>
      <br/>
      <p style="paddin:5px 0;font-weight:600">主评论：{{reply.content}}</p>
      <p style="text-align:right">
        <span v-if="reply.status==0" style="color:red;margin-right:20px">不显示</span>
        <span>{{reply.add_time}}</span>
      </p>
      <p style="text-align:center;padding-bottom:5px;">
        <el-button type="danger" size="mini" @click="enablesta(reply,1)">显示</el-button>
        <el-button type="primary" size="mini" @click="replyson(reply,0)">回复</el-button>
      </p>
      <br/>
      <br/>
      <hr color="#dadada" size="1">
      <div v-for="item in replydata">
        <p style="paddin:5px 0;">
          <span v-if="item.is_official==1&&item.is_twice==0&&item.to_fd!=0">【官方】{{item.user_name}}回复{{item.to_user_name}}:</span>
          <span v-if="item.is_official==0&&item.is_twice==1&&item.to_fd!=0">{{item.user_name}}【评论人】回复{{item.to_user_name}}：</span>
          <span v-if="item.is_official==0&&item.is_twice==0&&item.to_fd!=0">{{item.user_name}}【他人】回复{{item.to_user_name}}：</span>
          <span v-if="item.is_official==1&&item.is_twice==0&&item.to_fd==0">【官方】{{item.user_name}}：</span>
          <span v-if="item.is_official==0&&item.is_twice==1&&item.to_fd==0">【评论人】{{item.user_name}}：</span>
          <span v-if="item.is_official==0&&item.is_twice==0&&item.to_fd==0">【他人】{{item.user_name}}：</span>{{item.content}}</p>
        <p style="text-align:right">
          <span v-if="item.status==0" style="color:red;margin-right:20px">不显示</span>
          <span>{{item.add_time}}</span>
        </p>
        <p style="text-align:center;padding-bottom:5px;">
          <el-button type="danger" size="mini" @click="enablesta(item,0)">显示</el-button>
          <el-button type="primary" size="mini" @click="replyson(item,1)">回复</el-button>
        </p>
        <hr color="#dadada" size="1">
      </div>
    </el-dialog>
    <el-dialog title="提示" :visible.sync="replysonVisible" width="30%">
      客服名:
      <el-input v-model="reply_user_name" placeholder="客服名"></el-input>
      回复内容:
      <el-input type="textarea" v-model="reply_content" placeholder="输入回复内容"></el-input>
      <br/>
      <br/>
      <p style="text-align:right">
        </el-button>
        <el-button type="primary" size="mini" @click="replySubmit(btn)">确定</el-button>
      </p>
    </el-dialog>
    <!--编辑界面-->
    <el-dialog title="编辑" :visible.sync="editFormVisible">
      <el-form :model="editForm" label-width="150px" ref="editForm">
        <el-form-item label="评论内容" prop="content">
          <el-input type="text" v-model="editForm.content"></el-input>
        </el-form-item>
        <el-form-item label="评论登记" prop="comment_rank">
          <el-rate v-model="editForm.comment_rank"></el-rate>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="editFormVisible = false">取消</el-button>
        <el-button type="primary" @click.native="editSubmit">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { commentuserLists } from "../../api/api";
import { commentuserInfo } from "../../api/api";
import { commentuserSend } from "../../api/api";
import { enableCommentuser } from "../../api/api";
import { editCommentuser } from "../../api/api";
export default {
  data() {
    return {
      commentuserData: [],
      page: 1,
      pagecount: 1,
      tableloading: true,
      content: "",
      id_value: "",
      comment_rank: 99,
      comment_rankoptions: [
        {
          label: "全部",
          value: 99
        },
        {
          label: "没★",
          value: 0
        },
        {
          label: "★",
          value: 1
        },
        {
          label: "★★",
          value: 2
        },
        {
          label: "★★★",
          value: 3
        },
        {
          label: "★★★★",
          value: 4
        },
        {
          label: "★★★★★",
          value: 5
        }
      ],
      is_content: false,
      replyVisible: false,
      reply: {},
      comment_id: "",
      replydata: [],
      reply_comment_id: "",
      reply_user_name: "",
      reply_content: "",
      reply_user_id: "",
      replysonVisible: false,
      btn: 0,
      editFormVisible: false,
      editForm: {
        comment_rank: "",
        content: ""
      }
    };
  },
  methods: {
    getcommentuser() {
      //获取数据
      this.tableloading = true;
      let param = {};
      param.page = this.page;
      if (this.content != "") param.content = this.content;
      if (this.id_value != "") param.id_value = this.id_value;
      if (this.comment_rank != 99) param.comment_rank = this.comment_rank;
      if (this.is_content)
        param.is_content = this.changeswitch(this.is_content);
      commentuserLists(param).then(res => {
        if (res.data.code == 200) {
          this.pagecount = res.data.data.pagecount;
          this.commentuserData = res.data.data.item;
          this.tableloading = false;
        }
      });
    },
    handleCurrentChange(val) {
      this.page = val;
      this.getcommentuser();
    },
    handleInfo(params) {
      this.replyVisible = true;
      this.comment_id = params.comment_id;
      commentuserInfo({
        comment_id: params.comment_id
      }).then(res => {
        this.reply = params;
        this.replydata = res.data.data.item;
      });
    },
    replyson(params1, params2) {
      this.replysonVisible = true;
      this.reply_comment_id = params1.comment_id;
      // this.reply_user_name = params1.user_name;
      if (params1.user_id != undefined) this.reply_user_id = params1.user_id;
      this.btn = params2;
    },
    replySubmit() {
      let para = {
        comment_id: this.reply.comment_id,
        user_name: this.reply_user_name,
        content: this.reply_content
      };
      if (this.btn) {
        para.to_fd = this.reply_comment_id;
      }
      commentuserSend(para).then(res => {
        console.log(res);
        if (res.code == 200) {
          this.$message({
            message: res.msg,
            type: "success"
          });
          this.replysonVisible = false;
          commentuserInfo({
            comment_id: this.comment_id
          }).then(res => {
            this.replydata = res.data.data.item;
          });
        }
      });
    },
    enablesta(params1, params2) {
      enableCommentuser({
        comment_id: params1.comment_id
      }).then(res => {
        // console.log(res);
        if (res.code == 200) {
          this.$message({
            message: res.msg,
            type: "success"
          });
          if (params2) {
            this.reply.status = (this.reply.status - 1) * -1;
          }

          // one.onclick()
          commentuserInfo({
            comment_id: this.comment_id
          }).then(res => {
            this.replydata = res.data.data.item;
          });
        }
      });
    },
    handleEdit(params) {
      this.editFormVisible = true;
      this.editForm = params;
    },
    editSubmit() {
      var that = this;
      this.$confirm("确认提交吗？", "提示", {}).then(() => {
        let params = {
          comment_id: this.editForm.comment_id,
          comment_rank: this.editForm.comment_rank,
          content: this.editForm.content
        };
        editCommentuser(params).then(res => {
          if (res.code == 200) {
            that.$message({
              message: res.msg,
              type: "success"
            });
            that.editFormVisible = false;
            that.getcommentuser();
          } else {
            that.$message({
              message: res.msg,
              type: "warning"
            });
          }
        });
      });
    }
  },
  mounted() {
    this.getcommentuser();
  }
};
</script>
