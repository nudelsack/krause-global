<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class ExtractionService
{
    private $config;
    private $pdftotextAvailable;

    public function __construct($config)
    {
        $this->config = $config;
        $this->checkPdftotextAvailability();
    }

    private function checkPdftotextAvailability()
    {
        $path = $this->config['pdf']['pdftotext_path'];
        $this->pdftotextAvailable = file_exists($path) && is_executable($path);
    }

    public function extractFromPdf($filePath)
    {
        $method = $this->config['pdf']['extraction_method'];
        
        if ($method === 'auto') {
            if ($this->pdftotextAvailable) {
                return $this->extractWithPdftotext($filePath);
            } else {
                return $this->extractWithParser($filePath);
            }
        } elseif ($method === 'pdftotext') {
            return $this->extractWithPdftotext($filePath);
        } else {
            return $this->extractWithParser($filePath);
        }
    }

    private function extractWithPdftotext($filePath)
    {
        $pdftotext = $this->config['pdf']['pdftotext_path'];
        $output = [];
        $returnVar = 0;
        
        exec(escapeshellcmd($pdftotext) . ' ' . escapeshellarg($filePath) . ' -', $output, $returnVar);
        
        if ($returnVar === 0) {
            return [
                'text' => implode("\n", $output),
                'method' => 'text_layer',
                'success' => true,
            ];
        }
        
        // Fallback to parser if pdftotext fails
        return $this->extractWithParser($filePath);
    }

    private function extractWithParser($filePath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            
            return [
                'text' => $text,
                'method' => 'pdfparser',
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'text' => null,
                'method' => 'pdfparser',
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function extractFieldsFromLOI($text)
    {
        $fields = [];
        
        // Extract expiry/validity date
        $expiryDate = $this->extractExpiryDate($text);
        if ($expiryDate) {
            $fields['expiry_date'] = $expiryDate;
        }
        
        // Extract date
        if (preg_match('/date[:\s]+(\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4})/i', $text, $matches)) {
            $fields['date'] = $matches[1];
        }
        
        // Extract reference
        if (preg_match('/ref(?:erence)?[:\s#]+([A-Z0-9\-]+)/i', $text, $matches)) {
            $fields['reference'] = $matches[1];
        }
        
        // Extract buyer
        if (preg_match('/buyer[:\s]+([^\n]+)/i', $text, $matches)) {
            $fields['buyer'] = trim($matches[1]);
        }
        
        // Extract seller
        if (preg_match('/seller[:\s]+([^\n]+)/i', $text, $matches)) {
            $fields['seller'] = trim($matches[1]);
        }
        
        // Extract product
        if (preg_match('/product[:\s]+([^\n]+)/i', $text, $matches)) {
            $fields['product'] = trim($matches[1]);
        }
        
        // Extract quantity
        if (preg_match('/quantity[:\s]+([0-9,\.]+)\s*([a-z]+)?/i', $text, $matches)) {
            $fields['quantity'] = str_replace(',', '', $matches[1]);
            $fields['quantity_unit'] = $matches[2] ?? '';
        }
        
        // Extract price
        if (preg_match('/price[:\s]+(?:USD|EUR|GBP)?\s*([0-9,\.]+)/i', $text, $matches)) {
            $fields['price'] = str_replace(',', '', $matches[1]);
        }
        
        return $fields;
    }
    
    /**
     * Extract expiry/validity date from LOI text
     * Supports multiple formats:
     * - Valid until: DD.MM.YYYY / DD/MM/YYYY / YYYY-MM-DD
     * - Expiry date: January 31, 2026
     * - This LOI expires on: 31.01.2026
     * - Gültig bis: DD.MM.YYYY
     */
    public function extractExpiryDate($text)
    {
        // Pattern 1: "valid until", "expiry date", "expires on", "gültig bis"
        $patterns = [
            '/(?:valid\s+until|expiry\s+date|expires\s+on|g[üu]ltig\s+bis)[:\s]+(\d{1,2})[\.\/\-](\d{1,2})[\.\/\-](\d{2,4})/i',
            '/(?:valid\s+until|expiry\s+date|expires\s+on|g[üu]ltig\s+bis)[:\s]+(\d{4})[\.\/\-](\d{1,2})[\.\/\-](\d{1,2})/i',
            '/(?:valid\s+until|expiry\s+date|expires\s+on)[:\s]+(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{1,2}),?\s+(\d{4})/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                // Handle different date formats
                if (isset($matches[3])) {
                    // Check if it's month name format
                    if (preg_match('/^[A-Za-z]+$/', $matches[1])) {
                        $monthMap = [
                            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
                            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
                            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
                        ];
                        $month = $monthMap[strtolower($matches[1])];
                        $day = $matches[2];
                        $year = $matches[3];
                        
                        return sprintf('%04d-%02d-%02d', $year, $month, $day);
                    }
                    
                    // DD.MM.YYYY or YYYY-MM-DD
                    if (strlen($matches[1]) == 4) {
                        // YYYY-MM-DD format
                        return sprintf('%04d-%02d-%02d', $matches[1], $matches[2], $matches[3]);
                    } else {
                        // DD.MM.YYYY format
                        $day = $matches[1];
                        $month = $matches[2];
                        $year = strlen($matches[3]) == 2 ? '20' . $matches[3] : $matches[3];
                        
                        return sprintf('%04d-%02d-%02d', $year, $month, $day);
                    }
                }
            }
        }
        
        return null;
    }
    
    private function extractCurrency($text)
    {
        // Extract currency
        if (preg_match('/(USD|EUR|GBP|CHF)/i', $text, $matches)) {
            $fields['currency'] = strtoupper($matches[1]);
        }
        
        // Extract incoterms
        if (preg_match('/(FOB|CIF|CFR|EXW|FCA|DAP|DDP)/i', $text, $matches)) {
            $fields['incoterms'] = strtoupper($matches[1]);
        }
        
        // Extract payment terms
        if (preg_match('/payment[:\s]+([^\n]+)/i', $text, $matches)) {
            $fields['payment_terms'] = trim($matches[1]);
        }
        
        return $fields;
    }

    public function canExtract($mimeType)
    {
        return $mimeType === 'application/pdf';
    }
}
