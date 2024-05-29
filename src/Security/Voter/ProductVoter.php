<?php

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter
{
    final public const CREATE = 'CREATE';
    final public const EDIT = 'EDIT';
    final public const MANAGE = 'MANAGE';
    final public const DELETE = 'DELETE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
            in_array($attribute, [self::CREATE])
            || (
                in_array($attribute, [self::MANAGE, self::EDIT, self::DELETE])
                && $subject instanceof Product
            )
        ;
    }

    /**
     * @param Product|null $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(HasRoles::ADMIN)) {
            return true;
        }

        return match ($attribute) {
            self::MANAGE => $this->canManage($user, $subject),
            self::EDIT => $this->canEdit(),
            self::DELETE => $this->canDelete(),
            self::CREATE, => true,
            default => false,
        };
    }

    private function canManage(User $user, Product $product): bool
    {
        //return $recipe->getAuthor()->getId() === $user->getId();
        return $user->isVerified() && $user == $product->getAuthor();
    }

    private function canEdit(): bool
    {
        return $this->security->isGranted(HasRoles::EDITOR);
    }

    private function canDelete(): bool
    {
        return $this->security->isGranted(HasRoles::ADMIN);
    }
}
