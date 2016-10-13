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

interface ITemplate
{

    public function miniheader();

    /**
     * @param string $type
     * @param int    $num
     * @param double $percent
     */
    public function minirow($type, $num, $percent);

    /**
     * @param int $total
     */
    public function minifooter($total);

    public function mainheader();

    /**
     * @param int    $ornum
     * @param string $num
     * @param string $percent
     * @param string $query
     * @param bool   $sort
     * @param bool   $smpl
     */
    public function mainrow($ornum, $num, $percent, $query, $sort = false, $smpl = false);

    /**
     * @param int $total
     */
    public function mainfooter($total);
}
