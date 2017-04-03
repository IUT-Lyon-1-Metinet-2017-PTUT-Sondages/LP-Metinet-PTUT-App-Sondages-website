<template>
    <div class="asided">

        <div class="aside text-center">
            <button @click="addPageBefore(index)" class="btn btn-primary">Insérer une page avant</button>
        </div>

        <div class="card">
            <div class="card-header">
                Page {{ pageIndex + 1 }} sur {{ totalPages }}

                <div class="pull-right">
                    <button class="btn btn-danger btn-sm" @click="removePage"
                            :disabled="totalPages <= 1">&times;
                    </button>
                </div>
            </div>

            <div class="card-block">
                <div class="page--meta">
                    <div class="form-group" :class="{'has-danger': page.title.length == 0}" >
                        <input v-model="page.title" required
                                :name="'poll[pages][' + pageIndex + '][title]'"
                               class="form-control form-control-md" placeholder="Titre de la page (requis)">
                    </div>

                    <div class="form-group">
                        <textarea v-model="page.description"
                                  :name="'poll[pages][' + pageIndex + '][description]'"
                                  class="form-control" placeholder="Description (facultative)"></textarea>
                    </div>
                    <hr>
                </div>

                <div class="page--content">
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
            <button @click="addPageAfter(index)" class="btn btn-primary">Insérer une page après</button>
        </div>
    </div>
</template>

<script>
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';
  import Store from '../stores/admin-add-poll';

  export default {
    props: {
      page: {type: Object, required: true},
      pageIndex: {type: Number, required: true}
    },
    data () {
      return {
        Store,
      }
    },
    computed: {
      totalPages () {
        return Store.poll.pages.length;
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