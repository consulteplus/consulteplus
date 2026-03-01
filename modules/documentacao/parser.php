<?php
/**
 * DocsParser - Classe para processar arquivos DOCS.md
 * Converte Markdown para HTML e extrai metadados
 */
class DocsParser
{
    private $parsedown;

    public function __construct()
    {
        require_once __DIR__ . '/Parsedown.php';
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(true); // Previne XSS
    }

    /**
     * Lê e processa um arquivo DOCS.md
     * @param string $filePath Caminho absoluto para o arquivo
     * @return array ['html' => string, 'toc' => array, 'metadata' => array]
     */
    public function parse($filePath)
    {
        if (!file_exists($filePath)) {
            return [
                'html' => '<div class="alert alert-warning">Documentação não encontrada.</div>',
                'toc' => [],
                'metadata' => []
            ];
        }

        $markdown = file_get_contents($filePath);

        // Extrair metadados do cabeçalho
        $metadata = $this->extractMetadata($markdown);

        // Gerar TOC (Table of Contents)
        $toc = $this->generateTOC($markdown);

        // Converter Markdown para HTML
        $html = $this->parsedown->text($markdown);

        // Processar blocos Mermaid
        $html = $this->processMermaid($html);

        // Adicionar IDs aos headers para navegação
        $html = $this->addHeaderIDs($html);

        return [
            'html' => $html,
            'toc' => $toc,
            'metadata' => $metadata
        ];
    }

    /**
     * Extrai metadados do início do documento
     */
    private function extractMetadata($markdown)
    {
        $metadata = [
            'title' => 'Documentação',
            'version' => '1.0',
            'updated' => date('d/m/Y')
        ];

        // Procura por padrões como **Versão**: 2.0
        if (preg_match('/\*\*Versão\*\*:\s*(.+)/i', $markdown, $matches)) {
            $metadata['version'] = trim($matches[1]);
        }

        if (preg_match('/\*\*Última Atualização\*\*:\s*(.+)/i', $markdown, $matches)) {
            $metadata['updated'] = trim($matches[1]);
        }

        // Primeiro H1 é o título
        if (preg_match('/^#\s+(.+)$/m', $markdown, $matches)) {
            $metadata['title'] = trim($matches[1]);
        }

        return $metadata;
    }

    /**
     * Gera índice (TOC) baseado nos headers
     */
    private function generateTOC($markdown)
    {
        $toc = [];
        $lines = explode("\n", $markdown);

        foreach ($lines as $line) {
            // Detecta headers (## até ####)
            if (preg_match('/^(#{2,4})\s+(.+)$/', $line, $matches)) {
                $level = strlen($matches[1]); // 2, 3 ou 4
                $title = trim($matches[2]);
                $id = $this->slugify($title);

                $toc[] = [
                    'level' => $level,
                    'title' => $title,
                    'id' => $id
                ];
            }
        }

        return $toc;
    }

    /**
     * Processa blocos Mermaid para renderização
     */
    private function processMermaid($html)
    {
        // Substitui <code class="language-mermaid"> por <div class="mermaid">
        $html = preg_replace(
            '/<code class="language-mermaid">(.*?)<\/code>/s',
            '<div class="mermaid">$1</div>',
            $html
        );

        return $html;
    }

    /**
     * Adiciona IDs aos headers para navegação por âncora
     */
    private function addHeaderIDs($html)
    {
        // Adiciona IDs aos headers H2, H3, H4
        $html = preg_replace_callback(
            '/<h([234])>(.+?)<\/h\1>/i',
            function ($matches) {
                $level = $matches[1];
                $text = strip_tags($matches[2]);
                $id = $this->slugify($text);
                return "<h{$level} id=\"{$id}\">{$matches[2]}</h{$level}>";
            },
            $html
        );

        return $html;
    }

    /**
     * Converte texto para slug (URL-friendly)
     */
    private function slugify($text)
    {
        // Remove caracteres especiais
        $text = preg_replace('/[^a-z0-9\s-]/i', '', $text);
        // Substitui espaços por hífens
        $text = preg_replace('/\s+/', '-', trim($text));
        // Lowercase
        return strtolower($text);
    }
}
