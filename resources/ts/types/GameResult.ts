import GameResultPlayer from './GameResultPlayer'

interface GameResult {
  id: number
  rate: number
  gameResultPlayers: GameResultPlayer[]
}
export default GameResult
