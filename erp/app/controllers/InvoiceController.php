<?php

require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Company.php';
require_once __DIR__ . '/../models/Payment.php'; // Necesario para gestionar los pagos en show()

class InvoiceController {

    /* LISTADO DE FACTURAS */
    public static function index() {
        require_auth();

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT i.*, c.name AS client_name
            FROM invoices i
            JOIN clients c ON c.id = i.client_id
            WHERE i.user_id = ?
            ORDER BY i.created_at DESC
        ");
        $stmt->execute([auth_user()['id']]);
        $invoices = $stmt->fetchAll();

        // Cargar las vistas (Header -> Contenido -> Footer)
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/invoices/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    /* VER FACTURA (DETALLE) */
    public static function show() {
        require_auth();

        $user = auth_user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die("Factura no válida");
        }
        
        // Cargar los datos de la compañía del usuario
        $company = Company::findByUser($user['id']);

        $invoice = Invoice::findWithClient($id, $user['id']);
        if (!$invoice) {
            die("Factura no encontrada");
        }

        $items = Invoice::items($id);

        // Extraer datos de los pagos para enviarlos a la vista
        $payments = Payment::allByInvoice($id);
        $paid = Payment::totalPaid($id);
        $pending = $invoice['total'] - $paid;

        // Cargar las vistas con el HTML bonito
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/invoices/show.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    /* CAMBIAR ESTADO */
    public static function status() {
        require_auth();

        $user   = auth_user();
        $id     = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if (!$id || !in_array($status, ['pending', 'paid', 'cancelled'])) {
            die("Estado inválido");
        }

        Invoice::changeStatus($id, $user['id'], $status);

        header('Location: index.php?action=invoices_show&id=' . $id);
        exit;
    }

    /* GENERAR PDF */
    public static function pdf() {
        require_auth();

        require_once __DIR__ . '/../../vendor/autoload.php';

        $user = auth_user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die('Factura no válida');
        }

        $invoice = Invoice::findWithClient($id, $user['id']);
        if (!$invoice) {
            die('Factura no encontrada');
        }

        $items = Invoice::items($id);

        // Renderizar HTML
        ob_start();
        require __DIR__ . '/../views/invoices/pdf.php';
        $html = ob_get_clean();

        // Dompdf
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();

        // Descargar
        $dompdf->stream(
            "Factura-{$invoice['invoice_number']}.pdf",
            ['Attachment' => true]
        );
    }

    /* ENVIAR EMAIL */
    public static function sendEmail() {
        require_auth();

        require_once __DIR__ . '/../../vendor/autoload.php';

        $user = auth_user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die('Factura no válida');
        }

        $invoice = Invoice::findWithClient($id, $user['id']);
        if (!$invoice) {
            die('Factura no encontrada');
        }

        $items = Invoice::items($id);

        // Generar PDF en memoria
        ob_start();
        require __DIR__ . '/../views/invoices/pdf.php';
        $html = ob_get_clean();

        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();
        $pdfContent = $dompdf->output();

        // Enviar email
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Configuración SMTP (EJEMPLO Gmail)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tuemail@gmail.com';
            $mail->Password   = 'TU_PASSWORD_O_APP_PASSWORD';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('tuemail@gmail.com', 'Sistema de Facturación');
            $mail->addAddress($invoice['email'], $invoice['client_name']);

            $mail->isHTML(true);
            $mail->Subject = "Factura {$invoice['invoice_number']}";
            $mail->Body    = "
                <p>Hola {$invoice['client_name']},</p>
                <p>Adjuntamos la factura <strong>{$invoice['invoice_number']}</strong>.</p>
                <p>Importe total: <strong>{$invoice['total']} €</strong></p>
                <p>Gracias.</p>
            ";

            // Adjuntar PDF
            $mail->addStringAttachment(
                $pdfContent,
                "Factura-{$invoice['invoice_number']}.pdf",
                'base64',
                'application/pdf'
            );

            $mail->send();

            echo "✅ Factura enviada correctamente por email";

        } catch (Exception $e) {
            echo "❌ Error al enviar email: {$mail->ErrorInfo}";
        }
    }
}