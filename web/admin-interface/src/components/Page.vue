<template>
    <div class="asided">

        <!-- Bouton pour ajouter une page avant -->
        <button-insert-here v-if="pageIndex === 0" @click.prevent="addPageBefore(pageIndex)" :block="true" size="large">
            {{ $t('page.insert') }}
        </button-insert-here>

        <!-- La page -->
        <div class="card">
            <div class="card-header">
                {{ $t('page.x_on_y', {x: pageIndex + 1, y: totalPages}) }}

                <div class="pull-right">
                    <button class="btn btn-danger btn-sm" @click.prevent="removePage"
                            :disabled="totalPages <= 1">&times;
                    </button>
                </div>
            </div>

            <div class="card-block">
                <div class="page--meta">
                    <!-- Le titre de la page -->
                    <div class="form-group" :class="{'has-danger': page.title.length == 0}">
                        <input v-model="page.title" required
                               :name="'poll[pages][' + pageIndex + '][title]'"
                               :placeholder="$t('page.placeholder.title')"
                               class="form-control form-control-md">
                    </div>

                    <!-- La description de la page -->
                    <div class="form-group">
                        <textarea v-model="page.description"
                                  :name="'poll[pages][' + pageIndex + '][description]'"
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
                </div>
            </div>
        </div>

        <!-- Bouton pour ajouter une page après -->
        <button-insert-here @click.prevent="addPageAfter(pageIndex)" :block="true" size="large">
            {{ $t('page.insert') }}
        </button-insert-here>
    </div>
</template>

<script>
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';

  export default {
    props: {
      poll: {type: Object, required: true},
      page: {type: Object, required: true},
      pageIndex: {type: Number, required: true}
    },
    computed: {
      totalPages () {
        return this.poll.pages.length;
      }
    },
    methods: {
      addPageBefore(pageIndex) {
        Bus.$emit(Event.ADD_PAGE_BEFORE, pageIndex);
      },
      addPageAfter(pageIndex) {
        Bus.$emit(Event.ADD_PAGE_AFTER, pageIndex);
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