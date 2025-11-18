<?php

namespace App\Factory;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Exception;

/**
 * Factory.
 */
final class LoggerFactory
{
    private string $path;

    private int $level;

    private array $handler = [];

    private ?LoggerInterface $testLogger;


    /**
     * The constructor.
     *
     * @param array $settings The settings
     */
    public function __construct(array $settings = [])
    {
        $this->path = (string)($settings['path'] ?? __DIR__ . '/../../logs/');
        $this->level = (int)($settings['level'] ?? Logger::INFO);

        // This can be used for testing to make the Factory testable
        if (isset($settings['test'])) {
            $this->testLogger = $settings['test'];
        }
    }

    /**
     * Build the logger.
     *
     * @param string|null $name The logging channel
     *
     * @return LoggerInterface The logger
     * @throws Exception
     */
    public function createLogger(string $name = null): LoggerInterface
    {
        if (isset($this->testLogger)) {
            return $this->testLogger;
        }
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        $logger = new Logger($name ?: $uuid);

        foreach ($this->handler as $handler) {
            $logger->pushHandler($handler);
        }

        $this->handler = [];

        return $logger;
    }

    /**
     * Add a handler.
     *
     * @param HandlerInterface $handler The handler
     *
     * @return self The logger factory
     */
    public function addHandler(HandlerInterface $handler): self
    {
        $this->handler[] = $handler;

        return $this;
    }

    /**
     * Add rotating file logger handler.
     *
     * @param string $filename The filename
     * @param int|null $level The level (optional)
     *
     * @return self The logger factory
     */
    public function addFileHandler(string $filename, int $level = null): self
    {
        $filename = sprintf('%s/%s', $this->path, $filename);
        /** @phpstan-ignore-next-line */
        $rotatingFileHandler = new RotatingFileHandler($filename, 0, $level ?? $this->level, true, 0777);

        // The last "true" here tells monolog to remove empty []'s
        $rotatingFileHandler->setFormatter(new LineFormatter(null, null, false, true));

        $this->addHandler($rotatingFileHandler);

        return $this;
    }

    /**
     * Add a console logger.
     *
     * @param int|null $level The level (optional)
     *
     * @return self The logger factory
     */
    public function addConsoleHandler(int $level = null): self
    {
        /** @phpstan-ignore-next-line */
        $streamHandler = new StreamHandler('php://output', $level ?? $this->level);
        $streamHandler->setFormatter(new LineFormatter(null, null, false, true));

        $this->addHandler($streamHandler);

        return $this;
    }
}
