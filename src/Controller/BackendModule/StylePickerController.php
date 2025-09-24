<?php

declare(strict_types=1);

namespace ContaoGraveyard\StylePickerBundle\Controller\BackendModule;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\CoreBundle\Controller\AbstractBackendController;
use Contao\FilesModel;
use Contao\Input;
use Contao\PageModel;
use Contao\System;
use ContaoGraveyard\StylePickerBundle\Event\GetStylePickerFilterEvent;
use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '%contao.backend.route_prefix%/stylepicker',
    name: StylePickerController::class,
    defaults: ['_scope' => 'backend']
)]
class StylePickerController extends AbstractBackendController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TranslatorInterface $translator,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(Request $request): Response
    {
        Controller::loadLanguageFile('defaults');
        Controller::loadLanguageFile('modules');

        $template = new BackendTemplate('be_stylepicker4ward');
        $template->headline = $this->translator->trans('MSC.stylepicker4ward', [], 'contao_default');

        $inputName = Input::get('inputName');

        if (!preg_match('~^[a-z\\-_0-9]+$~i', $inputName)) {
            throw new \InvalidArgumentException('Field-Parameter ERROR!');
        }

        $template->field = $inputName;

        $table = Input::get('tbl');
        $field = Input::get('fld');
        $id  = Input::get('id');

        $section = false;
        $condition = false;
        $layout = [];

        // find pid (stylesheet-id) and section
        switch ($table) {
            case 'tl_content':
                $objContent = $this->connection->fetchAssociative(
                    'SELECT type, pid FROM tl_content WHERE id = ?',
                    [$id]
                );

                $id = $objContent['pid'] ?? null;
                $condition = $objContent['type'] ?? null;

            // no break

            case 'tl_article':
                $objArticle = $this->connection->fetchAssociative(
                    'SELECT pid, inColumn FROM tl_article WHERE id = ?',
                    [$id]
                );

                $section = $objArticle['inColumn'] ?? null;
                $id  = $objArticle['pid']  ?? null;

            // no break

            case 'tl_page':
                $objPage = PageModel::findWithDetails($id);
                $layout  = $objPage->layout;
                break;

            default:
                /*
                 * HOOK to get table,PID(s),section and condition
                 * in-parameter: str $table, int $id
                 * out-parameter as array or FALSE if the callback does not match:
                 * 		array($tbl,$pids,$sec,$cond)
                 * 		str $tbl: table name, mostly the same as from the in-parameter
                 * 		array $layout: ID of Pagelayout
                 * 		str $sec: a section (column) identifier
                 * 		str $cond: some addition condition
                 */
                /*if (isset($GLOBALS['TL_HOOKS']['stylepicker4ward_getFilter']) && is_array($GLOBALS['TL_HOOKS']['stylepicker4ward_getFilter'])) {
                    foreach ($GLOBALS['TL_HOOKS']['stylepicker4ward_getFilter'] as $callback) {
                        System::importStatic($callback[0]);
                        $result = $this->{$callback[0]}->{$callback[1]}($table, $id);
                        if (is_array($result)) {
                            [$table, $layout, $section, $condition] = $result;
                            break;
                        }
                    }
                }*/

                $event = new GetStylePickerFilterEvent($table, (int) $id);
                $this->eventDispatcher->dispatch($event);

                $layout = $event->getLayout();
                $section = $event->getSection();
                $condition = $event->getCondition();

                break;
        }

        // build where clause
        // respect the order for little query optimising
        if (!preg_match('~^[a-z0-9_\\-]+$~i', (string) $table)) {
            throw new \InvalidArgumentException('unexpected chars in tbl-param');
        }
        if (!preg_match('~^[a-z0-9_\\-]*$~i', (string) $section)) {
            throw new \InvalidArgumentException('unexpected chars in sec-param');
        }

        $arrWhere = ['c.tstamp <> 0'];

        if ($layout) {
            $arrWhere[] = 'FIND_IN_SET(' . $layout . ',c.layouts)';
        }
        $arrWhere[] = 'tbl="' . $table . '"';
        if ($section) {
            $arrWhere[] = 'sec="' . $section . '"';
        }
        if (strlen($field)) {
            $arrWhere[] = 'fld="' . $field . '"';
        }

        // get all classes
        $arrItems = $this->connection->fetchAllAssociative(
            'SELECT c.*, GROUP_CONCAT(DISTINCT t.cond SEPARATOR ",") AS cond
     FROM tl_stylepicker4ward_target AS t
     LEFT JOIN tl_stylepicker4ward AS c ON (t.pid = c.id)
     WHERE ' . implode(' AND ', $arrWhere) . '
     GROUP BY c.id
     ORDER BY c.title'
        );

        // resolve images
        foreach ($arrItems as &$item) {
            if (!empty($item['image'])) {
                $objFile = FilesModel::findByUuid($item['image']);
                $item['image'] = $objFile;
            }
        }
        unset($item);

        // filter condition
        if ($condition) {
            foreach ($arrItems as $k => $item) {
                if (!empty($item['cond'])) {
                    $arrConds = explode(',', (string) $item['cond']);
                    if (!in_array($condition, $arrConds, true)) {
                        unset($arrItems[$k]);
                    }
                }
            }
        }

        $template->items = $arrItems;

        return new Response($template->parse());
    }
}
