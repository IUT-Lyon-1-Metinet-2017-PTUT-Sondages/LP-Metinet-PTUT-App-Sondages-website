<template>
    <div class="asided">

        <!-- Ajouter une page avant -->
        <div class="aside text-center">
            <button @click.prevent="addPageBefore(pageIndex)" class="btn btn-primary">
                {{ $t('page.insert.before') }}
            </button>
        </div>

        <!-- La page -->
        <div class="card">
            <div class="card-header">
                {{ $t('page.x_on_y', {x: pageIndex + 1, y: totalPages}) }}

                <div class="pull-right">
                    <button class="btn btn-danger btn-sm" @click="removePage"
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
                    <hr>
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

        <div class="aside text-center">
            <button @click.prevent="addPageAfter(pageIndex)" class="btn btn-primary">
                {{ $t('page.insert.before') }}
            </button>
        </div>
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