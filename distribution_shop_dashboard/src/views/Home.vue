<template>
  <el-container>
      <div class="nav-menu" id="nav-menu" style="min-width:0px;width:auto">
          <el-menu
            :default-active="defActive"
            class="el-menu-vertical-demo"
            active-text-color="#ffd04b"
            :collapse="isCollapse"
            style="height:100%;"
            router>
              <el-menu-item index="/" style="border-bottom:1px solid #e6e6e6;height:60px;">
                  <!-- <i class="fa fa-home" aria-hidden="true" style="margin-top:29px;"></i> -->
                  <span slot="title" style="font-size:20px;"><i class="fa fa-home" aria-hidden="true"></i>后台</span>
              </el-menu-item>
              <el-submenu :index="index+'1'" :key="index+'1'" v-for="(item,index) in $router.options.routes" v-if="!item.hidden&&item.children.length>1" >
                  <template slot="title" >
                  <i :class="item.iconCls"></i>
                  <span>{{item.name}}</span>
                  </template>
                  <el-menu-item-group style="z-index:999999">
                  <el-menu-item :index="child.path" :key="child.path"  v-for="(child,i) in item.children" @click="createTag(child.name,child.path)">{{child.name}}</el-menu-item>
                </el-menu-item-group>
              </el-submenu>
              <el-menu-item  :index="item.children[0].path" :key="item.children[0].path" v-for="(item,index) in $router.options.routes" v-if="!item.hidden&&item.children.length==1" >
                  <i :class="item.iconCls"></i>
                  <span slot="title"  @click="createTag(item.children[0].name,item.children[0].path)">{{item.children[0].name}}</span>
              </el-menu-item>
          </el-menu>
      </div>
  <el-container>
      <el-header style="border-bottom:1px solid #e6e6e6;padding:0;">
          <div class="nav-hidden" @click="changeNav">
            <i class="fa fa-align-justify"></i>
          </div>
          <div class="createTag">
            <router-link class="tabs-view" v-for="tag in viewtags" :to="tag.path" :key="tag.path">
              <el-tag :closable="true" :type="isActive(tag.path)?'danger':''" @close="handleClose(tag,$event)" size="medium">
                {{tag.name}}
              </el-tag>
            </router-link>
          </div>
          <div class="login-out">
            <el-dropdown size="medium">
              <span class="el-dropdown-link">
              <img style="width: 40px; height: 40px;margin-top:10px;" :src="sysUserAvatar" alt="">
              </span>
              <el-dropdown-menu slot="dropdown" >
                <el-dropdown-item @click.native="dropOut" >退出</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </div>
           <div class="fullscreen" @click="fullScreen">
            <i class="fa fa-arrows-alt"></i>
          </div>
       </el-header>

    <el-main class="main">
						<el-breadcrumb separator="/" class="breadcrumb-inner" >
							<el-breadcrumb-item v-for="item in $route.matched" :key="item.path" >
								{{ item.name }}
							</el-breadcrumb-item>
						</el-breadcrumb>
      <div class="main-con">
        <router-view ></router-view>
      </div>
    </el-main>
  </el-container>
