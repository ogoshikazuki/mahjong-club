<template>
  <v-form v-loading="loading">
    <v-container>
      <v-row>
        <v-col cols="4" v-for="player in players" :key="player.id">
          <v-text-field
            :label="player.name"
            type="number"
            v-model="money[player.id]"
            :error-messages="errorMessages"
          ></v-text-field>
        </v-col>
      </v-row>
    </v-container>
    <v-btn color="primary" @click="updateMoney" v-loading="updating">更新</v-btn>
    <v-btn color="secondary" :to="{ name: 'home' }">戻る</v-btn>
  </v-form>
</template>

<script lang="ts">
import Vue from 'vue'
import ApiClient from '../ApiClient'
import MoneyPlayer from '../types/MoneyPlayer'
import Player from '../types/Player'

export default Vue.extend({
  data(): {
    money: { [key: number]: number }
    loading: boolean
    updating: boolean
    errorMessages: string[]
  } {
    return {
      money: {},
      loading: true,
      updating: false,
      errorMessages: [],
    }
  },

  computed: {
    players(): Player[] {
      return this.$store.state.players
    },
  },

  async created(): Promise<void> {
    this.money = (await ApiClient.getCurrentMoney()).money_players.reduce(
      (money: number[], moneyPlayer: MoneyPlayer) => {
        money[moneyPlayer.player.id] = moneyPlayer.money
        return money
      },
      {}
    )
    this.loading = false
  },

  methods: {
    async updateMoney(): Promise<void> {
      this.updating = true
      this.errorMessages = []

      const response = await ApiClient.updateMoney(this.money)

      this.updating = false

      if (response.ok) {
        this.$router.push({ name: 'home' })
        return
      }

      this.errorMessages = (await response.json()).errors.money
    },
  },
})
</script>
