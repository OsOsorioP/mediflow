<x-mail::message>
# 🔔 Recordatorio de Cita

Estimado(a) **{{ $patient->full_name }}**,

Le recordamos que tiene una cita programada para **mañana**:

<x-mail::panel>
**📅 Fecha:** {{ $appointment->formatted_date }}  
**🕐 Hora:** {{ $appointment->formatted_time }}  
**👨‍⚕️ Profesional:** Dr. {{ $doctor->name }}  
**📍 Clínica:** {{ $clinic->name }}
@if($appointment->reason)  
**💬 Motivo:** {{ $appointment->reason }}
@endif
</x-mail::panel>

## Recomendaciones

✓ Llegue 10 minutos antes de su cita  
✓ Traiga su documento de identidad  
✓ Si tiene exámenes recientes, por favor tráigalos

@if($clinic->phone)
Si necesita cancelar o reprogramar, llámenos al **{{ $clinic->phone }}**
@endif

Gracias,<br>
{{ $clinic->name }}
</x-mail::message>