<?php

require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Company.php';
require __DIR__ . '/../views/layout/header.php';
require __DIR__ . '/../views/invoices/show.php';
require __DIR__ . '/../views/layout/footer.php';

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

        echo "<h2>Facturas</h2>";

        if (!$invoices) {
            echo "No hay facturas.";
            return;
        }

        echo "
        <table border='1' cellpadding='5'>
            <tr>
                <th>Nº</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>";

        foreach ($invoices as $i) {
            echo "
            <tr>
                <td>
                    <a href='index.php?action=invoices_show&id={$i['id']}'>{$i['invoice_number']}</a>
                </td>
                <td>{$i['client_name']}</td>
                <td>{$i['total']} €</td>
                <td>{$i['status']}</td>
            </tr>";
        }

        echo "</table>";
    }

    /* VER FACTURA (DETALLE) */
    public static function show() {
        require_auth();

        $user = auth_user();
        $id = $_GET['id'] ?? null;
        
        // Cargar los datos de la compañía del usuario
        $company = Company::findByUser($user['id']);

        if (!$id) {
            echo "Factura no válida";
            return;
        }

        $invoice = Invoice::findWithClient($id, $user['id']);
        if (!$invoice) {
            echo "Factura no encontrada";
            return;
        }

        $items = Invoice::items($id);

        echo "<h2>Factura {$invoice['invoice_number']}</h2>";

        echo "
        <p>
            <strong>Cliente:</strong> {$invoice['client_name']}<br>
            <strong>CIF/NIF:</strong> {$invoice['tax_id']}<br>
            <strong>Email:</strong> {$invoice['email']}<br>
            <strong>Fecha:</strong> {$invoice['issue_date']}<br>
            <strong>Estado:</strong> {$invoice['status']}
        </p>
        <p>
            <strong>Emisor:</strong><br>
            {$company['company_name']}<br>
            {$company['tax_id']}<br>
            {$company['address']}
        </p>
        <hr>
        ";

        echo "
        <table border='1' cellpadding='5'>
            <tr>
                <th>Concepto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>IVA %</th>
                <th>Total</th>
            </tr>";

        foreach ($items as $item) {
            echo "
            <tr>
                <td>{$item['product_name']}</td>
                <td>{$item['quantity']}</td>
                <td>{$item['unit_price']} €</td>
                <td>{$item['vat_percent']}%</td>
                <td>{$item['line_total']} €</td>
            </tr>";
        }

        echo "
            <tr>
                <td colspan='4' align='right'><strong>Subtotal</strong></td>
                <td>{$invoice['subtotal']} €</td>
            </tr>
            <tr>
                <td colspan='4' align='right'><strong>IVA</strong></td>
                <td>{$invoice['vat_total']} €</td>
            </tr>
            <tr>
                <td colspan='4' align='right'><strong>Total</strong></td>
                <td><strong>{$invoice['total']} €</strong></td>
            </tr>
        </table>

        <br>
        <a href='index.php?action=invoices'>⬅ Volver</a>
        ";

        echo "<br><strong>Cambiar estado:</strong><br>";

        if ($invoice['status'] !== 'paid') {
            echo "<a href='index.php?action=invoice_status&id={$invoice['id']}&status=paid'>✅ Marcar como pagada</a><br>";
        }

        if ($invoice['status'] !== 'cancelled') {
            echo "<a href='index.php?action=invoice_status&id={$invoice['id']}&status=cancelled'>❌ Cancelar factura</a><br>";
        }

        if ($invoice['status'] !== 'pending') {
            echo "<a href='index.php?action=invoice_status&id={$invoice['id']}&status=pending'>⏳ Marcar como pendiente</a>";
        }

        echo "<br><br><a href='index.php?action=invoice_pdf&id={$invoice['id']}'>📄 Descargar PDF</a><br><br>";
        
        echo "<a href='index.php?action=invoice_email&id={$invoice['id']}'>✉️ Enviar por email</a><br><br>";

        $paid = Payment::totalPaid($invoice['id']);
        $pending = $invoice['total'] - $paid;

        echo "
        <hr>
        <h3>Pagos</h3>
        <p><strong>Total pagado:</strong> {$paid} €</p>
        <p><strong>Pendiente:</strong> {$pending} €</p>

        <form method='POST' action='index.php?action=payment_store'>
            <input type='hidden' name='csrf_token' value='".csrf_token()."'>
            
            <input type='hidden' name='invoice_id' value='{$invoice['id']}'>

            <input type='number' step='0.01' name='amount' placeholder='Importe' required>
            <input type='date' name='payment_date' value='".date('Y-m-d')."' required>
            <input type='text' name='method' placeholder='Método (transferencia, efectivo…)'>
            <button type='submit'>Registrar pago</button>
        </form>
        ";
        
    }

    public static function status() {
        require_auth();

        $user   = auth_user();
        $id     = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if (!$id || !in_array($status, ['pending', 'paid', 'cancelled'])) {
            echo "Estado inválido";
            return;
        }

        Invoice::changeStatus($id, $user['id'], $status);

        header('Location: index.php?action=invoices_show&id=' . $id);
        exit;
    }

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