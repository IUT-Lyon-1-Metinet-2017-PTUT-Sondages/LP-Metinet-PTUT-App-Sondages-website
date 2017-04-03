<template>
    <form class="form-horizontal container" @submit.prevent="onSubmit">
        <h1 class="text-center">Création du sondage</h1>
        <hr>

        <div class="form-group">
            <input v-model="Store.poll.title" class="form-control form-control-lg">
        </div>

        <div class="form-group">
            <textarea v-model="Store.poll.description" class="form-control"
                      placeholder="Description du formulaire"></textarea>
        </div>

        <hr>
        <transition-group name="fade" tag="div">
            <page v-for="page, index in Store.poll.pages" :key="page"
                  :page="page" :index="index"></page>
        </transition-group>
        <hr>

        <pre>{{ JSON.stringify(Store.poll, null, 2) }}</pre>

        <div class="text-center">
            <!-- style="cursor: pointer" CoreUI ta grosse daronne  -->
            <button type="submit" class="btn btn-secondary btn-lg">Sauvegarder le brouillon</button>
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
      }
    },
    methods: {
      onSubmit () {
        console.log(this.poll);
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
      addQuestionToPage (page) {
        page.questions.push({
          variant: {
            name: VariantsId.CHECKBOX
          },
          question: {
            title: 'Question sans titre',
          },
          propositions: [
            {title: ''},
            {title: ''},
            {title: ''},
          ]
        });
      },
      removeQuestionFromPage(page, question) {
        const index = page.questions.indexOf(question);
        page.questions.splice(index, 1);
      },
      _addPage(index, before = true) {
        index += (before ? 0 : 1);
        Store.poll.pages.splice(index, 0, {
          title: 'Page sans titre',
          description: '',
          questions: []
        });

        this.addQuestionToPage(Store.poll.pages[index]);
      },
    },
    mounted () {
      Bus.$on(Event.ADD_PAGE_BEFORE, this.addPageBefore);
      Bus.$on(Event.ADD_PAGE_AFTER, this.addPageAfter);
      Bus.$on(Event.REMOVE_PAGE, this.removePage);
      Bus.$on(Event.ADD_QUESTION_TO_PAGE, this.addQuestionToPage);
      Bus.$on(Event.REMOVE_QUESTION_FROM_PAGE, this.removeQuestionFromPage);

      this.addPageBefore();
    },
    destroyed () {
      Store.poll.pages = [];

      Bus.$off(Event.ADD_PAGE_BEFORE, this.addPageBefore);
      Bus.$off(Event.ADD_PAGE_AFTER, this.addPageAfter);
      Bus.$off(Event.REMOVE_PAGE, this.removePage);
      Bus.$off(Event.ADD_QUESTION_TO_PAGE, this.addQuestionToPage);
      Bus.$off(Event.REMOVE_QUESTION_FROM_PAGE, this.removeQuestionFromPage);
    }
  }
</script>

<style>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .3s
    }

    .fade-enter, .fade-leave-to {
        opacity: 0
    }
</style>