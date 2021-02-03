<template>
  <v-app>
    <v-navigation-drawer app v-model="drawer">
      <v-list>
        <v-list-item :to="{ name: 'aggregate' }">
          <v-list-item-icon>
            <v-icon>mdi-google-analytics</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>集計</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item :to="{ name: 'interpersonal-result' }">
          <v-list-item-icon>
            <v-icon>mdi-account-multiple</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>対人戦績</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar app dark>
      <v-app-bar-nav-icon @click="toggleDrawer"></v-app-bar-nav-icon>
      <v-toolbar-title>
        <router-link :to="{ name: 'home' }" tag="span">Mahjong</router-link>
      </v-toolbar-title>
    </v-app-bar>
    <v-main>
      <v-container>
        <router-view></router-view>
      </v-container>
    </v-main>
    <v-footer app dark></v-footer>
  </v-app>
</template>

<script lang="ts">
import Vue from 'vue'

export default Vue.extend({
  data(): {
    drawer: boolean
  } {
    return {
      drawer: false,
    }
  },

  methods: {
    toggleDrawer(): void {
      this.drawer = !this.drawer
    },
  },
})
</script>
