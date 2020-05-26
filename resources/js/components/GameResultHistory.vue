<template>
    <el-table :data="tableData" v-loading="loading" max-height="500">
        <el-table-column prop="rate" label="レート"></el-table-column>
        <el-table-column
            v-for="player in players"
            :key="player.id"
            :prop="String(player.id)"
            :label="player.name"
        ></el-table-column>
        <el-table-column>
            <template slot-scope="scope">
                <el-button
                    size="mini"
                    type="danger"
                    @click="deleteGameResult(scope.row.id)"
                    >削除</el-button
                >
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
import apiClient from "../ApiClient";

export default {
    data: function() {
        return {
            tableData: [],
            players: [],
            loading: true
        };
    },

    methods: {
        async load() {
            this.players = await apiClient.getAllPlayers();
            const currentGame = await apiClient.getCurrentGame();
            for (let gameResult of currentGame.gameResults) {
                let row = { rate: gameResult.rate };
                for (let player of this.players) {
                    for (let gameResultPlayer of gameResult.gameResultPlayers) {
                    }
                    row[player.id] = player.id;
                }
                this.tableData.push(row);
            }
            this.tableData = currentGame.gameResults.map(gameResult => {
                const row = { id: gameResult.id, rate: gameResult.rate };
                for (let player of this.players) {
                    for (let gameResultPlayer of gameResult.gameResultPlayers) {
                        if (gameResultPlayer.player_id === player.id) {
                            row[player.id] = gameResultPlayer.point;
                        }
                    }
                }
                return row;
            });

            this.loading = false;
        },

        async deleteGameResult(id) {
            if (!confirm("本当に削除しますか？")) {
                return;
            }
            this.loading = true;

            await apiClient.deleteGameResult(id);
            this.load();
        }
    },

    created: function() {
        this.load();
    }
};
</script>
