# The Command Bus

> This component can be used alone but it also fits very well in a CQRS-style application.

## Concepts

The Command Bus is an architectural pattern that helps keeping your application code decoupled from the presentation
layer or user interface.

A **command** represents a directive for your application, like "_Hey, I want to approve the order identified by this
number_". Commands are often modeled as simple [DTOs](https://en.wikipedia.org/wiki/Data_transfer_object) holding
minimal information required by a specific use case.

A **command handler** receives a command and executes the logic it encapsulates.

Finally, the **command bus** is responsible for dispatching commands to their command handler. 

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

## Implementing a Command Handler

A command handler class must implement `CommandHandlerInterface`. This interface defines a `handle(object $command)`
method used to handle a command object.

```php
class OrderCommandHandler implements CommandHandlerInterface
{
    // Constructor and other stuff...

    /** @param ApproveOrderCommand $command */
    public function handle(object $command): void
    {
        // Use $command to do something useful...
    }
}
```

If you create one distinct command handler class for each command, you will end up with a lot of these and some may look
alike. It's recommended to share one command handler with a set of related commands.

The `CommandHandlerTrait` comes in handy. It forwards the command object to a function based on its class and
prefixed by `handle*`. 

```php
class OrderCommandHandler implements CommandHandlerInterface
{
    use CommandHandlerTrait;

    // Constructor and other stuff...

    private function handleApproveOrderCommand(ApproveOrderCommand $command): void
    {
        // Use $command to do something useful...
    }
}
```

> Note that `handleApproveOrderCommand` visibility can be private.

## Configuring the Command Bus

You should create only one instance of `CommandBus` that will be shared across your application.

The bootstrap code is a generally good place to configure the `CommandBus` and register command handlers.

```php
$commandBus = new CommandBus();
```

## Mapping Commands to Handlers

The `CommandBus` must be instructed which handler it must invoke when dispatching a command.

Commands are identified by their fully qualified class name (FQCN).

```php
$handler = new OrderCommandHandler(/* inject dependencies... */);
$commandBus->registerHandler(ApproveOrderCommand::class, $handler);
```

## Dispatching Commands

Then you are ready to dispatch commands from wherever you have access to `CommandBus`.

```php
$command = new ApproveRequestCommand('123');
$commandBus->dispatch($command);
```

## Adding Middlewares

You can wrap the dispatch process with custom logic encapsulated in a class that implements `BusFactor\CommandBus\MiddlewareInterface`.

Every time you call `dispatch`, the middleware will be executed.

```php
class SomeCommandBusMiddleware implements MiddlewareInterface
{
    public function dispatch(object $command, CommandDispatcherInterface $next): void
    {
        /* Do something before... */
        $next->dispatch($command);
        /* Do something after... */
    }
}
```

Middlewares are usually added statically when configuring `CommandBus`.

```php
$commandBus->addMiddleware(new SomeCommandBusMiddleware());
``` 

## Best Practices

- Commands are named with an imperative verb.
- Commands should have only scalar properties.
- Commands should not validate themselves.
- Command validation and security-related checks must happen in command handlers.
- Group related commands with a shared command handler.
