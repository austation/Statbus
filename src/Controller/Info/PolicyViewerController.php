<?php

namespace App\Controller\Info;

use App\Controller\Controller;
use App\Domain\Admin\Repository\AdminLogRepository;
use App\Domain\Info\Service\PolicyService;
use App\Service\HTMLSanitizerService;
use App\Service\ServerInformationService;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class PolicyViewerController extends Controller
{

    #[Inject()]
    private PolicyService $policyService;

    public function action(): ResponseInterface
    {
        $policy = false;
        $server = $this->getArg('server');
        if ($server) {
            $server = ServerInformationService::getServerFromName(ucfirst($server));
            $url = sprintf("%sconfig/policy.json", strtolower($server->getPublicLogs()));
            $stack = HandlerStack::create();
            $stack->push(new CacheMiddleware(), 'cache');
            $client = new Client([
                'timeout'  => 2.0,
                'handler' => $stack
            ]);
            try {
                $response = $client->get($url);
                $policy = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                die($e);
                exit;
            }
            $cmconfig = [];
            $environment = new Environment($cmconfig);
            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new GithubFlavoredMarkdownExtension());
            $converter = new MarkdownConverter($environment);

            $config = \HTMLPurifier_Config::createDefault();
            $config->set('AutoFormat.Linkify', true);
            $config->set('HTML.Allowed', 'br, hr, a[href], font[color], b, h1, h2, h3, h4, h5, em, i, blockquote, ul, ol, li, B, BR, U, HR');
            $config->set('HTML.TargetBlank', true);
            foreach ($policy as &$p) {
                $p = HTMLSanitizerService::sanitizeStringWithConfig($config, $p);
                $p = $converter->convert($p);
            }
        }
        return $this->render('info/policy.html.twig', [
            'policy' => $policy,
            'servers' => $this->policyService->getServers()
        ]);
    }
}
