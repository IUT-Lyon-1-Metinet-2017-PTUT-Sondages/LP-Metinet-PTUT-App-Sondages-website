<template>
    <transition name="fade" appear>
        <div class="form-inline justify-content-center">

            <!-- <select> valeur minimale -->
            <select v-model="min" class="form-control">
                <option v-for="v in [0, 1]" :value="v">{{v}}</option>
            </select>

            <span class="m-1">
                {{ $t('proposition.variants.LinearScale.to') }}
            </span>

            <!-- <select> valeur maximale -->
            <select v-model="max" class="form-control">
                <option v-for="v in [2, 3, 4, 5, 6, 7, 8, 9, 10]" :value="v">{{v}}</option>
            </select>

            <!-- Rendu de <input type="hidden"> pour le formulaire -->
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
        // Créer un Array de taille (max - min + 1), où les valeurs == {title: min + index}
        // ex [{title: 0}, {title: 1}, {title: 2}, {title: 3}, ...]
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
        {title: this.$t('proposition.default.title')},
      ];
    }
  }
</script>