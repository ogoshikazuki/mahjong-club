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
import ApiClient from '../ApiClient'

export default Vue.extend({
  data() {
    return {
      moneyPlayers: [],
      loading: true,
    }
  },

  async created() {
    this.moneyPlayers = (await ApiClient.getCurrentMoney()).money_players
    this.loading = false
  },
})
</script>

<style scoped></style>
