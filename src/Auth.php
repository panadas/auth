<?php
namespace Panadas\Auth;

use Panadas\Auth\Handler\HandlerInterface;
use Panadas\Auth\UserFinder\UserFinderInterface;
use Panadas\HttpMessage\Cookie;
use Panadas\HttpMessage\Request;
use Panadas\HttpMessage\Response;

class Auth
{

    private $userFinder;
    private $handler;
    private $token;
    private $cookie;
    private $headerName;
    private $cookieName;
    private $cookiePath;
    private $cookieDomain;
    private $cookieSecure = true;
    private $cookieHttpOnly = true;

    public function __construct(UserFinderInterface $userFinder, HandlerInterface $handler)
    {
        $this
            ->setUserFinder($userFinder)
            ->setHandler($handler)
            ->setHeaderName("X-Auth-Token")
            ->setCookieName("authtoken");
    }

    public function getUserFinder()
    {
        return $this->userFinder;
    }

    protected function setUserFinder(UserFinderInterface $userFinder)
    {
        $this->userFinder = $userFinder;

        return $this;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    protected function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function hasToken()
    {
        return (null !== $this->getToken());
    }

    protected function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    protected function removeToken()
    {
        return $this->setToken(null);
    }

    public function getCookie()
    {
        return $this->cookie;
    }

    public function hasCookie()
    {
        return (null !== $this->getCookie());
    }

    protected function setCookie(Cookie $cookie = null)
    {
        $this->cookie = $cookie;

        return $this;
    }

    protected function removeCookie()
    {
        return $this->setCookie(null);
    }

    public function getHeaderName()
    {
        return $this->headerName;
    }

    public function setHeaderName($headerName)
    {
        $this->headerName = $headerName;

        return $this;
    }

    public function getCookieName()
    {
        return $this->cookieName;
    }

    public function setCookieName($cookieName)
    {
        $this->cookieName = $cookieName;

        return $this;
    }

    public function getCookiePath()
    {
        return $this->cookiePath;
    }

    public function hasCookiePath()
    {
        return (null !== $this->getCookiePath());
    }

    public function setCookiePath($cookiePath)
    {
        $this->cookiePath = $cookiePath;

        return $this;
    }

    public function removeCookiePath()
    {
        return $this->setCookiePath(null);
    }

    public function getCookieDomain()
    {
        return $this->cookieDomain;
    }

    public function hasCookieDomain()
    {
        return (null !== $this->getCookieDomain());
    }

    public function setCookieDomain($cookieDomain)
    {
        $this->cookieDomain = $cookieDomain;

        return $this;
    }

    public function isCookieSecure()
    {
        return $this->cookieSecure;
    }

    public function setCookieSecure($cookieSecure)
    {
        $this->cookieSecure = (bool) $cookieSecure;

        return $this;
    }

    public function isCookieHttpOnly()
    {
        return $this->cookieHttpOnly;
    }

    public function setCookieHttpOnly($cookieHttpOnly)
    {
        $this->cookieHttpOnly = (bool) $cookieHttpOnly;

        return $this;
    }

    public function isAuthed()
    {
        return $this->hasToken();
    }

    public function getUser()
    {
        if (!$this->hasToken()) {
            return null;
        }

        return $this->getUserFinder()->findById($this->getHandler()->retrieve($this->getToken()));
    }

    public function authenticate(Request $request)
    {
        $token = $request->getHeaders()->get(
            $this->getHeaderName(),
            $request->getCookies()->get($this->getCookieName())
        );

        if (null !== $token) {

            $handler = $this->getHandler();

            $handler->gc();

            if (null !== $handler->retrieve($token)) {
                $handler->update($token, new \DateTime());
                $this->setToken($token);
            } else {
                $this->destroy();
            }

        }

        return $this;
    }

    public function signIn($username, $password)
    {
        if ($this->isAuthed()) {
            throw new \RuntimeException("User is already authenticated");
        }

        $user = $this->getUserFinder()->findByCredentials($username, $password);
        if (null === $user) {
            throw new \InvalidArgumentException("Invalid credentials");
        }

        $token = $this->getHandler()->create($user);

        return $this
            ->setToken($token)
            ->setCookie($this->createCookie($token, null));
    }

    public function signOut($username, $password)
    {
        if (!$this->isAuthed()) {
            throw new \RuntimeException("User is not authenticated");
        }

        $this->getHandler()->delete($this->getToken());

        return $this->destroy();
    }

    protected function destroy()
    {
        return $this
            ->removeToken()
            ->setCookie($this->createCookie(null, new \DateTime("-1 year")));
    }

    protected function createCookie($value, $expires)
    {
        return new Cookie(
            $this->getCookieName(),
            $value,
            $expires,
            $this->getCookiePath(),
            $this->getCookieDomain(),
            $this->isCookieSecure(),
            $this->isCookieHttpOnly()
        );
    }

    public function applyCookie(Response $response)
    {
        if ($this->hasCookie()) {
            $response->getCookies()->add($this->getCookie());
        }

        return $this;
    }
}