</el-container>
</template>
<script>
import ScrollBar from "vue2-scrollbar";
import "font-awesome/css/font-awesome.min.css";
import screenfull from "screenfull";
import adminlogo from "../assets/adminlogo.jpg";
export default {
  data() {
    return {
      isCollapse: false,
      RoutesTags: [],
      viewtags: [],
      sysUserAvatar: adminlogo,
      isfullscreen: false,
      defActive: "",
      is_bang: "",
      bangFormVisible: false,
      bangForm: {
        user_name: ""
      }
    };
  },
  methods: {
    isActive(path) {
      return path === this.$route.path;
    },
    createTag(p1, p2) {
      var that = this;
      this.RoutesTags.push({ name: p1, path: p2 });
      this.RoutesTags = this.uniqeByKeys(this.RoutesTags, ["name"]);
      var start = 0;
      for (var i = 0; i < this.RoutesTags.length; i++) {
        if (this.RoutesTags[i].name == p1) {
          start = i;
        }
      }
      if (this.RoutesTags.length - start >= 6) {
        this.viewtags = this.RoutesTags.slice(start, start + 6);
      } else {
        this.viewtags = this.RoutesTags.slice(-6);
      }
    },
    handleClose(tag, $event) {
      this.RoutesTags.splice(this.RoutesTags.indexOf(tag), 1);
      this.viewtags = this.RoutesTags.slice(-6);
      if (this.viewtags.length == 0) {
        this.$router.push("/");
      } else {
        this.$router.push(this.viewtags[this.viewtags.length - 1].path);
      }
      $event.preventDefault();
    },
    fullScreen() {
      if (!screenfull.enabled) {
        this.$message({
          message: "you browser can not work",
          type: "warning"
        });
        return false;
      }
      screenfull.toggle();
    },
    dropOut() {
      this.$confirm("确认退出吗?", "提示", {}).then(() => {
        this.setcookie("name999", "", "-1");
        this.setcookie("password999", "", "-1");
        sessionStorage.removeItem("user");
        this.$router.push("/login");
        location.reload();
      });
    },
    changeNav() {
      // this.isCollapse=true
      if (this.isCollapse == true) {
        this.isCollapse = false;
        // document.getElementById('nav-menu').style.width='200px'
      } else {
        this.isCollapse = true;
        // document.getElementById('nav-menu').style.width='66px'
      }
    }
  },
  mounted() {
    this.defActive = this.$route.path; //设置路由默认展示
    this.is_bang = JSON.parse(sessionStorage.getItem("data")).data.anthor_name;
  }
};
</script>
<style scope lang="scss">
.createTag {
  float: left;
  line-height: 60px;
  .tabs-view {
    margin-left: 10px;
  }
}
.el-menu-vertical-demo:not(.el-menu--collapse) {
  width: 199px;
}
.nav-menu .fa {
  font-size: 20px;
  padding-right: 8px;
  color: #409eff;
}
.nav-menu::-webkit-scrollbar {
  width: 6px;
  height: 16px;
  background-color: #f5f5f5;
}
/*定义滚动条的轨道，内阴影及圆角*/
.nav-menu::-webkit-scrollbar-track {
  -webkit-box-shadow: inset 0 0 6px #f2f6fc;
  border-radius: 10px;
  background-color: #f5f5f5;
}
/*定义滑块，内阴影及圆角*/
.nav-menu::-webkit-scrollbar-thumb {
  /*width: 10px;*/
  height: 20px;
  border-radius: 10px;
  -webkit-box-shadow: inset 0 0 6px #f2f6fc;
  background-color: #3a8ee6;
}
.main::-webkit-scrollbar {
  width: 6px;
  height: 16px;
  background-color: #f5f5f5;
}
/*定义滚动条的轨道，内阴影及圆角*/
.main::-webkit-scrollbar-track {
  -webkit-box-shadow: inset 0 0 6px #f2f6fc;
  border-radius: 10px;
  background-color: #f5f5f5;
}
/*定义滑块，内阴影及圆角*/
.main::-webkit-scrollbar-thumb {
  /*width: 10px;*/
  height: 20px;
  border-radius: 10px;
  -webkit-box-shadow: inset 0 0 6px #f2f6fc;
  background-color: #3a8ee6;
}
.nav-hidden {
  float: left;
  line-height: 60px;
  padding: 0 10px;
}
.login-out {
  float: right;
  padding-right: 20px;
}
.fullscreen {
  float: right;
  line-height: 60px;
  font-size: 24px;
  margin-right: 20px;
  cursor: pointer;
}
.header-logo {
  height: 100%;
  font-size: 30px;
  line-height: 60px;
  border-right: 1px solid #e6e6e6;
  float: left;
  width: 199px;
}
.el-container {
  height: 100%;
}
.main {
  position: relative;
  height: 100%;
  padding: 10px;
}
.main-con {
  /* padding: 35px; */
  width: 99%;
  position: absolute;
  top: 30px;
  // left: 3%;
  right: 0px;
  bottom: 0px;
  padding: 0;
  margin: 0;
}
/* .main-con div{
 padding-bottom: 20px;
} */
#nav-menu {
  // height: 100%;
  overflow: auto;
  /* width: 200px; */
}
.el-table .cell {
  font-size: 12px;
  line-height: 16px;
}
// .el-table td{
//   padding: 3px;
// }
</style>
