<?php
// classes/SupabaseClient.php

class SupabaseClient
{
    private $url;
    private $key;

    public function __construct($url, $key)
    {
        $this->url = rtrim($url, '/');
        $this->key = $key;
    }

    /**
     * Realiza uma requisição GET para o Supabase (SELECT)
     * 
     * @param string $table Nome da tabela
     * @param array $params Query params (select, order, filters e.g. ['select' => '*', 'id' => 'eq.1'])
     * @return array
     */
    public function get($table, $params = [])
    {
        $queryString = http_build_query($params);
        $endpoint = "{$this->url}/rest/v1/{$table}?{$queryString}";

        return $this->request('GET', $endpoint);
    }

    /**
     * Realiza uma requisição POST para o Supabase (INSERT)
     */
    public function post($table, $data)
    {
        $endpoint = "{$this->url}/rest/v1/{$table}";
        return $this->request('POST', $endpoint, $data);
    }

    /**
     * Realiza uma requisição PATCH para o Supabase (UPDATE)
     * @param string $table
     * @param array $data Dados a atualizar
     * @param array $filters Filtros (ex: ['id' => 'eq.1'])
     */
    public function update($table, $data, $filters = [])
    {
        $queryString = http_build_query($filters);
        $endpoint = "{$this->url}/rest/v1/{$table}?{$queryString}";
        return $this->request('PATCH', $endpoint, $data);
    }

    private function request($method, $url, $data = null)
    {
        $curl = curl_init();

        $headers = [
            "apikey: {$this->key}",
            "Authorization: Bearer {$this->key}",
            "Content-Type: application/json",
            "Prefer: return=representation" // Para retornar os dados inseridos/atualizados
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false, // Importante para XAMPP local
            CURLOPT_SSL_VERIFYHOST => false, // Importante para XAMPP local
        ];

        if ($data) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            throw new Exception("cURL Error #: " . $err);
        }

        $decodedResponse = json_decode($response, true);

        if ($httpCode >= 400) {
            $msg = isset($decodedResponse['message']) ? $decodedResponse['message'] : 'Erro desconhecido no Supabase';
            throw new Exception("Supabase Error ({$httpCode}): {$msg}");
        }

        return $decodedResponse;
    }
}
