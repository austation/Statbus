<?php

namespace App\Middleware;

use App\Controller\Controller;
use App\Domain\User\Data\User;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Exception;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;
use Throwable;

class ExceptionMiddleware extends Controller implements MiddlewareInterface
{
    private $settings;

    private $logger;

    private $user;

    public function __construct(
        private ContainerInterface $containerInterface
    ) {
        $this->settings = $containerInterface->get('settings')['error'];
        $loggerFactory = $containerInterface->get(LoggerFactory::class);
        $this->logger = $loggerFactory
            ->addFileHandler('error.log')
            ->createLogger();
        $this->user = $containerInterface->get('User');
    }

    public function action(): ResponseInterface
    {
        return $this->getResponse();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $twig = $this->containerInterface->get(Twig::class);
        $session = $this->containerInterface->get(Session::class);
        try {
            return $handler->handle($request);
        } catch (Exception $exception) {
            $session->set('authRedirect', (string) $request->getUri()->withPort(null));
            $response = new Response($exception->getCode());
            if ($this->settings['log_errors']) {
                $error = $this->getErrorDetails($exception, $this->settings['log_error_details']);
                $error['method'] = $request->getMethod();
                $error['url'] = (string)$request->getUri()->withPort(null);
                $error['ip'] = $_SERVER['REMOTE_ADDR'];
                $error['user'] = $this->user ? $this->user->getCkey() : null;
                $this->logger->error($exception->getMessage(), $error);
            }
            return $twig->render(
                $response,
                'error.html.twig',
                [
                    'error' => $exception,
                    'display_error_details' => $this->settings['display_error_details'],
                    'narrow' => true
                ],
            );
        }
    }
    private function getErrorDetails(Throwable $exception, bool $displayErrorDetails): array
    {
        if ($displayErrorDetails === true) {
            return [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'previous' => $exception->getPrevious(),
                'trace' => $exception->getTrace(),
            ];
        }

        return [
            'message' => $exception->getMessage(),
        ];
    }
}
