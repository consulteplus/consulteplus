<?php

class ApiResponse
{
    /**
     * Envia uma resposta de sucesso (HTTP 200)
     * 
     * @param mixed $data Dados a serem retornados
     * @param string $message Mensagem opcional
     * @param array $meta Metadados (paginação, etc)
     */
    public static function success($data = null, $message = null, $meta = [])
    {
        self::send(true, $data, $message, null, 200, $meta);
    }

    /**
     * Envia uma resposta de criação (HTTP 201)
     * 
     * @param mixed $data Dados do recurso criado
     * @param string $message Mensagem opcional
     */
    public static function created($data = null, $message = 'Recurso criado com sucesso')
    {
        self::send(true, $data, $message, null, 201);
    }

    /**
     * Envia uma resposta de erro
     * 
     * @param string $message Mensagem de erro
     * @param int $statusCode Código HTTP (400, 401, 403, 404, 500)
     * @param mixed $errors Detalhes dos erros (array de validação, etc)
     */
    public static function error($message, $statusCode = 400, $errors = null)
    {
        self::send(false, null, $message, $errors, $statusCode);
    }

    /**
     * Método interno para formatar e enviar a resposta JSON
     */
    private static function send($success, $data, $message, $errors, $statusCode, $meta = [])
    {
        // Limpar output buffer se houver lixo anterior
        if (ob_get_length())
            ob_clean();

        // Headers
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);

        // Estrutura Padronizada
        $response = [
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'meta' => !empty($meta) ? $meta : null,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Remover campos nulos para limpar o payload (opcional, mas bom pra API clean)
        // Preferimos manter success/data sempre presentes para consistência no front

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
