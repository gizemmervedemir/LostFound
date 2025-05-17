<?php

namespace App\Models;

class QRCode
{
    private $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Generate QR code for an item
     */
    public function generate($itemId)
    {
        $item = $this->item->getById($itemId);
        if (!$item) {
            throw new \Exception('Item not found');
        }

        $data = [
            'type' => $item['type'],
            'title' => $item['title'],
            'id' => $item['id'],
            'timestamp' => time()
        ];

        $qrData = base64_encode(json_encode($data));
        $filename = 'qr_' . $item['id'] . '_' . time() . '.png';
        $filepath = public_path('uploads/qr/' . $filename);

        // Create QR code directory if not exists
        if (!file_exists(public_path('uploads/qr'))) {
            mkdir(public_path('uploads/qr'), 0755, true);
        }

        // Generate QR code using PHP QR Code library
        require_once __DIR__ . '/../Libraries/phpqrcode/qrlib.php';
        \QRcode::png(
            $qrData,
            $filepath,
            'L',
            10,
            2
        );

        // Add custom styling
        $this->addCustomStyling($filepath);

        return $filename;
    }

    /**
     * Add custom styling to QR code
     */
    private function addCustomStyling($filepath)
    {
        $im = imagecreatefrompng($filepath);
        
        // Add logo
        $logo = imagecreatefrompng(__DIR__ . '/../Images/logo.png');
        $qrWidth = imagesx($im);
        $qrHeight = imagesy($im);
        $logoWidth = imagesx($logo);
        $logoHeight = imagesy($logo);
        
        // Calculate positions
        $logoPos = [
            'x' => ($qrWidth - $logoWidth) / 2,
            'y' => ($qrHeight - $logoHeight) / 2
        ];
        
        // Add logo
        imagecopy($im, $logo, $logoPos['x'], $logoPos['y'], 0, 0, $logoWidth, $logoHeight);
        
        // Add text
        $font = __DIR__ . '/../Fonts/arial.ttf';
        $text = 'Lost & Found';
        $textSize = 20;
        $textColor = imagecolorallocate($im, 0, 0, 0);
        
        // Calculate text position
        $textBox = imagettfbbox($textSize, 0, $font, $text);
        $textWidth = $textBox[2] - $textBox[0];
        $textHeight = $textBox[1] - $textBox[7];
        $textPos = [
            'x' => ($qrWidth - $textWidth) / 2,
            'y' => $qrHeight - $textHeight - 20
        ];
        
        // Add text
        imagettftext($im, $textSize, 0, $textPos['x'], $textPos['y'], $textColor, $font, $text);
        
        // Save modified image
        imagepng($im, $filepath);
        
        // Clean up
        imagedestroy($im);
        imagedestroy($logo);
    }

    /**
     * Validate QR code data
     */
    public function validate($qrData)
    {
        try {
            $data = json_decode(base64_decode($qrData), true);
            if (!isset($data['id']) || !isset($data['type']) || !isset($data['title'])) {
                return false;
            }
            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }
}
