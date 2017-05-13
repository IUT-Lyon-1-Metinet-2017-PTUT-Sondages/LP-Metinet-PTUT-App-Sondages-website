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
        <input v-if="isEditingPoll && 'id' in proposition" :value="proposition.id" type="hidden"
               :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][propositions][' +  propositionIndex + '][id]'">

        <input :value="proposition.title.value" type="hidden"
               :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][propositions][' +  propositionIndex + '][title]'">
      </template>
    </div>
  </transition>
</template>

<script>
  import {mapGetters} from 'vuex';
  import Bus from '../bus/admin-add-poll';
  import {REMOVE_PROPOSITION} from '../bus/events';

  export default {
    props: {
      page: {type: Object, required: true},
      pageIndex: {type: Number, required: true},
      question: {type: Object, required: true},
      questionIndex: {type: Number, required: true},
    },
    computed: {
      ...mapGetters(['isEditingPoll'])
    },
    data () {
      return {
        min: 0,
        max: 5,
      }
    },
    watch: {
      min() {
        this.question.propositions = this.generatePropositions();
      },
      max() {
        this.question.propositions = this.generatePropositions();
      }
    },
    methods: {
      generatePropositions () {
        // Créer un Array de taille (max - min + 1), où les valeurs == {title: min + index}
        // ex [{title: 0}, {title: 1}, {title: 2}, {title: 3}, ...]
        return [...new Array(this.max - this.min + 1)].map((n, index) => {
          return {
            title: {
              value: this.min + index,
              error: null
            }
          }
        });
      },
      removeProposition(proposition) {
        Bus.$emit(REMOVE_PROPOSITION, this.question, proposition);
      }
    },
    created () {
      console.log(JSON.stringify(this.question.propositions));
      this.question.propositions.forEach(p => {
        this.removeProposition(p);
      })
    },
    mounted() {
      // équivaut à un tableau de propositions vides
      if (this.question.propositions.length === 0 || this.question.propositions[0].title.value === '') {
        this.question.propositions = this.generatePropositions();
      }
    },
    beforeDestroy() {
      this.question.propositions = [
        {
          title: {
            value: '',
            error: null
          }
        },
      ];
    }
  }
</script>