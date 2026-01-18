<x-mail::message>
# Registro Médico

Estimado(a) **{{ $patient->full_name }}**,

Adjunto a este correo encontrará su registro médico correspondiente a la consulta del **{{ $record->consultation_date->locale('es')->isoFormat('LL') }}**.

<x-mail::panel>
**📋 Tipo de Registro:** {{ $record->record_type->label() }}  
**👨‍⚕️ Profesional:** Dr. {{ $record->creator->name }}  
**📅 Fecha:** {{ $record->consultation_date->locale('es')->isoFormat('LL') }}
</x-mail::panel>

## Información Importante

- Este documento contiene información médica confidencial.
- Guárdelo de forma segura.
- Si tiene alguna duda sobre el tratamiento o las indicaciones, no dude en contactarnos.

@if($clinic->phone)
**Teléfono:** {{ $clinic->phone }}
@endif

@if($clinic->email)
**Email:** {{ $clinic->email }}
@endif

@if($clinic->address)
**Dirección:** {{ $clinic->address }}
@endif

Gracias por confiar en nosotros,<br>
**{{ $clinic->name }}**
</x-mail::message>