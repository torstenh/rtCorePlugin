<?php
/*
 * This file is part of the gumnut package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertSnippetAdminActions handles snippet admin functions.
 *
 * @package    gumnut
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 */
class BasertSnippetAdminActions extends sfActions
{
  public function preExecute()
  {
    parent::preExecute();
    rtTemplateToolkit::setBackendTemplateDir();
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->rt_snippets = Doctrine::getTable('rtSnippet')
      ->createQuery('a')
      ->execute();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new rtSnippetForm();
    $this->form->setDefault('collection', $request->getParameter('collection'));
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new rtSnippetForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($rt_snippet = Doctrine::getTable('rtSnippet')->find(array($request->getParameter('id'))), sprintf('Object rt_snippet does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtSnippetForm($rt_snippet);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($rt_snippet = Doctrine::getTable('rtSnippet')->find(array($request->getParameter('id'))), sprintf('Object rt_snippet does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtSnippetForm($rt_snippet);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($rt_snippet = Doctrine::getTable('rtSnippet')->find(array($request->getParameter('id'))), sprintf('Object rt_snippet does not exist (%s).', $request->getParameter('id')));
    $rt_snippet->delete();
    $this->clearCache();
    $this->redirect('rtSnippetAdmin/index');
  }


  public function executeVersions(sfWebRequest $request)
  {
    $this->rt_snippet = $this->getrtSnippet($request);
    $this->rt_snippet_versions = Doctrine::getTable('rtSnippetVersion')->findById($this->rt_snippet->getId());
  }

  public function executeCompare(sfWebRequest $request)
  {
    $this->rt_snippet = $this->getrtSnippet($request);
    $this->current_version = $this->rt_snippet->version;

    if(!$request->hasParameter('version1') || !$request->hasParameter('version2'))
    {
      $this->getUser()->setFlash('error', 'Please select two versions to compare.', false);
      $this->redirect('rtSnippetAdmin/versions?id='.$this->rt_snippet->getId());
    }

    $this->version_1 = $request->getParameter('version1');
    $this->version_2 = $request->getParameter('version2');
    $this->versions = array();

    $this->versions[1] = array(
      'title' => $this->rt_snippet->revert($this->version_1)->title,
      'content' => $this->rt_snippet->revert($this->version_1)->content,
      'collection' => $this->rt_snippet->revert($this->version_1)->collection,
      'updated_at' => $this->rt_snippet->revert($this->version_1)->updated_at
    );
    $this->versions[2] = array(
      'title' => $this->rt_snippet->revert($this->version_2)->title,
      'content' => $this->rt_snippet->revert($this->version_2)->content,
      'collection' => $this->rt_snippet->revert($this->version_1)->collection,
      'updated_at' => $this->rt_snippet->revert($this->version_1)->updated_at
    );
  }

  public function executeRevert(sfWebRequest $request)
  {
    $this->rt_snippet = $this->getrtSnippet($request);
    $this->rt_snippet->revert($request->getParameter('revert_to'));
    $this->rt_snippet->save();
    $this->getUser()->setFlash('notice', 'Reverted to version ' . $request->getParameter('revert_to'), false);
    $this->clearCache($this->rt_snippet);
    $this->redirect('rtSnippetAdmin/edit?id='.$this->rt_snippet->getId());
  }

  public function getrtSnippet(sfWebRequest $request)
  {
    $this->forward404Unless($rt_snippet = Doctrine::getTable('rtSnippet')->find(array($request->getParameter('id'))), sprintf('Object rt_snippet does not exist (%s).', $request->getParameter('id')));
    return $rt_snippet;
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $rt_snippet = $form->save();
      $this->clearCache($rt_snippet);
      $action = $request->getParameter('rt_post_save_action', 'index');
      
      if($action == 'edit')
      {
        $this->redirect('rtSnippetAdmin/edit?id='.$rt_snippet->getId());
      }

      $this->redirect('rtSnippetAdmin/index');
    }
  }

  public function clearCache($rt_snippet = null)
  {
    $cache = $this->getContext()->getViewCacheManager();
    
    if ($cache)
    {
      if(!is_null($rt_snippet))
      {
        $cache->remove('@sf_cache_partial?module=rtSnippet&action=_snippetPanel&sf_cache_key='.$rt_snippet->getCollection());
      }
      else
      {
        $cache->remove('@sf_cache_partial?module=rtSnippet&action=_snippetPanel&sf_cache_key=*');
      }
    }
  }
}
