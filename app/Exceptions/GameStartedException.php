<?php

namespace App\Exceptions;

use Exception;

class GameStartedException extends Exception
{
    public function __construct(string $message = 'ゲームは既に始まっています。', int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->view('errors.400', ['message' => $this->getMessage()], $this->getCode());
    }
}
