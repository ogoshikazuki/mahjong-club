import urlTemplate from 'url-template'
import queryString from 'query-string'
import Player from './types/Player'
import Game from './types/Game'
import GameResult from './types/GameResult'
import TenhouLog from './types/TenhouLog'
import Money from './types/Money'
import RepositoryResponse from './RepositoryResponse'

const URL_TEMPLATE = {
  'player.index': '/api/player',
  'game.get-current-game': '/api/game/get-current-game',
  'game.get-current-money-games': '/api/game/get-current-money-games',
  'game.show': '/api/game/{id}',
  'game.result.destroy': '/api/game/result/{id}',
  'game.result.store': '/api/game/result',
  'game.result.update': '/api/game/result/{id}',
  'game.result.index': '/api/game/result',
  'tenhou.download-log': '/api/tenhou/download-log',
  'tenhou.register-log': '/api/tenhou/register-log',
  'money.current': '/api/money/current',
  'money.past': '/api/money/past',
  'money.reset': '/api/money/reset',
  'money.update': '/api/money/update',
}

const headers = {
  'Content-Type': 'application/json; charset=utf-8',
  'X-Requested-With': 'XMLHttpRequest',
}

type UrlParameter = {
  [key: string]: number | string
}

type QueryParameter = {
  [key: string]: number | string | number[] | string[] | { [key in number | string]: unknown }
}

type RequestPayload = {
  [key: string]: unknown
}

const _url = (template: string, urlParameter: UrlParameter = {}, queryParameter: QueryParameter = {}): string => {
  const url = urlTemplate.parse(template).expand(urlParameter)
  if (Object.keys(queryParameter).length > 0) {
    return `${url}?${queryString.stringify(queryParameter)}`
  }
  return url
}

const _get = async (template: string, urlParameter: UrlParameter = {}, queryParameter: QueryParameter = {}) => {
  const url = _url(template, urlParameter, queryParameter)
  return (await (await fetch(url)).json()).data
}

const _post = (template: string, parameters: RequestPayload = {}) => {
  const url = _url(template)
  return fetch(url, {
    method: 'POST',
    headers,
    body: JSON.stringify(parameters),
  })
}

const _put = (template: string, id: number, parameters: RequestPayload) => {
  const url = _url(template, { id })
  return fetch(url, {
    method: 'PUT',
    headers,
    body: JSON.stringify(parameters),
  })
}

const _delete = (template: string, id: number): RepositoryResponse => {
  const url = _url(template, { id })
  return new RepositoryResponse(fetch(url, { method: 'DELETE' }))
}

export default {
  getAllPlayers(): Promise<Player[]> {
    return _get(URL_TEMPLATE['player.index'])
  },

  getCurrentGame(): Promise<Game> {
    return _get(URL_TEMPLATE['game.get-current-game'])
  },

  getCurrentMoneyGames(): Promise<Game[]> {
    return _get(URL_TEMPLATE['game.get-current-money-games'])
  },

  findGame(id: number): Promise<Game> {
    return _get(URL_TEMPLATE['game.show'], { id })
  },

  deleteGameResult(id: number): RepositoryResponse {
    return _delete(URL_TEMPLATE['game.result.destroy'], id)
  },

  updateGameResult(id: number, parameters = {}): Promise<Response> {
    return _put(URL_TEMPLATE['game.result.update'], id, parameters)
  },

  storeGameResult(parameters: RequestPayload): Promise<Response> {
    return _post(URL_TEMPLATE['game.result.store'], parameters)
  },

  getAllGameResults(): Promise<GameResult[]> {
    return _get(URL_TEMPLATE['game.result.index'])
  },

  downloadTenhouLog(parameters: QueryParameter): Promise<TenhouLog[]> {
    return _get(URL_TEMPLATE['tenhou.download-log'], {}, parameters)
  },

  registerTenhouLog(parameters: RequestPayload): Promise<Response> {
    return _post(URL_TEMPLATE['tenhou.register-log'], parameters)
  },

  getCurrentMoney(): Promise<Money> {
    return _get(URL_TEMPLATE['money.current'])
  },

  getPastMoney(): Promise<Money[]> {
    return _get(URL_TEMPLATE['money.past'])
  },

  resetMoney(): Promise<Response> {
    return _post(URL_TEMPLATE['money.reset'])
  },

  updateMoney(money: { [key: number]: number }): Promise<Response> {
    return _post(URL_TEMPLATE['money.update'], { money })
  },
}
