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
          <v-select v-if="editForm.id === gameResult.id" v-model="editForm.rate" :items="[50, 100]"></v-select>
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

<script lang="ts">
import Vue from 'vue'
import apiClient from '../ApiClient'
import Player from '../types/Player'
import GameResult from '../types/GameResult'

type EditForm = {
  id: number | null
  rate: number | null
  points: { [key: number]: number }
  tips: { [key: number]: number }
}

class EditErrors {
  points: string[] = []
  tips: string[] = []
}

export default Vue.extend({
  props: {
    gameResults: {
      type: Array,
      required: true,
    },
  },

  data(): {
    loading: boolean
    editForm: EditForm
    editErrors: EditErrors
  } {
    return {
      loading: false,
      editForm: { id: null, rate: null, points: {}, tips: {} },
      editErrors: new EditErrors(),
    }
  },

  methods: {
    async deleteGameResult(id: number): Promise<void> {
      if (!confirm('本当に削除しますか？')) {
        return
      }
      this.loading = true

      await apiClient.deleteGameResult(id)
      this.loading = false
      this.$emit('reload')
    },

    editGameResult(gameResult: GameResult): void {
      this.resetForm()

      this.$set(this.editForm, 'id', gameResult.id)
      this.$set(this.editForm, 'rate', gameResult.rate)
      for (let player of this.players) {
        let gameResultPlayer = gameResult.gameResultPlayers.find(
          (gameResultPlayer) => gameResultPlayer.player_id === player.id
        )
        this.$set(this.editForm.points, player.id, gameResultPlayer ? gameResultPlayer.point : null)
        this.$set(this.editForm.tips, player.id, gameResultPlayer ? gameResultPlayer.tip : null)
      }
    },

    resetForm(): void {
      this.$set(this.editForm, 'id', null)
      this.$set(this.editForm, 'rate', null)
      for (let player of this.players) {
        this.$set(this.editForm.points, player.id, null)
        this.$set(this.editForm.tips, player.id, null)
      }
      this.editErrors = new EditErrors()
    },

    async updateGameResult(): Promise<void> {
      if (this.editForm.id === null) {
        return
      }

      this.loading = true

      const response = await apiClient.updateGameResult(this.editForm.id, this.editForm)
      this.loading = false

      if (response.status === 422) {
        this.editErrors = (await response.json()).errors
        return
      }

      this.resetForm()

      this.loading = false
      this.$emit('reload')
    },

    getPointAndTip(gameResult: GameResult, player: Player): string {
      const gameResultPlayer = gameResult.gameResultPlayers.find(
        (gameResultPlayer) => gameResultPlayer.player_id === player.id
      )
      if (gameResultPlayer) {
        return `${gameResultPlayer.point}(${gameResultPlayer.tip}枚)`
      }
      return ''
    },
  },

  computed: {
    players(): Player[] {
      return this.$store.state.players
    },
  },
})
</script>
