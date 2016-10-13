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
 * Extracts normalized queries from mysql query log one by one
 *
 */
class Extractor extends Filereader implements IQueryFetcher
{

    /**
     * Fetch the next query pattern from stream
     *
     * @return string
     */
    public function getQuery()
    {
        static $newline;

        $return = $newline;
        $newline = null;

        $fp = $this->fp;

        while (($line = fgets($fp))) {
            $line = rtrim($line, "\r\n");

            // skip server start log lines
            if (substr($line, -13) === 'started with:') {
                fgets($fp); // skip TCP Port: 3306, Named Pipe: (null)
                fgets($fp); // skip Time                 Id Command    Argument
                continue;
            }

            $matches = [];
            if (preg_match("/^(?:\\d{6} {1,2}\\d{1,2}:\\d{2}:\\d{2}|\t)\\s+\\d+\\s+(\\w+)/", $line, $matches)) {
                // if log line
                $type = $matches[1];
                switch ($type) {
                    case 'Query':
                        if ($return) {
                            $newline = ltrim(substr($line, strpos($line, "Q") + 5), " \t");
                            break 2;
                        } else {
                            $return = ltrim(substr($line, strpos($line, "Q") + 5), " \t");
                        }
                        break;
                    case 'Execute':
                        if ($return) {
                            $newline = ltrim(substr($line, strpos($line, ']') + 1), " \t");
                            break 2;
                        } else {
                            $return = ltrim(substr($line, strpos($line, ']') + 1), " \t");
                        }
                        break;
                    default:
                        if ($return) {
                            break 2;
                        }
                        break;
                }
            } else {
                $return .= $line;
            }
        }

        return ($return === '' || null === $return ? false : $return);
    }
}
