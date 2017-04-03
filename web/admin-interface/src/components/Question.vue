<template>
    <div class="asided question">
        <div class="aside text-center">
            <button @click.prevent="addQuestionBefore"
                    class="btn btn-primary btn-sm">
                Insérer une question après
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                Question {{ questionIndex + 1 }} sur {{ totalPageQuestions }}

                <div class="pull-right">
                    <button @click.prevent="removeQuestion" :disabled="totalPageQuestions <= 1"
                            class="btn btn-danger btn-sm">
                        &times;
                    </button>
                </div>
            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-8 col-sm-6 form-group"
                         :class="{'has-danger': question.question.title.length == 0}">
                        <input v-model="question.question.title" required
                               :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][question][title]'"
                               placeholder="Titre de la question (requis)" class="form-control">
                    </div>
                    <div class="col-md-4 col-sm-6 form-group">
                        <select v-model="question.variant.name"
                                :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][variant][name]'"
                                class="form-control" title="Type de la question">
                            <option v-for="humanized, variant in HumanizedVariants" :value="VariantsId[variant]">
                                {{humanized}}
                            </option>
                        </select>
                    </div>
                </div>

                <div :is="'propositions' + question.variant.name"
                     :page="page" :pageIndex="pageIndex"
                     :question="question" :questionIndex="questionIndex"
                ></div>

                <div v-if="question.variant.name !== VariantsId.LINEAR_SCALE " class="text-center">
                    <button @click.prevent="question.propositions.push({title: ''})"
                            :disabled="question.propositions.length >= 12"
                            class="btn btn-sm">Ajouter une proposition
                    </button>
                </div>
            </div>
        </div>

        <div class="aside text-center">
            <button @click.prevent="addQuestionAfter" class="btn btn-primary btn-sm">
                Insérer une question après
            </button>
        </div>
    </div>
</template>

<script>
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';
  import {
    Id as VariantsId,
    Humanized as HumanizedVariants
  } from '../variants';

  export default {
    props: {
      page: {type: Object, required: true},
      pageIndex: {type: Number, required: true},
      question: {type: Object, required: true},
      questionIndex: {type: Number, required: true},
    },
    data () {
      return {
        VariantsId,
        HumanizedVariants
      }
    },
    computed: {
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
      }
    }
  }
</script>

<style scoped>
    .question {
        margin: 2em 0;
    }
</style>