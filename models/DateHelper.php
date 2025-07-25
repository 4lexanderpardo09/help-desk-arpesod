<?php
class DateHelper {

    /**
     * Calcula la fecha límite sumando solo días hábiles a una fecha de inicio.
     *
     * @param string $fecha_inicio_str La fecha de inicio (ej: '2025-07-24 10:00:00')
     * @param int $dias_habiles El número de días hábiles a sumar.
     * @return DateTime La fecha y hora límite.
     */
    public static function calcularFechaLimiteHabil($fecha_inicio_str, $dias_habiles) {
        // Lista de festivos en Colombia para 2025
        $festivos = [
            '2025-01-01', '2025-01-06', '2025-03-24', '2025-04-17', '2025-04-18',
            '2025-05-01', '2025-06-02', '2025-06-23', '2025-06-30', '2025-07-20',
            '2025-08-07', '2025-08-18', '2025-10-13', '2025-11-03', '2025-11-17',
            '2025-12-08', '2025-12-25'
        ];

        $fecha_actual = new DateTime($fecha_inicio_str);
        $dias_sumados = 0;

        while ($dias_sumados < $dias_habiles) {
            $fecha_actual->add(new DateInterval('P1D')); // Sumamos un día
            $dia_semana = $fecha_actual->format('N'); // 1 (Lunes) a 7 (Domingo)
            $fecha_simple = $fecha_actual->format('Y-m-d');

            // Si es un día de semana (no sábado ni domingo) Y no es festivo
            if ($dia_semana < 6 && !in_array($fecha_simple, $festivos)) {
                $dias_sumados++;
            }
        }
        return $fecha_actual;
    }
}
?>