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
    <v-btn color="secondary" href="/">戻る</v-btn>
  </v-form>
</template>

<script>
import { mapState } from "vuex";
import ApiClient from "../ApiClient";

export default {
  data() {
    return {
      money: {},
      loading: true,
      updating: false,
      errorMessages: [],
    };
  },

  computed: {
    ...mapState(["players"]),
  },

  async created() {
    this.money = (await ApiClient.getCurrentMoney()).money_players.reduce((money, moneyPlayer) => {
      money[moneyPlayer.player.id] = moneyPlayer.money;
      return money;
    }, {});
    this.loading = false;
  },

  methods: {
    async updateMoney() {
      this.updating = true;
      this.errorMessages = [];

      const response = await ApiClient.updateMoney(this.money);

      this.updating = false;

      if (response.ok) {
        location.href = "/";
        return;
      }

      this.errorMessages = (await response.json()).errors.money;
    }
  },
};
</script>
