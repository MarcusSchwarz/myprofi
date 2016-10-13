<?php

namespace MyProfi;

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
 * Main statistics gathering class
 *
 */
class MyProfi
{

    /**
     * Query fetcher class
     *
     * @var mixed
     */
    protected $fetcher;

    /**
     * Top number of queries to output in stats
     *
     * @var integer
     */
    protected $top;

    /**
     * Only queries of these types to calculate
     *
     * @var array
     */
    protected $types;

    /**
     * Will the input file be treated as CSV formatted
     *
     * @var boolean
     */
    protected $csv = false;

    /**
     * Will the input file be treated as slow query log
     *
     * @var boolean
     */
    protected $slow = false;

    /**
     * Will the statistics include a sample query for each
     * pattern
     *
     * @var boolean
     */
    protected $sample = false;

    /**
     * Field name to sort by
     *
     * @var string
     */
    protected $sort;

    /**
     * Input filename
     */
    protected $filename;

    protected $_queries = [];
    protected $_nums = [];
    protected $_types = [];
    protected $_samples = [];
    protected $_stats = [];

    protected $total = 0;

    /**
     * Set the object that can fetch queries one by one from
     * some storage
     *
     * @param \MyProfi\Reader\IQueryFetcher $prov
     */
    protected function setDataProvider(\MyProfi\Reader\IQueryFetcher $prov)
    {
        $this->fetcher = $prov;
    }

    /**
     * Set maximum number of queries
     *
     * @param integer $top
     */
    public function top($top)
    {
        $this->top = $top;
    }

    /**
     * Set array of query types to calculate
     *
     * @param string $types - comma separated list of types
     */
    public function types($types)
    {
        $types = explode(',', $types);
        $types = array_map('trim', $types);
        $types = array_map('strtolower', $types);
        $types = array_flip($types);

        $this->types = $types;
    }

    /**
     * Set the csv format of an input file
     *
     * @param boolean $csv
     */
    public function csv($csv)
    {
        $this->csv = $csv;
    }

    /**
     * Set the csv format of an input file
     *
     * @param boolean $slow
     *
     * @return boolean
     */
    public function slow($slow = null)
    {
        if (null === $slow) {
            return $this->slow;
        }

        return $this->slow = $slow;
    }

    /**
     * Keep one sample query for each pattern
     *
     * @param boolean $sample
     */
    public function sample($sample)
    {
        $this->sample = $sample;
    }

    /**
     * Set input file
     *
     * @param string $filename
     */
    public function setInputFile($filename)
    {
        if (!$this->csv && (strcasecmp('.csv', substr($filename, -4)) === 0)) {
            $this->csv(true);
        }

        $this->filename = $filename;
    }

    /**
     * @param $sort
     */
    public function sortby($sort)
    {
        $this->sort = $sort;
    }

