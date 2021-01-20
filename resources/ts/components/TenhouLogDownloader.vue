<template>
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
</template>

<script>
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
    };
  },

  computed: {
    roomNumber() {
      return `C${this.roomNumberNoPrefix}`;
    },
  },

  methods: {
    async downloadTenhouLog() {
      if (!this.validate()) {
        return;
      }

      this.loading = true;

      this.$emit("complete", await apiClient.downloadTenhouLog({ date: this.date, room_number: this.roomNumber }));

      this.loading = false;
    },

    validate() {
      this.$refs.form.validate();

      return this.valid;
    },
  },
}
</script>
