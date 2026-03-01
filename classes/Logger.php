<?php

class Logger
{
    private static $logFile;

    private static function init()
    {
        if (!self::$logFile) {
            // Define o caminho do log. Pode ser alterado via constante LOG_PATH se definida
            $path = defined('LOG_PATH') ? LOG_PATH : __DIR__ . '/../logs/app.log';
            self::$logFile = $path;

            // Garante que o diretório existe
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public static function log($level, $message, $context = [])
    {
        self::init();

        $date = date('Y-m-d H:i:s');
        $level = strtoupper($level);

        // Formata array de contexto como JSON string para log legível
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';

        $logLine = "[$date] $level: $message$contextStr" . PHP_EOL;

        // Append atômico
        file_put_contents(self::$logFile, $logLine, FILE_APPEND | LOCK_EX);
    }

    public static function info($message, $context = [])
    {
        self::log('INFO', $message, $context);
    }

    public static function warning($message, $context = [])
    {
        self::log('WARNING', $message, $context);
    }

    public static function error($message, $context = [])
    {
        self::log('ERROR', $message, $context);
    }

    public static function debug($message, $context = [])
    {
        // Debug só se ambiente permitir ou solicitado
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            self::log('DEBUG', $message, $context);
        }
    }
}
