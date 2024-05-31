<?php

namespace App\Service;

use Twig\Environment;
use App\Entity\Setting;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasRoles;
use App\Repository\SettingRepository;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\MailerInterface;
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
    public function updateEnv(string $name, string $value): void
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
    public function getEnv(string $name)
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
                    return new RedirectResponse($this->router->generate('dashboard_admin_' . $alt));
                } elseif ($this->authChecker->isGranted(HasRoles::MODERATOR)) {
                    return new RedirectResponse($this->router->generate('dashboard_moderator_' . $alt));
                } elseif ($this->authChecker->isGranted(HasRoles::TEAM)) {
                    return new RedirectResponse($this->router->generate('dashboard_team_' . $alt));
                } elseif ($this->authChecker->isGranted(HasRoles::EDITOR)) {
                    return new RedirectResponse($this->router->generate('dashboard_editor_' . $alt));
                } elseif ($this->authChecker->isGranted(HasRoles::DEFAULT)) {
                    return new RedirectResponse($this->router->generate('dashboard_user_' . $alt));
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

    // Returns the blog posts after applying the specified search criterias
    public function getBlogPosts($criterias): QueryBuilder
    {
        //$this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
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
        //$this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
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
        //$this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
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
        //$this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
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
        //$this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';

        return $this->em->getRepository("App\Entity\Page")->getPages($slug);
    }

    // Returns the products after applying the specified search criterias
    public function getProducts($criterias): QueryBuilder
    {
        //$this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);

        $id = array_key_exists('id', $criterias) ? $criterias['id'] : 'all';

        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : 'createdAt';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'ASC';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository("App\Entity\Product")->getProducts($id, $sort, $order, $limit, $count);
    }

    // Returns the users after applying the specified search criterias
    public function getUsers($criterias): QueryBuilder
    {
        //$this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
        $role = array_key_exists('role', $criterias) ? $criterias['role'] : 'all';
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : 'all';
        $createdbyrestaurantslug = array_key_exists('createdbyrestaurantslug', $criterias) ? $criterias['createdbyrestaurantslug'] : 'all';
        $restaurantname = array_key_exists('restaurantname', $criterias) ? $criterias['restaurantname'] : 'all';
        $restaurantslug = array_key_exists('restaurantslug', $criterias) ? $criterias['restaurantslug'] : 'all';
        $username = array_key_exists('username', $criterias) ? $criterias['username'] : 'all';
        $email = array_key_exists('email', $criterias) ? $criterias['email'] : 'all';
        $firstname = array_key_exists('firstname', $criterias) ? $criterias['firstname'] : 'all';
        $lastname = array_key_exists('lastname', $criterias) ? $criterias['lastname'] : 'all';
        $isVerified = array_key_exists('isVerified', $criterias) ? $criterias['isVerified'] : true;
        $isSuspended = array_key_exists('isSuspended', $criterias) ? $criterias['isSuspended'] : false;
        $countryslug = array_key_exists('countryslug', $criterias) ? $criterias['countryslug'] : 'all';
        $followedby = array_key_exists('followedby', $criterias) ? $criterias['followedby'] : 'all';
        $hasboughtsubscriptionforRecipe = array_key_exists('hasboughtsubscriptionfor', $criterias) ? $criterias['hasboughtsubscriptionfor'] : "all";
        $hasboughtsubscriptionforRestaurant = array_key_exists('hasboughtsubscriptionforrestaurant', $criterias) ? $criterias['hasboughtsubscriptionforrestaurant'] : "all";
        $apiKey = array_key_exists('apikey', $criterias) ? $criterias['apikey'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : 'all';
        $isOnHomepageSlider = array_key_exists('isOnHomepageSlider', $criterias) ? $criterias['isOnHomepageSlider'] : 'all';
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : 'all';
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : 'u.createdAt';
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : 'DESC';
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository("App\Entity\User")->getUsers($role, $keyword, $createdbyrestaurantslug, $restaurantname, $restaurantslug, $username, $email, $firstname, $lastname, $isVerified, $isSuspended, $countryslug, $slug, $followedby, $hasboughtsubscriptionforRecipe, $hasboughtsubscriptionforRestaurant, $apiKey, $isOnHomepageSlider, $limit, $sort, $order, $count);
    }

    // Returns the currencies
    public function getCurrencies(mixed $criterias): QueryBuilder
    {
        $ccy = \array_key_exists('ccy', $criterias) ? $criterias['ccy'] : 'all';
        $symbol = \array_key_exists('symbol', $criterias) ? $criterias['symbol'] : 'all';

        return $this->em->getRepository("App\Entity\Currency")->getCurrencies($ccy, $symbol);
    }

    // Returns the current protocol of the current request
    public function getCurrentRequestProtocol(): string
    {
        return $this->requestStack->getCurrentRequest()->getScheme();
    }

    // Returns the layout settings entity to be used in the twig templates
    public function getAppLayoutSettings()
    {
        $appLayoutSettings = $this->em->getRepository("App\Entity\AppLayoutSetting")->find(1);

        return $appLayoutSettings;
    }
}
