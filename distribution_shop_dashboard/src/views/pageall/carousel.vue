<template>
	<el-tabs v-model="activeName">
		<el-tab-pane label="轮播图管理" name="first">
			<div id="aaa">
				<pubcarousel v-if="activeName=='first'" carousel-position="0" carousel-type="1"></pubcarousel>
			</div>
		</el-tab-pane>
		<el-tab-pane label="文章中心" name="third">
			<div id="ccc">
				<pubcarousel v-if="activeName=='third'" carousel-position="2" carousel-type="1"></pubcarousel>
			</div>
		</el-tab-pane>
		<el-tab-pane label="品牌专区" name="four">
			<div id="ddd">
				<pubcarousel v-if="activeName=='four'" carousel-position="3" carousel-type="1" desc="第一张图片大小为370X580,第二第三张为370X288"></pubcarousel>
			</div>
		</el-tab-pane>
		<el-tab-pane label="限时折扣" name="five">
			<div id="eee">
				<pubcarousel v-if="activeName=='five'" carousel-position="4" carousel-type="1" ></pubcarousel>
			</div>
		</el-tab-pane>
		<!-- <el-tab-pane label="热门搜索" name="six">
			<div id="fff">
				<hotserch v-if="activeName=='six'"></hotserch>
			</div>
		</el-tab-pane> -->
	</el-tabs>
</template>
<script>
// import hotserch from "./adcompoments/hotserch";
import pubcarousel from "./adcompoments/pubcarousel";
export default {
  components: {
    // hotserch,
    pubcarousel
  },
  data() {
    return {
      activeName: "first",
      activeName2:"one",
      activeName3:"three",
      activeName4:"five",
      activeName5:"seven"
    };
  }
};
</script>
<style>
</style>

