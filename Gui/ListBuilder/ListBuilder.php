<?php
namespace Mezon\Gui;

/**
 * Class ListBuilder
 *
 * @package CrudService
 * @subpackage ListBuilder
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/12)
 * @copyright Copyright (c) 2019, aeon.org
 */
define('DESCRIPTION_FIELD_NAME', 'description');

/**
 * Class constructs grids.
 */
class ListBuilder
{

    /**
     * Fields
     *
     * @var array
     */
    protected $Fields = [];

    /**
     * Service logic adapter
     *
     * @var \Mezon\Gui\ListBuilder\ListBuilderAdapter
     */
    protected $ListBuilderAdapter = false;

    /**
     * List item transformation callback
     *
     * @var array
     */
    protected $RecordTransformer = [];

    /**
     * Constructor
     *
     * @param array $Fields
     *            List of fields
     * @param \Mezon\Gui\ListBuilder\ListBuilderAdapter $ListBuilderAdapter
     *            Adapter for the data source
     */
    public function __construct(
        array $Fields,
        \Mezon\Gui\ListBuilder\ListBuilderAdapter $ListBuilderAdapter)
    {
        $this->Fields = $Fields;

        $this->ListBuilderAdapter = $ListBuilderAdapter;
    }

    /**
     * Method returns end point for the create page form
     *
     * @return string Create page endpoint
     */
    protected function getCreatePageEndpoint(): string
    {
        if (isset($_GET['create-page-endpoint'])) {
            return ($_GET['create-page-endpoint']);
        }

        return ('../create/');
    }

    /**
     * Method shows "no records" message instead of listing
     *
     * @return string Compiled list view
     */
    protected function listingNoItems(): string
    {
        $Content = \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-no-items');

        $Content = str_replace('{create-page-endpoint}', $this->getCreatePageEndpoint(), $Content);

        return ($Content);
    }

    /**
     * Method displays list of possible buttons
     *
     * @param int $id
     *            Id of the record
     * @return string Compiled list buttons
     */
    protected function listOfButtons(int $id): string
    {
        $Content = \Mezon\WidgetsRegistry\BootstrapWidgets::get('list-of-buttons');

        return (str_replace('{id}', $id, $Content));
    }

    /**
     * Need to display actions in list
     *
     * @return bool Do we need add actions
     */
    protected function needActions(): bool
    {
        if (@$_GET['update_button'] == 1 || @$_GET['delete_button'] == 1) {
            return (true);
        } else {
            return (false);
        }
    }

    /**
     * Method compiles listing items cells
     *
     * @param bool $AddActions
     *            Do we need to add actions
     * @return string Compiled row
     */
    protected function listingItemsCells(bool $AddActions = true): string
    {
        $Content = '';

        foreach ($this->Fields as $Name) {
            if ($Name == 'domain_id') {
                continue;
            }
            if ($Name == 'id') {
                $Content .= \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-row-centered-cell');
            } else {
                $Content .= \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-row-cell');
            }
            $Content = str_replace('{name}', '{' . $Name . '}', $Content);
        }

        if ($AddActions && $this->needActions()) {
            $Content .= \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-actions');
        }

        return ($Content);
    }

    /**
     * Method transforms database record
     *
     * @param array $Record
     *            Transforming record
     * @return array Transformed record
     */
    protected function transformRecord(array $Record): array
    {
        // here we assume that we get from service
        // already transformed
        // and here we provide only additional transformations
        if (is_callable($this->RecordTransformer)) {
            $Record = call_user_func($this->RecordTransformer, $Record);
        }

        return ($Record);
    }

    /**
     * Method compiles listing items
     *
     * @param array $Records
     *            Listof records
     * @return string Compiled list items
     */
    protected function listingItems(array $Records): string
    {
        $Content = '';

        foreach ($Records as $Record) {
            $Record['actions'] = $this->listOfButtons(\Mezon\Functional\Functional::getField($Record, 'id'));

            $Content .= \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-row');
            $Content = str_replace('{items}', $this->listingItemsCells(), $Content);

            $Record = $this->transformRecord($Record);

            $Record = $this->ListBuilderAdapter->preprocessListItem($Record);

            $Content = \Mezon\TemplateEngine\TemplateEngine::printRecord($Content, $Record);
        }

        return ($Content);
    }

