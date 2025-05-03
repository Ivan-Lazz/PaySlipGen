<?php

namespace Core;

class Logger
{
    /**
     * The available log levels.
     *
     * @var array
     */
    protected $levels = [
        'debug' => 100,
        'info' => 200,
        'notice' => 250,
        'warning' => 300,
        'error' => 400,
        'critical' => 500,
        'alert' => 550,
        'emergency' => 600,
    ];

    /**
     * The log file path.
     *
     * @var string
     */
    protected $path;

    /**
     * The minimum log level.
     *
     * @var string
     */
    protected $minimumLevel;

    /**
     * Create a new logger instance.
     *
     * @param string $channel
     */
    public function __construct($channel = 'file')
    {
        $config = config('logging.channels.' . $channel);
        
        if (!$config) {
            $config = config('logging.channels.' . config('logging.default'));
        }
        
        $this->path = $config['path'] ?? dirname(__DIR__) . '/logs/app.log';
        $this->minimumLevel = $config['level'] ?? 'debug';
        
        // Create directory if it doesn't exist
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * Log an emergency message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->log('emergency', $message, $context);
    }

    /**
     * Log an alert message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->log('alert', $message, $context);
    }

    /**
     * Log a critical message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->log('critical', $message, $context);
    }

    /**
     * Log an error message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->log('error', $message, $context);
    }

    /**
     * Log a warning message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Log a notice message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->log('notice', $message, $context);
    }

    /**
     * Log an info message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->log('info', $message, $context);
    }

    /**
     * Log a debug message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Log a message.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log($level, $message, array $context = [])
    {
        if ($this->shouldLog($level)) {
            $entry = $this->formatEntry($level, $message, $context);
            $this->writeToLog($entry);
        }
    }

    /**
     * Determine if the log level is loggable.
     *
     * @param string $level
     * @return bool
     */
    protected function shouldLog($level)
    {
        return $this->levels[$level] >= $this->levels[$this->minimumLevel];
    }

    /**
     * Format the log entry.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function formatEntry($level, $message, array $context = [])
    {
        $date = date('Y-m-d H:i:s');
        $level = strtoupper($level);
        
        $message = $this->interpolate($message, $context);
        $context = !empty($context) ? json_encode($context) : '';
        
        return "[{$date}] [{$level}] {$message} {$context}" . PHP_EOL;
    }

    /**
     * Interpolate context values into the message placeholders.
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context = [])
    {
        $replace = [];
        
        foreach ($context as $key => $val) {
            if (is_string($val) || is_numeric($val) || is_bool($val)) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        
        return strtr($message, $replace);
    }

    /**
     * Write the log entry to the log file.
     *
     * @param string $entry
     * @return void
     */
    protected function writeToLog($entry)
    {
        file_put_contents($this->path, $entry, FILE_APPEND);
    }
}