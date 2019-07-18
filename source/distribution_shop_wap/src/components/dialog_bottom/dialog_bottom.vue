<template lang="html">

    <transition name="slide-up">
      <div class="b_dialog">
          <div class="b_model" @click="change_swt">

          </div>
          <div class="b_con">
            <slot></slot>
          </div>
      </div>
    </transition>


</template>

<script>
export default {
  methods: {
    change_swt() {
      this.$emit("change_swt");
    }
  }
};
</script>

<style lang="less">
.b_dialog {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0.91rem;
  z-index: 3;
  background: rgba(0, 0, 0, 0.6);
  transition: all 0.5s cubic-bezier(0.55, 0, 0.1, 1);
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
  .b_model {
    flex: 1;
  }
  .b_con {
    min-height: 8rem;
    background: white;
    position: relative;
  }
}

.slide-down-leave-active,
.slide-up-enter {
  opacity: 0;

  -webkit-transform: translate(0, 100%);
  transform: translate(0, 100%);
}
.slide-down-enter,
.slide-up-leave-active {
  opacity: 0;
  -webkit-transform: translate(0, 100%);
  transform: translate(0, 100%);
}
</style>
