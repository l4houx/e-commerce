<?php

namespace App\EventSubscriber;

use App\Entity\Traits\HasRoles;
use App\Http\Admin\Controller\BaseController;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Controller\Dashboard\Admin\AdminBaseController;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly string $dashboardPath, 
        private readonly AuthorizationCheckerInterface $auth
    ) {
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $uri = '/' . trim($event->getRequest()->getRequestUri(), '/') . '/';
        $path = '/' . trim($this->dashboardPath, '/') . '/';
        if (
            substr($uri, 0, mb_strlen($path)) === $path &&
            !$this->auth->isGranted(HasRoles::TEAM)
        ) {
            $exception = new AccessDeniedException();
            $exception->setSubject($event->getRequest());
            throw $exception;
        }
    }

    public function onController(ControllerEvent $event): void
    {
        if (false === $event->isMainRequest()) {
            return;
        }
        $controller = $event->getController();
        if (is_array($controller) && $controller[0] instanceof AdminBaseController && !$this->auth->isGranted(HasRoles::TEAM)) {
            $exception = new AccessDeniedException();
            $exception->setSubject($event->getRequest());
            throw $exception;
        }
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onController',
            RequestEvent::class => 'onRequest',
        ];
    }
}
