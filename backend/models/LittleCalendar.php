<?php
/**
 * Created by PhpStorm.
 * User: StasKozar
 * Date: 28/09/2016
 * Time: 10:33
 */

namespace backend\models;


class LittleCalendar
{
    const U = 'Unavailable';
    const F = 'Free';
    const B = 'Busy';
    public $next;
    public $prev;
    protected $periodToUse;
    protected $prepared = false;
    protected $days = array();

    public function __construct($begin, $interval, $recurrences)
    {
        /* Построение объекта DateTime для выводимого месяца */
        $this->periodToUse = new \DatePeriod($begin, $interval, $recurrences);
        $this->prepare();
    }

    protected function prepare()
    {
        foreach ($this->periodToUse as $date){
            if (!empty($unavailablePeriod)) {
                $start = $date->format('Y-m-d H:i');
                $end = $date->format('Y-m-d 23:59');
                $type = self::U;
                $previous = $unavailablePeriod[$i];
                $unavailablePeriod[] = new Helper($start, $end, $type, $previous);
                $unavailablePeriod[$i]->next = $unavailablePeriod[$k];
                $i++;
            } else {
                $start = $date->format('Y-m-d H:i');
                $end = $date->format('Y-m-d 23:59');
                $type = self::U;
                $unavailablePeriod[] = new Helper($start, $end, $type);
            }
        }
        // Затем выводятся заполнители до первого дня недели
        for ($i = 0, $j = $this->monthToUse->format('w'); $i < $j; $i++) {
            $this->days[] = array('type' => 'blank');
        }
        // Затем по одному элементу для каждого дня месяца
        $today = date('Y-m-d');
        $days = new DatePeriod($this->monthToUse,
            new DateInterval('P1D'),
            $this->monthToUse->format('t') - 1);
        foreach ($days as $day) {
            $isToday = ($day->format('Y-m-d') == $today);
            $endOfRow = ($day->format('w') == 6);
            $this->days[] = array('type' => 'day',
                'label' => $day->format('j'),
                'today' => $isToday,
                'endOfRow' => $endOfRow);
        }
        // Далее выводятся заполнители до конца месяца,
        // если конец недели не пришелся на последний день месяца
        if (!$endOfRow) {
            for ($i = 0, $j = 6 - $day->format('w'); $i < $j; $i++) {
                $this->days[] = array('type' => 'blank');
            }
        }
    }

    public function html($opts = array())
    {
        if (!isset($opts['id'])) {
            $opts['id'] = 'calendar';
        }
        if (!isset($opts['month_link'])) {
            $opts['month_link'] =
                '<a href="' . htmlentities($_SERVER['PHP_SELF']) . '?' .
                'month=%d&amp;year=%d">%s</a>';
        }
        $classes = array();
        foreach (array('prev', 'month', 'next', 'weekday', 'blank', 'day', 'today')
                 as $class) {
            if (isset($opts['class']) && isset($opts['class'][$class])) {
                $classes[$class] = $opts['class'][$class];
            } else {
                $classes[$class] = $class;
            }
        }
        /* Построение объекта DateTime для предыдущего месяца */
        $prevMonth = clone $this->monthToUse;
        $prevMonth->modify("-1 month");
        $prevMonthLink = sprintf($opts['month_link'],
            $prevMonth->format('m'),
            $prevMonth->format('Y'),
            '&laquo;');
        /* Построение объекта DateTime для следующего месяца */
        $nextMonth = clone $this->monthToUse;
        $nextMonth->modify("+1 month");
        $nextMonthLink = sprintf($opts['month_link'],
            $nextMonth->format('m'),
            $nextMonth->format('Y'),
            '&raquo;');
        $html = '<table id="' . htmlentities($opts['id']) . '">
 <tr>
 <td class="' . htmlentities($classes['prev']) . '">' .
            $prevMonthLink . '</td>
 <td class="' . htmlentities($classes['month']) . '" colspan="5">' .
            $this->monthToUse->format('F Y') . '</td>
 <td class="' . htmlentities($classes['next']) . '">' .
            $nextMonthLink . '</td>
 </tr>';
        $html .= '<tr>';
        $lastDayIndex = count($this->days) - 1;
        foreach ($this->days as $i => $day) {
            switch ($day['type']) {
                case 'dow':
                    $class = 'weekday';
                    $label = htmlentities($day['label']);
                    break;
                case 'blank':
                    $class = 'blank';
                    $label = '&nbsp;';
                    break;
                case 'day':
                    $class = $day['today'] ? 'today' : 'day';
                    $label = htmlentities($day['label']);
                    break;
            }
            $html .=
                '<td class="' . htmlentities($classes[$class]) . '">' .
                $label . '</td>';
            if (isset($day['endOfRow']) && $day['endOfRow']) {
                $html .= "</tr>\n";
                if ($i != $lastDayIndex) {
                    $html .= '<tr>';
                }
            }
        }
        $html .= '</table>';
        return $html;
    }

    public function text()
    {
        $lineLength = strlen('Su Mo Tu We Th Fr Sa');
        $header = $this->monthToUse->format('F Y');
        $headerSpacing = floor(($lineLength - strlen($header)) / 2);
        $text = str_repeat(' ', $headerSpacing) . $header . "\n";
        foreach ($this->days as $i => $day) {
            switch ($day['type']) {
                case 'dow':
                    $text .= sprintf('% 2s', $day['label']);
                    break;
                case 'blank':
                    $text .= ' ';
                    break;
                case 'day':
                    $text .= sprintf("% 2d", $day['label']);
                    break;
            }
            $text .= (isset($day['endOfRow']) && $day['endOfRow']) ? "\n" : " ";
        }
        if ($text[strlen($text) - 1] != "\n") {
            $text .= "\n";
        }
        return $text;
    }
}