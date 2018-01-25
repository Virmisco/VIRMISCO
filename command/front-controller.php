<?php
    use sednasoft\virmisco\domain\ChainOfResponsibilityCommandBus;
    use sednasoft\virmisco\domain\commandHandler\DbImportCommandHandler;
    use sednasoft\virmisco\domain\commandHandler\EditorCommandHandler;
    use sednasoft\virmisco\domain\commandHandler\FallbackCommandHandler;
    use sednasoft\virmisco\domain\commandHandler\LasFileService;
    use sednasoft\virmisco\domain\Factory;
    use sednasoft\virmisco\domain\MemoryEventStore;
    use sednasoft\virmisco\domain\projection\SqlQueue;
    use sednasoft\virmisco\domain\ProjectionEventBus;
    use sednasoft\virmisco\domain\RedisEventStore;
    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\error\MalformedUuidException;
    use sednasoft\virmisco\singiere\GenericRepository;
    use sednasoft\virmisco\singiere\Uuid;

    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';

    /** The report DB will be updated immediately (true) or when triggered by the next data retrieval (false) */
    const COMMIT_ON_WRITE = true;
    const COMMAND_NAMESPACE = 'sednasoft\virmisco\domain\command';

    header('Content-Type: text/plain; charset=UTF-8', true, 409); // start with a conflict in case of exceptions
    $commandName = isset($_GET['command']) ? $_GET['command'] : null;
    $simulationMode = isset($_GET['simulate']);
    try {
        $commandInstance = instantiateCommand($commandName, createParamRetriever($_POST));
        $factory = new Factory();
        $publisher = new ProjectionEventBus();
        if ($simulationMode) {
            $eventStore = new MemoryEventStore();
        } else {
            $eventStore = new RedisEventStore(REDIS_URI);
            $sqlQueue = new SqlQueue(REDIS_URI, REDIS_SQL_QUEUE);
            $databaseOptions = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            $databaseConnection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, $databaseOptions);
            $publisher->subscribe($sqlQueue);
        }
        $repository = new GenericRepository($eventStore, $factory, $publisher);
        $commandHandler = new FallbackCommandHandler($repository);
        $commandHandler = new EditorCommandHandler($repository, $commandHandler);
        $commandHandler = new DbImportCommandHandler($repository, $commandHandler);
        $commandHandler = new LasFileService($repository, $commandHandler);
        $commandBus = new ChainOfResponsibilityCommandBus($commandHandler);
        $commandBus->dispatch($commandInstance);
        if ($simulationMode) {
            header('Content-Type: text/plain', true, 200);
            echo 'Request has been processed only in simulation mode';
        } elseif (COMMIT_ON_WRITE) {
            foreach ($sqlQueue->commitToDatabase($databaseConnection) as $statement => $values) {
                // here the prepared statement has already been executed with the given values
            }
            header('Location: ' . aggregateUriFromCommand($commandInstance), true, 201);
        } else {
            header('Content-Type: text/plain', true, 202); // accepted for later processing, i. e. commit on next read
        }
    } catch (Exception $e) {
        formatException($e);
    }

    /**
     * @param AbstractCommand $commandInstance
     * @return string
     */
    function aggregateUriFromCommand(AbstractCommand $commandInstance)
    {
        return sprintf(
            'http://%s%s/%s/',
            $_SERVER['HTTP_HOST'],
            preg_replace('</command/.+$>', '/query', $_SERVER['REQUEST_URI']),
            $commandInstance->getAggregateId()
        );
    }

    /**
     * @param array $data
     * @return Closure
     */
    function createParamRetriever(array $data)
    {
        /**
         * @param string $name
         * @param string $type One of {b, i, f, s} (for bool, int, float or string) plus optionally one of {?, !} where '?'
         * stands for nullable and '!' for required (throws exception).
         * @return mixed
         * @throws InvalidArgumentException
         * @throws RuntimeException
         */
        return function ($name, $type) use ($data) {
            $value = isset($data[$name]) ? $data[$name] : null;
            $nullable = substr($type, 1, 1) === '?';
            if (substr($type, 1, 1) === '!' && ($value === null || $value === '')) {
                throw new RuntimeException(
                    sprintf('Required parameter %s is empty or was not provided in POST data', $name)
                );
            }
            switch (substr($type, 0, 1)) {
                case 'b':
                    $typedValue = strtolower($value) === 'true';
                    break;
                case 'i':
                    $typedValue = intval($value);
                    break;
                case 'f':
                    $typedValue = floatval($value);
                    break;
                case 's':
                    $typedValue = strval($value);
                    break;
                default:
                    throw new InvalidArgumentException('Invalid type specifier: ' . $type);
            }

            return $nullable && ($value === null || $value === '') ? null : $typedValue;
        };
    }

    /**
     * @param Exception $exception
     */
    function formatException(Exception $exception)
    {
        printf("%s\n\t%s", get_class($exception), str_replace("\n", "\n\t", $exception->getMessage()));
    }

    /**
     * @param string $commandName
     * @param Closure $get A callable accepting a param name and a type specifier to retrieve parameters. The type is
     * one of {b, i, f, s} (for bool, int, float or string), optionally followed by one of {?, !} where '?'  stands for
     * nullable and '!' for required (throws exception).
     * @return AbstractCommand
     * @throws MalformedUuidException When UUID is malformed.
     * @throws InvalidArgumentException When closure throws one.
     * @throws RuntimeException When command class is unknown or unsupported or closure throws one.
     */
    function instantiateCommand($commandName, Closure $get)
    {
        $commandName = $commandName ?: 'no\\such\\Class';
        $className = sprintf('%s\\%s', COMMAND_NAMESPACE, $commandName);
        if (!class_exists($className)) {
            throw new RuntimeException('Unknown command class: ' . $commandName);
        }
        $constructor = new ReflectionMethod($className, '__construct');
        $commandClass = $constructor->getDeclaringClass();
        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            if ($parameter->getClass() && $parameter->getClass()->getName() === Uuid::class) {
                $arguments[] = new Uuid($get($parameter->getName(), 's!'));
            } else {
//                $arguments[] = $get($parameter->getName(), $parameter->isOptional() ? 's?' : 's!');
                $arguments[] = $get($parameter->getName(), 's?');
            }
        }
        if ($commandClass->isSubclassOf(AbstractCommand::class)) {
            return $commandClass->newInstanceArgs($arguments);
        }
        throw new RuntimeException('Unsupported command class: ' . $commandName);
    }
