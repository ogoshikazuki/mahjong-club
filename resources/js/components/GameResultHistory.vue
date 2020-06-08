<template>
  <div class="table-responsive" v-loading="loading">
    <table class="table">
      <thead>
        <tr>
          <th>レート</th>
          <th v-for="player in players" :key="player.id">{{ player.name }}</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="gameResult in gameResults" :key="gameResult.id">
          <td>
            <select
              class="form-control form-control-sm"
              v-if="editForm.id === gameResult.id"
              v-model="editForm.rate"
            >
              <option :value="50">50</option>
              <option :value="100">100</option>
            </select>
            <template v-else>{{ gameResult.rate }}</template>
          </td>
          <td v-for="player in players" :key="player.id">
            <template v-if="editForm.id === gameResult.id">
              <input
                type="number"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': editErrors.points }"
                v-model="editForm.points[player.id]"
              />
              <div
                class="invalid-feedback"
                v-for="error of editErrors.points"
                :key="error"
              >{{ error }}</div>
              <input
                type="number"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': editErrors.tips }"
                v-model="editForm.tips[player.id]"
                placeholder="祝儀"
              />
              <div
                class="invalid-feedback"
                v-for="error of editErrors.tips"
                :key="error"
              >{{ error }}</div>
            </template>
            <template v-else>{{ getPointAndTip(gameResult, player) }}</template>
          </td>
          <td>
            <template v-if="editForm.id === gameResult.id">
              <button class="btn btn-primary btn-sm" @click="updateGameResult">更新</button>
              <button class="btn btn-secondary btn-sm" @click="resetForm">キャンセル</button>
            </template>
            <template v-else>
              <button class="btn btn-secondary btn-sm" @click="editGameResult(gameResult)">編集</button>
              <button class="btn btn-danger btn-sm" @click="deleteGameResult(gameResult.id)">削除</button>
            </template>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import apiClient from "../ApiClient";
import { mapState } from "vuex";

export default {
  data: function() {
    return {
      gameResults: [],
      loading: true,
      editForm: { id: null, rate: null, points: {}, tips: {} },
      editing: null,
      editErrors: {}
    };
  },

  methods: {
    async load() {
      this.loading = true;

      this.gameResults = [];

      this.gameResults = (await apiClient.getCurrentGame()).gameResults;

      this.loading = false;
    },

    async deleteGameResult(id) {
      if (!confirm("本当に削除しますか？")) {
        return;
      }
      this.loading = true;

      await apiClient.deleteGameResult(id);
      this.load();
    },

    editGameResult(gameResult) {
      this.resetForm();

      this.$set(this.editForm, "id", gameResult.id);
      this.$set(this.editForm, "rate", gameResult.rate);
      for (let player of this.players) {
        let gameResultPlayer = gameResult.gameResultPlayers.find(
          gameResultPlayer => gameResultPlayer.player_id === player.id
        );
        this.$set(
          this.editForm.points,
          player.id,
          gameResultPlayer ? gameResultPlayer.point : null
        );
        this.$set(
          this.editForm.tips,
          player.id,
          gameResultPlayer ? gameResultPlayer.tip : null
        );
      }
    },

    resetForm() {
      this.$set(this.editForm, "id", null);
      this.$set(this.editForm, "rate", null);
      for (let player of this.players) {
        this.$set(this.editForm.points, player.id, null);
        this.$set(this.editForm.tips, player.id, null);
      }
      this.editFormErrorMessages = [];
      this.editErrors = {};
    },

    async updateGameResult() {
      this.loading = true;

      const response = await apiClient.updateGameResult(this.editForm);
      this.loading = false;

      if (response.status === 422) {
        this.editErrors = (await response.json()).errors;
        return;
      }

      this.resetForm();

      this.load();
    },

    getPointAndTip(gameResult, player) {
      const gameResultPlayer = gameResult.gameResultPlayers.find(
        gameResultPlayer => gameResultPlayer.player_id === player.id
      );
      if (gameResultPlayer) {
        return `${gameResultPlayer.point}(${gameResultPlayer.tip}枚)`;
      }
    }
  },

  watch: {
    players() {
      this.load();
    }
  },

  computed: {
    ...mapState(["players"])
  }
};
</script>
