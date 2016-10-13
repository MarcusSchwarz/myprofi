<?php

namespace MyProfi\Reader;

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

/**
 * General file reader class
 *
 */
abstract class Filereader
{

    /**
     * File pointer
     *
     * @var resource
     */
    public $fp;

    /**
     * Attempts to open a file
     * Dies on failure
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        if (false === ($this->fp = @fopen($filename, 'rb'))) {
            (new \MyProfi\Helper())->doc('Error: cannot open input file ' . $filename);
        }
    }

    /**
     * Close file on exit
     *
     */
    public function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
    }
}
