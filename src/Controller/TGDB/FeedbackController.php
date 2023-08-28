<?php

namespace App\Controller\TGDB;

use App\Controller\Controller;
use App\Domain\Admin\Service\SetFeedbackLinkService;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class FeedbackController extends Controller
{
    #[Inject]
    private SetFeedbackLinkService $feedbackLinkService;

    public function action(): ResponseInterface
    {
        if($this->isPOST()) {
            $user =  $this->getUser();
            $ckey = $user->getCkey();
            $data = (array) $this->getRequest()->getParsedBody();
            $result = $this->feedbackLinkService->setFeedbackUrl($data['feedback'], $ckey);
            if($data['feedback'] !== $user->getFeedback()) {
                if($result) {
                    $this->addSuccessMessage("Your feedback link was successfully updated");
                } else {
                    $this->addErrorMessage("$result");
                }
            }
            return $this->routeRedirect('tgdb.feedback');
        }
        return $this->render('tgdb/feedback.html.twig', [
            
            'feedback' => $this->getUser()->getFeedback()
        ]);
    }
}
