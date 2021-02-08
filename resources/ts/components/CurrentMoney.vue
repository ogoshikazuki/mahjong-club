<template>
  <v-simple-table v-loading="loading">
    <thead>
      <tr>
        <th>名前</th>
        <th>金額</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="moneyPlayer in moneyPlayers" :key="moneyPlayer.player_id">
        <td>{{ moneyPlayer.player.name }}</td>
        <td>{{ moneyPlayer.money }}</td>
      </tr>
    </tbody>
  </v-simple-table>
</template>

<script lang="ts">
import Vue from 'vue'
import Repository from '../Repository'
import MoneyPlayer from '../types/MoneyPlayer'
import Money from '../types/Money'

export default Vue.extend({
  data(): {
    moneyPlayers: MoneyPlayer[]
    loading: boolean
  } {
    return {
      moneyPlayers: [],
      loading: true,
    }
  },

  async created(): Promise<void> {
    this.moneyPlayers = ((await Repository.getCurrentMoney().data()) as Money).money_players
    this.loading = false
  },
})
</script>

<style scoped></style>
