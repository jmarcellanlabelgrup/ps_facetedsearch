<?php

namespace PrestaShop\Module\FacetedSearch;

use PrestaShop\PrestaShop\Core\Product\Search\URLFragmentSerializer;
use PrestaShop\PrestaShop\Core\Product\Search\Filter;

class URLSerializer
{
    public function addFilterToFacetFilters(array $facetFilters, Filter $facetFilter, $facet)
    {
        if ($facet->getProperty('range')) {
            $facetValue = $facetFilter->getValue();
            $facetFilters[$facet->getLabel()] = [
                $facetFilter->getProperty('symbol'),
                $facetValue['from'],
                $facetValue['to'],
            ];
        } else {
            $facetFilters[$facet->getLabel()][$facetFilter->getLabel()] = $facetFilter->getLabel();
        }

        return $facetFilters;
    }

    public function removeFilterFromFacetFilters(array $facetFilters, Filter $facetFilter, $facet)
    {
        if ($facet->getProperty('range')) {
            unset($facetFilters[$facet->getLabel()]);
        } else {
            unset($facetFilters[$facet->getLabel()][$facetFilter->getLabel()]);
            if (empty($facetFilters[$facet->getLabel()])) {
                unset($facetFilters[$facet->getLabel()]);
            }
        }

        return $facetFilters;
    }

    public function getActiveFacetFiltersFromFacets(array $facets)
    {
        $facetFilters = [];
        foreach ($facets as $facet) {
            foreach ($facet->getFilters() as $facetFilter) {
                if ($facet->getProperty('range')) {
                    if ($facetFilter->isActive()) {
                        $facetValue = $facetFilter->getValue();
                        $facetFilters[$facet->getLabel()] = [
                            $facetFilter->getProperty('symbol'),
                            $facetValue['from'],
                            $facetValue['to'],
                        ];
                    }
                } else {
                    if ($facetFilter->isActive()) {
                        $facetFilters[$facet->getLabel()][$facetFilter->getLabel()] = $facetFilter->getLabel();
                    }
                }
            }
        }

        return $facetFilters;
    }

    public function serialize(array $facets)
    {
        $facetFilters = $this->getActiveFacetFiltersFromFacets($facets);
        $urlSerializer = new URLFragmentSerializer();

        return $urlSerializer->serialize($facetFilters);
    }
}
