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
 * Read mysql query log in csv format (as of mysql 5.1 it by default)
 *
 */
class CsvReader extends Filereader implements IQueryFetcher
{

    /**
     * Fetch next query from csv file
     *
     * @return string - or FALSE on file end
     */
    public function get_query()
    {
        while (false !== ($data = fgetcsv($this->fp))) {
            if ((!isset($data[4])) || (($data[4] !== "Query") && ($data[4] !== "Execute")) || (!$data[5])) {
                continue;
            }

            // cut statement id from prefix of prepared statement
            $d5 = $data[5];
            $query = ('Execute' == $data[4] ? substr($d5, strpos($d5, ']') + 1) : $d5);

            return str_replace(["\\\\", '\\"'], ["\\", '"'], $query);
        }

        return false;
    }
}
