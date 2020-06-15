<template>
  <div class="table-responsive" v-loading="loading">
    <table class="table">
      <thead>
        <tr>
          <th>終了日時</th>
          <th v-for="player in players" :key="player.id">{{ player.name }}</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="game in currentMoneyGames" :key="game.id">
          <td>{{ game.finished_at }}</td>
          <td v-for="player in players" :key="player.id">{{ culculateGamePlayerMoney(game, player) }}</td>
          <td>
            <button class="btn btn-primary btn-sm" @click="$emit('show-game', game)">詳細</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { mapState } from "vuex";
import apiClient from "../ApiClient";

export default {
  data() {
    return {
      currentMoneyGames: [],
      loading: true
    };
  },

  computed: {
    ...mapState(["players"])
  },

  methods: {
    culculateGamePlayerMoney(game, player) {
      let result = 0;
      for (let gameResult of game.gameResults) {
        for (let gameResultPlayer of gameResult.gameResultPlayers) {
          if (gameResultPlayer.player_id === player.id) {
            result += (gameResultPlayer.point + gameResultPlayer.tip * 2) * gameResult.rate;
          }
        }
      }

      return result;
    }
  },

  async created() {
    this.currentMoneyGames = await apiClient.getCurrentMoneyGames();

    this.loading = false;
  }
}
</script>