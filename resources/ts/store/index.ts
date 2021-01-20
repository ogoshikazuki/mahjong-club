import Vue from "vue";
import Vuex from "vuex";
import { SET_PLAYERS } from "./mutation_types";
import apiClient from "../ApiClient";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        players: []
    },

    mutations: {
        [SET_PLAYERS](state, players) {
            state.players = players;
        }
    },

    actions: {
        async loadPlayers(context) {
            context.commit(SET_PLAYERS, await apiClient.getAllPlayers());
        }
    }
});
