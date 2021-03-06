<?php

/**
 * PluginrtSnippetTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginrtSnippetTable extends Doctrine_Table
{
  /**
   * Adds a check for pages which have been published.
   *
   * @param Doctrine_Query $query
   * @return Doctrine_Query
   */
  public function findAllPublishedByCollection($collection, $limit = 1, Doctrine_Query $query = null)
  {
    $query = $this->addPublishedQuery($query);
    $query = $this->addSiteQuery($query);
    $query->andWhere('snippet.collection =?', $collection)
          ->orderBy('snippet.position')
          ->limit($limit);
    return $query->execute();
  }

  /**
   * Adds a check for pages which have been published.
   *
   * @param Doctrine_Query $query
   * @return Doctrine_Query
   */
  public function addPublishedQuery(Doctrine_Query $query = null)
  {
    $query = $this->getQuery($query);
    $query->andWhere('(snippet.published_from < ? OR snippet.published_from IS NULL)', date('Y-m-d H:i:s', time()));
    $query->andWhere('(snippet.published_to > ? OR snippet.published_to IS NULL)', date('Y-m-d H:i:s', time()));
    $query->andWhere('snippet.published = 1');
    return $query;
  }

  /**
   * Adds a check for pages which belong to the current domain/site.
   *
   * Note: this will only be activated if the rt_enable_multi_site config value is set to true.
   *
   * @param Doctrine_Query $query
   * @return Doctrine_Query
   */
  public function addSiteQuery(Doctrine_Query $query = null)
  {
    $query = $this->getQuery($query);

    if(rtSiteToolkit::isMultiSiteEnabled())
    {
      $query->leftJoin('snippet.rtSite site')
            ->andWhere('site.domain = ?', rtSiteToolkit::getCurrentDomain());
    }

    return $query;
  }

  /**
   * Return a query object, creting a new one if needed.
   *
   * @param Doctrine_Query $query
   * @return Doctrine_Query
   */
  public function getQuery(Doctrine_Query $query = null)
  {
    if(is_null($query))
    {
      $query = parent::createQuery('snippet');
    }

    return $query;
  }
    /**
     * Returns an instance of this class.
     *
     * @return object PluginrtSnippetTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PluginrtSnippet');
    }
}