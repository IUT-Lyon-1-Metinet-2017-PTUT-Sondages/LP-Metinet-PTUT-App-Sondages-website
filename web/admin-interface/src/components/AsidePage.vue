<template>
    <div class="aside-page text-center">
        <button @click="addPage" class="btn btn-primary">
            {{ before ? 'Insérer une page avant' : 'Insérer une page après' }}
        </button>
    </div>
</template>

<script>
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';

  export default {
    props: {
      before: {
        type: Boolean,
        default: true
      },
      index: {
        type: Number,
        required: true
      }
    },
    data () {
      return {}
    },
    methods: {
      addPage() {
        if(this.before) {
          Bus.$emit(Event.ADD_PAGE_BEFORE, this.index);
        } else {
          Bus.$emit(Event.ADD_PAGE_AFTER, this.index);
        }
      }
    }
  }
</script>