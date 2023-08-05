<?php

namespace App\Handler;

use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class ExceptionHandler extends Controller implements ErrorHandlerInterface
{
    public function action(): ResponseInterface
    {
        return $this->getResponse();
    }
    public function __invoke(Throwable $exception, bool $displayErrorDetails): ResponseInterface
    {
        return $this->render(
            'error.html.twig',
            [
                'error' => $exception,
                'narrow' => true
            ],
        );
    }
}
