<?php

namespace Sg\DatatablesBundle\Controller;

use Sg\DatatablesBundle\Datatable\Column\FilterableInterface;
use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Sg\DatatablesBundle\Datatable\Extension\Button;
use Sg\DatatablesBundle\Datatable\Extension\Buttons;
use Sg\DatatablesBundle\Datatable\Extension\Responsive;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;

class JsonController extends Controller
{

    /**
     * @var Packages
     */
    private $assetsHelper;

    /**
     * @var EngineInterface
     */
    private $engine;

    public function __construct(Packages $assetsHelper, EngineInterface $engine)
    {
        $this->assetsHelper = $assetsHelper;
        $this->engine = $engine;
    }

    public function encodeLanguage(Request $request, DatatableInterface $datatable): JsonResponse
    {
        $language = [];

        if ($datatable->getLanguage()->getLanguage() === null) {
            $requestLocale = \mb_substr($request->getLocale(), 0, 2);
            if ($datatable->getLanguage()->isCdnLanguageByLocale() === true) {
                $language['language']['url'] = $this->assetsHelper->getUrl(
                    $datatable->getLanguage()->getLanguageCDNFile()[$requestLocale],
                    'datatables_cdn'
                );
            }
            else {
                if ($datatable->getLanguage()->isLanguageByLocale() === true) {
                    $language['language'] = $this->getTranslationArray($requestLocale);
                }
            }
        }
        else {
            $language['language'] = $this->getTranslationArray(
                \mb_substr($datatable->getLanguage()->getLanguage(), 0, 2)
            );
        }

        return new JsonResponse($language);
    }

    public function encodeAjax(DatatableInterface $datatable): JsonResponse
    {
        $ajaxVars = [];

        if ($datatable->getAjax()->getUrl() !== null) {
            $ajaxVars['url'] = $datatable->getAjax()->getUrl();
        }
        $ajaxVars['type'] = $datatable->getAjax()->getType();
        if ($datatable->getAjax()->getData() !== null) {
            $ajaxVars['data'] = $datatable->getAjax()->getData();
        }

        $ajax = [];
        $ajax['serverSide'] = true;
        if ($datatable->getAjax()->getPipeline() > 0) {
            $ajax['ajax'] = '$.fn.dataTable.pipeline('
                .\json_encode(
                    \array_merge($ajaxVars, ['pages' => $datatable->getAjax()->getPipeline()]),
                    JSON_UNESCAPED_SLASHES
                )
                .')';
        }
        else {
            $ajax['ajax'] = $ajaxVars;
        }

        return new JsonResponse($ajax);
    }

    public function encodeOptions(DatatableInterface $datatable): JsonResponse
    {
        $datatableOptions = $datatable->getOptions();
        $options = [];
        if ($datatableOptions->getDeferLoading() !== null) {
            $options['deferLoading'] = $datatableOptions->getDeferLoading();
        }
        if ($datatableOptions->getDisplayStart() !== null) {
            $options['displayStart'] = $datatableOptions->getDisplayStart();
        }
        if ($datatableOptions->getDom() !== null) {
            // TODO: Escape using JS strategy
            $options['dom'] = $datatableOptions->getDom();
        }
        if ($datatableOptions->getLengthMenu() !== null) {
            $options['lengthMenu'] = $datatableOptions->getLengthMenu();
        }
        if ($datatableOptions->getOrder() !== null) {
            $options['order'] = $datatableOptions->getOrder();
        }
        if ($datatableOptions->isOrderCellsTop() !== null) {
            $options['orderCellsTop'] = $datatableOptions->isOrderCellsTop();
        }
        if ($datatableOptions->isOrderClasses() !== null) {
            $options['orderClasses'] = $datatableOptions->isOrderClasses();
        }
        if ($datatableOptions->getOrderFixed() !== null) {
            $options['orderFixed'] = $datatableOptions->getOrderFixed();
        }
        if ($datatableOptions->isOrderMulti() !== null) {
            $options['orderMulti'] = $datatableOptions->isOrderMulti();
        }
        if ($datatableOptions->getPageLength() !== null) {
            $options['pageLength'] = $datatableOptions->getPageLength();
        }
        if ($datatableOptions->getPagingType() !== null) {
            $options['pagingType'] = $datatableOptions->getPagingType();
        }
        if ($datatableOptions->getRenderer() !== null) {
            $options['renderer'] = $datatableOptions->getRenderer();
        }
        if ($datatableOptions->isRetrieve() !== null) {
            $options['retrieve'] = $datatableOptions->isRetrieve();
        }
        if ($datatableOptions->getRowId() !== null) {
            $options['rowId'] = $datatableOptions->getRowId();
        }
        if ($datatableOptions->isScrollCollapse() !== null) {
            $options['scrollCollapse'] = $datatableOptions->isScrollCollapse();
        }
        if ($datatableOptions->getSearchDelay() !== null) {
            $options['searchDelay'] = $datatableOptions->getSearchDelay();
        }
        if ($datatableOptions->getStateDuration() !== null) {
            $options['stateDuration'] = $datatableOptions->getStateDuration();
        }
        if ($datatableOptions->getStripeClasses() !== null) {
            $options['stripeClasses'] = $datatableOptions->getStripeClasses();
        }

        return new JsonResponse($options);
    }

