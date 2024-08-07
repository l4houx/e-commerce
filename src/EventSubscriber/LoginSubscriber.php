<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\AvatarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AvatarService $avatarService,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $event->getRequest()->getClientIp();
        if ($user instanceof User) {
            $ip = $event->getRequest()->getClientIp();
            if ($ip !== $user->getLastLoginIp()) {
                $user->setLastLoginIp($ip);
            }
            $user->setLastLogin(new \DateTimeImmutable());
            $avatar = $this->avatarService->createAvatar($user->getEmail());
            $user->setAvatar($avatar);
            $this->em->flush();
        }
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onLogin',
        ];
    }
}