    /**
     * Method compiles header cells
     *
     * @param bool $AddActions
     *            Do we need to add actions
     * @return string Compiled header
     */
    protected function listingHeaderCells(bool $AddActions = true): string
    {
        $Content = '';

        foreach ($this->Fields as $Name) {
            if ($Name == 'domain_id') {
                continue;
            }

            $IdClass = $Name == 'id' ? ' col-md-1' : '';
            $IdStyle = $Name == 'id' ? 'style="text-align: center;"' : '';

            $Content .= \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-header-cell');
            $Content = str_replace([
                '{id-class}',
                '{id-style}',
                '{title}'
            ], [
                $IdClass,
                $IdStyle,
                $Name
            ], $Content);
        }

        if ($AddActions && $this->needActions()) {
            $Content .= \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-header-actions');
        }

        return ($Content);
    }

    /**
     * Method returns listing header content
     *
     * @param
     *            string Compiled header
     */
    protected function listingHeaderContent(): string
    {
        if (@$_GET['create_button'] == 1) {
            $Content = \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-header');

            $Content = str_replace('{create-page-endpoint}', $this->getCreatePageEndpoint(), $Content);
        } else {
            $Content = \Mezon\WidgetsRegistry\BootstrapWidgets::get('simple-listing-header');
        }

        return ($Content);
    }

    /**
     * Method compiles listing header
     *
     * @return string Compiled header
     */
    protected function listingHeader(): string
    {
        $Content = $this->listingHeaderContent();

        $Content = str_replace(
            '{description}',
            isset($_GET[DESCRIPTION_FIELD_NAME]) ? $_GET[DESCRIPTION_FIELD_NAME] : 'Выберите необходимое действие',
            $Content);

        $Content = str_replace('{cells}', $this->listingHeaderCells(), $Content);

        return ($Content);
    }

    /**
     * Method compiles listing header
     *
     * @return string Compiled header
     */
    protected function simpleListingHeader(): string
    {
        $Content = \Mezon\WidgetsRegistry\BootstrapWidgets::get('simple-listing-header');

        $Content = str_replace(
            '{description}',
            isset($_GET[DESCRIPTION_FIELD_NAME]) ? $_GET[DESCRIPTION_FIELD_NAME] : 'Выберите необходимое действие',
            $Content);

        $Content = str_replace('{cells}', $this->listingHeaderCells(false), $Content);

        return ($Content);
    }

    /**
     * Method compiles listing items
     *
     * @param array $Records
     *            List of records
     * @return string Compiled simple list
     */
    protected function simpleListingItems(array $Records): string
    {
        $Content = '';

        foreach ($Records as $Record) {
            $Content .= str_replace(
                '{items}',
                $this->listingItemsCells(false),
                \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-row'));

            $Record = $this->transformRecord($Record);

            $Content = \Mezon\TemplateEngine\TemplateEngine::printRecord($Content, $Record);
        }

        return ($Content);
    }

    /**
     * Method compiles listing form
     *
     * @return string Compiled listing form
     */
    public function listingForm(): string
    {
        $Records = $this->ListBuilderAdapter->getRecords([
            'field' => 'id',
            'order' => 'ASC'
        ], isset($_GET['from']) ? $_GET['from'] : 0, isset($_GET['limit']) ? $_GET['limit'] : 100);

        if (count($Records)) {
            $Header = $this->listingHeader();

            $Items = $this->listingItems($Records);

            $Footer = \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-footer');

            return ($Header . $Items . $Footer);
        } else {
            return ($this->listingNoItems());
        }
    }

    /**
     * Method compiles simple_listing form
     *
     * @return string Compiled simple listing form
     */
    public function simpleListingForm(): string
    {
        $Records = $this->ListBuilderAdapter->all();

        if (count($Records)) {
            $Header = $this->simpleListingHeader();

            $Items = $this->simpleListingItems($Records);

            // they are the same with full feature listing
            $Footer = \Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-footer');

            return ($Header . $Items . $Footer);
        } else {
            return (\Mezon\WidgetsRegistry\BootstrapWidgets::get('listing-no-items'));
        }
    }

    /**
     * Method returns fields of the list
     *
     * @return array fields list
     */
    public function getFields(): array
    {
        return ($this->Fields);
    }
}
