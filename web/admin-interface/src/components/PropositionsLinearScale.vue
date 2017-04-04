<template>
    <transition name="fade" appear>
        <div class="form-inline justify-content-center">
            <select v-model="min" class="form-control">
                <option v-for="v in [0, 1]" :value="v">{{v}}</option>
            </select>

            <span class="m-1">
                {{ $t('poll.page.question.proposition.variants.LinearScale.to') }}
            </span>

            <select v-model="max" class="form-control">
                <option v-for="v in [2, 3, 4, 5, 6, 7, 8, 9, 10]" :value="v">{{v}}</option>
            </select>

            <template v-for="proposition, propositionIndex in question.propositions">
                <input :value="proposition.title" type="hidden"
                       :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][propositions][' +  propositionIndex + '][title]'">
            </template>

        </div>
    </transition>
</template>

<script>
  export default {
    props: {
      page: {type: Object, required: true},
      pageIndex: {type: Number, required: true},
      question: {type: Object, required: true},
      questionIndex: {type: Number, required: true},
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
    beforeDestroy() {
      this.question.propositions = [
        {title: ''},
      ];
    }
  }
</script>