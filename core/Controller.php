<?php

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        $file = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($file)) {
            require $file;
        } else {
            die("Halaman tidak ditemukan.");
        }
    }

    protected function downloadPdf($view, $data, $filename)
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../views/' . $view . '.php';
        $html = ob_get_clean();

        $pdf = new Dompdf\Dompdf(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans']);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        header('Cache-Control: no-store, private');
        $pdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    protected function redirect($url)
    {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    protected function sanitize($value)
    {
        return htmlspecialchars(
            $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
