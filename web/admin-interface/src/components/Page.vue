<template>
  <div class="card">
    <div class="card-header">
      {{ $t('page.x_on_y', {x: pageIndex + 1, y: totalPages}) }}

      <div class="pull-right">
        <button class="btn btn-danger btn-sm" @click.prevent="removePage"
                :disabled="isSubmittingPoll || totalPages <= 1">
          &times;
        </button>
      </div>
    </div>

    <div class="card-block">
      <div class="page--meta">
        <input v-if="isEditingPoll && 'id' in page" :value="page.id" :name="'poll[pages][' + pageIndex + '][id]'"
               type="hidden">

        <!-- Le titre de la page -->
        <div class="form-group" :class="{'has-danger': page.title.error || page.title.length == 0}">
          <input v-model="page.title.value" :name="'poll[pages][' + pageIndex + '][title]'"
                 :disabled="isSubmittingPoll"
                 :placeholder="$t('page.placeholder.title')"
                 class="form-control form-control-md">
          <div v-if="page.title.error" class="form-control-feedback">{{ page.title.error }}</div>
        </div>

        <!-- La description de la page -->
        <div class="form-group">
          <textarea v-model="page.description.value" :name="'poll[pages][' + pageIndex + '][description]'"
                    :disabled="isSubmittingPoll"
                    :placeholder="$t('page.placeholder.description')"
                    class="form-control"></textarea>
        </div>
      </div>

      <div class="page--content">
        <!-- Le rendu des questions de la page actuelle -->
        <transition-group name="fade" tag="div">
          <template v-for="question, questionIndex in page.questions">
            <question :key="questionIndex"
                      :page="page" :pageIndex="pageIndex"
                      :question="question" :questionIndex="questionIndex"></question>
          </template>
        </transition-group>

        <!-- Bouton pour ajouter une question avant -->
        <button-insert-here @click.prevent="addQuestion" :block="true">
          {{ $t('question.add') }}
        </button-insert-here>
      </div>
    </div>
  </div>
</template>

<script>
  import {mapGetters} from 'vuex';
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';

  export default {
    props: {
      poll: {type: Object, required: true},
      page: {type: Object, required: true},
      pageIndex: {type: Number, required: true}
    },
    computed: {
      ...mapGetters(['isEditingPoll', 'isSubmittingPoll']),
      totalPages () {
        return this.poll.pages.length;
      }
    },
    methods: {
      addQuestion() {
        Bus.$emit(Event.ADD_QUESTION, this.page)
      },
      removePage() {
        Bus.$emit(Event.REMOVE_PAGE, this.page);
      },
    },
  }
</script>

<style scoped lang="scss" rel="stylesheet/scss">
  $color: purple;

  .card {
    margin: 1em 0;
  }
</style>
