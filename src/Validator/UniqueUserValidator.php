<?php

namespace App\Validator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserValidator extends ConstraintValidator
{
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @param string $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        /** @var App\Validator\UniqueUser $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        switch ($this->security->getUser()) {
            case null:
                if (!$this->em->getRepository(User::class)->findOneBy(['email' => $value])) {
                    return;
                }
                break;
            case !null:
                if ($this->em->getRepository(User::class)->findOneBy(['email' => $value]) === $this->security->getUser()
                    || !$this->em->getRepository(User::class)->findOneBy(['email' => $value])) {
                    return;
                }
                break;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}

