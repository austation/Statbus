<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use App\Domain\User\Data\User;
use App\Domain\User\Service\AuthenticateUser;
use DI\Attribute\Inject;
use Slim\Routing\RouteContext;

abstract class Controller
{
    private $response;
    private $request;
    private ?array $query = null;
    private ?array $args = null;
    private ?RouteContext $route = null;

    private ?User $user;

    #[Inject]
    private ?AuthenticateUser $auth = null;

    public function __construct(
        protected ContainerInterface $container
    ) {
        $this->user = $this->container->get('User');
    }

    private function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    protected function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    private function setRequest(ServerRequestInterface $request): self
    {
        $this->request = $request;
        return $this;
    }

    protected function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    private function setQuery(): self
    {
        $this->query = $this->getRequest()->getQueryParams();
        return $this;
    }

    protected function getQuery(): ?array
    {
        return $this->query;
    }

    protected function getQueryPart(string $name): ?string
    {
        if(isset($this->getQuery()[$name])) {
            return $this->getQuery()[$name];
        }
        return null;
    }

    private function setArgs(array $args): self
    {
        $this->args = $args;
        return $this;
    }

    protected function getArgs(): ?array
    {
        return $this->args;
    }

    protected function getArg(string $key): mixed
    {
        if(isset($this->args[$key])) {
            return $this->args[$key];
        }
    }

    private function setRoute(): self
    {
        $this->route = RouteContext::fromRequest($this->getRequest());
        return $this;
    }

    public function getRoute(): RouteContext
    {
        return $this->route;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $this->setResponse($response);
        $this->setRequest($request);
        $this->setQuery();
        $this->setArgs($args);
        $this->setRoute();
        return $this->action();
    }

    protected function render(string $template, array $data = []): ResponseInterface
    {
        if(isset($_GET['json'])) {
            return $this->json($data);
        }
        $twig = $this->container->get(Twig::class);
        return $twig->render($this->getResponse(), $template, $data);
    }

    protected function json(array $data): ResponseInterface
    {
        $response = $this->response->withHeader("Content-Type", "application/json");
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    protected function getUriForRoute($route): string
    {
        $router = $this->container->get(RouteParserInterface::class);
        $uri = $router->fullUrlFor(
            $this->getRequest()->getUri()->withPort(null),
            $route
        );
        if ((isset($_SERVER['HTTPS']) && 'On' === $_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            $uri = str_replace('http://', 'https://', $uri);
        }
        return $uri;
    }

    protected function routeRedirect($route): ResponseInterface
    {
        return $this->redirect($this->getUriForRoute($route));
    }

    protected function redirect($uri): ResponseInterface
    {
        return $this->response->withStatus(301)->withHeader('Location', $uri);
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    abstract public function action(): ResponseInterface;
}
