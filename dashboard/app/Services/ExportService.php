<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Document;
use ZipArchive;

class ExportService
{
    private $config;
    private $dealModel;
    private $documentModel;

    public function __construct($config, Deal $dealModel, Document $documentModel)
    {
        $this->config = $config;
        $this->dealModel = $dealModel;
        $this->documentModel = $documentModel;
    }

    public function exportDealDossier($dealId)
    {
        $deal = $this->dealModel->find($dealId);
        if (!$deal) {
            throw new \RuntimeException('Deal not found');
        }

        $exportPath = $this->getAbsolutePath($this->config['paths']['exports']);
        if (!is_dir($exportPath)) {
            mkdir($exportPath, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $zipFilename = "{$deal['deal_code']}_{$timestamp}_dossier.zip";
        $zipPath = $exportPath . '/' . $zipFilename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Cannot create ZIP file');
        }

        // Add documents
        $documents = $this->documentModel->getByDeal($dealId);
        $documentList = [];

        foreach ($documents as $doc) {
            $latestVersion = $this->documentModel->getLatestVersion($doc['id']);
            if ($latestVersion) {
                $sourcePath = $this->getAbsolutePath($latestVersion['stored_path']);
                if (file_exists($sourcePath)) {
                    $zipPath = 'documents/' . $doc['category'] . '/' . $latestVersion['original_filename'];
                    $zip->addFile($sourcePath, $zipPath);
                    
                    $documentList[] = [
                        'category' => $doc['category'],
                        'title' => $doc['title'],
                        'filename' => $latestVersion['original_filename'],
                        'uploaded_at' => $latestVersion['uploaded_at'],
                        'sha256' => $latestVersion['sha256'],
                    ];
                }
            }
        }

        // Add deal data as JSON
        $parties = $this->dealModel->getParties($dealId);
        $dealData = [
            'deal' => $deal,
            'parties' => $parties,
            'documents' => $documentList,
            'exported_at' => date('Y-m-d H:i:s'),
        ];
        
        $zip->addFromString('deal_data.json', json_encode($dealData, JSON_PRETTY_PRINT));

        // Add HTML index
        $htmlIndex = $this->generateHtmlIndex($deal, $parties, $documentList);
        $zip->addFromString('index.html', $htmlIndex);

        $zip->close();

        return $zipFilename;
    }

    private function generateHtmlIndex($deal, $parties, $documents)
    {
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deal Dossier: ' . htmlspecialchars($deal['deal_code']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1 { color: #2C1810; border-bottom: 3px solid #D4AF37; padding-bottom: 10px; }
        h2 { color: #8B6F47; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 4px; background: #e8f4f8; }
        .meta { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <h1>Deal Dossier</h1>
    
    <h2>Deal Information</h2>
    <table>
        <tr><th>Deal Code</th><td>' . htmlspecialchars($deal['deal_code']) . '</td></tr>
        <tr><th>Title</th><td>' . htmlspecialchars($deal['title']) . '</td></tr>
        <tr><th>Type</th><td>' . htmlspecialchars($deal['deal_type']) . '</td></tr>
        <tr><th>Subtype</th><td>' . htmlspecialchars($deal['deal_subtype'] ?? '-') . '</td></tr>
        <tr><th>Status</th><td><span class="status">' . htmlspecialchars($deal['status']) . '</span></td></tr>
        <tr><th>Quantity</th><td>' . htmlspecialchars($deal['quantity'] ?? '-') . ' ' . htmlspecialchars($deal['quantity_unit'] ?? '') . '</td></tr>
        <tr><th>Price</th><td>' . htmlspecialchars($deal['price'] ?? '-') . ' ' . htmlspecialchars($deal['currency'] ?? '') . '</td></tr>
        <tr><th>Incoterms</th><td>' . htmlspecialchars($deal['incoterms'] ?? '-') . '</td></tr>
    </table>
    
    <h2>Parties</h2>
    <table>
        <tr><th>Role</th><th>Company</th><th>Country</th></tr>';
        
        foreach ($parties as $party) {
            $html .= '<tr>
                <td>' . htmlspecialchars($party['role_in_deal']) . '</td>
                <td>' . htmlspecialchars($party['company_name']) . '</td>
                <td>' . htmlspecialchars($party['country'] ?? '-') . '</td>
            </tr>';
        }
        
        $html .= '</table>
    
    <h2>Documents</h2>
    <table>
        <tr><th>Category</th><th>Filename</th><th>Uploaded</th><th>SHA256</th></tr>';
        
        foreach ($documents as $doc) {
            $html .= '<tr>
                <td>' . htmlspecialchars($doc['category']) . '</td>
                <td><a href="documents/' . htmlspecialchars($doc['category']) . '/' . htmlspecialchars($doc['filename']) . '">' . htmlspecialchars($doc['filename']) . '</a></td>
                <td class="meta">' . htmlspecialchars($doc['uploaded_at']) . '</td>
                <td class="meta" style="font-family: monospace; font-size: 0.8em;">' . substr(htmlspecialchars($doc['sha256']), 0, 16) . '...</td>
            </tr>';
        }
        
        $html .= '</table>
    
    <p class="meta" style="margin-top: 40px;">Exported: ' . date('Y-m-d H:i:s') . ' | Krause Global Resources & Trade</p>
</body>
</html>';

        return $html;
    }

    private function getAbsolutePath($path)
    {
        if ($path[0] === '/') {
            return $path;
        }
        return __DIR__ . '/../../' . $path;
    }
}
