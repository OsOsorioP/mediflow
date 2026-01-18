<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago - {{ $payment->payment_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            padding: 30px;
        }
        
        .header {
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .clinic-name {
            font-size: 22pt;
            font-weight: bold;
            color: #10b981;
            margin-bottom: 5px;
        }
        
        .clinic-info {
            color: #666;
            font-size: 9pt;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            margin: 30px 0;
            color: #059669;
            text-transform: uppercase;
        }
        
        .receipt-number {
            text-align: center;
            font-size: 14pt;
            color: #666;
            margin-bottom: 30px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #059669;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-table td {
            padding: 5px 0;
        }
        
        .info-label {
            font-weight: bold;
            width: 40%;
            color: #555;
        }
        
        .info-value {
            color: #000;
        }
        
        .amount-box {
            background-color: #d1fae5;
            border: 3px solid #10b981;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        
        .amount-label {
            font-size: 12pt;
            color: #059669;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .amount-value {
            font-size: 32pt;
            font-weight: bold;
            color: #047857;
        }
        
        .amount-words {
            font-size: 10pt;
            color: #666;
            font-style: italic;
            margin-top: 10px;
        }
        
        .footer {
            position: fixed;
            bottom: 30px;
            left: 30px;
            right: 30px;
            border-top: 2px solid #e5e7eb;
            padding-top: 15px;
            font-size: 9pt;
            color: #666;
        }
        
        .signature-section {
            margin-top: 60px;
            text-align: center;
        }
        
        .signature-line {
            width: 300px;
            border-top: 2px solid #333;
            margin: 0 auto 10px;
        }
        
        .signature-name {
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: bold;
        }
        
        .status-completed {
            background-color: #d1fae5;
            color: #047857;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100pt;
            color: rgba(16, 185, 129, 0.05);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark">PAGADO</div>

    <!-- Header -->
    <div class="header">
        <div class="clinic-name">{{ $clinic->name }}</div>
        <div class="clinic-info">
            @if($clinic->address)
                {{ $clinic->address }}<br>
            @endif
            @if($clinic->phone)
                Tel: {{ $clinic->phone }}
            @endif
            @if($clinic->email)
                · Email: {{ $clinic->email }}
            @endif
        </div>
    </div>

    <!-- Título -->
    <div class="receipt-title">Recibo de Pago</div>
    <div class="receipt-number">
        No. {{ $payment->payment_number }}
        <span class="status-badge status-{{ $payment->status === \App\Enums\PaymentStatus::COMPLETED ? 'completed' : 'pending' }}">
            {{ $payment->status->label() }}
        </span>
    </div>

    <!-- Información del Paciente -->
    <div class="section">
        <div class="section-title">Datos del Paciente</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Nombre:</td>
                <td class="info-value">{{ $patient->full_name }}</td>
            </tr>
            <tr>
                <td class="info-label">Documento:</td>
                <td class="info-value">{{ $patient->full_identification }}</td>
            </tr>
            <tr>
                <td class="info-label">Teléfono:</td>
                <td class="info-value">{{ $patient->phone }}</td>
            </tr>
            @if($patient->email)
            <tr>
                <td class="info-label">Email:</td>
                <td class="info-value">{{ $patient->email }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Detalles del Pago -->
    <div class="section">
        <div class="section-title">Detalles del Pago</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Fecha de Pago:</td>
                <td class="info-value">{{ $payment->payment_date->locale('es')->isoFormat('LL') }}</td>
            </tr>
            <tr>
                <td class="info-label">Concepto:</td>
                <td class="info-value">{{ $payment->concept }}</td>
            </tr>
            @if($payment->description)
            <tr>
                <td class="info-label">Descripción:</td>
                <td class="info-value">{{ $payment->description }}</td>
            </tr>
            @endif
            <tr>
                <td class="info-label">Método de Pago:</td>
                <td class="info-value">{{ $payment->payment_method->label() }}</td>
            </tr>
            @if($payment->reference_number)
            <tr>
                <td class="info-label">Referencia:</td>
                <td class="info-value">{{ $payment->reference_number }}</td>
            </tr>
            @endif
            <tr>
                <td class="info-label">Recibido por:</td>
                <td class="info-value">{{ $payment->creator->name }}</td>
            </tr>
        </table>
    </div>

    <!-- Monto Total -->
    <div class="amount-box">
        <div class="amount-label">MONTO TOTAL</div>
        <div class="amount-value">{{ $payment->formatted_amount }}</div>
        <div class="amount-words">
            {{ $payment->currency }}
        </div>
    </div>

    <!-- Firma -->
    <div class="signature-section">
        <div class="signature-line"></div>
        <div class="signature-name">{{ $payment->creator->name }}</div>
        <div>{{ $clinic->name }}</div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="text-align: center;">
            Recibo generado el {{ now()->locale('es')->isoFormat('LLL') }}<br>
            Este es un documento válido · {{ $clinic->name }}
        </div>
    </div>
</body>
</html>