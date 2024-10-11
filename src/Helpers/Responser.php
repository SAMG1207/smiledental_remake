<?php

namespace App\Helpers;

class Responser {
    /**
     * Envía una respuesta JSON al cliente.
     *
     * @param int $code Código de estado HTTP.
     * @param string|array|bool $message Mensaje o datos a enviar.
     
     * @return void
     */
    public static function response(int $code, string|array|bool $message) {
        // Establecer el tipo de contenido
        header('Content-Type: application/json');
        // Establecer el código de respuesta HTTP
        http_response_code($code);

        // Determinar el estado
        $status = match (true) {
            $code >= 200 && $code < 300 => "success",
            $code >= 300 && $code < 500 => "client_error",
            $code >= 500 => "server_error",
            default => "unknown"
        };

        // Construir la respuesta
        $response = [
            'status' => $status,
            'code' => $code,
            'message' => $message
        ];

        // Enviar la respuesta como JSON
        echo json_encode($response);
    }

    /**
     * Envía una respuesta de error.
     *
     * @param int $code Código de estado HTTP.
     * @param string $message Mensaje de error.
     * @return void
     */
    public static function error(int $code, string $message) {
        self::response($code, $message);
    }

    /**
     * Envía una respuesta de éxito.
     *
     * @param int $code Código de estado HTTP.
     * @param string|array $message Mensaje de éxito.
     * @param array|null $data Datos adicionales (opcional).
     * @return void
     */
    public static function success(int $code, string|array $message) {
        self::response($code, $message);
    }
}
