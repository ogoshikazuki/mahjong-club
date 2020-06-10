<template>
  <div>
    <h1>集計</h1>
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link" :class="{ active: mode === 4 }" @click="setMode(4)">四麻</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: mode === 3 }" @click="setMode(3)">三麻</a>
      </li>
    </ul>
    <div class="table-responsive">
      <table class="table" v-loading="loading">
        <thead>
          <tr>
            <th>名前</th>
            <th>ゲーム数</th>
            <th>平均着順</th>
            <th>祝儀</th>
            <th v-for="(value, key) in finishOrderCount" :key="key">{{ key }}着</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="player in players" :key="player.id">
            <td>{{ player.name }}</td>
            <td>{{ gameCount[player.id] }}</td>
            <td>{{ averageFinishOrder[player.id].toFixed(2) }}</td>
            <td>{{ tipCount[player.id] }}</td>
            <td
              v-for="(value, key) in finishOrderCount"
              :key="key"
            >{{ value[player.id] }}({{ (value[player.id] / gameCount[player.id] * 100).toFixed(2) }}%)</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
import apiClient from "../ApiClient";

export default {
  data() {
    return {
      mode: 4,
      loading: true,
      gameResults: []
    };
  },

  methods: {
    setMode(mode) {
      this.mode = mode;
    },

    async loadGameResults() {
      this.loading = true;

      this.gameResults = await apiClient.getAllGameResults();

      this.loading = false;
    }
  },

  computed: {
    gameCount() {
      let gameCount = this.players.reduce((gameCount, player) => {
        gameCount[player.id] = 0;
        return gameCount;
      }, {});

      return this.gameResults
        .filter(gameResult => gameResult.gameResultPlayers.length === this.mode)
        .reduce((gameCount, gameResult) => {
          return gameResult.gameResultPlayers.reduce(
            (gameCount, gameResultPlayer) => {
              gameCount[gameResultPlayer.player_id]++;
              return gameCount;
            },
            gameCount
          );
        }, gameCount);
    },

    finishOrderCount() {
      let finishOrderCount = {};
      for (let finishOrder = 1; finishOrder <= this.mode; finishOrder++) {
        finishOrderCount[finishOrder] = this.players.reduce(
          (finishOrderCount, player) => {
            finishOrderCount[player.id] = 0;
            return finishOrderCount;
          },
          {}
        );
      }

      return this.gameResults
        .filter(gameResult => gameResult.gameResultPlayers.length === this.mode)
        .reduce((finishOrderCount, gameResult) => {
          return gameResult.gameResultPlayers
            .sort((a, b) => b.point - a.point)
            .reduce((finishOrderCount, gameResultPlayer, index) => {
              finishOrderCount[index + 1][gameResultPlayer.player_id]++;
              return finishOrderCount;
            }, finishOrderCount);
        }, finishOrderCount);
    },

    totalFinishOrder() {
      let totalFinishOrder = this.players.reduce((gameCount, player) => {
        gameCount[player.id] = 0;
        return gameCount;
      }, {});

      return Object.keys(this.finishOrderCount).reduce(
        (totalFinishOrder, finishOrder) => {
          for (let player of this.players) {
            totalFinishOrder[player.id] +=
              this.finishOrderCount[finishOrder][player.id] * finishOrder;
          }
          return totalFinishOrder;
        },
        totalFinishOrder
      );
    },

    averageFinishOrder() {
      return this.players.reduce((averageFinishOrder, player) => {
        averageFinishOrder[player.id] =
          this.totalFinishOrder[player.id] / this.gameCount[player.id];
        return averageFinishOrder;
      }, {});
    },

    tipCount() {
      let tipCount = this.players.reduce((tipCount, player) => {
        tipCount[player.id] = 0;
        return tipCount;
      }, {});

      return this.gameResults
        .filter(gameResult => gameResult.gameResultPlayers.length === this.mode)
        .reduce((tipCount, gameResult) => {
          return gameResult.gameResultPlayers.reduce(
            (tipCount, gameResultPlayer) => {
              tipCount[gameResultPlayer.player_id] += gameResultPlayer.tip;
              return tipCount;
            },
            tipCount
          );
        }, tipCount);
    },

    ...mapState(["players"])
  },

  created() {
    this.loadGameResults();
  }
};
</script>

<style scoped>
a:not([href]) {
  color: #3490dc;
}
</style>