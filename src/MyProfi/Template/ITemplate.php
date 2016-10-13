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

    public function minirow($type, $num, $percent);

    public function minifooter($total);

    public function mainheader();

    public function mainrow($ornum, $num, $percent, $query, $sort = false, $smpl = false);

    public function mainfooter($total);
}
