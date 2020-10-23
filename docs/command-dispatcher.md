# The Command Dispatcher

This component provides a flexible implementation of the [Command Dispatcher/Bus](https://www.google.com/search?q=command+dispatcher+pattern) pattern.

Its goal is to decouple application logic from the UI layer (e.g. HTTP or CLI controllers).

## Modeling Commands

A command can be any instantiable class. A common practice it to use private properties set via the constructor and
accessed with public getters.

An `ApproveOrderCommand` class may look like this:

```php
class ApproveOrderCommand
{
    private string $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }
    
    public function getOrderId(): string
    {
        return $this->orderId;
    }
}
```

## Implementing a Handler

A handler class must implement `BusFactor\CommandDispatcher\HandlerInterface`. This interface defines a
`handle(object $command): void` method used to handle a command object.

```php
use BusFactor\CommandDispatcher\HandlerInterface;

class OrderCommandHandler implements HandlerInterface
{
    // Constructor and other stuff...

    /** @param ApproveOrderCommand $command */
    public function handle(object $command): void
    {
        // Use $command to do something useful...
    }
}
```

If you create a distinct handler class for every command, you may end up with a lot of these. A solution is to share
the same handler with a set of related commands.

The `BusFactor\CommandDispatcher\HandleCommandTrait` trait comes in handy. It passes the command object to a function
which name starts with `handle` and ends with the command's class name. 

```php
use BusFactor\CommandDispatcher\HandleCommandTrait;
use BusFactor\CommandDispatcher\HandlerInterface;

class OrderCommandHandler implements HandlerInterface
{
    use HandleCommandTrait;

    // Constructor and other stuff...

    private function handleApproveOrderCommand(ApproveOrderCommand $command): void
    {
        // Use $command to do something useful...
    }

    // Some other functions to handle other commands...
}
```

> The `handleApproveOrderCommand` function must not be public because it will never be called from outside the class.

## Configuring the Dispatcher

Your application's bootstrap code is a generally good place to configure the `Dispatcher` and register handlers.

If you're building on Laravel, then do it in a [Service Provider](https://laravel.com/docs/8.x/providers).

Only one instance of `Dispatcher` should be shared across your application.

```php
use BusFactor\CommandDispatcher\Dispatcher;

$dispatcher = new Dispatcher();
```

## Mapping Commands to Handlers

The `Dispatcher` must be told which handler to invoke when dispatching a command. It's done with the
`registerHandler` method.

```php
$handler = new OrderCommandHandler(/* inject required dependencies... */);

$dispatcher->registerHandler(ApproveOrderCommand::class, $handler);
```

Commands are identified by their fully qualified class name (FQCN).

## Dispatching Commands

Typically, commands are dispatched from within the UI layer.

Here's a realistic example of a [PSR-15](https://www.php-fig.org/psr/psr-15/) controller (aka *request handler*)
dispatching a command:

```php
use BusFactor\CommandDispatcher\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApproveOrderController implements RequestHandlerInterface
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Create the command object
        $orderId = $request->getParsedBody()['orderId'];
        $command = new ApproveOrderCommand($orderId);
        
        // Dispatch the command
        $this->dispatcher->dispatch($command);
        
        // Return a response...
    }
}
```

## Extending With Middlewares

You can wrap the dispatch process with custom logic using your own middlewares classes.
 
A middleware class must implement `BusFactor\CommandDispatcher\MiddlewareInterface`.

```php
use BusFactor\CommandDispatcher\DispatcherInterface;
use BusFactor\CommandDispatcher\MiddlewareInterface;

class SomeCommandDispatcherMiddleware implements MiddlewareInterface
{
    public function dispatch(object $command, DispatcherInterface $next): void
    {
        /* Do something before... */
        $next->dispatch($command);
        /* Do something after... */
    }
}
```

We add middlewares to `Dispatcher` with the `addMiddleware` method. This is usually done when configuring the
`Dispatcher` instance.

```php
$dispatcher->addMiddleware(new SomeCommandDispatcherMiddleware());
``` 

Every time you call the `dispatch` method on `Dispatcher`, middlewares will be executed in the order they were added.

## Best Practices

- Commands are named with an imperative verb.
- Commands should have only scalar properties.
- Commands should not validate themselves.
- Command validation and security-related checks must happen in command handlers.
- Group related commands with a shared handler.
