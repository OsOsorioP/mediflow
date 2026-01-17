<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receta Médica - {{ $patient->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }
        
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .clinic-name {
            font-size: 24pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .clinic-info {
            color: #666;
            font-size: 10pt;
        }
        
        .document-title {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            margin: 30px 0;
            color: #1e40af;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            padding: 5px 10px 5px 0;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        
        .prescription-box {
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background-color: #f8fafc;
        }
        
        .prescription-content {
            white-space: pre-wrap;
            line-height: 1.8;
        }
        
        .footer {
            position: fixed;
            bottom: 40px;
            left: 40px;
            right: 40px;
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 50px;
        }
        
        .signature-line {
            width: 300px;
            border-top: 2px solid #333;
            margin: 60px auto 10px;
            text-align: center;
        }
        
        .signature-info {
            text-align: center;
            font-weight: bold;
        }
        
        .page-number {
            text-align: center;
            color: #666;
            font-size: 10pt;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100pt;
            color: rgba(0, 0, 0, 0.03);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ $clinic->name }}</div>

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
    <div class="document-title">RECETA MÉDICA</div>

    <!-- Información del Paciente -->
    <div class="section">
        <div class="section-title">Información del Paciente</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value">{{ $patient->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Documento:</div>
                <div class="info-value">{{ $patient->full_identification }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Edad:</div>
                <div class="info-value">{{ $patient->age }} años</div>
            </div>
            @if($patient->blood_type)
            <div class="info-row">
                <div class="info-label">Tipo de Sangre:</div>
                <div class="info-value">{{ $patient->blood_type }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Fecha:</div>
                <div class="info-value">{{ $record->consultation_date->locale('es')->isoFormat('LL') }}</div>
            </div>
        </div>
    </div>

    <!-- Diagnóstico -->
    @if($record->diagnosis)
    <div class="section">
        <div class="section-title">Diagnóstico</div>
        <div class="prescription-content">{{ $record->diagnosis }}</div>
    </div>
    @endif

    <!-- Prescripción -->
    @if($record->prescriptions)
    <div class="prescription-box">
        <div class="section-title" style="border: none; margin-bottom: 15px;">Prescripción Médica</div>
        <div class="prescription-content">{{ $record->prescriptions }}</div>
    </div>
    @endif

    <!-- Plan de Tratamiento -->
    @if($record->treatment_plan)
    <div class="section">
        <div class="section-title">Indicaciones y Plan de Tratamiento</div>
        <div class="prescription-content">{{ $record->treatment_plan }}</div>
    </div>
    @endif

    <!-- Firma -->
    <div class="signature-line"></div>
    <div class="signature-info">
        Dr. {{ $doctor->name }}<br>
        <span style="font-weight: normal; font-size: 10pt;">
            {{ $clinic->name }}
        </span>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="page-number">
            Página 1 de 1 · Generado el {{ now()->locale('es')->isoFormat('LLL') }}
        </div>
    </div>
</body>
</html>