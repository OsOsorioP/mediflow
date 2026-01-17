<x-mail::message>
# Confirmación de Cita

Estimado(a) **{{ $patient->full_name }}**,

Su cita ha sido confirmada con los siguientes detalles:

<x-mail::panel>
**📅 Fecha:** {{ $appointment->formatted_date }}  
**🕐 Hora:** {{ $appointment->formatted_time }}  
**👨‍⚕️ Profesional:** Dr. {{ $doctor->name }}  
**📍 Clínica:** {{ $clinic->name }}
@if($appointment->reason)  
**💬 Motivo:** {{ $appointment->reason }}
@endif
</x-mail::panel>

## Información Importante

- Por favor, llegue **10 minutos antes** de su cita.
- Si necesita cancelar o reprogramar, contáctenos con al menos 24 horas de anticipación.
- Traiga su documento de identidad y cualquier examen previo.

@if($clinic->address)
**Dirección:** {{ $clinic->address }}
@endif

@if($clinic->phone)
**Teléfono:** {{ $clinic->phone }}
@endif

<x-mail::button :url="config('app.url')">
Ir a MediFlow
</x-mail::button>

Gracias por confiar en nosotros,<br>
{{ $clinic->name }}
</x-mail::message>