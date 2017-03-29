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
            <div v-for="page in Store.poll.pages" is="page" :page="page" :key="page"></div>
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
      addPageBefore(index) {
        this._addPage(index);
      },
      addPageAfter(index) {
        this._addPage(index, false);
      },
      removePage (index) {
        Store.poll.pages.splice(index, 1);
      },
      _addPage(index, before = true) {
        index += (before ? 0 : 1);
        Store.poll.pages.splice(index, 0, {
          title: 'Page sans titre',
          description: '',
          questions: []
        });
      },
    },
    mounted () {
      Bus.$on(Event.ADD_PAGE_BEFORE, this.addPageBefore);
      Bus.$on(Event.ADD_PAGE_AFTER, this.addPageAfter);
      Bus.$on(Event.REMOVE_PAGE, this.removePage);
    }
  }
</script>

<style>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .3s
    }

    .fade-enter, .fade-leave-to
    {
        opacity: 0
    }
</style>