<template>
  <transition name="fade" appear>
    <div class="form-inline justify-content-center">
      <!-- <select> valeur minimale -->
      <select v-model="min"
              :disabled="isSubmittingPoll"
              class="form-control">
        <option v-for="v in [0, 1]" :value="v">{{v}}</option>
      </select>

      <span class="m-1">
        {{ $t('proposition.variants.LinearScale.to') }}
      </span>

      <!-- <select> valeur maximale -->
      <select v-model="max"
              :disabled="isSubmittingPoll"
              class="form-control">
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
  import * as Event from '../bus/events';

  export default {
    props: {
      page: {type: Object, required: true},
      pageIndex: {type: Number, required: true},
      question: {type: Object, required: true},
      questionIndex: {type: Number, required: true},
    },
    computed: {
      ...mapGetters(['isEditingPoll', 'isSubmittingPoll'])
    },
    data () {
      return {
        min: 0,
        max: 5,
      }
    },
    watch: {
      min () {
        this.question.propositions = [];
        this.generatePropositions();
      },
      max () {
        this.question.propositions = [];
        this.generatePropositions();
      }
    },
    methods: {
      generatePropositions () {
        for (let i = this.min; i <= this.max; i++) {
          Bus.$emit(Event.ADD_PROPOSITION, this.question, i);
        }
      },
    },
    created () {
//      this.question.propositions.forEach(p => {
//        this.removeProposition(p);
//      })
    },
    mounted() {
      // équivaut à un tableau de propositions vides
      if (this.question.propositions.length === 0 || this.question.propositions[0].title.value === '') {
        this.question.propositions = [];
        this.generatePropositions();
      } else {
        const values = this.question.propositions.map(p => parseInt(p.title.value, 10));
        const min = Math.min(...values);
        const max = Math.max(...values);

        // On supprime les propositions à partir de la fin, car si on supprime
        // p[0], alors p[1] devient p[0], et on supprime que la moitié des propositions
        for (let i = this.question.propositions.length; i--;) {
          const proposition = this.question.propositions[i];
          Bus.$emit(Event.REMOVE_PROPOSITION, proposition, this.question);
        }

        if (!isNaN(min)) {
          this.min = min;
        }

        if (!isNaN(max)) {
          this.max = max;
        }

        this.generatePropositions();
      }
    },
    beforeDestroy() {
      this.question.propositions = [];
      Bus.$emit(Event.ADD_PROPOSITION, this.question);
    }
  }
</script>
