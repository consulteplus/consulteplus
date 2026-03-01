<?php

class AsaasService
{
    private $apiKey;
    private $apiUrl;

    public function __construct()
    {
        $this->apiKey = ASAAS_API_KEY;
        $this->apiUrl = ASAAS_API_URL;
    }

    /**
     * Helper para fazer requisições CURL
     */
    private function request($method, $endpoint, $data = [])
    {
        $curl = curl_init();

        $url = $this->apiUrl . $endpoint;
        $headers = [
            "Content-Type: application/json",
            "access_token: " . $this->apiKey,
            "User-Agent: ConsultePlus/1.0"
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
            CURLOPT_SSL_VERIFYPEER => false, // Fix for Local XAMPP
            CURLOPT_SSL_VERIFYHOST => 0,
        ];

        if (!empty($data) && ($method == 'POST' || $method == 'PUT')) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        if ($method == 'GET' && !empty($data)) {
            $url = $url . '?' . http_build_query($data);
            $options[CURLOPT_URL] = $url;
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            return ['success' => false, 'error' => "cURL Error: " . $err];
        }

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => "Falha ao decodificar JSON. Resposta Bruta: " . $response,
                'raw' => $response
            ];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'data' => $decoded];
        } else {
            // Improve Error Reporting
            $errorMsg = $decoded['errors'][0]['description'] ?? 'Erro desconhecido. Resposta: ' . json_encode($decoded);
            return [
                'success' => false,
                'error' => $errorMsg,
                'raw' => $decoded
            ];
        }
    }

    /**
     * 1. Criar ou Atualizar Cliente
     */
    public function createCustomer($name, $cpfCnpj, $email)
    {
        // Primeiro, tenta buscar se já existe pelo CPF/CNPJ
        // Isso evita duplicidade no Asaas se o nosso banco local perdeu o ID
        $search = $this->request('GET', '/customers', ['cpfCnpj' => $cpfCnpj]);

        if ($search['success'] && !empty($search['data']['data'])) {
            return ['success' => true, 'id' => $search['data']['data'][0]['id']];
        }

        // Se não existe, cria
        $payload = [
            'name' => $name,
            'cpfCnpj' => $cpfCnpj,
            'email' => $email
        ];

        $result = $this->request('POST', '/customers', $payload);

        if ($result['success']) {
            return ['success' => true, 'id' => $result['data']['id']];
        }

        return $result;
    }

    /**
     * 1.5 Atualizar Cliente
     */
    /**
     * 1.5 Atualizar Cliente
     */
    public function updateCustomer($asaasId, $name, $cpfCnpj, $email)
    {
        $payload = [
            'name' => $name,
            'cpfCnpj' => $cpfCnpj,
            'email' => $email
        ];

        return $this->request('POST', '/customers/' . $asaasId, $payload);
    }

    /**
     * 1.7 Criar Cobrança Única (One-off)
     */
    public function createPayment($customerId, $value, $dueDate, $description, $billingType = 'UNDEFINED')
    {
        $payload = [
            'customer' => $customerId,
            'billingType' => $billingType, // UNDEFINED permite que o cliente escolha PIX/BOLETO/CARTAO
            'value' => number_format($value, 2, '.', ''),
            'dueDate' => $dueDate,
            'description' => $description
        ];

        return $this->request('POST', '/payments', $payload);
    }

    /**
     * 2. Criar Assinatura
     */
    public function createSubscription($customerId, $value, $cycle, $nextDueDate, $description, $billingType = 'BOLETO')
    {
        $payload = [
            'customer' => $customerId,
            'billingType' => $billingType,
            'value' => number_format($value, 2, '.', ''),
            'nextDueDate' => $nextDueDate,
            'cycle' => $cycle, // MONTHLY, QUARTERLY, SEMIANNUALLY, YEARLY
            'description' => $description
        ];

        $result = $this->request('POST', '/subscriptions', $payload);

        return $result;
    }

    /**
     * 3. Buscar Assinatura por ID
     */
    /**
     * 3. Buscar Assinatura por ID
     */
    public function getSubscription($subscriptionId)
    {
        return $this->request('GET', '/subscriptions/' . $subscriptionId);
    }

    /**
     * 4. Listar Pagamentos Pendentes
     */
    public function listPendingPayments($customerId)
    {
        $params = [
            'customer' => $customerId,
            'status' => 'PENDING,OVERDUE', // Pendente ou Vencido
            'limit' => 20
        ];
        return $this->request('GET', '/payments', $params);
    }

    /**
     * 5. Obter QR Code Pix
     */
    public function getPixQrCode($paymentId)
    {
        return $this->request('GET', '/payments/' . $paymentId . '/pixQrCode');
    }

    /**
     * 6. Obter Linha Digitável Boleto
     */
    public function getBoletoCode($paymentId)
    {
        return $this->request('GET', '/payments/' . $paymentId . '/identificationField');
    }

    /**
     * 7. Pagar com Cartão de Crédito
     */
    public function payWithCreditCard($paymentId, $cardData)
    {
        // $cardData deve conter: creditCard (holderName, number, expiryMonth, expiryYear, ccv) e creditCardHolderInfo
        return $this->request('POST', '/payments/' . $paymentId . '/payWithCreditCard', $cardData);
    }
}
