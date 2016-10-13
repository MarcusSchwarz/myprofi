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

class HtmlTemplate implements ITemplate
{

    public function miniheader()
    {
        printf('<html><head><title>MyProfi Report</title>
<style type="text/css">
	* { font-size: 10px; font-family: Verdana }
	thead * {font-weight:bold}
</style></head><body>');
        printf('<table border="1"><thead><tr><td colspan="3">Queries by type:</td></tr></thead><tbody>');
    }

    public function minirow($type, $num, $percent)
    {
        printf("<tr><td>%s</td><td>%s</td><td>%s%%</td></tr>", htmlspecialchars($type), number_format($num, 0, '', ' '),
            number_format($percent, 2));
    }

    public function minifooter($total)
    {
        printf('</tbody><tfoot><tr><td colspan="4">Total: %s queries</td></tr></tfoot></table>',
            number_format($total, 0, '', ' '));
    }

    public function mainheader()
    {
        printf('<table border="1"><thead><tr><td colspan="4">Queries by pattern:</td></tr>');
        printf('<tr><td>#</td><td>Qty</td><td>%%</td><td>Query</td></tr></thead><tbody>');
    }

    public function mainrow($ornum, $num, $percent, $query, $sort = false, $smpl = false)
    {
        if ($sort) {
            printf('<tr><td>%d</td><td>%s</td><td>%s</td><td>%s</td></tr>', $ornum, $num, $percent,
                htmlspecialchars($query));
        } else {
            printf('<tr><td>%d</td><td>%s</td><td>%s%%</td><td>%s</td></tr>', $ornum, $num, $percent,
                htmlspecialchars($query));
        }

        if ($smpl) {
            printf('<tr><td colspan="4"><textarea style="width:100%%" onClick="javascript:this.focus();this.select();">%s</textarea></td></tr>',
                htmlspecialchars($smpl));
        }
    }

    public function mainfooter($total)
    {
        printf('</tbody><tfoot><tr><td colspan="4">Total: %s patterns</td></tr></tfoot></table></body></html>',
            number_format($total, 0, '', ' '));
    }
}
