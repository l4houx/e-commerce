<?php

namespace App\Security\Voter;

use App\Entity\SuperAdministrator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SuperAdministratorVoter extends Voter
{
    public function __construct(private readonly string $appEnvironment)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, $subject): bool
    {
        return !in_array($attribute, ['IS_IMPERSONATOR']);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var SuperAdministrator $administrator */
        $administrator = $token->getUser();

        if (!$administrator instanceof SuperAdministrator) {
            return false;
        }

        if ('prod' === $this->appEnvironment) {
            return 'superadmin' === $administrator->getUsername() && 1 === $administrator->getId();
        }

        return 'superadmin' === $administrator->getUsername();
    }
}
