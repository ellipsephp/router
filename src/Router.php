<?php declare(strict_types=1);

namespace Ellipse;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Interop\Http\Server\RequestHandlerInterface;

use Ellipse\Router\RouterAdapterInterface;
use Ellipse\Router\Exceptions\RequestNotHandledException;

class Router implements RequestHandlerInterface
{
    /**
     * The input name of the value overriding the request method.
     *
     * @var string
     */
    const METHOD_INPUT_KEY = '_method';

    /**
     * The router adapter.
     *
     * @var \Ellipse\Router\RouterAdapterInterface
     */
    private $adapter;

    /**
     * The return of the adapter ->match() method.
     *
     * @var null|\Ellipse\Router\Match
     */
    private $match;

    /**
     * Set up a router with the given router adapter.
     *
     * @param \Ellipse\Router\RouterAdapterInterface $adapter
     */
    public function __construct(RouterAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Return the match name.
     *
     * @return string
     * @throws \Ellipse\Router\Exceptions\RequestNotHandledException
     */
    public function getMatchName(): string
    {
        if (is_null($this->match)) {

            throw new RequestNotHandledException;

        }

        return (string) $this->match;
    }

    /**
     * Use the adapter to get a match for the given request and return a
     * response by proxying the match ->handle() method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();

        $method = strtoupper($body[self::METHOD_INPUT_KEY] ?? $request->getMethod());

        $request = $request->withMethod($method);

        $this->match = $this->adapter->match($request);

        return $this->match->handle($request);
    }
}
