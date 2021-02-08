<template>
  <v-form v-model="valid" ref="form" @submit.prevent="downloadTenhouLog">
    <v-container>
      <v-row>
        <v-col cols="6">
          <v-text-field
            v-model="roomNumberNoPrefix"
            :rules="roomNumberRules"
            :readonly="loading"
            prefix="C"
            placeholder="部屋番号"
            hint="上4桁を入力"
          ></v-text-field>
        </v-col>
        <v-col cols="6">
          <v-text-field type="date" v-model="date" :rules="dateRules" :readonly="loading"></v-text-field>
        </v-col>
        <v-col cols="6">
          <v-btn color="primary" :loading="loading" type="submit">天鳳ログ取得</v-btn>
        </v-col>
      </v-row>
    </v-container>
  </v-form>
</template>

<script lang="ts">
import Vue from 'vue'
import moment from 'moment'
import Repository from '../Repository'

export default Vue.extend({
  data(): {
    roomNumberNoPrefix: string
    date: string
    roomNumberRules: ((v: string) => boolean | string)[]
    dateRules: ((v: string) => boolean | string)[]
    valid: boolean
    loading: boolean
  } {
    return {
      roomNumberNoPrefix: '',
      date: moment().format('YYYY-MM-DD'),
      roomNumberRules: [
        (v: string) => !!v || '部屋番号を入力してください。',
        (v: string) => v.length === 4 || '部屋番号は上4桁を入力してください。',
      ],
      dateRules: [(v: string) => !!v || '日付を入力してください。'],
      valid: true,
      loading: false,
    }
  },

  computed: {
    roomNumber(): string {
      return `C${this.roomNumberNoPrefix}`
    },
    form(): Vue & { validate: () => boolean } {
      return this.$refs.form as Vue & { validate: () => boolean }
    },
  },

  methods: {
    async downloadTenhouLog(): Promise<void> {
      if (!this.validate()) {
        return
      }

      this.loading = true

      this.$emit(
        'complete',
        await Repository.downloadTenhouLog({ date: this.date, room_number: this.roomNumber }).data()
      )

      this.loading = false
    },

    validate(): boolean {
      this.form.validate()

      return this.valid
    },
  },
})
</script>
