<?php
// Worker para SSE (Server Sent Events)
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

require_once __DIR__ . '/../../../config/config.php';
$ftpConfigFile = __DIR__ . '/../../../config/ftp_deploy.json';
$maintenanceFile = __DIR__ . '/../../../.maintenance';

// Função de Log para o Frontend
function sendLog($type, $msg)
{
    echo "data: " . json_encode(['type' => $type, 'msg' => $msg]) . "\n\n";
    ob_flush();
    flush();
}

// 1. Carregar Configurações
$configFile = __DIR__ . '/../../../config/ftp_deploy.json';
if (!file_exists($configFile)) {
    sendLog('error', 'Arquivo de configuração não encontrado. Configure primeiro.');
    exit;
}
$config = json_decode(file_get_contents($configFile), true);

$baseDir = realpath(__DIR__ . '/../../../'); // Project Root
$ftpHost = $config['host'];
$ftpUser = $config['user'];
$ftpPass = $config['pass'];
$ftpRoot = rtrim($config['path'], '/');
$excludes = array_filter(array_map('trim', explode("\n", $config['exclude'])));

// 2. Conectar FTP (COM DEBUG REFORÇADO)
sendLog('log', "Tentando conectar em: <strong>$ftpHost</strong> (Porta: " . $config['port'] . ")...");

// Teste de DNS básico
if (gethostbyname($ftpHost) === $ftpHost && !filter_var($ftpHost, FILTER_VALIDATE_IP)) {
    sendLog('log', "⚠️ Aviso: O host parece não estar resolvendo DNS. Verifique se digitou o endereço correto.");
}

$conn_id = @ftp_connect($ftpHost, $config['port'], 15); // 15s timeout
if (!$conn_id) {
    sendLog('error', "FALHA FATAL: Não foi possível conectar ao servidor FTP. <br>Possíveis causas: Host incorreto, Firewall bloqueando ou Porta errada.");
    exit;
}

sendLog('log', "Conectado! Tentando login com usuário: <strong>$ftpUser</strong>...");

if (@ftp_login($conn_id, $ftpUser, $ftpPass)) {
    sendLog('log', "Login aceito! ✅");
    ftp_pasv($conn_id, true);
    sendLog('log', "Modo Passivo ativado.");
} else {
    sendLog('error', "LOGIN RECUSADO ❌. Verifique usuário e senha.");
    ftp_close($conn_id);
    exit;
}

// Mudar para o diretório raiz remoto
if ($ftpRoot !== '' && $ftpRoot !== '/') {
    if (!@ftp_chdir($conn_id, $ftpRoot)) {
        // Tentar listar diretórios para ajudar
        $list = ftp_nlist($conn_id, ".");
        $dirs = $list ? implode(", ", array_slice($list, 0, 5)) : "Nenhum detectado (Pasta vazia ou sem permissão)";
        sendLog('error', "DIRETÓRIO RAIZ NÃO ENCONTRADO: '$ftpRoot'. <br>Pastas visíveis na raiz do login: [$dirs]");
        exit;
    } else {
        sendLog('log', "Diretório raiz '$ftpRoot' acessado com sucesso.");
    }
} else {
    sendLog('log', "Usando a raiz do usuário FTP (/).");
}

