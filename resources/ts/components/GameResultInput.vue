<template>
  <form @submit.prevent="onSubmit" v-loading="loading || Object.keys(players).length === 0">
    <div class="form-group row">
      <label class="col-4 col-form-label">レート</label>
      <div class="col-8">
        <select class="form-control" :class="{ 'is-invalid': errors.rate }" v-model="rate">
          <option></option>
          <option :value="50">50</option>
          <option :value="100">100</option>
        </select>
        <div class="invalid-feedback" v-for="error in errors.rate" :key="error">{{ error }}</div>
      </div>
    </div>
    <div class="form-group row" v-for="player in players" :key="player.id">
      <label class="col-4 col-form-label">{{ player.name }}</label>
      <div class="col-4">
        <input
          type="number"
          class="form-control"
          :class="{ 'is-invalid': errors.points }"
          v-model="points[player.id]"
        />
        <div class="invalid-feedback" v-for="error in errors.points" :key="error">{{ error }}</div>
      </div>
      <div class="col-4">
        <div class="input-group">
          <input
            type="number"
            class="form-control"
            :class="{ 'is-invalid': errors.tips }"
            v-model="tips[player.id]"
            placeholder="祝儀"
          />
          <div class="invalid-feedback" v-for="error in errors.tips" :key="error">{{ error }}</div>
          <div class="input-group-append">
            <span class="input-group-text">枚</span>
          </div>
        </div>
      </div>
    </div>
    <button class="btn btn-primary">登録</button>
  </form>
</template>

<script lang="ts">
import Vue from 'vue'
import { mapState } from 'vuex'
import apiClient from '../ApiClient'

export default Vue.extend({
  data: function () {
    return {
      rate: null,
      points: {},
      tips: {},
      loading: false,
      errors: {},
      conflictOccurred: false,
    }
  },

  methods: {
    onSubmit: async function () {
      this.loading = true
      this.conflictOccurred = false

      const response = await apiClient.storeGameResult({
        rate: this.rate,
        points: this.points,
        tips: this.tips,
      })

      if (response.status === 422) {
        this.errors = (await response.json()).errors
        this.loading = false
        return
      }

      this.resetForm()
      this.$emit('store-game-result')

      this.loading = false
    },

    resetForm: function () {
      this.rate = null
      this.points = {}
      this.tips = {}
      this.errors = {}
    },
  },

  computed: {
    ...mapState(['players']),
  },
})
</script>
