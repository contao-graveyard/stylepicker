<?php

declare(strict_types=1);

namespace ContaoGraveyard\StylePickerBundle;

use Contao\Controller;
use Contao\Database;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;

class DcaHelper extends Controller
{
    /**
     * @var Database
     */
    protected $Database;

    /**
     * Construct the class.
     */
    public function __construct()
    {
        parent::__construct();

        $this->Database = Database::getInstance();
    }

    /**
     * Inject the stylepicker wizard into DCAs.
     */
    public function injectStylepicker($table): void
    {
        // dont inject in some system-modules
        if (\in_array(Input::get('do'), ['repository_manager', 'repository_catalog', 'maintenance', 'settings', 'log', 'autoload', 'composer'], true)) {
            return;
        }

        $objErg = $this->Database->prepare('SELECT DISTINCT(fld) FROM tl_stylepicker4ward_target WHERE tbl=?')->execute($table);
        if ($objErg->numRows <= 0) {
            return;
        }

        while ($objErg->next()) {
            $GLOBALS['TL_DCA'][$table]['fields'][$objErg->fld]['eval']['tl_class'] .= ' wizard';
            $GLOBALS['TL_DCA'][$table]['fields'][$objErg->fld]['wizard']['stylepicker'] = self::getStylepicker(...);
        }

        // little hack to adjust the wizard for the article-section
        if ($table === 'tl_article') {
            $GLOBALS['TL_DCA'][$table]['fields']['inColumn']['eval']['submitOnChange'] = true;
        }
    }

    /**
     * Return the stylepicker wizard html.
     *
     * @return string
     */
    public function getStylepicker($dc)
    {
        $GLOBALS['TL_CSS']['stylepicker4ward'] = 'bundles/contaograveyardstylepicker/assets/style.css';

        $url = 'bundles/contaograveyardstylepicker/public/popup.php?tbl=' . $dc->table . '&fld=' . $dc->field . '&inputName=ctrl_' . $dc->inputName . '&id=' . $dc->id;

        static $injected = false;

        if (!$injected) {
            $str = <<<'CssButton'
                <script type="text/javascript">
                function openStylepickerPopup(url) {
                	var opt = {width: 775};
                	var max = (window.getSize().y-180).toInt();
                	if (!opt.height || opt.height > max) opt.height = max;
                	var M = new SimpleModal({
                		'width': opt.width,
                		'draggable': false,
                		'okBtn': 'OK',
                		'overlayOpacity': .5,
                		'onShow': function() { document.body.setStyle('overflow', 'hidden'); },
                		'onHide': function() { document.body.setStyle('overflow', 'auto'); }
                	});
                	M.show({
                		'title': 'Stylepicker',
                		'contents': '<iframe src="' + url + '" width="100%" height="' + opt.height + '" frameborder="0"></iframe>'
                	});
                }
                </script>
                CssButton;
            $injected = true;
        } else {
            $str = '';
        }

        return $str . ' <a href="javascript:openStylepickerPopup(\'' . $url . '\');">' . Image::getHtml('bundles/contaograveyardstylepicker/assets/icon.png', $GLOBALS['TL_LANG']['MSC']['stylepicker4ward'] ?? '', 'style="vertical-align:top;margin-left:3px;margin-top:3px;"') . '</a>';
    }

    public function generateItem($arrRow)
    {
        return $arrRow['title'] . ': ' . $arrRow['cssclass'];
    }

    /* =========================== */
    /* Pages */
    /* =========================== */
    public function savePages($val, $dc)
    {
        // delete all records for this table/pid
        $this->truncateTargets($dc->id, 'tl_page');

        if (\strlen((string) $val)) {
            $this->saveTarget($dc->id, 'tl_page', 'cssClass');
        }

        return '';
    }

    public function loadPages($val, $dc)
    {
        $arrReturn = [];
        $objTargets = $this->Database->prepare('SELECT count(pid) AS anz FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=?')->execute($dc->id, 'tl_page');

        return $objTargets->anz > 0 ? '1' : '';
    }

    /* =========================== */
    /* Articles */
    /* =========================== */
    public function saveArticles($val, $dc)
    {
        // delete all records for this table/pid
        $this->truncateTargets($dc->id, 'tl_article', 'cssID');

        if (\strlen((string) $val)) {
            // get sections
            $secs = Input::post('_Article_Row');
            if (!\is_array($secs) || !\count($secs)) {
                return '';
            }

            // save foreach section
            foreach ($secs as $sec) {
                $this->saveTarget($dc->id, 'tl_article', 'cssID', $sec);
            }
        }

        return '';
    }

    public function loadArticles($val, $dc)
    {
        $objTargets = $this->Database->prepare('SELECT count(pid) AS anz FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=? AND fld=?')->execute($dc->id, 'tl_article', 'cssID');

        return $objTargets->anz > 0 ? '1' : '';
    }

    public function saveArticleTeasers($val, $dc)
    {
        // delete all records for this table/pid
        $this->truncateTargets($dc->id, 'tl_article', 'teaserCssID');

        if (\strlen((string) $val)) {
            // get sections
            $secs = Input::post('_Article_Row');
            if (!\is_array($secs) || !\count($secs)) {
                return '';
            }

            // save foreach section
            foreach ($secs as $sec) {
                $this->saveTarget($dc->id, 'tl_article', 'teaserCssID', $sec);
            }
        }

        return '';
    }

