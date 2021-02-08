export default class RepositoryResponse {
  constructor(private responsePromise: Promise<Response>) {}

  async data(): Promise<unknown> {
    return (await (await this.responsePromise).json()).data
  }

  async invalid(): Promise<boolean> {
    return (await this.responsePromise).status === 422
  }

  async errors(): Promise<{ [key: string]: string[] }> {
    return (await (await this.responsePromise).json()).errors
  }

  async ok(): Promise<boolean> {
    return (await this.responsePromise).ok
  }
}
