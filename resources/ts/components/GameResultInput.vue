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
import Repository from '../Repository'
import Player from '../types/Player'

export default Vue.extend({
  data(): {
    rate: number | null
    points: { [key: number]: number }
    tips: { [key: number]: number }
    loading: boolean
    errors: { [key: string]: string[] }
    conflictOccurred: boolean
  } {
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
    async onSubmit(): Promise<void> {
      this.loading = true
      this.conflictOccurred = false

      const repositoryResponse = Repository.storeGameResult({
        rate: this.rate,
        points: this.points,
        tips: this.tips,
      })

      if (await repositoryResponse.invalid()) {
        this.errors = await repositoryResponse.errors()
        this.loading = false
        return
      }

      this.resetForm()
      this.$emit('store-game-result')

      this.loading = false
    },

    resetForm(): void {
      this.rate = null
      this.points = {}
      this.tips = {}
      this.errors = {}
    },
  },

  computed: {
    players(): Player[] {
      return this.$store.state.players
    },
  },
})
</script>
