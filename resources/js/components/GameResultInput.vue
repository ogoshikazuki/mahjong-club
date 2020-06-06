<template>
  <form @submit.prevent="onSubmit" v-loading="loading">
    <div class="form-group row">
      <label class="col-5 col-form-label">レート</label>
      <div class="col-7">
        <select class="form-control" :class="{ 'is-invalid': errors.rate }" v-model="rate">
          <option :value="50">50</option>
          <option :value="100">100</option>
        </select>
        <div class="invalid-feedback" v-for="error in errors.rate" :key="error">{{ error }}</div>
      </div>
    </div>
    <div class="form-group row" v-for="player in players" :key="player.id">
      <label class="col-5 col-form-label">{{ player.name }}</label>
      <div class="col-7">
        <input
          type="number"
          class="form-control"
          :class="{ 'is-invalid': errors.points }"
          v-model="points[player.id]"
        />
        <div class="invalid-feedback" v-for="error in errors.points" :key="error">{{ error }}</div>
      </div>
    </div>
    <button class="btn btn-primary">登録</button>
  </form>
</template>

<script>
import apiClient from "../ApiClient";

export default {
  data: function() {
    return {
      rate: null,
      points: {},
      players: [],
      loading: true,
      errors: {},
      conflictOccurred: false
    };
  },

  methods: {
    onSubmit: async function() {
      this.loading = true;
      this.conflictOccurred = false;

      const response = await apiClient.storeGameResult({
        rate: this.rate,
        points: this.points
      });

      if (response.status === 422) {
        this.errors = (await response.json()).errors;
        this.loading = false;
        return;
      }

      this.resetForm();
      this.$emit("store-game-result");

      this.loading = false;
    },

    resetForm: function() {
      this.rate = null;
      this.points = {};
      this.errors = {};
    }
  },

  created: async function() {
    this.players = await apiClient.getAllPlayers();

    this.loading = false;
  }
};
</script>
