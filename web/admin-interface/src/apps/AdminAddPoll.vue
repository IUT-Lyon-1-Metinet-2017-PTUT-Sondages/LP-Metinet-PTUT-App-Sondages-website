<template>
    <form class="form-horizontal container" method="post" :action="FORM_ACTION">
        <h1 class="text-center">{{ $t('poll.creation') }}</h1>
        <hr>

        <!-- Titre du sondage -->
        <div class="form-group" :class="{'has-danger': poll.title.length == 0}">
            <input v-model="poll.title" name="poll[title]" required
                   class="form-control form-control-lg" :placeholder="$t('poll.placeholder.title')">
        </div>

        <!-- Description du sondage -->
        <div class="form-group">
            <textarea v-model="poll.description" name="poll[description]"
                      class="form-control" :placeholder="$t('poll.placeholder.description')"></textarea>
        </div>

        <hr>

        <!-- Les pages sondage, avec une transion fade -->
        <transition-group name="fade" tag="div">
            <page v-for="page, pageIndex in poll.pages" :key="page"
                  :poll="poll"
                  :page="page" :pageIndex="pageIndex"></page>
        </transition-group>
        <hr>

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">{{ $t('poll.create') }}</button>
        </div>
    </form>
</template>

<script>
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';

  export default {
    data () {
      return {
        FORM_ACTION,
        poll: { // L'objet qui va contenir les pages, les questions, et les propositions
          title: this.$t('poll.default.title'), // this.$t = fonction rajoutée par VueI18n
          description: this.$t('poll.default.description'),
          pages: []
        },
      }
    },
    methods: {
      addPageBefore(index = 0) {
        this._addPage(index);
      },
      addPageAfter(index = 0) {
        this._addPage(index, false);
      },
      removePage (page) {
        const index = this.poll.pages.indexOf(page);
        this.poll.pages.splice(index, 1);
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
        // si !before, on incrémente le pageIndex pour ajouter la page après
        pageIndex += (before ? 0 : 1);

        // Ajout de la page
        this.poll.pages.splice(pageIndex, 0, {
          title: this.$t('page.default.title'),
          description: this.$t('page.default.description'),
          questions: []
        });

        // Puis on y rajoute une question
        this.addQuestionBefore(this.poll.pages[pageIndex]);
      },
      _addQuestion(page, questionIndex = 0, before = true) {
        questionIndex += (before ? 0 : 1);

        page.questions.splice(questionIndex, 0, {
          title: this.$t('question.default.title'),
          variant: {
            name: window['VARIANTS'][ Object.keys(window['VARIANTS'])[0] ] // premier élément d'un objet
          },
          propositions: [
            {title: ''},
          ]
        });
      }
    },
    created () { // Cycle de vie VueJS: 1er hook dispo après l'instanciation de l'app VueJS
      Bus.$on(Event.ADD_PAGE_BEFORE, this.addPageBefore);
      Bus.$on(Event.ADD_PAGE_AFTER, this.addPageAfter);
      Bus.$on(Event.REMOVE_PAGE, this.removePage);

      Bus.$on(Event.ADD_QUESTION_BEFORE, this.addQuestionBefore);
      Bus.$on(Event.ADD_QUESTION_AFTER, this.addQuestionAfter);
      Bus.$on(Event.REMOVE_QUESTION, this.removeQuestion);

      this.addPageBefore();
    },
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