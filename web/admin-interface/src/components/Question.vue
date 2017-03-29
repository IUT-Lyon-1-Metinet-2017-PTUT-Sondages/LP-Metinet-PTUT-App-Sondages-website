<template>
    <div>
        <hr>
        {{ question }}

        <button @click="remove">Supprimer la question</button>
    </div>
</template>
<script>
  export default {
    props: {
      page: {
        type: Object,
        required: true,
      },
      question: {
        type: Object,
        required: true
      },
    },
    methods: {
      remove() {
        const index = this.page.questions.indexOf(this.question);

        if (index > -1) {
          this.page.questions.splice(index, 1);
        }
      }
    }
  }
</script>

<style>

</style>