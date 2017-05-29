<template>
  <form @submit.prevent="onSubmit" class="form-horizontal container-fluid" method="post">
    <input v-if="isEditingPoll && 'id' in poll" :value="poll.id" type="hidden" name="poll[id]">
    <template v-if="isEditingPoll">
      <input v-for="pageId in toDelete.pages" name="toDelete[Page][]" :value="pageId" type="hidden">
      <input v-for="questionId in toDelete.questions" name="toDelete[Question][]" :value="questionId" type="hidden">
      <input v-for="propositionId in toDelete.propositions" name="toDelete[Proposition][]" :value="propositionId"
             type="hidden">
    </template>

    <h1 class="text-center">
      {{ isEditingPoll ? $t('poll.updating') : $t('poll.creation') }}
    </h1>
    <hr>

    <!-- Titre du sondage -->
    <div class="form-group" :class="{'has-danger': poll.title.error}">
      <input v-model="poll.title.value" name="poll[title]"
             :disabled="isSubmittingPoll"
             :placeholder="$t('poll.placeholder.title')"
             class="form-control form-control-lg">
      <div v-if="poll.title.error" class="form-control-feedback">{{ poll.title.error }}</div>
    </div>

    <!-- Description du sondage -->
    <div class="form-group" :class="{'has-danger': poll.description.error}">
      <textarea v-model="poll.description.value" name="poll[description]"
                :disabled="isSubmittingPoll"
                :placeholder="$t('poll.placeholder.description')"
                class="form-control"></textarea>
      <div v-if="poll.description.error" class="form-control-feedback">{{ poll.description.error }}</div>
    </div>

    <!-- Les pages sondage, avec une transion fade -->
    <hr>
    <transition-group name="fade" tag="div">
      <page v-for="page, pageIndex in poll.pages" :key="page"
            :poll="poll" :page="page" :pageIndex="pageIndex"></page>
    </transition-group>

    <!-- Bouton pour ajouter une page après -->
    <button-insert-here @click.prevent="addPage" :block="true" size="large">
      {{ $t('page.add') }}
    </button-insert-here>
    <hr>

    <div class="text-center">
      <button :disabled="isSubmittingPoll" type="submit" class="btn btn-primary btn-lg">
        <template v-if="isSubmittingPoll">
          <i v-if="isSubmittingPoll" class="fa fa-cog fa-spin fa-fw"></i> {{ $t('loading') }} ...
        </template>
        <template v-else-if="isEditingPoll">
          {{ $t('poll.update') }}
        </template>
        <template v-else>
          {{ $t('poll.create') }}
        </template>
      </button>
    </div>
  </form>
</template>