    public function loadArticleTeasers($val, $dc)
    {
        $objTargets = $this->Database->prepare('SELECT count(pid) AS anz FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=? AND fld=?')->execute($dc->id, 'tl_article', 'teaserCssID');

        return $objTargets->anz > 0 ? '1' : '';
    }

    public function loadArticle_Rows($val, $dc)
    {
        $arrReturn = [];
        $objTargets = $this->Database->prepare('SELECT DISTINCT(sec) FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=?')->execute($dc->id, 'tl_article');

        while ($objTargets->next()) {
            $arrReturn[] = $objTargets->sec;
        }

        return serialize($arrReturn);
    }

    /* =========================== */
    /* Content elements */
    /* =========================== */
    public function saveCEs($val, $dc)
    {
        // delete all records for this table/pid
        $this->truncateTargets($dc->id, 'tl_content');

        $vals = unserialize($val);

        if (!\is_array($vals) && Input::post('_CE_Row')) {
            throw new \Exception($GLOBALS['TL_LANG']['tl_stylepicker4ward']['_ceError'] ?? '');
        }

        if (\is_array($vals)) {
            // get sections
            $secs = Input::post('_CE_Row');
            if (!\is_array($secs) || !\count($secs)) {
                throw new \Exception($GLOBALS['TL_LANG']['tl_stylepicker4ward']['_rowError'] ?? '');
            }

            // save CEs foreach section
            foreach ($secs as $sec) {
                foreach ($vals as $val) {
                    $this->saveTarget($dc->id, 'tl_content', 'cssID', $sec, $val);
                }
            }
        }

        return '';
    }

    public function loadCEs($val, $dc)
    {
        $arrReturn = [];
        $objTargets = $this->Database->prepare('SELECT DISTINCT(cond) FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=?')->execute($dc->id, 'tl_content');

        while ($objTargets->next()) {
            $arrReturn[] = $objTargets->cond;
        }

        return serialize($arrReturn);
    }

    public function loadCE_Rows($val, $dc)
    {
        $arrReturn = [];
        $objTargets = $this->Database->prepare('SELECT DISTINCT(sec) FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=?')->execute($dc->id, 'tl_content');

        while ($objTargets->next()) {
            $arrReturn[] = $objTargets->sec;
        }

        return serialize($arrReturn);
    }

    /**
     * lädt vorhandene Inhaltselemente aus $GLOBALS['TL_CTE'].
     * @return array
     */
    public function getContentElements()
    {
        $arrCEs = [];

        foreach ($GLOBALS['TL_CTE'] as $key => $arr) {
            foreach ($arr as $elementName => $val) {
                $arrCEs[] = $elementName;
            }
        }

        return $arrCEs;
    }

    public function loadPagelayouts($val)
    {
        $val = explode(',', (string) $val);

        return serialize($val);
    }

    public function savePagelayouts($val)
    {
        $val = StringUtil::deserialize($val, true);

        return implode(',', $val);
    }

    /**
     * get all sections.
     * @return array tl_stylepicker4ward_target
     */
    public function getSections()
    {
        $this->loadLanguageFile('tl_article');
        $ret = ['header', 'left', 'right', 'main', 'footer'];

        $custom = explode(',', (string) ($GLOBALS['TL_CONFIG']['customSections'] ?? null));
        if (\strlen((string) ($GLOBALS['TL_CONFIG']['customSections'] ?? null)) && \is_array($custom)) {
            $ret = array_merge($ret, $custom);
        }

        return $ret;
    }

    /**
     * get all pagelayouts for the current theme.
     * @param  DataContainer $dc
     * @return array
     */
    public function getPagelayouts($dc)
    {
        $objLayouts = $this->Database->prepare('SELECT id,name FROM tl_layout WHERE pid=?')->execute($dc->activeRecord->pid);
        $arrLayouts = [];

        while ($objLayouts->next()) {
            $arrLayouts[$objLayouts->id] = $objLayouts->name;
        }

        return $arrLayouts;
    }

    /**
     * void function for some callbacks.
     * @return string ''
     */
    public function doNothing()
    {
        return '';
    }

    /**
     * copy a definition.
     * @param int           $insertID
     * @param DataContainer $dc
     */
    public function copy($insertID, $dc): void
    {
        // also copy targets
        $this->Database->prepare('INSERT INTO tl_stylepicker4ward_target (pid,tstamp,tbl,fld,cond,sec)
									SELECT ?, UNIX_TIMESTAMP(), tbl,fld,cond,sec
									FROM tl_stylepicker4ward_target
									WHERE pid=?')->execute($insertID, $dc->id);
    }

    /**
     * Helperfunction to save a target.
     * @param int    $pid
     * @param string $tbl
     * @param string $field
     * @param string $section
     * @param string $condition
     */
    protected function saveTarget($pid, $tbl, $field, $section = '', $condition = ''): void
    {
        // delete old CEs
        $this->Database->prepare('INSERT INTO tl_stylepicker4ward_target SET pid=?,tbl=?,fld=?,sec=?,cond=?,tstamp=?')
                       ->execute($pid, $tbl, $field, $section, $condition, time())
        ;
    }

    /**
     * Helperfunction to trunce old targets.
     * @param int         $pid
     * @param string      $tbl
     * @param string|bool $fld
     */
    protected function truncateTargets($pid, $tbl, $fld = false): void
    {
        if ($fld) {
            $this->Database->prepare('DELETE FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=? AND fld=?')->execute($pid, $tbl, $fld);
        } else {
            $this->Database->prepare('DELETE FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=?')->execute($pid, $tbl);
        }
    }
}