    public function encodeFeatures(DatatableInterface $datatable): JsonResponse
    {
        $datatableFeatures = $datatable->getFeatures();
        $features = [];

        if ($datatableFeatures->getAutoWidth() !== null) {
            $features['autoWidth'] = $datatableFeatures->getAutoWidth();
        }
        if ($datatableFeatures->getDeferRender() !== null) {
            $features['deferRender'] = $datatableFeatures->getDeferRender();
        }
        if ($datatableFeatures->getInfo() !== null) {
            $features['info'] = $datatableFeatures->getInfo();
        }
        if ($datatableFeatures->getLengthChange() !== null) {
            $features['lengthChange'] = $datatableFeatures->getLengthChange();
        }
        if ($datatableFeatures->getOrdering() !== null) {
            $features['ordering'] = $datatableFeatures->getOrdering();
        }
        if ($datatableFeatures->getPaging() !== null) {
            $features['paging'] = $datatableFeatures->getPaging();
        }
        if ($datatableFeatures->getProcessing() !== null) {
            $features['processing'] = $datatableFeatures->getProcessing();
        }
        if ($datatableFeatures->getScrollX() !== null) {
            $features['scrollX'] = $datatableFeatures->getScrollX();
        }
        if ($datatableFeatures->getScrollY() !== null) {
            $features['scrollY'] = $datatableFeatures->getScrollY();
        }
        if ($datatableFeatures->getSearching() !== null) {
            $features['searching'] = $datatableFeatures->getSearching();
        }
        if ($datatableFeatures->getStateSave() !== null) {
            $features['stateSave'] = $datatableFeatures->getStateSave();
        }

        return new JsonResponse($features);
    }

    public function encodeCallbacks(DatatableInterface $datatable): JsonResponse
    {
        $datatableCallbacks = $datatable->getCallbacks();
        $callbacks = [];

        if ($datatableCallbacks->getCreatedRow() !== null) {
            $callbacks['createdRow'] = $this->renderCallbackTemplate($datatableCallbacks->getCreatedRow());
        }
        if ($datatableCallbacks->getDrawCallback() !== null) {
            $callbacks['drawCallback'] = $this->renderCallbackTemplate($datatableCallbacks->getDrawCallback());
        }
        if ($datatableCallbacks->getFooterCallback() !== null) {
            $callbacks['footerCallback'] = $this->renderCallbackTemplate($datatableCallbacks->getFooterCallback());
        }
        if ($datatableCallbacks->getFormatNumber() !== null) {
            $callbacks['formatNumber'] = $this->renderCallbackTemplate($datatableCallbacks->getFormatNumber());
        }
        if ($datatableCallbacks->getHeaderCallback() !== null) {
            $callbacks['headerCallback'] = $this->renderCallbackTemplate($datatableCallbacks->getHeaderCallback());
        }
        if ($datatableCallbacks->getInfoCallback() !== null) {
            $callbacks['infoCallback'] = $this->renderCallbackTemplate($datatableCallbacks->getInfoCallback());
        }
        if ($datatableCallbacks->getInitComplete() !== null) {
            $callbacks['initComplete'] = $this->renderCallbackTemplate($datatableCallbacks->getInitComplete());
        }
        if ($datatableCallbacks->getPreDrawCallback() !== null) {
            $callbacks['preDrawCallback'] = $this->renderCallbackTemplate($datatableCallbacks->getPreDrawCallback());
        }
        if ($datatableCallbacks->getRowCallback() !== null) {
            $callbacks['rowCallback'] = $this->renderCallbackTemplate($datatableCallbacks->getRowCallback());
        }
        if ($datatableCallbacks->getStateLoadCallback() !== null) {
            $callbacks['stateLoadCallback'] = $this->renderCallbackTemplate(
                $datatableCallbacks->getStateLoadCallback()
            );
        }
        if ($datatableCallbacks->getStateLoaded() !== null) {
            $callbacks['stateLoaded'] = $this->renderCallbackTemplate($datatableCallbacks->getStateLoaded());
        }
        if ($datatableCallbacks->getStateLoadParams() !== null) {
            $callbacks['stateLoadParams'] = $this->renderCallbackTemplate($datatableCallbacks->getStateLoadParams());
        }
        if ($datatableCallbacks->getStateSaveCallback() !== null) {
            $callbacks['stateSaveCallback'] = $this->renderCallbackTemplate(
                $datatableCallbacks->getStateSaveCallback()
            );
        }
        if ($datatableCallbacks->getStateSaveParams() !== null) {
            $callbacks['stateSaveParams'] = $this->renderCallbackTemplate($datatableCallbacks->getStateSaveParams());
        }

        return new JsonResponse($callbacks);
    }

