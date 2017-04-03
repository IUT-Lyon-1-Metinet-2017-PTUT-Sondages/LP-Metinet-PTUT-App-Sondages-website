<template>
    <transition name="fade" appear>
        <div class="form-inline justify-content-center">
            <select v-model="min" class="form-control">
                <option v-for="v in [0, 1]" :value="v">{{v}}</option>
            </select>
            <span class="m-1">à</span>
            <select v-model="max" class="form-control">
                <option v-for="v in [2, 3, 4, 5, 6, 7, 8, 9, 10]" :value="v">{{v}}</option>
            </select>
        </div>
    </transition>
</template>

<script>
  export default {
    props: {
      question: {type: Object, required: true},
    },
    data () {
      return {
        min: 0,
        max: 5,
      }
    },
    watch: {
      min() {
        this.generatePropositionsArray();
      },
      max() {
        this.generatePropositionsArray();
      }
    },
    methods: {
      generatePropositionsArray () {
        this.question.propositions = [...new Array(this.max - this.min + 1)].map((n, index) => {
          return {title: this.min + index}
        });
      }
    },
    mounted() {
      this.generatePropositionsArray();
    },
    destroyed() {
      this.question.propositions = [
        {title: ''},
        {title: ''},
        {title: ''}
      ];
    }
  }
</script>