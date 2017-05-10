<template>
    <div class="asided question">

        <!-- Ajouter une question avant -->
        <div class="aside text-center">
            <button @click.prevent="addQuestionBefore" class="btn btn-primary btn-sm">
                {{ $t('question.insert.before') }}
            </button>
        </div>

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
                    <!-- Titre de la question -->
                    <div class="col-md-8 col-sm-6 form-group"
                         :class="{'has-danger': question.title.length == 0}">

                        <input v-model="question.title" required
                               :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][title]'"
                               :placeholder="$t('question.placeholder.title')"
                               class="form-control">
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

                <!-- Bouton ajouter une proposition, si c'est pas une variant LINEAR_SCALE -->
                <div v-if="question.variant.name !== 'LinearScale'" class="text-center">
                    <button @click.prevent="addProposition"
                            :disabled="question.propositions.length >= 12"
                            class="btn btn-sm">
                        {{ $t('proposition.add') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Ajouter une question après -->
        <div class="aside text-center">
            <button @click.prevent="addQuestionAfter" class="btn btn-primary btn-sm">
                {{ $t('question.insert.after') }}
            </button>
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
      ...mapGetters(['variants']),
      totalPageQuestions () {
        return this.page.questions.length;
      }
    },
    methods: {
      addQuestionBefore() {
        Bus.$emit(Event.ADD_QUESTION_BEFORE, this.page, this.index)
      },
      addQuestionAfter() {
        Bus.$emit(Event.ADD_QUESTION_AFTER, this.page, this.index)
      },
      removeQuestion() {
        Bus.$emit(Event.REMOVE_QUESTION, this.page, this.index);
      },
      addProposition() {
        this.question.propositions.push({
          title: this.$t('proposition.default.title')
        })
      }
    }
  }
</script>

<style scoped>
    .question {
        margin: 2em 0;
    }
</style>