    public function encodeExtensions(DatatableInterface $datatable): JsonResponse
    {
        $datatableExtensions = $datatable->getExtensions();
        $extensions = [];

        if (($buttons = $datatableExtensions->getButtons()) !== null) {
            // the easiest way to activate the extension - buttons is a boolean value (true)
            if ($buttons === true) {
                $extensions['buttons'] = true;
            }
            // handle the Buttons class options
            else {
                if ($buttons instanceof Buttons) {
                    if ($buttons->getShowButtons() !== null) {
                        $extensions['buttons']['showButtons'] = $buttons->getShowButtons();
                    }
                    if ($buttons->getCreateButtons() !== null) {
                        foreach ($buttons->getCreateButtons() as $createButton) {
                            if ($createButton instanceof Button) {
                                $extensions['buttons'][] = $this->createButtonToArray($createButton);
                            }
                        }
                    }
                }
            }
        }

        if (($responsive = $datatableExtensions->getResponsive()) !== null) {
            // the easiest way to activate the extension - responsive is a boolean value (true)
            if ($responsive === true) {
                $extensions['responsive'] = true;
            }
            else {
                if ($responsive instanceof Responsive) {
                    $responsiveDetails = $responsive->getDetails();
                    // details is a simple boolean value
                    if (\is_iterable($responsiveDetails) === false) {
                        $extensions['responsive']['details'] = $responsive->getDetails() ? 'true' : 'false';
                    }
                    // details is an array
                    else {
                        $detailsArray = [];

                        if (\array_key_exists('type', $responsiveDetails)) {
                            $detailsArray['type'] = $responsiveDetails['type'];
                        }
                        if (\array_key_exists('target', $responsiveDetails)) {
                            $detailsArray['target'] = $responsiveDetails['target'];
                        }
                        if (\array_key_exists('renderer', $responsiveDetails)) {
                            $detailsArray['renderer'] = $this->renderCallbackTemplate($responsiveDetails['renderer']);
                        }
                        if (\array_key_exists('display', $responsiveDetails)) {
                            $detailsArray['display'] = $this->renderCallbackTemplate($responsiveDetails['display']);
                        }

                        $extensions['responsive']['details'] = $detailsArray;
                    }
                }
            }
        }

        return new JsonResponse($extensions);
    }

    public function encodeColumns(DatatableInterface $datatable): JsonResponse
    {
        $columns = [];

        foreach ($datatable->getColumnBuilder()->getColumns() as $column) {
            $columns[] = $column->getOptionsArray();
        }

        return new JsonResponse($columns);
    }

    public function encodeInitialSearch(DatatableInterface $datatable): JsonResponse
    {
        $initialSearch = [];

        foreach ($datatable->getColumnBuilder()->getColumns() as $column) {
            $search = null;
            if ($column instanceof FilterableInterface && ($initialSearchString = $column->getFilter(
                )->getInitialSearch()) !== null) {
                $search['search'] = $initialSearchString;
            }
            $initialSearch['searchCols'][] = $search;
        }

        return new JsonResponse($initialSearch);
    }

