<template>
    <div class="page">
        <aside-page :index="index"></aside-page>
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
                        <question v-for="question, index in page.questions" :key="question"
                                  :page="page" :question="question" :index="index"></question>
                    </transition-group>
                    <button @click="addQuestion" class="btn">Ajouter une question</button>
                </div>
            </div>
        </div>
        <aside-page :index="index" :before="false"></aside-page>
    </div>
</template>

<script>
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';
  import Store from '../stores/admin-add-poll';

  export default {
    props: {
      page: {
        type: Object,
        required: true
      }
    },
    data () {
      return {
        Store,
      }
    },
    computed: {
      index () {
        return Store.poll.pages.indexOf(this.page);
      },
      totalPages () {
        return Store.poll.pages.length;
      }
    },
    methods: {
      removePage() {
        Bus.$emit(Event.REMOVE_PAGE, this.index);
      },
      addQuestion() {
        Bus.$emit(Event.ADD_QUESTION_TO_PAGE, this.index);
      },
    },
  }
</script>

<style scoped lang="scss" rel="stylesheet/scss">
    $color: purple;

    .card {
        margin: 1em 0;
    }

    .page {
        position: relative;
        margin: 24px 0;

        &:hover {
            .aside-page {
                display: block;
            }
        }
    }

    .aside-page {
        position: absolute;
        z-index: 1;
        left: 50%;
        top: 0;
        transform: translate(-50%, -50%);
        display: none
    }

    .card ~ .aside-page {
        top: auto;
        bottom: 0;
        transform: translate(-50%, 50%);
    }
</style>