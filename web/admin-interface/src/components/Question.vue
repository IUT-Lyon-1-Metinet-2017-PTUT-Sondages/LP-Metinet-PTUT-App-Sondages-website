<template>
    <div class="asided question">
        <div class="aside text-center">
            <button @click="addQuestionBefore" class="btn btn-primary btn-sm">
                Insérer une question après
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                Question {{ index + 1 }} sur {{ totalPageQuestions }}


                <div class="pull-right">
                    <button class="btn btn-danger btn-sm" @click="removeQuestion"
                            :disabled="totalPageQuestions <= 1">&times;
                    </button>
                </div>
            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-8 col-sm-6 form-group">
                        <input v-model="question.question.title" class="form-control">
                    </div>
                    <div class="col-md-4 col-sm-6 form-group">
                        <select v-model="question.variant.name"
                                class="form-control" title="Type de la question">
                            <option v-for="humanized, variant in HumanizedVariants" :value="VariantsId[variant]">
                                {{humanized}}
                            </option>
                        </select>
                    </div>
                </div>

                <div :is="'propositions' + question.variant.name" :question="question"></div>

                <div v-if="question.variant.name !== VariantsId.LINEAR_SCALE " class="text-center">
                    <button @click="question.propositions.push({title: ''})"
                            :disabled="question.propositions.length >= 12"
                            class="btn btn-sm">Ajouter une proposition
                    </button>
                </div>
            </div>
        </div>

        <div class="aside text-center">
            <button @click="addQuestionAfter" class="btn btn-primary btn-sm">
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
      question: {type: Object, required: true},
      index: {type: Number, required: true}
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