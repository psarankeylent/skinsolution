<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Ui\Component\Listing\Column;

/**
 * ViewAction Class
 */
class ViewAction extends \Magento\Sales\Ui\Component\Listing\Column\ViewAction
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $primaryKey = $this->getData('config/indexField') ?: 'entity_id';

        if (isset($dataSource['data']['items'])) {
            $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
            $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'entity_id';
            $linkTextFromColumn = $this->getData('config/linkTextFromColumn');
            $staticLinkText = $this->getData('config/staticLinkText') ?: 'View';

            foreach ($dataSource['data']['items'] as & $item) {
                $links = [];

                if (isset($item[$primaryKey])) {
                    $links['view'] = [
                        'href' => $this->urlBuilder->getUrl(
                            $viewUrlPath,
                            [
                                $urlEntityParamName => $item[$primaryKey]
                            ]
                        ),
                        'label' => $linkTextFromColumn ? $item[$linkTextFromColumn] : __($staticLinkText)
                    ];
                }

                if (!empty($links)) {
                    $item[$this->getData('name')] = $links;
                }
            }
        }

        return $dataSource;
    }
}
