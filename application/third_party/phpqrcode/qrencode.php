<?php
class QRcode {
    /**
     * png: genera una representación simple de «QR» (placeholder).
     * Si GD está disponible, crea una imagen PNG con el texto. Si no, crea un SVG.
     * Si se proporciona $outfile y termina en .png pero GD no existe, también dejará
     * una copia .svg al lado para evitar perder la información.
     *
     * Nota: esta implementación es un fallback minimalista. Para QR reales use
     * la librería oficial o endroid/qr-code.
     */
    public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        // Si GD está disponible usamos GD para crear PNG simple
        if (function_exists('imagecreatetruecolor') && function_exists('imagepng')) {
            $imgw = max(200, 10 * mb_strlen($text));
            $imgh = 200;
            $im = imagecreatetruecolor($imgw, $imgh);
            $white = imagecolorallocate($im,255,255,255);
            $black = imagecolorallocate($im,0,0,0);
            imagefilledrectangle($im,0,0,$imgw,$imgh,$white);
            // Texto simple
            imagestring($im, 5, 10, 10, $text, $black);
            ob_start();
            imagepng($im);
            $data = ob_get_clean();
            imagedestroy($im);
            if ($outfile) {
                file_put_contents($outfile, $data);
                return true;
            }
            header('Content-Type: image/png');
            echo $data;
            return true;
        }

        // Fallback: si GD no está disponible generamos un simple SVG con el texto
        $escaped = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $width = max(200, 10 * mb_strlen($text));
        $height = 200;
        $svg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $svg .= "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$width}\" height=\"{$height}\">\n";
        $svg .= "  <rect width=\"100%\" height=\"100%\" fill=\"#ffffff\"/>\n";
        $svg .= "  <text x=\"10\" y=\"50\" font-family=\"Arial, Helvetica, sans-serif\" font-size=\"16\" fill=\"#000\">{$escaped}</text>\n";
        $svg .= "</svg>\n";

        if ($outfile) {
            file_put_contents($outfile, $svg);
            // Si el usuario pidió .png pero no hay GD, también dejamos un .svg con el mismo nombre
            $pathinfo = pathinfo($outfile);
            if (!empty($pathinfo['extension']) && strtolower($pathinfo['extension']) === 'png') {
                $svgfile = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . '.svg';
                // Sólo escribir si no existe o es diferente
                file_put_contents($svgfile, $svg);
            }
            return true;
        }
        header('Content-Type: image/svg+xml');
        echo $svg;
        return true;
    }
}
