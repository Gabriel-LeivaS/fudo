<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Wrapper simple para la librería phpqrcode (sin Composer).
 * Requiere que los ficheros de phpqrcode estén en:
 * application/third_party/phpqrcode/qrlib.php
 */
class Ciqrcode {

        public function __construct() {}

        /**
         * Genera un QR y lo guarda en disco.
         * $params:
         *  - data: string (requerido)
         *  - savename: absolute path (requerido)
         *  - level: 'L'|'M'|'Q'|'H' (opcional)
         *  - size: int (opcional)
         *  - format: 'png'|'svg' (opcional)
         */
        public function generate($params = []) {
                if (empty($params['data']) || empty($params['savename'])) {
                        throw new InvalidArgumentException('data y savename son requeridos');
                }

                $level = isset($params['level']) ? strtoupper($params['level']) : 'H';
                $size  = isset($params['size']) ? (int)$params['size'] : 300; // tamaño en píxeles para endroid
                $format = isset($params['format']) ? strtolower($params['format']) : null;

                $outfile = $params['savename'];

                // Asegurar carpeta destino
                $dir = dirname($outfile);
                if (!is_dir($dir)) {
                        if (!mkdir($dir, 0755, true)) {
                                throw new RuntimeException("No se pudo crear el directorio: $dir");
                        }
                }

                // Intentar usar endroid/qr-code si está instalado via Composer
                $autoload = FCPATH . 'vendor/autoload.php';
                if (file_exists($autoload)) {
                        require_once $autoload;
                        try {
                                // Preferir Builder API de Endroid (v4+)
                                if (class_exists('\Endroid\QrCode\Builder\Builder')) {
                                        // mapear tamaño: si el usuario pasó un tamaño pequeño (p.ej. 8) lo convertimos a píxeles
                                        $sizePx = $size;
                                        if ($sizePx > 0 && $sizePx < 50) {
                                                $sizePx = max(100, $sizePx * 40);
                                        }

                                        // mapear nivel a objeto si existe
                                        switch ($level) {
                                                case 'L': $errObj = '\Endroid\\QrCode\\ErrorCorrectionLevel\\ErrorCorrectionLevelLow'; break;
                                                case 'M': $errObj = '\Endroid\\QrCode\\ErrorCorrectionLevel\\ErrorCorrectionLevelMedium'; break;
                                                case 'Q': $errObj = '\Endroid\\QrCode\\ErrorCorrectionLevel\\ErrorCorrectionLevelQuartile'; break;
                                                case 'H':
                                                default: $errObj = '\Endroid\\QrCode\\ErrorCorrectionLevel\\ErrorCorrectionLevelHigh'; break;
                                        }

                                        $builder = \Endroid\QrCode\Builder\Builder::create()
                                            ->data($params['data'])
                                            ->size($sizePx)
                                            ->margin(10);

                                        if ($format === 'svg') {
                                                $builder->writer(new \Endroid\QrCode\Writer\SvgWriter());
                                        } else {
                                                $builder->writer(new \Endroid\QrCode\Writer\PngWriter());
                                        }

                                        if (class_exists($errObj)) {
                                                $builder->errorCorrectionLevel(new $errObj());
                                        }

                                        $result = $builder->build();
                                        // Guardar en archivo
                                        $result->saveToFile($outfile);
                                        return $outfile;
                                }
                                // Si no existe Builder pero existe QrCode, intentar método alternativo
                                if (class_exists('\Endroid\QrCode\QrCode') && class_exists('\Endroid\QrCode\Writer\PngWriter')) {
                                        $sizePx = $size;
                                        if ($sizePx > 0 && $sizePx < 50) {
                                                $sizePx = max(100, $sizePx * 40);
                                        }
                                        $qr = new \Endroid\QrCode\QrCode($params['data']);
                                        if (method_exists($qr,'setSize')) {
                                                $qr->setSize($sizePx);
                                        }
                                        $writer = new \Endroid\QrCode\Writer\PngWriter();
                                        $result = $writer->write($qr);
                                        if (method_exists($result,'saveToFile')) {
                                                $result->saveToFile($outfile);
                                        } else {
                                                file_put_contents($outfile, $result->getString());
                                        }
                                        return $outfile;
                                }
                        } catch (\Throwable $e) {
                                // si falla endroid, caemos al fallback local (log para debugging)
                                if (function_exists('log_message')) {
                                        log_message('error', 'endroid QR failed: ' . $e->getMessage());
                                }
                        }
                }

                // Fallback: usar la librería local phpqrcode
                $libPath = APPPATH . 'third_party/phpqrcode/qrlib.php';
                if (!file_exists($libPath)) {
                        throw new RuntimeException("No se encontró phpqrcode en: $libPath y tampoco está disponible endroid/qr-code. Instala vía Composer o coloca la librería en application/third_party/phpqrcode/");
                }

                require_once($libPath);

                // mapear nivel a constantes si existen
                $levelConst = defined('QR_ECLEVEL_H') ? QR_ECLEVEL_H : 3;
                switch ($level) {
                        case 'L': $levelConst = defined('QR_ECLEVEL_L') ? QR_ECLEVEL_L : 0; break;
                        case 'M': $levelConst = defined('QR_ECLEVEL_M') ? QR_ECLEVEL_M : 1; break;
                        case 'Q': $levelConst = defined('QR_ECLEVEL_Q') ? QR_ECLEVEL_Q : 2; break;
                        case 'H':
                        default: $levelConst = defined('QR_ECLEVEL_H') ? QR_ECLEVEL_H : 3; break;
                }

                // Si se pidió SVG explícito pero la librería local sólo tiene png-like, manejamos por extensión
                // QRcode::png($text, $outfile=false, $level=QR_ECLEVEL_L, $size=3, $margin=4)
                \QRcode::png($params['data'], $outfile, $levelConst, max(1, (int)($size / 50)), 2);

                return $outfile;
        }

}
