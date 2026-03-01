<?php
// modules/deploy/processar.php
session_start();
set_time_limit(600); // 10 minutos
ini_set('memory_limit', '512M');

require_once __DIR__ . '/../../config/config.php';

// Validar Permissão
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    die("Acesso Negado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Receber Dados
$ftp_host = $_POST['ftp_host'];
$ftp_user = $_POST['ftp_user'];
$ftp_pass = $_POST['ftp_pass'];
$ftp_port = (int) ($_POST['ftp_port'] ?? 21);
$ftp_path = rtrim($_POST['ftp_path'], '/') . '/'; // Garante barra final
$prod_url = isset($_POST['prod_url']) && !empty($_POST['prod_url']) ? $_POST['prod_url'] : "https://app.consulteplus.com.br/"; // Default fallback

// 1. CRIAR ZIP DO PROJETO LOCAL
$rootDir = realpath(__DIR__ . '/../../');
$zipFile = __DIR__ . '/deploy_package.zip';
$zip = new ZipArchive();

if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    header("Location: index.php?status=error&msg=Erro ao criar arquivo ZIP local.");
    exit;
}

// Função Recursiva para adicionar arquivos
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

// Pastas/Arquivos a Ignorar
$ignored = ['.git', '.vscode', 'node_modules', 'deploy_package.zip', 'unzipper.php', 'teste_banco.php', 'ver_erros.php', '.env'];

foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootDir) + 1);
        $relativePath = str_replace('\\', '/', $relativePath); // Windows Fix

        // Checar ignorados
        $skip = false;
        foreach ($ignored as $ignore) {
            if (strpos($relativePath, $ignore) === 0 || strpos($filePath, $ignore) !== false) {
                $skip = true;
                break;
            }
        }

        // Ignorar o próprio módulo de deploy? Não, pode ser útil.
        // Ignorar configs locais se pedido?
        // if (isset($_POST['ignore_config']) && ($relativePath == 'config/database.php' || $relativePath == 'config/config.php')) {
        //     $skip = true;
        // }
        // DECISÃO: Como fizemos configs "Híbridas" (Env Aware), é seguro enviar! não vou ignorar.

        if (!$skip) {
            // TRUQUE DO MESTRE: Alterar .htaccess em voo para Produção
            if ($relativePath === '.htaccess') {
                $htContent = file_get_contents($filePath);
                // Troca /consulteplus/ por /
                $htContent = str_replace('RewriteBase /consulteplus/', 'RewriteBase /', $htContent);
                $zip->addFromString($relativePath, $htContent);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
$zip->close();

if (!file_exists($zipFile)) {
    header("Location: index.php?status=error&msg=ZIP não foi gerado.");
    exit;
}

// 2. CRIAR SCRIPT UNZIPPER (Para rodar no servidor)
$unzipperContent = '<?php
// Extrator Automático
ini_set("display_errors", 1);
$zip = new ZipArchive;
$res = $zip->open("deploy_package.zip");
if ($res === TRUE) {
  $zip->extractTo(__DIR__);
  $zip->close();
  echo "SUCESSO: Arquivos extraídos!";
  // Auto-destruição
  unlink("deploy_package.zip");
  unlink(__FILE__);
} else {
  echo "ERRO: Falha ao abrir ZIP. Código: " . $res;
}
?>';
$unzipperFile = __DIR__ . '/unzipper.php';
file_put_contents($unzipperFile, $unzipperContent);


// 3. UPLOAD VIA FTP
$conn_id = ftp_connect($ftp_host, $ftp_port);
if (!$conn_id) {
    unlink($zipFile);
    unlink($unzipperFile);
    header("Location: index.php?status=error&msg=Falha ao conectar no FTP.");
    exit;
}

if (!ftp_login($conn_id, $ftp_user, $ftp_pass)) {
    ftp_close($conn_id);
    unlink($zipFile);
    unlink($unzipperFile);
    header("Location: index.php?status=error&msg=Login FTP incorreto.");
    exit;
}

ftp_pasv($conn_id, true); // Modo Passivo obrigatório

// Upload ZIP
if (!ftp_put($conn_id, $ftp_path . 'deploy_package.zip', $zipFile, FTP_BINARY)) {
    $err = error_get_last();
    ftp_close($conn_id);
    unlink($zipFile);
    unlink($unzipperFile);
    header("Location: index.php?status=error&msg=Erro upload ZIP: " . $err['message']);
    exit;
}

// Upload Unzipper
if (!ftp_put($conn_id, $ftp_path . 'unzipper.php', $unzipperFile, FTP_ASCII)) {
    ftp_close($conn_id);
    unlink($zipFile);
    unlink($unzipperFile);
    header("Location: index.php?status=error&msg=Erro upload Unzipper.");
    exit;
}

ftp_close($conn_id);

// 4. DISPARAR EXTRAÇÃO (TRIGGER REMOTO)
// Tenta chamar via HTTP Curl
$targetUrl = $prod_url . 'unzipper.php';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Evitar erro SSL local
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Limpeza Local
unlink($zipFile);
unlink($unzipperFile);

if (strpos($response, 'SUCESSO') !== false) {
    header("Location: index.php?status=success");
} else {
    // Se falhar o trigger, avisa para rodar manual
    $link = $targetUrl;
    // Limpar resposta para evitar erro de Header
    $safeResp = preg_replace('/\s+/', ' ', substr($response, 0, 100));
    header("Location: index.php?status=error&msg=Arquivos subidos. Extração manual necessária: $link | Resp: " . urlencode($safeResp));
}
exit;
?>