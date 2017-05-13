<template>
  <div class="card">
    <div class="card-header">
      {{ $t('question.x_on_y', {x: questionIndex + 1, y: totalPageQuestions}) }}

      <div class="pull-right">
        <button @click.prevent="removeQuestion" :disabled="totalPageQuestions <= 1"
                class="btn btn-danger btn-sm">
          &times;
        </button>
      </div>
    </div>
    <div class="card-block">
      <div class="row">
        <input v-if="isEditingPoll && 'in' " :value="question.id"
               :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][id]'"
               type="hidden">

        <!-- Titre de la question -->
        <div class="col-md-8 col-sm-6 form-group"
             :class="{'has-danger': question.title.error}">
          <input v-model="question.title.value"
                 :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][title]'"
                 :placeholder="$t('question.placeholder.title')"
                 class="form-control">
          <div v-if="question.title.error" class="form-control-feedback">{{ question.title.error }}</div>
        </div>

        <!-- Type de la question (variante) -->
        <div class="col-md-4 col-sm-6 form-group">
          <select v-model="question.variant.name" class="form-control"
                  :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][variant][name]'"
                  :title="$t('question.type')">
            <option v-for="variant in variants" :value="variant">
              {{ $t('proposition.variants.types.' + variant) }}
            </option>
          </select>
        </div>
      </div>

      <!-- Rendu dynamique (:is) des propositions en fonction de la variante -->
      <div :is="'propositions' + question.variant.name"
           :page="page" :pageIndex="pageIndex"
           :question="question" :questionIndex="questionIndex"
      ></div>

      <!-- Bouton ajouter une proposition, si c'est pas une variant LinearScale-->
      <button-insert-here v-if="question.variant.name !== 'LinearScale'"
                          @click.prevent="addProposition" :disabled="question.propositions.length > 12">
        {{ $t('proposition.add') }}
      </button-insert-here>
    </div>
  </div>
</template>

<script>
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';
  import {mapGetters} from "vuex";

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
      ...mapGetters(['isEditingPoll', 'variants']),
      totalPageQuestions () {
        return this.page.questions.length;
      }
    },
    methods: {
      removeQuestion() {
        Bus.$emit(Event.REMOVE_QUESTION, this.question, this.page);
      },
      addProposition() {
        Bus.$emit(Event.ADD_PROPOSITION, this.question);
      }
    }
  }
</script>