    private function createButtonToArray(Button $createButton): array
    {
        $createButtonArray = [];

        if ($createButton->getAction() !== null) {
            $createButtonArray['action'] = $this->renderCallbackTemplate(
                $createButton->getAction()
            );
        }
        if ($createButton->getAvailable() !== null) {
            $createButtonArray['available'] = $this->renderCallbackTemplate(
                $createButton->getAvailable()
            );
        }
        if ($createButton->getClassName() !== null) {
            $createButtonArray['className'] = $createButton->getClassName();
        }
        if ($createButton->getDestroy() !== null) {
            $createButtonArray['destroy'] = $this->renderCallbackTemplate(
                $createButton->getDestroy()
            );
        }
        if ($createButton->getEnabled() !== null) {
            $createButtonArray['enabled'] = $createButton->getEnabled();
        }
        if ($createButton->getExtend() !== null) {
            $createButtonArray['extend'] = $createButton->getExtend();
        }
        if ($createButton->getInit() !== null) {
            $createButtonArray['init'] = $createButton->getInit();
        }
        if ($createButton->getKey() !== null) {
            $createButtonArray['key'] = $createButton->getKey();
        }
        if ($createButton->getName() !== null) {
            $createButtonArray['name'] = $createButton->getName();
        }
        if ($createButton->getNamespace() !== null) {
            $createButtonArray['namespace'] = $createButton->getNamespace();
        }
        if ($createButton->getText() !== null) {
            $createButtonArray['text'] = $createButton->getText();
        }
        if ($createButton->getTitleAttr() !== null) {
            $createButtonArray['titleAttr'] = $createButton->getTitleAttr();
        }
        if ($createButton->getButtonOptions() !== null) {
            foreach ($createButton->getButtonOptions() as $key => $buttonOption) {
                $createButtonArray[$key] = \json_encode($buttonOption, JSON_UNESCAPED_SLASHES);
            }
        }

        return $createButtonArray;
    }

    private function renderCallbackTemplate(array $callback): string
    {
        $vars = [];
        if (\array_key_exists('vars', $callback)) {
            $vars = $callback['vars'];
        }

        return $this->engine->render($callback['template'], $vars);
    }

    private function getTranslationArray(string $locale): array
    {
        $translator = $this->get('translator');
        $translator->setLocale($locale);
        $locale =
            [
                'sEmptyTable' => $translator->trans('sg.datatables.sEmptyTable', [], 'datatable'),
                'sInfo' => $translator->trans('sg.datatables.sInfo', [], 'datatable'),
                'sInfoEmpty' => $translator->trans('sg.datatables.sInfoEmpty', [], 'datatable'),
                'sInfoFiltered' => $translator->trans('sg.datatables.sInfoFiltered', [], 'datatable'),
                'sInfoPostFix' => $translator->trans('sg.datatables.sInfoPostFix', [], 'datatable'),
                'sInfoThousands' => $translator->trans('sg.datatables.sInfoThousands', [], 'datatable'),
                'sLengthMenu' => $translator->trans('sg.datatables.sLengthMenu', [], 'datatable'),
                'sLoadingRecords' => $translator->trans('sg.datatables.sLoadingRecords', [], 'datatable'),
                'sProcessing' => $translator->trans('sg.datatables.sProcessing', [], 'datatable'),
                'sSearch' => $translator->trans('sg.datatables.sSearch', [], 'datatable'),
                'sZeroRecords' => $translator->trans('sg.datatables.sZeroRecords', [], 'datatable'),
                'oPaginate' =>
                    [
                        'sFirst' => $translator->trans('sg.datatables.oPaginate.sFirst', [], 'datatable'),
                        'sLast' => $translator->trans('sg.datatables.oPaginate.sLast', [], 'datatable'),
                        'sNext' => $translator->trans('sg.datatables.oPaginate.sNext', [], 'datatable'),
                        'sPrevious' => $translator->trans('sg.datatables.oPaginate.sPrevious', [], 'datatable'),
                    ],
                'oAria' =>
                    [
                        'sSortAscending' => $translator->trans('sg.datatables.oAria.sSortAscending', [], 'datatable'),
                        'sSortDescending' => $translator->trans('sg.datatables.oAria.sSortDescending', [], 'datatable'),
                    ],
            ];

        return $locale;
    }

}