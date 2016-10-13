<?php

namespace MyProfi\Template;

/**
 * MyProfi is mysql profiler and analizer, which outputs statistics of mostly
 * used queries by reading query log file.
 *
 * Copyright (C) 2006 camka at camka@users.sourceforge.net
 * 2016 - Marcus Schwarz <github@maswaba.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author  camka
 * @package MyProfi
 */

class PlainTemplate implements ITemplate
{

    public function miniheader()
    {
        printf("Queries by type:\n================\n");
    }

    /**
     * @param string $type
     * @param int    $num
     * @param float  $percent
     */
    public function minirow($type, $num, $percent)
    {
        printf("% -20s % -10s [%5s%%] \n", $type, number_format($num, 0, '', ' '), number_format($percent, 2));
    }

    /**
     * @param int $total
     */
    public function minifooter($total)
    {
        printf("---------------\nTotal: %s queries\n\n\n", number_format($total, 0, '', ' '));
    }

    public function mainheader()
    {
        printf("Queries by pattern:\n===================\n");
    }

    /**
     * @param int    $ornum
     * @param string $num
     * @param string $percent
     * @param string $query
     * @param bool   $sort
     * @param bool   $smpl
     */
    public function mainrow($ornum, $num, $percent, $query, $sort = false, $smpl = false)
    {
        if ($sort) {
            printf(
                "%d.\t% -10s [%10s] - %s\n",
                $ornum,
                number_format($num, 0, '', ' '),
                number_format($percent, 2),
                $query
            );
        } else {
            printf(
                "%d.\t% -10s [% 5s%%] - %s\n",
                $ornum,
                number_format($num, 0, '', ' '),
                number_format($percent, 2),
                $query
            );
        }

        if ($smpl) {
            printf("%s\n\n", $smpl);
        }
    }

    /**
     * @param int $total
     */
    public function mainfooter($total)
    {
        printf("---------------\nTotal: %s patterns", number_format($total, 0, '', ' '));
    }
}
