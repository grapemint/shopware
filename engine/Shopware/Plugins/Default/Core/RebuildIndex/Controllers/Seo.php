<?php
/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * @category  Shopware
 * @package   Shopware\Plugins\RebuildIndex\Controllers\Backend
 * @copyright Copyright (c) 2012, shopware AG (http://www.shopware.de)
 */
class Shopware_Controllers_Backend_Seo extends Shopware_Controllers_Backend_ExtJs
{

    /**
     * Helper function to get the new seo index component with auto completion
     * @return Shopware_Components_SeoIndex
     */
    public function SeoIndex()
    {
        return Shopware()->SeoIndex();
    }

    /**
     * Helper function to get the sRewriteTable class with auto completion.
     * @return sRewriteTable
     */
    public function RewriteTable()
    {
        return Shopware()->Modules()->RewriteTable();
    }



    /**
     * Clean up seo links. remove links of non-existing categories, articles...
     */
    public function initSeoAction()
    {
        $shopId = (int) $this->Request()->getParam('shop', 1);

        @set_time_limit(1200);

        // Create shop
        $this->SeoIndex()->registerShop($shopId);

        $this->RewriteTable()->baseSetup();
        $this->RewriteTable()->sCreateRewriteTableCleanup();


        $this->View()->assign(array(
            'success' => true
        ));
    }

    public function getCountAction()
    {
        $shopId = (int) $this->Request()->getParam('shop', 1);
        @set_time_limit(1200);
        $category = $this->SeoIndex()->countCategories($shopId);
        $article = $this->SeoIndex()->countArticles($shopId);
        $blog = $this->SeoIndex()->countBlogs($shopId);
        $emotion = $this->SeoIndex()->countEmotions($shopId);

        $statistic = 1;
        $content = 0;
        if ($shopId === 1) {
            $content = $this->SeoIndex()->countContent();
        }

        $this->View()->assign(array(
            'success' => true,
            'data' => array('counts' => array(
                'category' => $category,
                'article' => $article,
                'blog' => $blog,
                'emotion' => $emotion,
                'statistic' => $statistic,
                'content' => $content
            ))
        ));
    }

    /**
     * Create static seo links
     */
    public function seoStaticAction()
    {

        $shopId = (int) $this->Request()->getParam('shop', 1);
        @set_time_limit(1200);

        // Create shop
        $this->SeoIndex()->registerShop($shopId);

        $this->RewriteTable()->baseSetup();
        $this->RewriteTable()->sCreateRewriteTableStatic();

        $this->View()->assign(array(
            'success' => true
        ));
    }

    /**
     * Creates seo links for categories
     */
    public function seoCategoryAction()
    {

        @set_time_limit(1200);
        $offset = $this->Request()->getParam('offset');
        $limit = $this->Request()->getParam('limit', 50);
        $shopId = (int) $this->Request()->getParam('shop', 1);

        // Create shop
        $shop = $this->SeoIndex()->registerShop($shopId);

        $this->RewriteTable()->baseSetup();
        $this->RewriteTable()->sCreateRewriteTableCategories($offset, $limit);

        $this->View()->assign(array(
            'success' => true
        ));
    }

    /**
     * Create blog SEO links
     */
    public function seoBlogAction()
    {
        @set_time_limit(1200);

        $offset = $this->Request()->getParam('offset', 0);
        $limit = $this->Request()->getParam('limit', 50);
        $shopId = (int) $this->Request()->getParam('shop', 1);

        // Create shop
        $shop = $this->SeoIndex()->registerShop($shopId);

        $this->RewriteTable()->baseSetup();
        $this->RewriteTable()->sCreateRewriteTableBlog($offset, $limit);

        $this->View()->assign(array(
            'success' => true
        ));
    }

    /**
     * Create SEO urls for articles
     */
    public function seoArticleAction()
    {
        @set_time_limit(1200);
        $limit = $this->Request()->getParam('limit', 50);
        $shopId = (int) $this->Request()->getParam('shop', 1);

        // Create shop
        $$this->SeoIndex()->registerShop($shopId);

        list($cachedTime, $elementId, $shopId) = $this->SeoIndex()->getCachedTime();

        $this->RewriteTable()->baseSetup();

        $currentTime = Shopware()->Db()->fetchOne('SELECT ?', array(new Zend_Date()));
        $this->SeoIndex()->setCachedTime($currentTime, $elementId, $shopId);

        $this->RewriteTable()->sCreateRewriteTableArticles('1900-01-01', $limit);
        $this->SeoIndex()->setCachedTime($currentTime, $elementId, $shopId);

        $this->View()->assign(array(
            'success' => true
        ));
    }

    /**
     * Create SEO urls for emotion landing pages
     */
    public function seoEmotionAction()
    {
        @set_time_limit(1200);
        $offset = $this->Request()->getParam('offset', 0);
        $limit = $this->Request()->getParam('limit', 50);
        $shopId = (int) $this->Request()->getParam('shop', 1);

        // Create shop
        $shop = $this->SeoIndex()->registerShop($shopId);

        // Make sure a template is available
        $this->RewriteTable()->baseSetup();

        $this->RewriteTable()->sCreateRewriteTableCampaigns($offset, $limit);

        $this->View()->assign(array(
            'success' => true
        ));
    }

    /**
     * Create SEO links for CMS/tickets
     */
    public function seoContentAction()
    {
        @set_time_limit(1200);
        $offset = $this->Request()->getParam('offset', 0);
        $limit = $this->Request()->getParam('limit', 50);
        $shopId = (int) $this->Request()->getParam('shop', 1);

        // Create shop
        $shop = $this->SeoIndex()->registerShop($shopId);

        // Make sure a template is available
        $this->RewriteTable()->baseSetup();

        $this->RewriteTable()->sCreateRewriteTableContent($offset, $limit);

        $this->View()->assign(array(
            'success' => true
        ));
    }
}

