<?php

namespace App\Middleware;

use App\Controller\Controller;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Exception;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

class ExceptionMiddleware extends Controller implements MiddlewareInterface
{
    public function __construct(
        private ContainerInterface $containerInterface
    ) {
    }

    public function action(): ResponseInterface
    {
        return $this->getResponse();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $twig = $this->containerInterface->get(Twig::class);
        $session = $this->containerInterface->get(Session::class);
        $session->set('authRedirect', (string) $request->getUri()->withPort(null));
        try {
            return $handler->handle($request);
        } catch (Exception $exception) {
            if($exception instanceof StatbusUnauthorizedException) {
                $response = new Response($exception->getCode());
                return $twig->render(
                    $response,
                    'error.html.twig',
                    [
                        'error' => $exception,
                        'narrow' => true
                    ],
                );
            } else {
                $response = $handler->handle($request);
                return $response;
            }
        }
    }
}
