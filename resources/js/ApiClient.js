const URL = {
    "player.index": "api/player",
    "game.get-current-game": "api/game/get-current-game",
    "game.result.destroy": "api/game/result/{id}"
};

const _get = async url => (await (await fetch(url)).json()).data;
const _delete = (url, id) => {
    fetch(url.replace("{id}", id), { method: "DELETE" });
};

class ApiClient {
    getAllPlayers() {
        return _get(URL["player.index"]);
    }

    getCurrentGame() {
        return _get(URL["game.get-current-game"]);
    }

    deleteGameResult(id) {
        _delete(URL["game.result.destroy"], id);
    }
}

export default new ApiClient();
