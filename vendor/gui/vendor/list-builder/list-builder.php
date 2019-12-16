<?php
/**
 * Class ListBuilder
 *
 * @package     CRUDService
 * @subpackage  ListBuilder
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/12)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../functional/functional.php');
require_once (__DIR__ . '/../../../template-engine/template-engine.php');
require_once (__DIR__ . '/../../../widgets-registry/vendor/bootstrap-widgets/bootstrap-widgets.php');

require_once (__DIR__ . '/vendor/crud-service-client-adapter/crud-service-client-adapter.php');

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
    var $Fields = [];

    /**
     * Service logic adapter
     *
     * @var ListBuilderAdapter
     */
    var $ListBuilderAdapter = false;

    /**
     * List item transformation callback
     *
     * @var array
     */
    var $RecordTransformer = [];

    /**
     * Constructor
     *
     * @param array $Fields
     *            List of fields
     * @param ListBuilderAdapter $ListBuilderAdapter
     *            Adapter for the data source
     */
    public function __construct(array $Fields, ListBuilderAdapter $ListBuilderAdapter)
    {
        $this->Fields = $Fields;

        $this->ListBuilderAdapter = $ListBuilderAdapter;
    }

    /**
     * Method returns end point for the create page form
     *
     * @return string Create page endpoint
     */
    protected function get_create_page_endpoint(): string
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
    protected function listing_no_items(): string
    {
        $Content = BootstrapWidgets::get('listing-no-items');

        $Content = str_replace('{create-page-endpoint}', $this->get_create_page_endpoint(), $Content);

        return ($Content);
    }

    /**
     * Method displays list of possible buttons
     *
     * @param integer $id
     *            Id of the record
     * @return string Compiled list buttons
     */
    protected function list_of_buttons(int $id): string
    {
        $Content = BootstrapWidgets::get('list-of-buttons');

        return (str_replace('{id}', $id, $Content));
    }

    /**
     * Need to display actions in list
     *
     * @return bool Do we need add actions
     */
    protected function need_actions(): bool
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
    protected function listing_items_cells(bool $AddActions = true): string
    {
        $Content = '';

        foreach ($this->Fields as $Name) {
            if ($Name == 'domain_id') {
                continue;
            }
            if ($Name == 'id') {
                $Content .= BootstrapWidgets::get('listing-row-centered-cell');
            } else {
                $Content .= BootstrapWidgets::get('listing-row-cell');
            }
            $Content = str_replace('{name}', '{' . $Name . '}', $Content);
        }

        if ($AddActions && $this->need_actions()) {
            $Content .= BootstrapWidgets::get('listing-actions');
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
    protected function transform_record(array $Record): array
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
    protected function listing_items(array $Records): string
    {
        $Content = '';

        foreach ($Records as $Record) {
            $Record['actions'] = $this->list_of_buttons(Functional::get_field($Record, 'id'));

            $Content .= BootstrapWidgets::get('listing-row');
            $Content = str_replace('{items}', $this->listing_items_cells(), $Content);

            $Record = $this->transform_record($Record);

            $Record = $this->ListBuilderAdapter->preprocess_list_item($Record);

            $Content = TemplateEngine::print_record($Content, $Record);
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
    protected function listing_header_cells(bool $AddActions = true): string
    {
        $Content = '';

        foreach ($this->Fields as $Name) {
            if ($Name == 'domain_id') {
                continue;
            }

            $IdClass = $Name == 'id' ? ' col-md-1' : '';
            $IdStyle = $Name == 'id' ? 'style="text-align: center;"' : '';

            $Content .= BootstrapWidgets::get('listing-header-cell');
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

        if ($AddActions && $this->need_actions()) {
            $Content .= BootstrapWidgets::get('listing-header-actions');
        }

        return ($Content);
    }

    /**
     * Method returns listing header content
     *
     * @param
     *            string Compiled header
     */
    protected function listing_header_content(): string
    {
        if (@$_GET['create_button'] == 1) {
            $Content = BootstrapWidgets::get('listing-header');

            $Content = str_replace('{create-page-endpoint}', $this->get_create_page_endpoint(), $Content);
        } else {
            $Content = BootstrapWidgets::get('simple-listing-header');
        }

        return ($Content);
    }

    /**
     * Method compiles listing header
     *
     * @return string Compiled header
     */
    protected function listing_header(): string
    {
        $Content = $this->listing_header_content();

        $Content = str_replace('{description}', isset($_GET[DESCRIPTION_FIELD_NAME]) ? $_GET[DESCRIPTION_FIELD_NAME] : 'Выберите необходимое действие', $Content);

        $Content = str_replace('{cells}', $this->listing_header_cells(), $Content);

        return ($Content);
    }

    /**
     * Method compiles listing header
     *
     * @return string Compiled header
     */
    protected function simple_listing_header(): string
    {
        $Content = BootstrapWidgets::get('simple-listing-header');

        $Content = str_replace('{description}', isset($_GET[DESCRIPTION_FIELD_NAME]) ? $_GET[DESCRIPTION_FIELD_NAME] : 'Выберите необходимое действие', $Content);

        $Content = str_replace('{cells}', $this->listing_header_cells(false), $Content);

        return ($Content);
    }

    /**
     * Method compiles listing items
     *
     * @param array $Records
     *            List of records
     * @return string Compiled simple list
     */
    protected function simple_listing_items(array $Records): string
    {
        $Content = '';

        foreach ($Records as $Record) {
            $Content .= str_replace('{items}', $this->listing_items_cells(false), BootstrapWidgets::get('listing-row'));

            $Record = $this->transform_record($Record);

            $Content = TemplateEngine::print_record($Content, $Record);
        }

        return ($Content);
    }

    /**
     * Method compiles listing form
     *
     * @return string Compiled listing form
     */
    public function listing_form(): string
    {
        $Records = $this->ListBuilderAdapter->get_records([
            'field' => 'id',
            'order' => 'ASC'
        ], isset($_GET['from']) ? $_GET['from'] : 0, isset($_GET['limit']) ? $_GET['limit'] : 100);

        if (count($Records)) {
            $Header = $this->listing_header();

            $Items = $this->listing_items($Records);

            $Footer = BootstrapWidgets::get('listing-footer');

            return ($Header . $Items . $Footer);
        } else {
            return ($this->listing_no_items());
        }
    }

    /**
     * Method compiles simple_listing form
     *
     * @return string Compiled simple listing form
     */
    public function simple_listing_form(): string
    {
        $Records = $this->ListBuilderAdapter->all();

        if (count($Records)) {
            $Header = $this->simple_listing_header();

            $Items = $this->simple_listing_items($Records);

            // they are the same with full feature listing
            $Footer = BootstrapWidgets::get('listing-footer');

            return ($Header . $Items . $Footer);
        } else {
            return (BootstrapWidgets::get('listing-no-items'));
        }
    }
}

?>