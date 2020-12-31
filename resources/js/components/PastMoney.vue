<template>
  <v-simple-table>
    <thead>
      <tr>
        <th></th>
        <th v-for="player in players" :key="player.id" class="text-no-wrap">{{ player.name }}</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>合計</td>
        <td v-for="player in players" :key="player.id">{{ totalMoneys[player.id] }}</td>
      </tr>
      <tr v-for="money in moneys" :key="money.id">
        <td>{{ money.finished_at }}</td>
        <td v-for="player in players" :key="player.id">
          {{ money.money_players.find((moneyPlayer) => moneyPlayer.player.id === player.id).money }}
        </td>
      </tr>
    </tbody>
  </v-simple-table>
</template>

<script>
import { mapState } from "vuex";
import ApiClient from "../ApiClient";

export default {
  data() {
    return {
      moneys: [],
      loading: true,
    };
  },

  computed: {
    totalMoneys() {
      let result = this.players.reduce((reduceResult, player) => {
        reduceResult[player.id] = 0;
        return reduceResult;
      }, {});

      for (const money of this.moneys) {
        for (const moneyPlayer of money.money_players) {
          result[moneyPlayer.player.id] += moneyPlayer.money;
        }
      }

      return result;
    },
    ...mapState(["players"]),
  },

  async created() {
    this.moneys = await ApiClient.getPastMoney();
    this.loading = false;
  },
};
</script>

<style scoped>
</style>
