<template>
  <div class="text-center">
    <button @click="emit($event)" :disabled="isSubmittingPoll" class="btn btn-outline-primary" :class="classes">
      <i class="fa fa-plus-circle"></i>
      <slot></slot>
    </button>
  </div>
</template>

<script>
  import {mapGetters} from "vuex";
  export default {
    props: {
      size: {type: String, default: ''},
      block: {type: Boolean, default: false}
    },
    data()  {
      return {
        classes: {
          'btn-block': !!this.block,
          'btn-sm': this.size === 'small',
          'btn-lg': this.size === 'large',
        }
      }
    },
    computed: {
      ...mapGetters(['isSubmittingPoll'])
    },
    methods: {
      emit(e) {
        this.$emit('click', e)
      }
    }
  }
</script>

<style scoped>
  .btn {
    margin: 1em 0;
  }
</style>
