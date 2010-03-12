<?php

/*
 * This file is part of the gumnut package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * gnResponseToolkit provides a set of worker methods for dealing with response objects.
 *
 * @package    gumnut
 * @subpackage toolkit
 * @author     Piers Warmers <piers@wranglers.com.au>
 */
class gnResponseToolkit
{
  /**
   * Update the response meta values with the data contained within a gnPage object.
   *
   * @param gnPage $gn_page
   * @param sfUser $sf_user
   * @param sfWebResponse $sf_response
   * @return void
   */
  public static function setCommonMetasFromPage(gnPage $gn_page, sfUser $sf_user, sfWebResponse $sf_response)
  {
    $data = array();
    $data['robots'] = $gn_page->getSearchable();
    $data['keywords'] = $gn_page->Translation[$sf_user->getCulture()]->getTags();
    $data['title'] = $gn_page->getTitle();
    $data['description'] = $gn_page->getDescription();
    self::setCommonMetas($data, $sf_response);
  }
  
  /**
   * Update the response meta values with the data contained within an array.
   *
   * @param array $data
   * @param sfWebResponse $sf_response
   * @return void
   */
  public static function setCommonMetas(array $data, sfWebResponse $sf_response)
  {
    if(isset($data['searchable']))
    {
      $sf_response->addMeta('robots', $data['searchable'] ? 'index, follow' : 'NONE');
    }

    if(isset($data['keywords']))
    {
      $sf_response->addMeta('keywords', is_array($data['keywords']) ? implode(', ', $data['keywords']) : $data['keywords']);
    }

    if(isset($data['title']))
    {
      $sf_response->addMeta('title', $data['title']);
    }

    if(isset($data['title']))
    {
      $sf_response->addMeta('description', $data['description']);
    }
  }
}