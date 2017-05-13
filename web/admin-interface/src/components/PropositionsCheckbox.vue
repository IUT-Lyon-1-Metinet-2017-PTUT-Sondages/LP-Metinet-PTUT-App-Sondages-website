<template>
  <transition-group name="fade">
    <div v-for="proposition, propositionIndex in question.propositions" :key="propositionIndex"
         class="row my-q no-gutters">
      <input v-if="isEditingPoll && 'id' in proposition" :value="proposition.id" type="hidden"
             :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][propositions][' +  propositionIndex + '][id]'">

      <!-- Input checkbox -->
      <div class="col col-auto kdt-checkbox col-form-label mr-h">
        <input type="checkbox" disabled="disabled">
        <label></label>
      </div>

      <!-- Titre de la proposition -->
      <div class="col" :class="{'has-danger': proposition.title.error }">
        <input v-model="proposition.title.value"
               :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][propositions][' +  propositionIndex + '][title]'"
               :placeholder="$t('proposition.placeholder.proposition_x', {x: propositionIndex + 1})"
               class="form-control d-inline-block">
        <div v-if="proposition.title.error" class="form-control-feedback">{{ proposition.title.error }}</div>
      </div>

      <!-- Bouton supprimer -->
      <div class="col col-auto ml-h">
        <button class="btn btn-outline-danger"
                @click.prevent="removeProposition(proposition)"
                :disabled="question.propositions.length <= 1">&times;
        </button>
      </div>
    </div>
  </transition-group>
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
    data () {
      return {}
    },
    computed: {
      ...mapGetters(['isEditingPoll'])
    },
    methods: {
      removeProposition(proposition) {
        Bus.$emit(REMOVE_PROPOSITION, proposition, this.question);
      }
    }
  }
</script>