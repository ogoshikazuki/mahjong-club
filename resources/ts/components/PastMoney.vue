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
          {{ getMoney(money, player.id) }}
        </td>
      </tr>
    </tbody>
  </v-simple-table>
</template>

<script lang="ts">
import Vue from 'vue'
import { mapState } from 'vuex'
import ApiClient from '../ApiClient'
import Player from '../types/Player'
import Money from '../types/Money'

export default Vue.extend({
  data(): {
    moneys: Money[]
    loading: boolean
  } {
    return {
      moneys: [],
      loading: true,
    }
  },

  computed: {
    totalMoneys(): number[] {
      let result = this.players.reduce((reduceResult: number[], player: Player) => {
        reduceResult[player.id] = 0
        return reduceResult
      }, {})

      for (const money of this.moneys) {
        for (const moneyPlayer of money.money_players) {
          result[moneyPlayer.player.id] += moneyPlayer.money
        }
      }

      return result
    },
    ...mapState(['players']),
  },

  async created() {
    this.moneys = await ApiClient.getPastMoney()
    this.loading = false
  },

  methods: {
    getMoney(money: Money, playerId: number): number {
      const moneyPlayer = money.money_players.find(({ player }) => player.id === playerId)

      if (moneyPlayer === undefined) {
        return 0
      }

      return moneyPlayer.money
    },
  },
})
</script>

<style scoped></style>