    /**
     * The main routine so count statistics
     *
     */
    public function processQueries()
    {
        if ($this->csv) {
            if ($this->slow) {
                $this->setDataProvider(new \MyProfi\Reader\SlowCsvReader($this->filename));
            } else {
                $this->setDataProvider(new \MyProfi\Reader\CsvReader($this->filename));
            }
        } elseif ($this->slow) {
            $this->setDataProvider(new \MyProfi\Reader\SlowExtractor($this->filename));
        } else {
            $this->setDataProvider(new \MyProfi\Reader\Extractor($this->filename));
        }

        // counters
        $i = 0;

        // stats arrays
        $queries = [];
        $nums = [];
        $types = [];
        $samples = [];
        $stats = [];

        // temporary assigned properties
        $prefx = $this->types;
        $ex = $this->fetcher;

        // group queries by type and pattern
        while (($line = $ex->get_query())) {
            $stat = false;

            if (is_array($line)) {
                $stat = $line;
                $line = array_pop($stat); // extract statement, it's always the last element of array
            }

            // keep query sample
            $smpl = $line;

            if ('' === ($line = self::normalize($line))) {
                continue;
            }

            // extract first word to determine query type
            $t = preg_split("/[\\W]/", $line, 2);
            $type = $t[0];

            if (null !== $prefx && !isset($prefx[$type])) {
                continue;
            }

            $hash = md5($line);

            // calculate query by type
            if (!array_key_exists($type, $types)) {
                $types[$type] = 1;
            } else {
                $types[$type]++;
            }

            // calculate query by pattern
            if (!array_key_exists($hash, $queries)) {
                $queries[$hash] = $line; // patterns
                $nums[$hash] = 1; // pattern counts
                $stats[$hash] = []; // slow query statistics

                if ($this->sample) {
                    $samples[$hash] = $smpl;
                } // patterns samples
            } else {
                $nums[$hash]++;
            }

            // calculating statistics
            if ($stat) {
                foreach ($stat as $k => $v) {
                    if (isset($stats[$hash][$k])) {
                        // sum with total
                        $stats[$hash][$k]['t'] += $v;

                        if ($v > $stats[$hash][$k]['m']) {
                            // increase maximum, if the current value is bigger
                            $stats[$hash][$k]['m'] = $v;
                        }
                    } else {
                        // set total and maximum values
                        $stats[$hash][$k] = [
                            't' => $v,
                            'm' => $v,
                        ];
                    }
                }
            }

            $i++;
        }

        $stats2 = [];
        if ($this->slow) {
            foreach ($stats as $hash => $col) {
                foreach ($col as $k => $v) {
                    $stats2[$hash][$k . '_total'] = $v['t'];
                    $stats2[$hash][$k . '_avg'] = $v['t'] / $nums[$hash];
                    $stats2[$hash][$k . '_max'] = $v['m'];
                }
            }
        }

        $stats = $stats2;

        if ($this->sort) {
            uasort($stats, [$this, 'cmp']);
        } else {
            arsort($nums);
        }

        arsort($types);

        if (null !== $this->top) {
            if ($this->sort) {
                $stats = array_slice($stats, 0, $this->top);
            } else {
                $nums = array_slice($nums, 0, $this->top);
            }

        }

        $this->_queries = $queries;
        $this->_nums = $nums;
        $this->_types = $types;
        $this->_samples = $samples;
        $this->_stats = $stats;

        $this->total = $i;
    }

    /**
     * @return \ArrayIterator
     */
    public function getTypesStat()
    {
        return new \ArrayIterator($this->_types);
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    protected function cmp($a, $b)
    {
        $f = $a[$this->sort];
        $s = $b[$this->sort];

        return ($f < $s) ? 1 : ($f > $s ? -1 : 0);
    }

    /**
     * @return array|bool
     */
    public function getPatternStats()
    {
        $stat = [];

        if ($this->sort) {
            $tmp =& $this->_stats;
        } else {
            $tmp =& $this->_nums;
        }

        if (list($h, $n) = each($tmp)) {
            if ($this->sort) {
                $stat = $n;
                $n = $this->_nums[$h];
            }

            if ($this->sample) {
                return [$n, $this->_queries[$h], $this->_samples[$h], $stat];
            } else {
                return [$n, $this->_queries[$h], false, $stat];
            }
        } else {
            return false;
        }
    }

    /**
     * @return int
     */
    public function total()
    {
        return $this->total;
    }

    /**
     * Normalize query: remove variable data and replace it with {}
     *
     * @param string $q
     *
     * @return string
     */
    private static function normalize($q)
    {
        $query = $q;
        $query = preg_replace("/\\/\\*.*\\*\\//sU", '', $query);                       // remove multiline comments
        $query = preg_replace("/([\"'])(?:\\\\.|\"\"|''|.)*\\1/sU", "{}", $query);     // remove quoted strings
        $query = preg_replace("/(\\W)(?:-?\\d+(?:\\.\\d+)?)/", "\\1{}", $query);       // remove numbers
        $query = preg_replace("/(\\W)null(?:\\Wnull)*(\\W|\$)/i", "\\1{}\\2", $query); // remove nulls
        $query = str_replace(["\\n", "\\t", "\\0"], ' ', $query);                 // replace escaped linebreaks
        $query = preg_replace("/\\s+/", ' ', $query);                                  // remove multiple spaces
        $query = preg_replace("/ (\\W)/", "\\1", $query);                              // remove spaces bordering with non-characters
        $query = preg_replace("/(\\W) /", "\\1", $query);                              // --,--
        $query = preg_replace("/\\{\\}(?:,?\\{\\})+/", "{}", $query);                  // repetitive {},{} to single {}
        $query = preg_replace("/\\(\\{\\}\\)(?:,\\(\\{\\}\\))+/", "({})", $query);     // repetitive ({}),({}) to single ({})
        $query = strtolower(trim($query, " \t\n)("));                                  // trim spaces and strtolower
        return $query;
    }
}
