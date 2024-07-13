<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use Twig\Environment;
use App\Entity\Shop\Order;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasRoles;
use App\Entity\Settings\Setting;
use Symfony\Component\Mime\Address;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\Settings\SettingRepository;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SettingService
{
    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly EntityManagerInterface $em,
        private readonly AuthorizationCheckerInterface $authChecker,
        private readonly RequestStack $requestStack,
        private readonly KernelInterface $kernel,
        private readonly CacheItemPoolInterface $cache,
        private readonly UrlGeneratorInterface $router,
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
        private readonly MailerInterface $mailer,
        private readonly ParameterBagInterface $parameter,
        private readonly Environment $templating,
        private readonly UrlMatcherInterface $urlMatcherInterface
    ) {
    }

    public function findAll(): array
    {
        return $this->settingRepository->findAllForTwig();
    }

    public function getValue(string $name): mixed
    {
        return $this->settingRepository->getValue($name);
    }

    // Gets a setting from the cache / db
    public function getSettings(string $name): mixed
    {
        $settingcache = $this->cache->getItem('setting_'.$name);
        if ($settingcache->isHit()) {
            return $settingcache->get();
        }

        /** @var Setting $setting */
        /** @phpstan-ignore-next-line */
        $setting = $this->em->getRepository(Setting::class)->findOneByName($name);
        if (!$setting) {
            return null;
        }

        $settingcache->set($setting->getValue());
        $this->cache->save($settingcache);

        return $setting ? ($setting->getValue()) : (null);
    }

    // Sets a option from the cache / db
    public function setSettings(string $name, $value): int
    {
        /** @var Setting $setting */
        /** @phpstan-ignore-next-line */
        $setting = $this->em->getRepository(Setting::class)->findOneByName($name);
        if ($setting) {
            $setting->setValue($value);
            $this->em->flush();
            $settingcache = $this->cache->getItem('setting_'.$name);
            $settingcache->set($value);
            $this->cache->save($settingcache);

            if ('website_name' === $name || 'website_no_reply_email' === $name || 'website_root_url' === $name) {
                $this->updateEnv(mb_strtoupper($name), $value);
            }

            return 1;
        }

        return 0;
    }

    // Updates the .env name with the choosen value
    function updateEnv(string $name, string $value): void
    {
        if (0 == strlen($name)) {
            return;
        }

        $value = trim($value);
        if ($value == trim($value) && false !== strpos($value, ' ')) {
            $value = '"'.$value.'"';
        }

        $envFile = $this->kernel->getProjectDir().'/.env';
        $lines = file($envFile);
        $newLines = [];

        foreach ($lines as $line) {
            preg_match('/'.$name.'=/i', $line, $matches);
            if (!\count($matches)) {
                $newLines[] = $line;
                continue;
            }
            $newLine = trim($name).'='.trim($value)."\n";
            $newLines[] = $newLine;
        }

        $newContent = implode('', $newLines);
        file_put_contents($envFile, $newContent);
    }

    // Gets the value with the entered name from the .env file
    function getEnv(string $name)
    {
        if (0 == strlen($name)) {
            return;
        }

        $envFile = $this->kernel->getProjectDir().'/.env';
        $lines = file($envFile);
        foreach ($lines as $line) {
            preg_match('/'.$name.'=/i', $line, $matches);

            if (!\count($matches)) {
                continue;
            }

            $value = trim(explode('=', $line, 2)[1]);

            return trim($value, '"');
        }

        return null;
    }

    // Generates a random string iwth a specified length
    public function generateReference(int $length): string
    {
        $reference = implode('', [
            bin2hex(random_bytes(2)),
            bin2hex(random_bytes(2)),
            bin2hex(\chr((\ord(random_bytes(1)) & 0x0F) | 0x40)).bin2hex(random_bytes(1)),
            bin2hex(\chr((\ord(random_bytes(1)) & 0x3F) | 0x80)).bin2hex(random_bytes(1)),
            bin2hex(random_bytes(2)),
        ]);

        return mb_strlen($reference) > $length ? mb_substr($reference, 0, $length) : $reference;
    }

    // Checks if string ends with string
    public function endsWith(mixed $haystack, mixed $needle): bool
    {
        $length = mb_strlen($needle);
        if (!$length) {
            return true;
        }

        return mb_substr($haystack, -$length) === $needle;
    }

    // Get route name from path
    public function getRouteName($path = null): mixed
    {
        try {
            if ($path) {
                return $this->urlMatcherInterface->match($path)['_route'];
            }

            return $this->urlMatcherInterface->match($this->requestStack->getCurrentRequest()->getPathInfo())['_route'];
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
            return null;
        }
    }

    // Redirects to the referer page when available, if not, redirects to the dashboard index
    public function redirectToReferer(mixed $alt = null): RedirectResponse
    {
        if ($this->requestStack->getCurrentRequest()->headers->get('referer')) {
            return new RedirectResponse($this->requestStack->getCurrentRequest()->headers->get('referer'));
        } else {
            if ($alt) {
                if ($this->authChecker->isGranted(HasRoles::ADMINAPPLICATION) || $this->authChecker->isGranted(HasRoles::ADMIN)) {
                    return new RedirectResponse($this->router->generate('dashboard_admin_'.$alt));
                } elseif ($this->authChecker->isGranted(HasRoles::MODERATOR)) {
                    return new RedirectResponse($this->router->generate('dashboard_moderator_'.$alt));
                } elseif ($this->authChecker->isGranted(HasRoles::TEAM)) {
                    return new RedirectResponse($this->router->generate('dashboard_team_'.$alt));
                } elseif ($this->authChecker->isGranted(HasRoles::EDITOR)) {
                    return new RedirectResponse($this->router->generate('dashboard_editor_'.$alt));
                } elseif ($this->authChecker->isGranted(HasRoles::DEFAULT)) {
                    return new RedirectResponse($this->router->generate('dashboard_user_'.$alt));
                } else {
                    return new RedirectResponse($this->router->generate($alt));
                }
            } else {
                return new RedirectResponse($this->router->generate('dashboard_main'));
            }
        }
    }

    // Shows the soft deleted entities for ROLE_SUPER_ADMIN
    public function disableSofDeleteFilterForAdmin(EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker): void
    {
        $em->getFilters()->enable('softdeleteable');
        if ($authChecker->isGranted(HasRoles::ADMINAPPLICATION)) {
            $em->getFilters()->disable('softdeleteable');
        }
    }

    // Handles all the operations needed after a successful payment processing
    public function handleSuccessfulPayment(string $orderReference): void
    {
        $order = $this->getOrders(['status' => 0, 'ref' => $orderReference])->getQuery()->getOneOrNullResult();
        $order->setStatus(1);

        $this->em->persist($order);
        $this->em->flush();
    }

    // Handles all the operations needed after a failed payment processing
    public function handleFailedPayment(string $orderReference, string $note = null): void
    {
        $order = $this->getOrders(['status' => 0, 'ref' => $orderReference])->getQuery()->getOneOrNullResult();
        $order->setStatus(-2);
        $order->setNote($note);

        $this->em->persist($order);
        $this->em->flush();
    }

    // Handles all the operations needed after a canceled payment processing
    public function handleCanceledPayment(string $orderReference, string $note = null): void
    {
        $order = $this->getOrders(['status' => 'all', 'ref' => $orderReference])->getQuery()->getOneOrNullResult();

        foreach ($order->getOrderDetails() as $orderDetail) {
            foreach ($orderDetail->getProduct() as $product) {
                $this->em->remove($product);
                $this->em->flush();
            }
        }

        $order->setStatus(-1);
        $order->setNote($note);

        $this->em->persist($order);
        $this->em->flush();
    }

    // Removes all the specified user cart elements
    public function emptyCart(User $user): void
    {

    }

    // Sends the orders to the customer
    public function sendOrderConfirmationEmail(Order $order, string $emailTo): int
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($pdfOptions);
        $html = $this->templating->render('dashboard/shared/shop/order/customers-pdf.html.twig', [
            'order' => $order,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $ordersPdfFile = $dompdf->output();

        $this->mailer->send(
            (new TemplatedEmail())
                ->from(new Address(
                    $this->parameter->get('website_no_reply_email'),
                    $this->parameter->get('website_name'),
                ))
                ->subject($this->translator->trans('Your orders bought from') . ' ' . $this->parameter->get('website_name'))
                ->to(new Address($emailTo))
                ->htmlTemplate('mails/order-confirmation-email.html.twig')
                ->attach(
                    $ordersPdfFile,
                    $this->parameter->get('website_name'),
                    $this->translator->trans("invoice-").$order->getId().'.pdf',
                )
                ->context(['order' => $order])
        );

        return 1;
    }

    // Returns the orders after applying the specified search criterias
    public function getOrders($criterias): QueryBuilder
    {
        //$this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $status = array_key_exists('status', $criterias) ? $criterias['status'] : 1;
        $ref = array_key_exists('ref', $criterias) ? $criterias['ref'] : "all";
        //$user = array_key_exists('user', $criterias) ? $criterias['user'] : "all";
        $orderDetails = array_key_exists('orderDetails', $criterias) ? $criterias['orderDetails'] : "all";
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "createdAt";
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : "DESC";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository("\App\Entity\Shop\Order")->getOrders($status, $ref/*, $user*/, $orderDetails, $sort, $order, $limit, $count);
    }

    // Returns the blog posts after applying the specified search criterias
    public function getBlogPosts($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $selecttags = array_key_exists('selecttags', $criterias) ? $criterias['selecttags'] : false;
        $isOnline = \array_key_exists('isOnline', $criterias) ? $criterias['isOnline'] : true;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $category = array_key_exists('category', $criterias) ? $criterias['category'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $otherthan = array_key_exists('otherthan', $criterias) ? $criterias['otherthan'] : 'all';
        $sort = array_key_exists('order', $criterias) ? $criterias['order'] : 'createdAt';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'DESC';

        return $this->em->getRepository("App\Entity\Post")->getBlogPosts($selecttags, $isOnline, $keyword, $slug, $category, $limit, $sort, $order, $otherthan);
    }

    // Returns the posts types after applying the specified search criterias
    public function getPostsTypes($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $isOnline = array_key_exists('isOnline', $criterias) ? $criterias['isOnline'] : true;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : 'p.name';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'ASC';
        $hasvenues = array_key_exists('hasvenues', $criterias) ? $criterias['hasvenues'] : 'all';

        return $this->em->getRepository("App\Entity\PostType")->getPostsTypes($isOnline, $keyword, $slug, $limit, $sort, $order, $hasvenues);
    }

    // Returns the blog posts categories after applying the specified search criterias
    public function getBlogPostCategories($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $isOnline = \array_key_exists('isOnline', $criterias) ? $criterias['isOnline'] : true;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $order = \array_key_exists('order', $criterias) ? $criterias['order'] : 'c.name';
        $sort = \array_key_exists('sort', $criterias) ? $criterias['sort'] : 'ASC';

        return $this->em->getRepository("App\Entity\PostCategory")->getBlogPostCategories($isOnline, $keyword, $slug, $limit, $order, $sort);
    }

    // Returns the comments after applying the specified search criterias
    public function getComments($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $id = array_key_exists('id', $criterias) ? $criterias['id'] : 'all';
        $user = array_key_exists('user', $criterias) ? $criterias['user'] : 'all';
        $isApproved = array_key_exists('isApproved', $criterias) ? $criterias['isApproved'] : true;
        $isRGPD = array_key_exists('isRGPD', $criterias) ? $criterias['isRGPD'] : 'all';
        $ip = \array_key_exists('ip', $criterias) ? $criterias['ip'] : 'all';
        $post = array_key_exists('post', $criterias) ? $criterias['post'] : 'all';
        $parent = array_key_exists('parent', $criterias) ? $criterias['parent'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : 'publishedAt';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'DESC';

        return $this->em->getRepository("App\Entity\Comment")->getComments($keyword, $id, $user, $isApproved, $isRGPD, $ip, $post, $parent, $limit, $count, $sort, $order);
    }

    // Returns the pages after applying the specified search criterias
    public function getPages($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';

        return $this->em->getRepository("App\Entity\Settings\Page")->getPages($slug);
    }

    // Returns the reviews after applying the specified search criterias
    public function getReviews($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $user = array_key_exists('user', $criterias) ? $criterias['user'] : 'all';
        $product = array_key_exists('product', $criterias) ? $criterias['product'] : 'all';
        $isVisible = array_key_exists('isVisible', $criterias) ? $criterias['isVisible'] : true;
        $rating = array_key_exists('rating', $criterias) ? $criterias['rating'] : 'all';
        $minrating = array_key_exists('minrating', $criterias) ? $criterias['minrating'] : 'all';
        $maxrating = array_key_exists('maxrating', $criterias) ? $criterias['maxrating'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : 'createdAt';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'DESC';

        return $this->em->getRepository("App\Entity\Shop\Review")->getReviews($keyword, $slug, $user, $product, $isVisible, $rating, $minrating, $maxrating, $limit, $count, $sort, $order);
    }

    // Returns the categories after applying the specified search criterias
    public function getCategories($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        //$isOnline = array_key_exists('isOnline', $criterias) ? $criterias['isOnline'] : true;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $id = array_key_exists('id', $criterias) ? $criterias['id'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : 'c.name';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'ASC';

        return $this->em->getRepository("App\Entity\Shop\Category")->getCategories($keyword, $id, $limit, $sort, $order);
    }

    // Returns the products after applying the specified search criterias
    public function getProducts($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $selecttags = array_key_exists('selecttags', $criterias) ? $criterias['selecttags'] : false;
        $isOnline = \array_key_exists('isOnline', $criterias) ? $criterias['isOnline'] : true;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $id = array_key_exists('id', $criterias) ? $criterias['id'] : 'all';
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $addedtofavoritesby = array_key_exists('addedtofavoritesby', $criterias) ? $criterias['addedtofavoritesby'] : 'all';
        $isOnHomepageSlider = array_key_exists('isOnHomepageSlider', $criterias) ? $criterias['isOnHomepageSlider'] : 'all';
        $subCategories = array_key_exists('subCategories', $criterias) ? $criterias['subCategories'] : 'all';
        $ref = array_key_exists('ref', $criterias) ? $criterias['ref'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $otherthan = array_key_exists('otherthan', $criterias) ? $criterias['otherthan'] : 'all';
        $sort = array_key_exists('order', $criterias) ? $criterias['order'] : 'createdAt';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'DESC';
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository("App\Entity\Shop\Product")->getProducts($selecttags, $isOnline, $keyword, $id, $slug, $addedtofavoritesby, $isOnHomepageSlider, $subCategories, $ref, $limit, $sort, $order, $otherthan, $count);
    }

    // Returns the users after applying the specified search criterias
    public function getUsers($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $role = array_key_exists('role', $criterias) ? $criterias['role'] : 'all';
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $username = array_key_exists('username', $criterias) ? $criterias['username'] : 'all';
        $email = array_key_exists('email', $criterias) ? $criterias['email'] : 'all';
        $firstname = array_key_exists('firstname', $criterias) ? $criterias['firstname'] : 'all';
        $lastname = array_key_exists('lastname', $criterias) ? $criterias['lastname'] : 'all';
        $isVerified = array_key_exists('isVerified', $criterias) ? $criterias['isVerified'] : true;
        $isSuspended = array_key_exists('isSuspended', $criterias) ? $criterias['isSuspended'] : false;
        $followedby = array_key_exists('followedby', $criterias) ? $criterias['followedby'] : 'all';
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $isOnHomepageSlider = array_key_exists('isOnHomepageSlider', $criterias) ? $criterias['isOnHomepageSlider'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : 'createdAt';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'DESC';
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository("App\Entity\User")->getUsers($role, $keyword, $username, $email, $firstname, $lastname, $isVerified, $isSuspended, $slug, $followedby, $isOnHomepageSlider, $limit, $sort, $order, $count);
    }

    // Returns the help center categories after applying the specified search criterias
    public function getHelpCenterCategories($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $parent = \array_key_exists('parent', $criterias) ? $criterias['parent'] : 'all';
        $isOnline = \array_key_exists('isOnline', $criterias) ? $criterias['isOnline'] : true;
        $keyword = \array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $slug = \array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $limit = \array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $order = \array_key_exists('order', $criterias) ? $criterias['order'] : 'c.name';
        $sort = \array_key_exists('sort', $criterias) ? $criterias['sort'] : 'ASC';

        return $this->em->getRepository("App\Entity\HelpCenterCategory")->getHelpCenterCategories($parent, $isOnline, $keyword, $slug, $limit, $order, $sort);
    }

    // Returns the help center articles after applying the specified search criterias
    public function getHelpCenterArticles($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $selecttags = \array_key_exists('selecttags', $criterias) ? $criterias['selecttags'] : false;
        $isOnline = \array_key_exists('isOnline', $criterias) ? $criterias['isOnline'] : true;
        $isFeatured = \array_key_exists('isFeatured', $criterias) ? $criterias['isFeatured'] : 'all';
        $keyword = \array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $slug = \array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $category = \array_key_exists('category', $criterias) ? $criterias['category'] : 'all';
        $limit = \array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $otherthan = \array_key_exists('otherthan', $criterias) ? $criterias['otherthan'] : 'all';
        $sort = \array_key_exists('sort', $criterias) ? $criterias['sort'] : 'createdAt';
        $order = \array_key_exists('order', $criterias) ? $criterias['order'] : 'DESC';

        return $this->em->getRepository("App\Entity\HelpCenterArticle")->getHelpCenterArticles($selecttags, $isOnline, $isFeatured, $keyword, $slug, $category, $limit, $sort, $order, $otherthan);
    }

    // Returns the help center faqs after applying the specified search criterias
    public function getHelpCenterFaqs($criterias): QueryBuilder
    {
        // $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $selecttags = \array_key_exists('selecttags', $criterias) ? $criterias['selecttags'] : false;
        $isOnline = \array_key_exists('isOnline', $criterias) ? $criterias['isOnline'] : true;
        $keyword = \array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $id = \array_key_exists('id', $criterias) ? $criterias['id'] : 'all';
        $limit = \array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $otherthan = \array_key_exists('otherthan', $criterias) ? $criterias['otherthan'] : 'all';
        $sort = \array_key_exists('sort', $criterias) ? $criterias['sort'] : 'createdAt';
        $order = \array_key_exists('order', $criterias) ? $criterias['order'] : 'DESC';

        return $this->em->getRepository("App\Entity\HelpCenterFaq")->getHelpCenterFaqs($selecttags, $isOnline, $keyword, $id, $limit, $sort, $order, $otherthan);
    }

    // Returns the testimonials after applying the specified search criterias
    public function getTestimonials($criterias): QueryBuilder
    {
        //$this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $user = array_key_exists('user', $criterias) ? $criterias['user'] : 'all';
        $isOnline = array_key_exists('isOnline', $criterias) ? $criterias['isOnline'] : true;
        $rating = array_key_exists('rating', $criterias) ? $criterias['rating'] : 'all';
        $minrating = array_key_exists('minrating', $criterias) ? $criterias['minrating'] : 'all';
        $maxrating = array_key_exists('maxrating', $criterias) ? $criterias['maxrating'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : 'createdAt';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'DESC';

        return $this->em->getRepository("App\Entity\Testimonial")->getTestimonials($keyword, $slug, $user, $isOnline, $rating, $minrating, $maxrating, $limit, $count, $sort, $order);
    }

    // Returns the currencies
    public function getCurrencies(mixed $criterias): QueryBuilder
    {
        $ccy = \array_key_exists('ccy', $criterias) ? $criterias['ccy'] : 'all';
        $symbol = \array_key_exists('symbol', $criterias) ? $criterias['symbol'] : 'all';

        return $this->em->getRepository("App\Entity\Settings\Currency")->getCurrencies($ccy, $symbol);
    }

    // Returns the current protocol of the current request
    public function getCurrentRequestProtocol(): string
    {
        return $this->requestStack->getCurrentRequest()->getScheme();
    }

    // Returns the layout settings entity to be used in the twig templates
    public function getAppLayoutSettings()
    {
        $appLayoutSettings = $this->em->getRepository("App\Entity\Settings\AppLayoutSetting")->find(1);

        return $appLayoutSettings;
    }
}
