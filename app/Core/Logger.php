<?php

/**
 * Wrapper class for Psr\Log
 *
 * Usage:
 * ----------------------------------------------------
 * logger(__METHOD__)->info('User Data', [
 *     'context1' => 'context1',
 *     'context2' => 'context2'
 * ]);
 *
 * logger(__METHOD__)->fatal('Missing base_path', [
 *     'context1' => 'context1',
 *     'context2' => 'context2'
 * ]);
 *
 * logger(__METHOD__)->info('Response Body - ' . str_replace('modified', 'updated', $response->getBody()));
 *
 * logger(__METHOD__)->exception('Exception', $e);
 * ----------------------------------------------------
 */

namespace Core;

use Models\AppLogs;
use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;
use Throwable;

/**
 * Class Logger
 *
 * @package Core
 */
class Logger implements LoggerInterface
{
    public const DEBUG     = 100;
    public const INFO      = 200;
    public const NOTICE    = 250;
    public const WARNING   = 300;
    public const ERROR     = 400;
    public const CRITICAL  = 500;
    public const ALERT     = 550;
    public const EMERGENCY = 600;
    public const FATAL     = 700;

    protected static $levels = [
        self::DEBUG     => 'DEBUG',
        self::INFO      => 'INFO',
        self::NOTICE    => 'NOTICE',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
        self::CRITICAL  => 'CRITICAL',
        self::ALERT     => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
        self::FATAL     => 'FATAL'
    ];

    protected $log_group;
    protected $log_stream;
    protected $log_push;
    protected $method;

    /**
     * Logger constructor.
     *
     * @param string $method
     */
    public function __construct(string $method = '')
    {
        $this->log_group  = config('logs.log_group');
        $this->log_stream = md5($this->log_group . date('Y-m-d H:i'));
        $this->log_push   = config('logs.log_push');

        if (!empty($method)) {
            $this->method = $method;
        }

        // Register error handlers;
        $this->registerErrorHandlers();
    }

    /**
     * @param int $level
     * @param null|string $message
     * @param array $context
     *
     * @return  void
     */
    public function message(int $level, string $message = null, array $context = []): void
    {
        // Get level name
        $level_name = $this->getLevelName($level);

        $event = [
            'group_name'  => $this->log_group,
            'stream_name' => $this->log_stream,
            'level_name'  => strtolower($level_name),
            'method'      => $this->method,
            'message'     => $message,
            'context'     => json_encode($context ?? [])
        ];

        try {

            // Push event to logs table
            (new AppLogs())->insertAppLog($event);

        } catch (Throwable $e) {
            //$this->exception($e, $event);
            error_log(print_r($e, true));
        }
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        $level = $this->getLevelCode($level);

        $this->message($level, (string) $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = []): void
    {
        $this->message(static::DEBUG, (string) $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = []): void
    {
        // Array of strings to exclude from info logs
//        $excluded = ['password', 'password_confirm', 'DB_PASS', 'DB_READ_PASS'];
//        $filtered = array_diff($context, $excluded);

        unset($context['password'], $context['password_confirm'], $context['DB_PASS'], $context['DB_READ_PASS']);

        $this->message(static::INFO, (string) $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = []): void
    {
        $this->message(static::NOTICE, (string) $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = []): void
    {
        $this->message(static::WARNING, (string) $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = []): void
    {
        $this->message(static::ERROR, (string) $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = []): void
    {
        $this->message(static::CRITICAL, (string) $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = []): void
    {
        $this->message(static::EMERGENCY, (string) $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function fatal($message, array $context = []): void
    {
        $this->message(static::FATAL, (string) $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = [])
    {
        $this->message(static::ALERT, (string) $message, $context);
    }

    /**
     * @param $message
     * @param $context
     *
     * @return  void
     */
    public function exception($message, $context): void
    {
        if (!is_array($context)) {
            $context = (array) $context;
        }

        $this->fatal($message, $context);
    }

    /**
     * Custom exception handler
     *
     * @param $e
     */
    public function exceptionHandler($e)
    {
        $context = $this->formatUncaughtErrors($e);

        $this->exception('Uncaught Exception', [
            'context' => $context
        ]);
    }

    /**
     * Custom error handler
     *
     * @param $e
     */
//    public function errorHandler($e)
//    {
//        $context = $this->formatUncaughtErrors($e);
//
//        $this->error('Uncaught Error', [
//            'context' => $context
//        ]);
//    }

    /**
     * Formatter for uncaught errors
     *
     * @param $e
     *
     * @return array
     */
    public function formatUncaughtErrors($e): array
    {
        return [
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTrace()
        ];
    }

    /**
     * Register error handlers
     */
    public function registerErrorHandlers()
    {
//        set_error_handler(function ($e) {
//            $this->errorHandler($e);
//        });

        set_exception_handler(function ($e) {
            $this->exceptionHandler($e);
        });
    }

    /**
     * @return array
     */
    public static function getLevels(): array
    {
        return array_flip(static::$levels);
    }

    /**
     * Gets the name of the logging level.
     *
     * @param int $level
     *
     * @return string
     * @throws InvalidArgumentException If level is not defined
     */
    public function getLevelName(int $level): string
    {
        if (!isset(static::$levels[$level])) {
            throw new InvalidArgumentException('Level "' . $level . '" is not defined, use one of: ' . implode(', ', array_keys(static::$levels)));
        }

        return static::$levels[$level];
    }

    /**
     * Converts PSR-3 levels to Monolog ones if necessary
     *
     * @param string|int $level Level number (monolog) or name (PSR-3)
     *
     * @throws InvalidArgumentException If level is not defined
     */
    public function getLevelCode($level): int
    {
        if (is_string($level)) {
            if (is_numeric($level)) {
                return intval($level);
            }

            // Contains chars of all log levels and avoids using strtoupper() which may have
            $upper = strtr($level, 'abcdefgilmnortuwy', 'ABCDEFGILMNORTUWY');
            if (defined(__CLASS__ . '::' . $upper)) {
                return constant(__CLASS__ . '::' . $upper);
            }

            throw new InvalidArgumentException('Level "' . $level . '" is not defined, use one of: ' . implode(', ', array_keys(static::$levels)));
        }

        if (!is_int($level)) {
            throw new InvalidArgumentException('Level "' . var_export($level, true) . '" is not defined, use one of: ' . implode(', ', array_keys(static::$levels)));
        }

        return $level;
    }
}
