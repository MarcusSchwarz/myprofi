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
 * Read mysql slow query log in csv format (as of mysql 5.1 it by default)
 *
 */
class SlowCsvReader extends Filereader implements IQueryFetcher
{

    /**
     * Fetch next query from csv file
     *
     * @return string - or FALSE on file end
     */
    public function get_query()
    {
        while (false !== ($data = fgetcsv($this->fp))) {
            if (!isset($data[10])) {
                continue;
            }

            $query_time = self::time_to_int($data[2]);
            $lock_time = self::time_to_int($data[3]);
            $rows_sent = $data[4];
            $rows_examined = $data[5];

            $statement = str_replace(["\\\\", '\\"'], ["\\", '"'], $data[10]);

            // cut statement id from prefix of prepared statement

            return [
                'qt' => $query_time,
                'lt' => $lock_time,
                'rs' => $rows_sent,
                're' => $rows_examined,
                $statement,
            ];
        }

        return false;
    }

    /**
     * Converts time value in format H:i:s into integer
     * representation of number of total seconds
     *
     * @param string $time
     *
     * @return integer
     */
    protected static function time_to_int($time)
    {
        list($h, $m, $s) = explode(':', $time);

        return ($h * 3600 + $m * 60 + $s);
    }
}
