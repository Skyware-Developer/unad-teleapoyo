<?php

return [
    // Horas tras las cuales una consulta sin respuesta se muestra como "Vencida".
    'sla_hours' => (int) env('TELEAPOYO_SLA_HOURS', 72),
];
