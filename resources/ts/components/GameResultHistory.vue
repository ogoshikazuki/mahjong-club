<template>
  <v-simple-table class="text-no-wrap">
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
          <v-select
            v-if="editForm.id === gameResult.id"
            v-model="editForm.rate"
            :items="[50, 100]"
          ></v-select>
          <template v-else>{{ gameResult.rate }}</template>
        </td>
        <td v-for="player in players" :key="player.id">
          <template v-if="editForm.id === gameResult.id">
            <v-text-field
              type="number"
              :class="{ 'is-invalid': editErrors.points }"
              v-model="editForm.points[player.id]"
              :error-messages="editErrors.points"
            />
            <v-text-field
              type="number"
              :class="{ 'is-invalid': editErrors.tips }"
              v-model="editForm.tips[player.id]"
              placeholder="祝儀"
              :error-messages="editErrors.tips"
            />
          </template>
          <template v-else>{{ getPointAndTip(gameResult, player) }}</template>
        </td>
        <td>
          <template v-if="editForm.id === gameResult.id">
            <v-btn color="primary" small @click="updateGameResult">更新</v-btn>
            <v-btn color="secondary" small @click="resetForm">キャンセル</v-btn>
          </template>
          <template v-else>
            <v-btn color="secondary" small @click="editGameResult(gameResult)">編集</v-btn>
            <v-btn color="danger" small @click="deleteGameResult(gameResult.id)">削除</v-btn>
          </template>
        </td>
      </tr>
    </tbody>
  </v-simple-table>
</template>

<script>
import apiClient from "../ApiClient";
import { mapState } from "vuex";

export default {
  props: {
    gameResults: {
      type: Array,
      required: true
    }
  },

  data: function() {
    return {
      loading: false,
      editForm: { id: null, rate: null, points: {}, tips: {} },
      editing: null,
      editErrors: {}
    };
  },

  methods: {
    async deleteGameResult(id) {
      if (!confirm("本当に削除しますか？")) {
        return;
      }
      this.loading = true;

      await apiClient.deleteGameResult(id);
      this.loading = false;
      this.$emit("reload");
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

      this.loading = false;
      this.$emit("reload");
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

  computed: {
    ...mapState(["players"])
  }
};
</script>
