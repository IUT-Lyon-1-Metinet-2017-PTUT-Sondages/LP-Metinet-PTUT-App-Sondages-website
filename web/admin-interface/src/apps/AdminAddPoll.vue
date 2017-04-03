<template>
    <form class="form-horizontal container" method="post" :action="FORM_ACTION">
        <h1 class="text-center">Création du sondage</h1>
        <hr>

        <div class="form-group" :class="{'has-danger': Store.poll.title.length == 0}">
            <input v-model="Store.poll.title" name="poll[title]" required
                   class="form-control form-control-lg" placeholder="Titre du formulaire (requis)">
        </div>

        <div class="form-group">
            <textarea v-model="Store.poll.description" name="poll[description]"
                      class="form-control" placeholder="Description du formulaire (facultative)"></textarea>
        </div>

        <hr>
        <transition-group name="fade" tag="div">
            <page v-for="page, pageIndex in Store.poll.pages"
                  :key="page"
                  :page="page" :pageIndex="pageIndex"></page>
        </transition-group>
        <hr>

        <pre>{{ JSON.stringify(Store.poll, null, 2) }}</pre>

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">Créer</button>
        </div>
    </form>
</template>

<script>
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';
  import {
    Id as VariantsId
  } from '../variants';
  import Store from '../stores/admin-add-poll';

  export default {
    data () {
      return {
        Store,
        FORM_ACTION,
      }
    },
    methods: {
      onSubmit () {
        console.log(JSON.stringify(Store.poll));
      },
      addPageBefore(index = 0) {
        this._addPage(index);
      },
      addPageAfter(index = 0) {
        this._addPage(index, false);
      },
      removePage (page) {
        const index = Store.poll.pages.indexOf(page);
        Store.poll.pages.splice(index, 1);
      },
      addQuestionBefore(page, questionIndex = 0) {
        this._addQuestion(page, questionIndex);
      },
      addQuestionAfter(page, questionIndex = 0) {
        this._addQuestion(page, questionIndex, false);
      },
      removeQuestion(page, questionIndex) {
        page.questions.splice(questionIndex, 1);
      },
      _addPage(pageIndex = 0, before = true) {
        pageIndex += (before ? 0 : 1);

        Store.poll.pages.splice(pageIndex, 0, {
          title: 'Page sans titre',
          description: '',
          questions: []
        });

        this.addQuestionBefore(Store.poll.pages[pageIndex]);
      },
      _addQuestion(page, questionIndex = 0, before = true) {
        questionIndex += (before ? 0 : 1);

        page.questions.splice(questionIndex, 0, {
          variant: {
            name: VariantsId.CHECKBOX
          },
          question: {
            title: 'Question sans titre',
          },
          propositions: [
            {title: ''},
          ]
        });
      }
    },
    mounted () {
      Bus.$on(Event.ADD_PAGE_BEFORE, this.addPageBefore);
      Bus.$on(Event.ADD_PAGE_AFTER, this.addPageAfter);
      Bus.$on(Event.REMOVE_PAGE, this.removePage);

      Bus.$on(Event.ADD_QUESTION_BEFORE, this.addQuestionBefore);
      Bus.$on(Event.ADD_QUESTION_AFTER, this.addQuestionAfter);
      Bus.$on(Event.REMOVE_QUESTION, this.removeQuestion);

      this.addPageBefore();
    },
    destroyed () {
      Store.poll.pages = [];

      Bus.$off(Event.ADD_PAGE_BEFORE, this.addPageBefore);
      Bus.$off(Event.ADD_PAGE_AFTER, this.addPageAfter);
      Bus.$off(Event.REMOVE_PAGE, this.removePage);

      Bus.$off(Event.ADD_QUESTION_BEFORE, this.addQuestionBefore);
      Bus.$off(Event.ADD_QUESTION_AFTER, this.addQuestionAfter);
      Bus.$off(Event.REMOVE_QUESTION, this.removeQuestion);
    }
  }
</script>

<style lang="scss" rel="stylesheet/scss">
    .asided {
        position: relative;
        margin: 24px 0;
    }

    .aside {
        position: absolute;
        z-index: 1;
        left: 50%;
        top: 0;
        transform: translate(-50%, -50%);

        transition: opacity .2s ease-in-out;
        opacity: 0;
    }

    .asided .aside:last-child {
        top: auto;
        bottom: 0;
        transform: translate(-50%, 50%);
    }

    .asided:hover > .aside {
        opacity: 1;
    }
</style>

<style>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .2s
    }

    .fade-enter, .fade-leave-to {
        opacity: 0
    }
</style>