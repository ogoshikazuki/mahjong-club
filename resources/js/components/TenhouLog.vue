<template>
  <v-app>
    <v-form v-model="valid" ref="form" @submit.prevent="downloadTenhouLog">
      <v-container>
        <v-row>
          <v-col cols="6">
            <v-text-field v-model="roomNumberNoPrefix" :rules="roomNumberRules" :readonly="loading" prefix="C" placeholder="部屋番号" hint="上4桁を入力"></v-text-field>
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
    <template v-if="tenhouLogs.length > 0">
      <v-data-table
        :headers="headers"
        :items="items"
        show-select
        v-model="selected"
        item-key="startTime"
        :mobile-breakpoint="null"
        hide-default-footer
        disable-pagination
      ></v-data-table>
      <v-divider></v-divider>
      <v-btn color="primary" disabled>チェックしたログを登録(近日実装)</v-btn>
    </template>
  </v-app>
</template>

<script>
import { mapState } from "vuex";
import moment from "moment";
import apiClient from "../ApiClient";

export default {
  data () {
    return {
      roomNumberNoPrefix: "",
      date: moment().format("YYYY-MM-DD"),
      roomNumberRules: [
        v => !!v || "部屋番号を入力してください。",
        v => v.length === 4 || "部屋番号は上4桁を入力してください。",
      ],
      dateRules: [v => !!v || "日付を入力してください。"],
      valid: true,
      loading: false,
      tenhouLogs: [],
      headers: [
        { text: "開始時刻", value: "startTime" },
        { text: "結果", value: "gameResultsText" },
      ],
      selected: [],
    };
  },

  computed: {
    ...mapState["players"],

    roomNumber() {
      return `C${this.roomNumberNoPrefix}`;
    },

    items() {
      return this.tenhouLogs.map(tenhouLog => {
        tenhouLog["gameResultsText"] = tenhouLog.gameResults.reduce((gameResults, gameResult) => {
            if (gameResults) {
              gameResults += " ";
            }
            return gameResults += `${gameResult.playerName}:${gameResult.point},${gameResult.tip}枚`;
          }, "");
        return tenhouLog;
      })
    },
  },

  methods: {
    async downloadTenhouLog() {
      if (!this.validate()) {
        return;
      }

      this.loading = true;

      this.tenhouLogs = await apiClient.downloadTenhouLog({ date: this.date, room_number: this.roomNumber });

      this.loading = false;
    },

    validate() {
      this.$refs.form.validate();

      return this.valid;
    },
  },
}
</script>
