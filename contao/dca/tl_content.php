<?php

declare(strict_types=1);

use Contao\StringUtil;

// Add Callback to show the css-classes in the contentelement-listing
$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] = ['tl_content_stylepicker', 'addClassNames'];

class tl_content_stylepicker extends tl_content
{
    /**
     * Show the css-classes in the contentelement listing.
     *
     * @return string
     */
    public function addClassNames($arrRow)
    {
        $ret = '';
        $cssID = StringUtil::deserialize($arrRow['cssID']);
        $ret .= $this->addCteType($arrRow);
        if (!empty($cssID[1])) {
            $ret .= '<div style="position: absolute; top: 30px; color: #999; right: 10px">' . ($GLOBALS['TL_LANG']['tl_content']['_cssclasses'] ?? null) . '' . $cssID[1] . '</div>';
        }

        return $ret;
    }
}
