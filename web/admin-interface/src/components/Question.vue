<template>
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
                <div class="col-md-8 form-group">
                    <input v-model="question.question.title" class="form-control">
                </div>
                <div class="col-md-4 form-group">
                    <select v-model="question.variant.name"
                            class="form-control" title="Type de la question">
                        <option v-for="humanized, variant in HumanizedVariants" :value="VariantsId[variant]">
                            {{humanized}}
                        </option>
                    </select>
                </div>
            </div>
            <button @click="question.propositions.push({title: 'qsd'})"
                    class="btn btn-sm"
                    :disabled="question.propositions.length >= 10">
                Ajouter prop
            </button>
            <ol>
                <li v-for="proposition in question.propositions">
                    <input v-model="proposition.title" class="form-control">
                </li>
            </ol>
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
      page: {
        type: Object,
        required: true,
      },
      question: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      }
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
      removeQuestion() {
        Bus.$emit(Event.REMOVE_QUESTION_FROM_PAGE, this.page, this.index);
      }
    }
  }
</script>

<style>

</style>