<?php

namespace Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RouterInterface;

class Framework
{
    /** @var  RequestMatcherInterface */
    protected $routeMatcher;

    /** @var  ControllerResolverInterface */
    protected $controllerResolver;

    /** @var  ArgumentResolverInterface */
    protected $argumentResolver;

    /**
     * @param RequestMatcherInterface $matcher
     * @param ControllerResolverInterface $controllerResolver
     * @param ArgumentResolverInterface $argumentResolver
     */
    public function __construct(
        RequestMatcherInterface $matcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver
    ) {
        $this->routeMatcher = $matcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * @param Request $request
     * @return mixed|Response
     */
    public function handle(Request $request)
    {
        try {
            $request->attributes->add($this->routeMatcher->matchRequest($request));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            return new JsonResponse(call_user_func_array($controller, $arguments));
        } catch (ResourceNotFoundException $e) {
            return new JsonResponse('Resource not found', 404);
        } catch (\Exception $e) {
            var_dump($e);
            return new JsonResponse('An error occurred', 500);
        }
    }
}