<script>
  import axios from "axios";
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';
  import {mapGetters} from "vuex";

  export default {
    data () {
      return {
        submitting: false,
        toDelete: {
          pages: [],
          questions: [],
          propositions: []
        }
      }
    },
    computed: {
      ...mapGetters(['isEditingPoll', 'isSubmittingPoll', 'poll', 'variants', 'formAction'])
    },
    methods: {
      /**
       * Appelée lors de la soumission du formulaire
       */
      onSubmit() {
        this.$store.commit('pollIsSubmitting');
        const $$form = $(this.$el);

        /*
         * Reset des erreurs
         */
        this.poll.title.error = null;
        this.poll.description.error = null;
        this.poll.pages.forEach(page => {
          page.title.error = null;
          page.title.description = null;
          page.questions.forEach(question => {
            question.title.error = null;
            question.propositions.forEach(proposition => {
              proposition.error = null;
            })
          })
        });

        function handleError(entity, error) {
          const field = entity[error['property']];

          if (error['constraintName'] === 'NotBlank' && field.value.length === 0) {
            field.error = error.message
          }
        }

        // Envoi du formulaire en AJAX
        axios
          .post(this.formAction, $$form.serialize())
          .then(response => {
            // Le poll a bien été créé
            if (!response.request.responseURL.endsWith(this.formAction)) {
              window.location.replace(response.request.responseURL);
              return {}
            }

            return response;
          })
          .then(response => response.data)
          .then(errors => {
            if (!errors) {
              return;
            }

            /*
             * Traitement des erreurs
             */
            errors.forEach(error => {
              error['entityName'] === 'poll' && handleError(this.poll, error);
              this.poll.pages.forEach(page => {
                error['entityName'] === 'page' && handleError(page, error);
                page.questions.forEach(question => {
                  error['entityName'] === 'question' && handleError(question, error);
                  question.propositions.forEach(proposition => {
                    error['entityName'] === 'proposition' && handleError(proposition, error);
                  });
                });
              });
            });

            /*
             * Une fois que le rendu est terminé, on scroll vers le premier .has-danger
             */
            this.$nextTick(function () {
              const $$firstFieldWithError = $$form.find('.has-danger:first');
              $(document.body).animate({
                scrollTop: $$firstFieldWithError.offset().top - 100
              });
            });

            /*
             * On peut ré-activer le bouton de soumission
             */
            this.$store.commit('pollIsNotSubmitting');
          })
          .catch(error => {
            console.error('Erreur durant la validation', error);
            alert('Erreur durant la validation du sondage.');
            this.$store.commit('pollIsNotSubmitting');
          });
      },

      /**
       * Ajoute une page au poll.
       */
      addPage() {
        const page = {
          title: {
            value: '',
            error: null
          },
          description: {
            value: '',
            error: null
          },
          questions: []
        };
        this.addQuestion(page);
        this.poll.pages.push(page);
      },

      /**
       * Supprime la page passée en paramètre.
       * @param page
       */
      removePage (page) {
        const index = this.poll.pages.indexOf(page);
        this.poll.pages.splice(index, 1);
        this.isEditingPoll && 'id' in page && this.toDelete.pages.push(page.id);
      },

      /**
       * Ajoute une question à la page passée en paramètre.
       * @param {Object} page
       */
      addQuestion(page) {
        const question = {
          title: {
            value: '',
            error: null,
          },
          variant: {
            name: this.variants[Object.keys(this.variants)[0]] // premier élément d'un objet
          },
          propositions: [{
            title: {
              value: '',
              error: null
            }
          }]
        };

        page.questions.push(question);
      },

      /**
       * Supprime la question de la page passée en paramètre.
       * @param {Object} question
       * @param {Object} page
       */
      removeQuestion(question, page) {
        const index = page.questions.indexOf(question);
        page.questions.splice(index, 1);
        this.isEditingPoll && 'id' in question && this.toDelete.questions.push(question.id);
      },

      /**
       * Ajoute une proposition à la question passée en paramètre.
       */
      addProposition(question, title = '') {
        question.propositions.push({
          title: {
            value: title,
            error: null,
          }
        })
      },

      /**
       * Supprime la proposition de la question passée en paramètre.
       * @param {Object} proposition
       * @param {Object} question
       */
      removeProposition(proposition, question) {
        const index = question.propositions.indexOf(proposition);
        question.propositions.splice(index, 1);
        this.isEditingPoll && 'id' in proposition && this.toDelete.propositions.push(proposition.id);
      },
    },
    created () { // Cycle de vie VueJS: 1er hook dispo après l'instanciation de l'app VueJS
      Bus.$on(Event.ADD_PAGE, this.addPage);
      Bus.$on(Event.REMOVE_PAGE, this.removePage);

      Bus.$on(Event.ADD_QUESTION, this.addQuestion);
      Bus.$on(Event.REMOVE_QUESTION, this.removeQuestion);

      Bus.$on(Event.ADD_PROPOSITION, this.addProposition);
      Bus.$on(Event.REMOVE_PROPOSITION, this.removeProposition);

      // Ajout dans le store...
      'VARIANTS' in window && this.$store.commit('setVariants', window['VARIANTS']);
      'FORM_ACTION' in window && this.$store.commit('setFormAction', window['FORM_ACTION']);
      'IS_EDITING_POLL' in window && this.$store.commit('pollIsEditing');

      // On part sur l'édition d'un poll
      if ('POLL' in window) {
        const poll = window['POLL'];

        // Normalisation des fields, 'field' devient {value: field, error: null}
        poll.title = {value: poll.title, error: null};
        poll.description = {value: poll.description, error: null};
        poll.pages.forEach(page => {
          page.title = {value: page.title, error: null};
          page.description = {value: page.description, error: null};
          page.questions.forEach(question => {
            question.title = {value: question.title, error: null};
            question.propositions.forEach(proposition => {
              proposition.title = {value: proposition.title, error: null};
            })
          })
        });

        this.$store.commit('setPoll', poll);
      } else {
        this.$store.commit('setPoll', { // L'objet qui va contenir les pages, les questions, et les propositions
          title: {value: '', error: null},
          description: {value: '', error: null},
          pages: []
        });

        this.addPage();
      }
    },
  }
</script>

<style scoped lang="scss">
  form {
    max-width: 1024px;
    position: relative;
  }
</style>

<style lang="scss">
  .form-control:disabled, .form-control[readonly] {
    background-color: rgba(#cfd8dc, .3);
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
