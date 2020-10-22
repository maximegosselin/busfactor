# The Command Dispatcher

> We assume that you know what the *Command Dispatcher/Bus* pattern is. If not, please take some time to familiarize
> yourself with [these nice articles](https://www.google.com/search?q=command+dispatcher+pattern).

## Modeling a Command

A command can be any instantiable class. A common practice it to use private properties set via the constructor and
public getters.

An `ApproveOrderCommand` class may look like:

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

If you create one distinct handler class for every command, you will end up with a lot of these and some may look
alike. It's recommended to share the same handler with a set of related commands.

The `BusFactor\CommandDispatcher\HandleCommandTrait` comes in handy. It passes the command object to a function which
name starts with `handle` and ends with the command class name. 

```php
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

> Note that `handleApproveOrderCommand` visibility can be private.

## Configuring the Command Dispatcher

You should instantiate only one `Dispatcher` that will be shared across your application.

The bootstrap code is a generally good place to configure the `Dispatcher` and register handlers.

```php
$dispatcher = new Dispatcher();
```

## Mapping Commands to Handlers

The `Dispatcher` must be instructed which handler it must invoke when dispatching a command.

Commands are identified by their fully qualified class name (FQCN).

```php
$handler = new OrderCommandHandler(/* inject dependencies... */);
$dispatcher->registerHandler(ApproveOrderCommand::class, $handler);
```

## Dispatching Commands

Then you are ready to dispatch commands from wherever you have access to `Dispatcher`.

```php
$command = new ApproveOrderCommand('123');
$dispatcher->dispatch($command);
```

## Adding Middlewares

You can wrap the dispatch process with custom logic encapsulated in a class that implements
`BusFactor\CommandDispatcher\MiddlewareInterface`.

Every time you call `dispatch`, the middleware will be executed.

```php
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

Middlewares are usually added statically when configuring `CommandDispatcher`.

```php
$dispatcher->addMiddleware(new SomeCommandDispatcherMiddleware());
``` 

## Best Practices

- Commands are named with an imperative verb.
- Commands should have only scalar properties.
- Commands should not validate themselves.
- Command validation and security-related checks must happen in command handlers.
- Group related commands with a shared command handler.
