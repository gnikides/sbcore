<?php namespace Core\ProductSearch;

use Core\Services\Elastic\QueryOptions as Options;

class OptionsFromRequest
{
    const DEFAULT_PER_PAGE              = 500;
    const SORT_REVERSE_CHRONOLOGICAL    = 'reverse_chronological';
    const SORT_CHRONOLOGICAL            = 'chronological';
    const SORT_STARS                    = 'stars';
    const SORT_REVERSE_STARS            = 'reverse_stars';
    const ACCEPTED_SORTS = [
        self::SORT_REVERSE_CHRONOLOGICAL,
        self::SORT_CHRONOLOGICAL,
        self::SORT_STARS,
        self::SORT_REVERSE_STARS
    ];
    const ACCEPTED_FILTERS = [
        'site_id',
        'category_id',
        'country_code',
        //'price_range',
        //'tags'
    ];
    
    public function make($input)
    {
        $options = new Options();
        $sort = $input->get('sort');
        if (!in_array($sort, self::ACCEPTED_SORTS)) {
            $sort = self::SORT_REVERSE_CHRONOLOGICAL;
        }
        if (self::SORT_CHRONOLOGICAL == $sort) {
            $options->setSortColumn('updated_at');
            $options->setSortDirection('asc');
        } elseif (self::SORT_STARS == $sort) {
            $options->setSortColumn('average_rating');
            $options->setSortDirection('desc');
        } elseif (self::SORT_REVERSE_STARS == $sort) {
            $options->setSortColumn('average_rating');
            $options->setSortDirection('asc');
        } else {
            //  self::SORT_REVERSE_CHRONOLOGICAL is default
            $options->setSortColumn('updated_at');
            $options->setSortDirection('desc');
        }
        $options->setPerPage($input->get('per_page', self::DEFAULT_PER_PAGE));
        $options->setPage($input->get('page', 1));
        $options->setIds((array) $input->get('ids'));
        $options->setFilters($input->only(self::ACCEPTED_FILTERS)->toArray() ?? []);
        return $options;
    }
}