// Função Recursiva de Upload
function uploadDir($conn_id, $dir, $remoteDir, $excludes, $baseLocalDir)
{
    global $baseDir;

    // Verificar Exclusões
    $relativePath = str_replace($baseLocalDir . DIRECTORY_SEPARATOR, '', $dir);
    $relativePath = str_replace('\\', '/', $relativePath); // Normalize windows paths

    // Ignorar caminhos que começam com algo na lista de exclusão
    foreach ($excludes as $exclude) {
        if ($exclude === '')
            continue;
        if ($relativePath === $exclude || strpos($relativePath, $exclude . '/') === 0) {
            return;
        }
    }

    // Criar diretório remoto se não existir
    if ($remoteDir !== '' && $remoteDir !== '.') {
        if (!@ftp_chdir($conn_id, $remoteDir)) {
            if (ftp_mkdir($conn_id, $remoteDir)) {
                // sendLog('log', "[DIR] Criado: $remoteDir");
            }
        }
        // Voltar para raiz RELATIVA do deploy para garantir que os próximos comandos funcionem
        // Isso é complexo com recursao e chdir. Melhor estratégia: Usar caminhos sempre relativos a raiz ou absolutos se possível.
        // FTP simples não suporta 'path absoluto' facilmente se o chroot variar.
        // TRUQUE: NÃO FAZER CHDIR. Usar caminhos completos no ftp_put.
        // Minha lógica anterior usava chdir, o que quebra a recursão se não voltar.
        // CORREÇÃO: Vamos sempre voltar para a raiz do DEPLOY antes de continuar? Não, muito overhead.
        // Melhor: Usar o $remoteDir como string no path do arquivo.
    }

    // Como não demos chdir (apenas testamos), o pwd deve estar na raiz do deploy ou subpasta.
    // Vamos garantir que estamos na raiz do deploy no inicio da função? Não, muito lento.
    // Vamos assumir que a structura de pastas existe.

    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..')
            continue;

        $localFile = $dir . DIRECTORY_SEPARATOR . $file;
        $remoteFile = ($remoteDir === '.' ? '' : $remoteDir . '/') . $file;
        $relFile = ($relativePath ? $relativePath . '/' : '') . $file;

        // Verificar Exclusões de Arquivo
        $skip = false;
        foreach ($excludes as $exclude) {
            if ($exclude === '')
                continue;
            if ($relFile === $exclude || strpos($relFile, $exclude) === 0) {
                $skip = true;
                break;
            }
        }
        if ($skip)
            continue;

        if (is_dir($localFile)) {
            // Tentar criar dir remoto
            if (!@ftp_chdir($conn_id, $remoteFile)) {
                ftp_mkdir($conn_id, $remoteFile);
            }
            // Voltar para onde estava? O chdir muda o estado!
            // CUIDADO: Se eu dei chdir, preciso voltar.
            // Vamos usar apenas ftp_mkdir sem chdir para criar.
            // ftp_mkdir aceita caminhos relativos ao PWD.

            ftp_chdir($conn_id, "."); // No-op mas garante refresh? Nao.

            uploadDir($conn_id, $localFile, $remoteFile, $excludes, $baseLocalDir);
        } else {
            // Upload Arquivo
            if (ftp_put($conn_id, $remoteFile, $localFile, FTP_BINARY)) {
                sendLog('log', "✓ $relFile");
            } else {
                // Tenta criar pasta pai caso não exista (fallback)
                $parent = dirname($remoteFile);
                if ($parent != '.' && !@ftp_chdir($conn_id, $parent)) {
                    ftp_mkdir($conn_id, $parent);
                }

                // Retry
                if (@ftp_put($conn_id, $remoteFile, $localFile, FTP_BINARY)) {
                    sendLog('log', "✓ $relFile (Retry)");
                } else {
                    $err = error_get_last();
                    sendLog('log', "❌ Erro ($relFile): " . $err['message']);
                }
            }
        }
    }
}

// 3. Iniciar
sendLog('log', "Iniciando Upload...");

// A função uploadDir precisa ser ajustada para não usar chdir excessivamente ou gerenciar estado.
// Simplificação: Vamos fazer uma iteração linear com RecursiveIteratorIterator que é mais robusto para upload.
// Substituindo lógica recursiva manual por Iterator do PHP (mais limpo e sem problemas de chdir)

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    // Caminho Relativo
    $localPath = $file->getRealPath();
    $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $localPath);
    $relativePath = str_replace('\\', '/', $relativePath);

    // Filtros de Exclusão
    $skip = false;
    foreach ($excludes as $exclude) {
        if ($exclude === '')
            continue;
        // Match exato ou pasta pai
        if ($relativePath === $exclude || strpos($relativePath, $exclude . '/') === 0 || strpos($relativePath, '/' . $exclude . '/') !== false) {
            $skip = true;
            break;
        }
    }
    if ($skip)
        continue;

    if ($file->isDir()) {
        // Criar diretório
        if (!@ftp_chdir($conn_id, $relativePath)) {
            ftp_mkdir($conn_id, $relativePath);
            ftp_chdir($conn_id, "/"); // Reset para raiz (absoluta? nao sabemos root real)
            ftp_chdir($conn_id, $ftpRoot); // Reset para raiz do deploy
        } else {
            ftp_chdir($conn_id, $ftpRoot); // Volta
        }
    } else {
        // Arquivo

        // TRATAMENTO INTELIGENTE DO .htaccess
        if (basename($localPath) === '.htaccess') {
            sendLog('log', "⚙️ Ajustando .htaccess para Produção (RewriteBase /)...");
            $content = file_get_contents($localPath);
            // Substitui /cinco/ por /
            $prodContent = str_replace('RewriteBase /cinco/', 'RewriteBase /', $content);

            // Cria temp file
            $tempFile = stream_get_meta_data(tmpfile())['uri'];
            file_put_contents($tempFile, $prodContent);

            if (ftp_put($conn_id, $relativePath, $tempFile, FTP_BINARY)) {
                sendLog('log', "✓ $relativePath (Ajustado)");
                unlink($tempFile); // Limpa temp
                continue; // Pula upload normal
            } else {
                sendLog('log', "❌ Falha ao enviar .htaccess ajustado.");
                unlink($tempFile);
            }
        }

        // Upload Normal
        if (ftp_put($conn_id, $relativePath, $localPath, FTP_BINARY)) {
            sendLog('log', "✓ $relativePath");
        } else {
            // Tenta criar pasta pai
            $dir = dirname($relativePath);
            if ($dir != '.') {
                // Tenta criar recursivamente se falhar?
                // Simplesmente tenta criar o pai direto
                @ftp_mkdir($conn_id, $dir);
            }
            if (!@ftp_put($conn_id, $relativePath, $localPath, FTP_BINARY)) {
                sendLog('log', "❌ Falha: $relativePath");
            } else {
                sendLog('log', "✓ $relativePath (Retry)");
            }
        }
    }
}

ftp_close($conn_id);
sendLog('success', "Deploy Concluído!");
?>