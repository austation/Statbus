<?php

namespace App;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Data\Payload;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Views\Twig;

class Controller
{
    public $responder;

    public $template = 'home/home.twig';

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private Twig $twig
    ) {

    }

    public function createResponse(): ResponseInterface
    {
        return $this->responseFactory->createResponse()->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function render(string $template, array $data = []): ResponseInterface
    {
        $response = $this->createResponse();
        return $this->twig->render($response, $template, $data);
    }

    // abstract protected function action(array $args = []): Payload;

    // public function __invoke(
    //     ServerRequestInterface $request,
    //     ResponseInterface $response,
    //     array $args = []
    // ): ResponseInterface {
    //     $this->request = $request;
    //     try {
    //         return $this->responder->processPayload($response, $this->action($args), $this->template);
    //     } catch (\Exception $e) {
    //         $payload = new Payload();
    //         $payload->throwError(500, $e->getMessage());
    //         return $this->responder->processPayload($response, $payload, $this->template);
    //     }
    // }
}
