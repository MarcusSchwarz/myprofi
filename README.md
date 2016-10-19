#MyProfi v1.0.0
####MySQL Log Profiler and Analyzer

Originally written 2006 by camka at camka@users.sourceforge.net

Usage: `php myprofi.php [OPTIONS] INPUTFILE`

Options:

- `top N` Output only N top queries.
    
- `type "query types"` Output only statistics for the queries of given query types. Query types
are comma separated words that queries may begin with.

- `html` Output statistics in html format.

- `sample` Output one sample query per each query pattern to be able to use it with EXPLAIN query
to analyze its performance.

- `csv` Considers an input file to be in csv format. Note, that if the input file extension is
.csv, it is also considered as csv.

- `slow` Treats an input file as a slow query log.
	
- `sort <CRITERIA>` Sort output statistics by given CRITERIA. **Works only for slow query log
format**.<br>
Possible values of CRITERIA: `qt_total` | `qt_avg` | `qt_max` | `lt_total` | `lt_avg` | `lt_max`
| `rs_total` | `rs_avg` | `rs_max` | `re_total` | `re_avg` | `re_max`, where two-letter prefix
stands for "Query time", "Lock time", "Rows sent", "Rows executed".<br>
Values taken from data provided by slow query log respectively.<br>
Suffix after _ character tells MyProfi to take total, maximum or average calculated values.


Example:

    php parser.php -csv -top 10 -type "select, update" general_log.csv


MyProfi is mysql profiler and analizer, which outputs statistics of mostly
used queries by reading query log file.

Copyright (C)
- 2006 camka at camka@users.sourceforge.net
- 2016 - Marcus Schwarz <github@maswaba.de>
