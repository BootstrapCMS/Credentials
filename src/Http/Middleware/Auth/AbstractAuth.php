<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Http\Middleware\Auth;

use Closure;
use GrahamCampbell\Credentials\Credentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * This is the abstract auth middleware class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
abstract class AbstractAuth
{
    /**
     * The credentials instance.
     *
     * @var \GrahamCampbell\Credentials\Credentials
     */
    protected $credentials;

    /**
     * The logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Create a new instance.
     *
     * @param \GrahamCampbell\Credentials\Credentials $credentials
     * @param \Psr\Log\LoggerInterface                $logger
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException|\Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return void
     */
    public function __construct(Credentials $credentials, LoggerInterface $logger)
    {
        $this->credentials = $credentials;
        $this->logger = $logger;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->credentials->check()) {
            $this->logger->info(trans('credentials::credentials.user_tried_to_access_without_login'), ['path' => $request->path()]);
            if ($request->ajax()) {
                throw new UnauthorizedHttpException(trans('credentials::credentials.action_requires_login'));
            }

            return Redirect::guest(URL::route('account.login'))
                ->with('error', trans('credentials::credentials.you_must_be_logged_in'));
        }

        if (!$this->credentials->hasAccess($level = $this->level())) {
            $this->logger->warning(
                trans('credentials::credentials.user_tried_to_access_without_permission'),
                ['path' => $request->path(), 'permission' => $level]
            );
            throw new AccessDeniedHttpException(ucfirst($level).' '.trans('credentials::credentials.permissions_are_required'));
        }

        return $next($request);
    }

    /**
     * Get the required authentication level.
     *
     * We're using reflection here to grab the short class name of the
     * extending class, and then returning the lowercase value.
     *
     * @return string
     */
    protected function level()
    {
        $reflection = new ReflectionClass($this);

        $level = $reflection->getShortName();

        return strtolower($level);
    }

    /**
     * Get credentials instance.
     *
     * @return \GrahamCampbell\Credentials\Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Get logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
