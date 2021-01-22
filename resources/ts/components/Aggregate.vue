<template>
  <div>
    <v-tabs>
      <v-tab @change="setMode(4)">四麻</v-tab>
      <v-tab @change="setMode(3)">三麻</v-tab>
    </v-tabs>
    <v-simple-table v-loading="loading" class="text-no-wrap">
      <thead>
        <tr>
          <th>名前</th>
          <th>ゲーム数</th>
          <th>平均着順</th>
          <th>祝儀</th>
          <th>金額</th>
          <th v-for="(value, key) in finishOrderCount" :key="key">{{ key }}着</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="player in players" :key="player.id">
          <td>{{ player.name }}</td>
          <td>{{ gameCount[player.id] }}</td>
          <td>{{ averageFinishOrder[player.id].toFixed(2) }}</td>
          <td>{{ tipCount[player.id] }}</td>
          <td>{{ money[player.id] }}</td>
          <td
            v-for="(value, key) in finishOrderCount"
            :key="key"
          >{{ value[player.id] }}({{ (value[player.id] / gameCount[player.id] * 100).toFixed(2) }}%)</td>
        </tr>
      </tbody>
    </v-simple-table>
  </div>
</template>

<script lang="ts">
import Vue from "vue";
import { mapState } from "vuex";
import apiClient from "../ApiClient";
import Player from "../types/Player";

type GameResultPlayer = {
  point: number,
  tip: number,
  player_id: number,
};
type GameResult = {
  rate: number,
  gameResultPlayers: GameResultPlayer[],
};

type ResultByPlayers = {
  [key: number]: number,
};

interface Data {
  mode: number,
  loading: boolean,
  gameResults: GameResult[],
}
interface Methods {
  setMode: (mode: number) => void,
  loadGameResults: () => Promise<void>,
}
interface Computed {
  gameCount: ResultByPlayers,
  finishOrderCount: {[key: number]: ResultByPlayers},
  totalFinishOrder: ResultByPlayers,
  averageFinishOrder: ResultByPlayers,
  tipCount: ResultByPlayers,
  money: ResultByPlayers,
  players: Player[],
}

export default Vue.extend<Data, Methods, Computed, Record<string, never>>({
  data(): Data {
    return {
      mode: 4,
      loading: true,
      gameResults: []
    };
  },

  methods: {
    setMode(mode: number) {
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
      let gameCount = this.players.reduce((gameCount: ResultByPlayers, player: Player) => {
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
      let finishOrderCount: {[key: number]: ResultByPlayers} = {};
      for (let finishOrder = 1; finishOrder <= this.mode; finishOrder++) {
        finishOrderCount[finishOrder] = this.players.reduce(
          (finishOrderCount: ResultByPlayers, player) => {
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
      let totalFinishOrder = this.players.reduce((gameCount: ResultByPlayers, player) => {
        gameCount[player.id] = 0;
        return gameCount;
      }, {});

      return Object.keys(this.finishOrderCount).map(Number).reduce(
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
      return this.players.reduce((averageFinishOrder: ResultByPlayers, player) => {
        averageFinishOrder[player.id] =
          this.totalFinishOrder[player.id] / this.gameCount[player.id];
        return averageFinishOrder;
      }, {});
    },

    tipCount() {
      let tipCount = this.players.reduce((tipCount: ResultByPlayers, player) => {
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

    money() {
      let money = this.players.reduce((money: ResultByPlayers, player) => {
        money[player.id] = 0;
        return money;
      }, {});

      return this.gameResults
        .filter(gameResult => gameResult.gameResultPlayers.length === this.mode)
        .reduce((money, gameResult) => {
          return gameResult.gameResultPlayers.reduce((money, gameResultPlayer) => {
            let point = gameResultPlayer.point + gameResultPlayer.tip * 2;
            money[gameResultPlayer.player_id] += point * gameResult.rate;
            return money;
          }, money);
        }, money)
    },

    ...mapState(["players"])
  },

  created() {
    this.loadGameResults();
  }
});
</script>

<style scoped>
a:not([href]) {
  color: #3490dc;
}
</style>
