<template>
    <div class="asided">

        <div class="aside text-center">
            <button @click="addPageBefore(index)" class="btn btn-primary">Insérer une page avant</button>
        </div>

        <div class="card">
            <div class="card-header">
                Page {{ index + 1 }} sur {{ totalPages }}

                <div class="pull-right">
                    <button class="btn btn-danger btn-sm" @click="removePage"
                            :disabled="totalPages <= 1">&times;
                    </button>
                </div>
            </div>

            <div class="card-block">
                <div class="page--meta">
                    <div class="form-group">
                        <input :id="'page-title-' + index" v-model="page.title"
                               class="form-control form-control-md">
                    </div>

                    <div class="form-group">
                        <textarea :id="'page-description-' + index" v-model="page.description"
                                  class="form-control" placeholder="Description (facultative)"></textarea>
                    </div>
                    <hr>
                </div>

                <div class="page--content">
                    <transition-group name="fade" tag="div">
                        <template v-for="question, index in page.questions">
                            <question :key="index" :page="page" :question="question" :index="index"></question>
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
      index: {type: Number, required: true}
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