import urlTemplate from "url-template";
import queryString from "query-string";

const URL_TEMPLATE = {
    "player.index": "/api/player",
    "game.get-current-game": "/api/game/get-current-game",
    "game.get-current-money-games": "/api/game/get-current-money-games",
    "game.show": "/api/game/{id}",
    "game.result.destroy": "/api/game/result/{id}",
    "game.result.store": "/api/game/result",
    "game.result.update": "/api/game/result/{id}",
    "game.result.index": "/api/game/result",
    "tenhou.download-log": "/api/tenhou/download-log",
    "tenhou.register-log": "/api/tenhou/register-log",
    "money.current": "/api/money/current",
    "money.past": "/api/money/past",
    "money.reset": "/api/money/reset",
    "money.update": "/api/money/update",
};

const headers = {
    "Content-Type": "application/json; charset=utf-8",
    "X-Requested-With": "XMLHttpRequest"
};

const _url = (template: string, urlParameter = {}, queryParameter = {}) => {
    const url = urlTemplate.parse(template).expand(urlParameter);
    if (Object.keys(queryParameter).length > 0) {
        return `${url}?${queryString.stringify(queryParameter)}`;
    }
    return url;
};

const _get = async (template: string, urlParameter = {}, queryParameter = {}) => {
    const url = _url(template, urlParameter, queryParameter);
    return (await (await fetch(url)).json()).data;
};

const _post = (template: string, parameters = {}) => {
    const url = _url(template);
    return fetch(url, {
        method: "POST",
        headers,
        body: JSON.stringify(parameters)
    });
};

// eslint-disable-next-line @typescript-eslint/ban-types
const _put = (template: string, id: number, parameters: object) => {
    const url = _url(template, { id });
    return fetch(url, {
        method: "PUT",
        headers,
        body: JSON.stringify(parameters)
    });
};

const _delete = (template: string, id: number) => {
    const url = _url(template, { id });
    fetch(url, { method: "DELETE" });
};

class ApiClient {
    getAllPlayers() {
        return _get(URL_TEMPLATE["player.index"]);
    }

    getCurrentGame() {
        return _get(URL_TEMPLATE["game.get-current-game"]);
    }

    getCurrentMoneyGames() {
        return _get(URL_TEMPLATE["game.get-current-money-games"]);
    }

    findGame(id: number) {
        return _get(URL_TEMPLATE["game.show"], { id });
    }

    deleteGameResult(id: number) {
        _delete(URL_TEMPLATE["game.result.destroy"], id);
    }

    updateGameResult(parameters: { id: number }) {
        return _put(
            URL_TEMPLATE["game.result.update"],
            parameters.id,
            parameters
        );
    }

    // eslint-disable-next-line @typescript-eslint/ban-types
    storeGameResult(parameters: object) {
        return _post(URL_TEMPLATE["game.result.store"], parameters);
    }

    getAllGameResults() {
        return _get(URL_TEMPLATE["game.result.index"]);
    }

    // eslint-disable-next-line @typescript-eslint/ban-types
    downloadTenhouLog(parameters: object) {
        return _get(URL_TEMPLATE["tenhou.download-log"], {}, parameters);
    }

    // eslint-disable-next-line @typescript-eslint/ban-types
    registerTenhouLog(parameters: object) {
        return _post(URL_TEMPLATE["tenhou.register-log"], parameters);
    }

    getCurrentMoney() {
        return _get(URL_TEMPLATE["money.current"]);
    }

    getPastMoney() {
        return _get(URL_TEMPLATE["money.past"]);
    }

    resetMoney() {
        return _post(URL_TEMPLATE["money.reset"]);
    }

    updateMoney(money: number) {
        return _post(URL_TEMPLATE["money.update"], { money });
    }
}

export default new ApiClient();