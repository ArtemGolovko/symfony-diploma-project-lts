<?php

namespace App\Controller\Dashboard;

use App\Entity\ValueObject\Subscription;
use Carbon\Carbon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("IS_AUTHENTICATED_AND_VERIFIED")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard(SessionInterface $session): Response
    {
        /** @var Subscription $subscription */
        $subscription = $this->getUser()->getSubscription();

        if ($subscription->getLevel() !== Subscription::FREE) {
            $diff = $subscription->getExpiresAt()->diff(new \DateTimeImmutable())->days;
            if ($diff !== false && $diff < 3) {
                $session
                    ->getFlashBag()
                    ->add('warning',
                        sprintf("Подписка истекает через %d %s", $diff, ($diff === 1) ? "день" : "дня")
                    )
                ;
            }
        }

        return $this->render('dashboard/profile/dashboard.html.twig');
    }

    /**
     * @Route("/dashboard/subscription", name="app_dashboard_subscription")
     */
    public function subscription(): Response
    {
        return $this->render('dashboard/profile/subscription.html.twig');
    }

    /**
     * @Route("/dashboard/profile", name="app_dashboard_profile")
     */
    public function profile(): Response
    {
        return $this->render('dashboard/profile/profile.html.twig');
    }

    /**
     * @Route("/dashboard/modules", name="app_dashboard_modules")
     */
    public function modules(): Response
    {
        return $this->render('dashboard/profile/modules.html.twig');
    }
}
