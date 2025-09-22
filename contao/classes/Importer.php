<?php

declare(strict_types=1);

/**
 * @copyright 4ward.media 2013 <http://www.4wardmedia.de>
 */

namespace Psi\Stylepicker4ward;

use Contao\System;
use Contao\Controller;
use Contao\SelectMenu;
use Contao\Widget;
use Contao\CheckBox;
use Contao\Input;
use Contao\Database;
use Contao\Message;
use Contao\LayoutModel;
use Contao\StringUtil;
use Contao\BackendTemplate;

class Importer
{
    public function generate()
    {
        Controller::loadLanguageFile('stylepicker4ward_import');

        $presetOptions = [];

        if (isset($GLOBALS['STYLEPICKER_PRESET'])) {
            foreach ($GLOBALS['STYLEPICKER_PRESET'] as $contentElementName => $config) {
                $presetOptions[$contentElementName] = $config['label'];
            }
        }

        $presetWidget = new SelectMenu(
            Widget::getAttributesFromDca(
                [
                    'label' => &$GLOBALS['TL_LANG']['stylepicker4ward_import']['preset'],
                    'options' => $presetOptions,
                    'eval' => [
                        'includeBlankOption' => true,
                        'mandatory' => true,
                    ],
                ],
                'preset',
                'layouts',
            ),
        );

        $layoutsWidget = new CheckBox(
            Widget::getAttributesFromDca(
                [
                    'label' => &$GLOBALS['TL_LANG']['stylepicker4ward_import']['layouts'],
                    'foreignKey' => 'tl_layout.name',
                    'eval' => [
                        'mandatory' => true,
                        'multiple' => true,
                    ],
                ],
                'layouts',
            ),
        );

        if (Input::post('import')) {
            $presetWidget->value = Input::post('preset');
            $presetWidget->validate();

            $presetName = $presetWidget->value;

            $layoutsWidget->value = Input::post('layouts');
            $layoutsWidget->validate();

            if (
                !$presetWidget->hasErrors()
                && $presetWidget->submitInput()
                && !$layoutsWidget->hasErrors()
                && $layoutsWidget->submitInput()
                && isset($GLOBALS['STYLEPICKER_PRESET'][$presetName])
            ) {
                $database = Database::getInstance();

                foreach ($GLOBALS['STYLEPICKER_PRESET'][$presetName]['classes'] as $class => $config) {
                    $row = [
                        'id' => null,
                        'pid' => Input::get('id'),
                        'tstamp' => time(),
                        'title' => $config['title'] ?? $class,
                        'description' => $config['description'],
                        'cssclass' => $class,
                        'layouts' => implode(',', $layoutsWidget->value),
                    ];

                    $this->objDc->id = $database
                        ->prepare('INSERT INTO tl_stylepicker4ward %s')
                        ->set($row)
                        ->execute()
                        ->insertId
                    ;

                    $this->objDc->activeRecord = $database
                        ->prepare('SELECT * FROM tl_stylepicker4ward WHERE id=?')
                        ->execute($this->objDc->id)
                    ;

                    $helper = new DcaHelper();

                    // Save content element assignment
                    if (isset($config['ce'])) {
                        $assignedContentElementNames = [];
                        $assignedSectionNames = [];

                        foreach ((array) $config['ce'] as $contentElementPatterns) {
                            foreach ($this->getContentElementNames() as $contentElementName) {
                                foreach ((array) $contentElementPatterns as $contentElementPattern) {
                                    if (fnmatch($contentElementPattern, $contentElementName)) {
                                        $assignedContentElementNames[] = $contentElementName;
                                    }
                                }
                            }
                        }

                        $sectionPatterns = isset($config['section']) ? (array) $config['section'] : ['*'];

                        foreach ($sectionPatterns as $sectionPattern) {
                            foreach ($this->getSectionNames() as $sectionName) {
                                if (fnmatch($sectionPattern, $sectionName)) {
                                    $assignedSectionNames[] = $sectionName;
                                }
                            }
                        }

                        Input::setPost('_CEs', $assignedContentElementNames);
                        Input::setPost('_CE_Row', $assignedSectionNames);

                        $helper->saveCEs(serialize($assignedContentElementNames), $this->objDc);
                    }

                    // Save article assignment
                    if (isset($config['article'])) {
                        $assignedSectionNames = [];

                        foreach ((array) $config['article'] as $sectionPattern) {
                            foreach ($this->getSectionNames() as $sectionName) {
                                if (fnmatch($sectionPattern, $sectionName)) {
                                    $assignedSectionNames[] = $sectionName;
                                }
                            }
                        }

                        Input::setPost('_Article_Row', $assignedSectionNames);

                        $helper->saveArticles(serialize($assignedSectionNames), $this->objDc);

                        // Save article teaser assignment
                        if (isset($config['articleTeaser'])) {
                            $helper->saveArticleTeasers(true, $this->objDc);
                        }
                    }

                    // Save page assignment
                    if (isset($config['page']) && $config['page']) {
                        $helper->savePages($config['page'], $this->objDc);
                    }
                }

                Message::addConfirmation(
                    \sprintf(
                        $GLOBALS['TL_LANG']['stylepicker4ward_import']['confirmation'],
                        $GLOBALS['STYLEPICKER_PRESET'][$presetName]['label'],
                    ),
                );

                Controller::redirect(
                    \sprintf(
                        'contao/main.php?do=themes&table=tl_stylepicker4ward&id=%s&rt=%s&ref=%s',
                        Input::get('id'),
                        System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
                        TL_REFERER_ID,
                    ),
                );
            }
        }

        $template = new BackendTemplate('be_stylepicker4ward_importer');
        $template->presetWidget = $presetWidget;
        $template->layoutsWidget = $layoutsWidget;

        return $template->parse();
    }

    protected function getContentElementNames()
    {
        $contentElements = [];

        foreach ($GLOBALS['TL_CTE'] as $groupedContentElements) {
            $contentElements = array_merge($contentElements, array_keys($groupedContentElements));
        }

        return $contentElements;
    }

    protected function getSectionNames()
    {
        $sections = ['header', 'left', 'right', 'main', 'footer'];

        $layouts = LayoutModel::findBy(['sections!=?'], ['']);
        if ($layouts) {
            foreach ($layouts as $layout) {
                $layoutSections = StringUtil::trimsplit(',', $layout->sections);

                foreach ($layoutSections as $section) {
                    if (!\in_array($section, $sections, true)) {
                        $sections[] = $section;
                    }
                }
            }
        }

        return $sections;
    }
}
