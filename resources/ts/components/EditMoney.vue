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
import Repository from '../Repository'
import MoneyPlayer from '../types/MoneyPlayer'
import Player from '../types/Player'
import Money from '../types/Money'

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
    this.money = ((await Repository.getCurrentMoney().data()) as Money).money_players.reduce(
      (money: { [playerId: number]: number }, moneyPlayer: MoneyPlayer) => {
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

      const repositoryResponse = Repository.updateMoney(this.money)

      if (await repositoryResponse.ok()) {
        this.$router.push({ name: 'home' })
        this.updating = false
        return
      }

      this.errorMessages = (await repositoryResponse.errors()).money
      this.updating = false
    },
  },
})
</script>
